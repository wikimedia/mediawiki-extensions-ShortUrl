<?php
/**
 * Functions used for decoding/encoding ids in ShortUrl Extension
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

/**
 * Utility functions for encoding and decoding short URLs
 */
class ShortUrlUtils {

	/**
	 * @param $title Title
	 * @return mixed|string
	 */
	public static function encodeTitle( Title $title ) {
		global $wgMemc;

		$memcKey = wfMemcKey( 'shorturls', 'title', md5( $title->getPrefixedText() ) );

		$id = $wgMemc->get( $memcKey );
		if ( !$id ) {
			$id = wfGetDB( DB_SLAVE )->selectField(
				'shorturls',
				'su_id',
				array(
					'su_namespace' => $title->getNamespace(),
					'su_title' => $title->getDBkey()
				),
				__METHOD__
			);

			// Automatically create an ID for this title if missing...
			if ( !$id ) {
				$dbw = wfGetDB( DB_MASTER );
				$dbw->insert(
					'shorturls',
					array(
						'su_id' => $dbw->nextSequenceValue( 'shorturls_id_seq' ),
						'su_namespace' => $title->getNamespace(),
						'su_title' => $title->getDBkey()
					),
					__METHOD__,
					array( 'IGNORE' )
				);

				if ( $dbw->affectedRows() ) {
					$id = $dbw->insertId();
				} else {
					// Raced out; get the winning ID
					$id = $dbw->selectField(
						'shorturls',
						'su_id',
						array(
							'su_namespace' => $title->getNamespace(),
							'su_title' => $title->getDBkey()
						),
						__METHOD__,
						array( 'LOCK IN SHARE MODE' ) // ignore snapshot
					);
				}
			}

			$wgMemc->set( $memcKey, $id );
		}

		return base_convert( $id, 10, 36 );
	}

	/**
	 * @param $urlFragment String
	 * @return Title
	 */
	public static function decodeURL( $urlFragment ) {
		global $wgMemc;

		$id = intval( base_convert( $urlFragment, 36, 10 ) );
		$memcKey = wfMemcKey( 'shorturls', 'id', $id );
		$entry = $wgMemc->get( $memcKey );
		if ( !$entry ) {
			$dbr = wfGetDB( DB_SLAVE );
			$entry = $dbr->selectRow(
				'shorturls',
				array( 'su_namespace', 'su_title' ),
				array( 'su_id' => $id ),
				__METHOD__
			);

			if ( $entry === false ) {
				return false; // No such shorturl exists
			}
			$wgMemc->set( $memcKey, $entry, 0 );
		}

		return Title::makeTitle( $entry->su_namespace, $entry->su_title );
	}

	/**
	 * @param $title Title
	 * @return Boolean: true if a short URL needs to be displayed
	 */
	public static function needsShortUrl( $title ) {
		return $title->exists() && !$title->isMainPage();
	}
}
