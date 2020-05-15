<?php
/**
 * Hooks for ShortUrl for adding link to toolbox
 *
 * @file
 * @ingroup Extensions
 * @author Yuvi Panda, http://yuvi.in
 * @copyright © 2011 Yuvaraj Pandian (yuvipanda@yuvi.in)
 * @license BSD-3-Clause
 */

use Wikimedia\Rdbms\DBReadOnlyError;

class ShortUrlHooks {
	/**
	 * @param PathRouter $router
	 * @return bool
	 *
	 * Adds ShortURL rules to the URL router.
	 */
	public static function setupUrlRouting( $router ) {
		global $wgShortUrlTemplate;
		if ( $wgShortUrlTemplate ) {
			// Hardcode full title to avoid T78018. It shouldn't matter because the
			// page redirects immediately.
			$router->add( $wgShortUrlTemplate, [ 'title' => 'Special:ShortUrl/$1' ] );
		}
		return true;
	}

	/**
	 * Add toolbox link to modern skins e.g. Vector
	 * @param Skin $skin
	 * @param array &$sidebar
	 */
	public static function onSidebarBeforeOutput( $skin, &$sidebar ) {
		if ( isset( $sidebar['TOOLBOX'] ) ) {
			$link = self::addToolboxLink( $skin );
			if ( $link ) {
				$sidebar['TOOLBOX'][] = $link;
			}
		}
	}

	/**
	 * Add the URL shorterner link to legacy skins.
	 * @param SkinTemplate $tpl
	 */
	public static function onSkinTemplateToolboxEnd( $tpl ) {
		$link = self::addToolboxLink( $tpl->getSkin() );
		if ( $link ) {
			echo Html::rawElement( 'li', [ 'id' => $link['id'] ],
				Html::element( 'a', [
					'href' => $link['href'],
					'title' => $link['title'],
				], $link['text'] )
			);
		}
	}

	/**
	 * @param Skin $skin
	 * @return array|bool
	 */
	public static function addToolboxLink( $skin ) {
		global $wgShortUrlTemplate, $wgServer, $wgShortUrlReadOnly;
		if ( $wgShortUrlReadOnly ) {
			return false;
		}

		if ( !is_string( $wgShortUrlTemplate ) ) {
			$urlTemplate = SpecialPage::getTitleFor( 'ShortUrl', '$1' )->getFullUrl();
		} else {
			$urlTemplate = $wgServer . $wgShortUrlTemplate;
		}

		$title = $skin->getTitle();
		if ( ShortUrlUtils::needsShortUrl( $title ) ) {
			try {
				$shortId = ShortUrlUtils::encodeTitle( $title );
			} catch ( DBReadOnlyError $e ) {
				$shortId = false;
			}
			if ( $shortId !== false ) {
				$shortURL = str_replace( '$1', $shortId, $urlTemplate );

				return [
					'id' => 't-shorturl',
					'href' => $shortURL,
					'title' => wfMessage( 'shorturl-toolbox-title' )->text(),
					'text' => wfMessage( 'shorturl-toolbox-text' )->text(),
				];
			}
		}
		return false;
	}

	/**
	 * @param OutputPage &$out
	 * @param string &$text the HTML text to be added
	 * @return bool
	 */
	public static function onOutputPageBeforeHTML( &$out, &$text ) {
		global $wgShortUrlReadOnly;
		$title = $out->getTitle();
		if ( !$wgShortUrlReadOnly && ShortUrlUtils::needsShortUrl( $title ) ) {
			$out->addModules( 'ext.shortUrl' );
		}
		return true;
	}

	/**
	 * @param DatabaseUpdater $du
	 * @return bool
	 */
	public static function setupSchema( DatabaseUpdater $du ) {
		$base = dirname( __DIR__ ) . '/schemas';
		$du->addExtensionTable( 'shorturls', "$base/shorturls.sql" );
		return true;
	}
}
