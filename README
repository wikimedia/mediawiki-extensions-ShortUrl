--------------------------------------------------------------------------
README for the ShortUrl extension
Copyright © 2011 Yuvi Panda
Licenses: Modified BSD License
--------------------------------------------------------------------------

<https://mediawiki.org/wiki/Extension:ShortUrl>

== Installing ==

Copy the ShortUrl directory into the extensions folder of your
MediaWiki installation. Then add the following lines to your
LocalSettings.php file (near the end):

  require_once( "$IP/extensions/ShortUrl/ShortUrl.php" );

== Configuration ==

$wgShortUrlTemplate specifies the template to use for the shorturl.
$1 in the template is replaced with the ShortURL id.
$wgServer is prepended before $wgShortUrlTemplate.
Defaults to using the longer form (with Special:ShortUrl). Can be
condensed down to use anything else, via mod_rewrite rules
