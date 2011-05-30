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
	public static function EncodeTitle ( $title ) {
		global $wgMemc;

		$id = $wgMemc->get( wfMemcKey( 'shorturls', 'title', $title->getFullText() ) );
		if ( !$id ) {
			$dbr = wfGetDB( DB_SLAVE );
			$query = $dbr->select(
				'shorturls',
				array( 'su_id' ),
				array( 'su_namespace' => $title->getNamespace(), 'su_title' => $title->getDBkey() ),
				__METHOD__ );
			if ( $dbr->numRows( $query ) > 0 ) {
				$entry = $dbr->fetchObject( $query );
				$id = $entry->su_id;
			} else {
				$dbw = wfGetDB( DB_MASTER );
				$row_data = array(
					'su_id' => $dbw->nextSequenceValue( 'shorturls_id_seq' ),
					'su_namespace' => $title->getNamespace(),
					'su_title' => $title->getDBkey()
				);
				$dbw->insert( 'shorturls', $row_data );
				$id = $dbw->insertId();
			}
			$wgMemc->set( wfMemcKey( 'shorturls', 'title', $title->getFullText() ), $id, 0 );
		}
		return base_convert( $id, 10, 36 );
	}

	/**
	 * @param $data string
	 * @return Title
	 */
	public static function DecodeURL ( $urlfragment ) {
		global $wgMemc;

		$id = intval( base_convert ( $urlfragment, 36, 10 ) );
		$entry = $wgMemc->get( wfMemcKey( 'shorturls', 'id', $id ) );
		if ( !$entry ) {
			$dbr = wfGetDB( DB_SLAVE );
			$query = $dbr->select(
				'shorturls',
				array( 'su_namespace', 'su_title' ),
				array( 'su_id' => $id ),
				__METHOD__
			);

			$entry = $dbr->fetchRow( $query ); // Less overhead on memcaching
			$wgMemc->set( wfMemcKey( 'shorturls', 'id', $id ), $entry, 0 );
		}
		return Title::makeTitle( $entry['su_namespace'], $entry['su_title'] );
	}

	/**
	 * @param $title Title 
	 * @return True if a shorturl needs to be displayed
	 */
	public static function NeedsShortUrl( $title ) {
		return $title->exists() && ! $title->equals( Title::newMainPage() );
	}
}
