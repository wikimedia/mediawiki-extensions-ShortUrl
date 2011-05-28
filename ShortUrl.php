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
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

// Extension credits that will show up on Special:Version
$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'ShortUrl',
	'author' => 'Yuvi Panda',
	'url' => 'http://www.mediawiki.org/wiki/Extension:ShortUrl',
	'descriptionmsg' => 'shorturl-desc',
);

// Set up the new special page
$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['ShortUrl'] = $dir . 'ShortUrl.i18n.php';

$wgAutoloadClasses['ShortUrlHooks'] = $dir . 'ShortUrl.hooks.php';
$wgAutoloadClasses['SpecialShortUrl'] = $dir . 'SpecialShortUrl.php';
$wgSpecialPages['ShortUrl'] = 'SpecialShortUrl';

$wgHooks['SkinTemplateToolboxEnd'][] = 'ShortUrlHooks::AddToolboxLink';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'ShortUrlHooks::SetupSchema';
$wgHooks['OutputPageBeforeHTML'][] = 'ShortUrlHooks::OutputPageBeforeHTML';

$wgResourceModules['ext.shortUrl'] = array(
	'scripts' => 'js/ext.shortUrl.js',
	'styles' => 'css/ext.shortUrl.css',
	'dependencies' => array( 'jquery' ),
	'localBasePath' => dirname( __FILE__ ),
	'remoteExtPath' => 'ShortUrl'
);

// Configuration
$wgShortUrlPrefix = '/wiki/Special:ShortUrl/';
