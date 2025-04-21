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

namespace MediaWiki\Extension\ShortUrl;

use MediaWiki\Hook\SidebarBeforeOutputHook;
use MediaWiki\Hook\WebRequestPathInfoRouterHook;
use MediaWiki\Output\Hook\BeforePageDisplayHook;
use MediaWiki\Output\OutputPage;
use MediaWiki\Request\PathRouter;
use MediaWiki\Skin\Skin;
use MediaWiki\SpecialPage\SpecialPage;
use Wikimedia\Rdbms\DBReadOnlyError;

class Hooks implements
	SidebarBeforeOutputHook,
	BeforePageDisplayHook,
	WebRequestPathInfoRouterHook
{
	/**
	 * Add ShortURL rules to the URL router.
	 * @param PathRouter $router
	 */
	public function onWebRequestPathInfoRouter( $router ) {
		global $wgShortUrlTemplate;
		if ( $wgShortUrlTemplate ) {
			// Hardcode full title to avoid T78018. It shouldn't matter because the
			// page redirects immediately.
			$router->add( $wgShortUrlTemplate, [ 'title' => 'Special:ShortUrl/$1' ] );
		}
	}

	/**
	 * Add toolbox link the sidebar
	 *
	 * @param Skin $skin
	 * @param array &$sidebar
	 */
	public function onSidebarBeforeOutput( $skin, &$sidebar ): void {
		$link = self::addToolboxLink( $skin );
		if ( $link ) {
			$sidebar['TOOLBOX']['shorturl'] = $link;
		}
	}

	/**
	 * Create toolbox link
	 *
	 * @param Skin $skin
	 * @return array|bool
	 */
	public static function addToolboxLink( Skin $skin ) {
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
		if ( Utils::needsShortUrl( $title ) ) {
			try {
				$shortId = Utils::encodeTitle( $title );
			} catch ( DBReadOnlyError $e ) {
				$shortId = false;
			}
			if ( $shortId !== false ) {
				$shortURL = str_replace( '$1', $shortId, $urlTemplate );

				return [
					'id' => 't-shorturl',
					'href' => $shortURL,
					'title' => $skin->msg( 'shorturl-toolbox-title' )->text(),
					'text' => $skin->msg( 'shorturl-toolbox-text' )->text(),
				];
			}
		}
		return false;
	}

	/**
	 * @param OutputPage $out
	 * @param Skin $skin
	 */
	public function onBeforePageDisplay( $out, $skin ): void {
		global $wgShortUrlReadOnly;
		$title = $out->getTitle();

		if ( !$wgShortUrlReadOnly && Utils::needsShortUrl( $title ) ) {
			$out->addModules( 'ext.shortUrl' );
		}
	}
}
