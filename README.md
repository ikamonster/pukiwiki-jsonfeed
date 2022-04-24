# PukiWiki用プラグイン<br>JSON Feed出力 jsonfeed.inc.php

JSON Feed を出力する[PukiWiki](https://pukiwiki.osdn.jp/)用プラグイン。  
標準プラグイン rss.inc.php の JSON Feed 版です。  
具体的には、RecentChanges をフィードに変換して出力します。  
出力はファイルにキャッシュし、次回からページが更新されるまで処理を省きます。

JSON Feed とは、2017年に制定された JSON形式のウェブフィードです。  
XML形式の RSS や Atom に比べ、パースが簡単に済みます。  
大量のフィードも比較的高速に処理でき、JavaScriptでの扱いも楽です。

|対象PukiWikiバージョン|対象PHPバージョン|
|:---:|:---:|
|PukiWiki 1.5.3 ~ 1.5.4 (UTF-8)|PHP 7.4 ~ 8.1|

## インストール

下記GitHubページからダウンロードした jsonfeed.inc.php を PukiWiki の plugin ディレクトリに配置してください。

[https://github.com/ikamonster/pukiwiki-jsonfeed](https://github.com/ikamonster/pukiwiki-jsonfeed)

## 使い方

```
（ウィキのURL）?plugin=jsonfeed[&ver=1.1]
```

ver … JSON Feed のバージョン。1.0か1.1。省略時の既定値は1.1
