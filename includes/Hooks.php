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

use DatabaseUpdater;
use OutputPage;
use PathRouter;
use Skin;
use SpecialPage;
use Wikimedia\Rdbms\DBReadOnlyError;

class Hooks {
	/**
	 * Add ShortURL rules to the URL router.
	 * @param PathRouter $router
	 */
	public static function onWebRequestPathInfoRouter( $router ): void {
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
	public static function onSidebarBeforeOutput( Skin $skin, array &$sidebar ) {
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
	public static function onBeforePageDisplay( $out, $skin ): void {
		global $wgShortUrlReadOnly;
		$title = $out->getTitle();

		if ( !$wgShortUrlReadOnly && Utils::needsShortUrl( $title ) ) {
			$out->addModules( 'ext.shortUrl' );
		}
	}

	/**
	 * @param DatabaseUpdater $updater
	 */
	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater ): void {
		$dbType = $updater->getDB()->getType();
		if ( $dbType === 'mysql' ) {
			$updater->addExtensionTable( 'shorturls',
				dirname( __DIR__ ) . '/schemas/tables-generated.sql'
			);
		} elseif ( $dbType === 'sqlite' ) {
			$updater->addExtensionTable( 'shorturls',
				dirname( __DIR__ ) . '/schemas/sqlite/tables-generated.sql'
			);
		} elseif ( $dbType === 'postgres' ) {
			$updater->addExtensionTable( 'shorturls',
				dirname( __DIR__ ) . '/schemas/postgres/tables-generated.sql'
			);
		}
	}
}
