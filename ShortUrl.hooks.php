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

class ShortUrlHooks {
	/**
	 * @param $router
	 * @return bool
	 *
	 * Adds ShortURL rules to the URL router.
	 */
	public static function setupUrlRouting( $router ) {
		global $wgShortUrlTemplate;
		if ( $wgShortUrlTemplate ) {
			$router->add( $wgShortUrlTemplate,
				array( 'title' => SpecialPage::getTitleFor( 'ShortUrl', '$1' )->getPrefixedText()  )
			);
		}
		return true;
	}

	/**
	 * @param $tpl
	 * @return bool
	 */
	public static function addToolboxLink( &$tpl ) {
		global $wgShortUrlTemplate;

		if ( !is_string( $wgShortUrlTemplate ) ) {
			$urlTemplate = SpecialPage::getTitleFor( 'ShortUrl', '$1' )->getFullUrl();
		} else {
			$urlTemplate = $wgServer . $wgShortUrlTemplate;
		}

		$title = $tpl->getSkin()->getTitle();
		if ( ShortUrlUtils::needsShortUrl( $title ) ) {
			$shortId = ShortUrlUtils::encodeTitle( $title );
			$shortURL = str_replace( '$1', $shortId, $urlTemplate );
			$html = Html::rawElement( 'li',	array( 'id' => 't-shorturl' ),
				Html::Element( 'a', array(
					'href' => $shortURL,
					'title' => wfMsg( 'shorturl-toolbox-title' )
				),
				wfMsg( 'shorturl-toolbox-text' ) )
			);

			echo $html;
		}
		return true;
	}

	/**
	 * @param $out OutputPage
	 * @param $text string the HTML text to be added
	 * @return bool
	 */
	public static function onOutputPageBeforeHTML( &$out, &$text ) {
		$title = $out->getTitle();
		if ( ShortUrlUtils::needsShortUrl( $title ) ) {
			$out->addModules( 'ext.shortUrl' );
		}
		return true;
	}

	/**
	 * @param $du DatabaseUpdater
	 * @return bool
	 */
	public static function setupSchema( DatabaseUpdater $du ) {
		$base = dirname( __FILE__ ) . '/schemas';
		$du->addExtensionTable( 'shorturls', "$base/shorturls.sql" );
		return true;
	}

}

