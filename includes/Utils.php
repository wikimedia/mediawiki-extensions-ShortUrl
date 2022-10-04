<?php
/**
 * Functions used for decoding/encoding ids in ShortUrl Extension
 *
 * @file
 * @ingroup Extensions
 * @author Yuvi Panda, http://yuvi.in
 * @copyright © 2011 Yuvaraj Pandian (yuvipanda@yuvi.in)
 * @license BSD-3-Clause
 */

namespace MediaWiki\Extension\ShortUrl;

use MediaWiki\MediaWikiServices;
use Title;

/**
 * Utility functions for encoding and decoding short URLs
 */
class Utils {

	/**
	 * @param Title $title
	 * @return string|bool false if read-only mode
	 */
	public static function encodeTitle( Title $title ) {
		global $wgShortUrlReadOnly;

		if ( $wgShortUrlReadOnly ) {
			// Not creating any new ids
			return false;
		}

		$fname = __METHOD__;
		$cache = MediaWikiServices::getInstance()->getMainWANObjectCache();

		return $cache->getWithSetCallback(
			$cache->makeKey( 'shorturls-title', md5( $title->getPrefixedText() ) ),
			$cache::TTL_MONTH,
			static function () use ( $title, $fname ) {
				$id = wfGetDB( DB_REPLICA )->selectField(
					'shorturls',
					'su_id',
					[
						'su_namespace' => $title->getNamespace(),
						'su_title' => $title->getDBkey()
					],
					$fname
				);

				// Automatically create an ID for this title if missing...
				if ( !$id ) {
					$dbw = wfGetDB( DB_PRIMARY );
					$dbw->insert(
						'shorturls',
						[
							'su_id' => $dbw->nextSequenceValue( 'shorturls_id_seq' ),
							'su_namespace' => $title->getNamespace(),
							'su_title' => $title->getDBkey()
						],
						$fname,
						[ 'IGNORE' ]
					);

					if ( $dbw->affectedRows() ) {
						$id = $dbw->insertId();
					} else {
						// Raced out; get the winning ID
						$id = $dbw->selectField(
							'shorturls',
							'su_id',
							[
								'su_namespace' => $title->getNamespace(),
								'su_title' => $title->getDBkey()
							],
							$fname,
							// ignore snapshot
							[ 'LOCK IN SHARE MODE' ]
						);
					}
				}

				return base_convert( $id, 10, 36 );
			}
		);
	}

	/**
	 * @param string|null $urlFragment
	 * @return Title|bool
	 */
	public static function decodeURL( $urlFragment ) {
		if ( $urlFragment === null
			|| !preg_match( '/^[0-9a-z]+$/i', $urlFragment )
		) {
			return false;
		}

		$id = intval( base_convert( $urlFragment, 36, 10 ) );

		$fname = __METHOD__;
		$cache = MediaWikiServices::getInstance()->getMainWANObjectCache();
		$row = $cache->getWithSetCallback(
			$cache->makeKey( 'shorturls-id', $id ),
			$cache::TTL_MONTH,
			static function () use ( $id, $fname ) {
				$dbr = wfGetDB( DB_REPLICA );

				$row = $dbr->selectRow(
					'shorturls',
					[ 'su_namespace', 'su_title' ],
					[ 'su_id' => $id ],
					$fname
				);

				return $row ? (array)$row : false;
			}
		);

		return $row ? Title::makeTitle( $row['su_namespace'], $row['su_title'] ) : false;
	}

	/**
	 * @param Title $title
	 * @return bool true if a short URL needs to be displayed
	 */
	public static function needsShortUrl( $title ) {
		return $title->exists() && !$title->isMainPage();
	}
}
