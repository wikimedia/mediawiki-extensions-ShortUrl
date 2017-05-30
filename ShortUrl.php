<?php
/**
 * Setup for ShortUrl extension, a special page that provides redirects to articles
 * via their page IDs
 *
 * @file
 * @ingroup Extensions
 * @author Yuvi Panda, http://yuvi.in
 * @copyright © 2011 Yuvaraj Pandian (yuvipanda@yuvi.in)
 * @licence Modified BSD License
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is an extension to the MediaWiki software and cannot be used standalone.\n";
	die( 1 );
}

/**
 * Configuration variables
 * Template to use for the shortened URL. $1 is replaced with the ShortURL id.
 * $wgServer is prepended to $wgShortUrlTemplate for displaying the URL.
 * mod_rewrite (or equivalent) needs to be setup to produce a shorter URL.
 * See example redirect.htaccess file.
 * Default is false which just uses the (not so short) URL that all Special Pages get
 * Eg: /wiki/Special:ShortUrl/5234
 * An example value for this variable might be:
 * $wgShortUrlPrefix = '/r/$1';
 */
$wgShortUrlTemplate = false;

/**
 * If read-only mode is enabled,
 * no new short URLs will be created, but existing
 * ones will continue to be routed properly
 *
 * @var bool
 */
$wgShortUrlReadOnly = false;

// Extension credits that will show up on Special:Version
$wgExtensionCredits['specialpage'][] = [
	'path' => __FILE__,
	'name' => 'ShortUrl',
	'version' => '1.2.0',
	'author' => 'Yuvi Panda',
	'url' => 'https://www.mediawiki.org/wiki/Extension:ShortUrl',
	'descriptionmsg' => 'shorturl-desc',
];

// Set up the new special page
$wgMessagesDirs['ShortUrl'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['ShortUrl'] = __DIR__ . '/ShortUrl.i18n.php';
$wgExtensionMessagesFiles['ShortUrlAlias'] = __DIR__ . '/ShortUrl.alias.php';

$wgAutoloadClasses['ShortUrlUtils'] = __DIR__ . '/ShortUrl.utils.php';
$wgAutoloadClasses['ShortUrlHooks'] = __DIR__ . '/ShortUrl.hooks.php';
$wgAutoloadClasses['SpecialShortUrl'] = __DIR__ . '/SpecialShortUrl.php';
$wgSpecialPages['ShortUrl'] = 'SpecialShortUrl';

$wgHooks['SkinTemplateToolboxEnd'][] = 'ShortUrlHooks::addToolboxLink';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'ShortUrlHooks::setupSchema';
$wgHooks['OutputPageBeforeHTML'][] = 'ShortUrlHooks::onOutputPageBeforeHTML';
$wgHooks['WebRequestPathInfoRouter'][] = 'ShortUrlHooks::setupUrlRouting';

$wgResourceModules['ext.shortUrl'] = [
	'scripts' => 'js/ext.shortUrl.js',
	'styles' => 'css/ext.shortUrl.css',
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'ShortUrl',
	'dependencies' => [ 'mediawiki.Uri' ],
];
