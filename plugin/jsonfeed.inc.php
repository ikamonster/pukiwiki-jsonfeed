<?php
// PukiWiki - Yet another WikiWikiWeb clone
// jsonfeed.inc.php v1.11
// Copyright 2020 M.Taniguchi
// License: GPL v3 or (at your option) any later version
//
// JSON Feed plugin: Publishing JSON Feed of RecentChanges

// Usage: plugin=jsonfeed[&ver=1.1] (Default: 1.1)

function plugin_jsonfeed_action() {
	global $vars, $rss_max;

	$json = plugin_jsonfeed_makejson($rss_max, isset($vars['ver'])? $vars['ver'] : '', true);
	pkwk_common_headers();
	header('Content-type: application/feed+json; charset=utf-8');
	echo $json;

	exit;
}

function plugin_jsonfeed_makejson($rss_max = 10, $version = '1.1', $action = false) {
	global $page_title, $whatsnew;

	switch ($version) {
	case '':
	case '1.1':
		$version = '1.1';
		break;

	case '1.0':
	case '1':
		$version = '1.0';
		break;

	default:
		if ($action) die('Invalid JSON Feed version!!');
		return false;
	}

	$recent = CACHE_DIR . PKWK_MAXSHOW_CACHE;
	if (!file_exists($recent)) {
		if ($action) die(PKWK_MAXSHOW_CACHE . ' is not found');
		return false;
	}

	$cacheFile = CACHE_DIR . 'jsonfeed-' . $version . '.dat';
	if (!file_exists($cacheFile) || (filemtime($cacheFile) < filemtime($recent))) {
		$page_title_utf8 = mb_convert_encoding($page_title, 'UTF-8', SOURCE_ENCODING);
		$self = get_base_uri(PKWK_URI_ABSOLUTE);

		$items = array();
		if ($rss_max > 0) {
			foreach (file_head($recent, $rss_max) as $line) {
				list($time, $page) = explode("\t", rtrim($line));
				$url = get_page_uri($page, PKWK_URI_ABSOLUTE);
				$title  = mb_convert_encoding($page, 'UTF-8', SOURCE_ENCODING);

				switch ($version) {
				case '1.0':
				case '1.1':
					$date = date('Y-m-d\TH:i:sP', $time);
					$summary = date('Y-m-d\TH:i:sP', $time);
					$items[] = array(
						'id' => $url,
						'url' => $url,
						'title' => $title,
						'date_published' => $date
					);
					break;
				}
			}
		}

		$r_whatsnew = pagename_urlencode($whatsnew);
		$description = 'PukiWiki RecentChanges';

		switch ($version) {
		case '1.0':
			$feed = array(
				'version' => 'https:'.'//jsonfeed.org/version/1',
				'title' => $page_title_utf8,
				'home_page_url' => $self . '?' . $r_whatsnew,
				'feed_url' => $self . '?plugin=jsonfeed',
				'description' => $description,
				'items' => $items
			);
			break;

		case '1.1':
			$feed = array(
				'version' => 'https:'.'//jsonfeed.org/version/1.1',
				'title' => $page_title_utf8,
				'home_page_url' => $self . '?' . $r_whatsnew,
				'feed_url' => $self . '?plugin=jsonfeed',
				'description' => $description,
				'items' => $items
			);
			break;
		}

		$json = json_encode($feed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

		$fp = fopen($cacheFile, 'w');
		flock($fp, LOCK_EX);
		rewind($fp);
		fwrite($fp, $json);
		flock($fp, LOCK_UN);
		fclose($fp);
	} else {
		$fp = fopen($cacheFile, 'r');
		$json = fread($fp, filesize($cacheFile));
		fclose($fp);
	}

	return $json;
}
