<?php
/**
 * Hooks for ShortUrl for adding link to toolbox
 *
 * @file
 * @ingroup Extensions
 * @author Yuvi Panda, http://yuvi.in
 * @copyright © 2011 Yuvaraj Pandian (yuvipanda@yuvi.in)
 * @licence Modified BSD License
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	exit( 1 );
}

require_once "ShortUrl.functions.php";

class ShortUrlHooks {
	/**
	 * @param $tpl
	 * @return bool
	 */
	public static function AddToolboxLink( &$tpl ) {
		global $wgOut, $wgShortUrlPrefix;
		$title = $wgOut->getTitle();
		if ( $title->exists() && ! $title->equals( Title::newMainPage() ) ) {
			$shortId = shorturlEncode( $title );
			$shortURL = $wgShortUrlPrefix . $shortId;
			$html = Html::rawElement( 'li',	array( 'id' => 't-shorturl' ),
				Html::Element( 'a', array(
					'href' => $shortURL,
					'title' => wfMsg( 'shorturl-toolbox-title' )
				),
				wfMsg ( 'shorturl-toolbox-text' ) )
			);

			echo $html;
		}
		return true;
	}

	/**
	 * @param $du DatabaseUpdater
	 * @return bool
	 */
	public static function SetupSchema( DatabaseUpdater $du ) {
		$base = dirname( __FILE__ ) . '/schemas';
		$du->addExtensionTable( "shorturls", "$base/shorturls.sql" );
		return true;
	}

}

