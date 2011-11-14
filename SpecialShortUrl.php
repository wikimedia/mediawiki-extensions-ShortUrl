<?php
/**
 * A special page that provides redirects to articles via their page IDs
 *
 * @file
 * @ingroup Extensions
 * @author Yuvi Panda, http://yuvi.in
 * @copyright © 2011 Yuvaraj Pandian (yuvipanda@yuvi.in)
 * @licence Modified BSD License
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo( "not a valid entry point.\n" );
	die( 1 );
}

/**
 * Provides the actual redirection
 * @ingroup SpecialPage
 */
class SpecialShortUrl extends SpecialPage {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'ShortUrl' );
	}

	/**
	 * Main execution function
	 *
	 * @param $par Mixed: Parameters passed to the page
	 */
	public function execute( $par ) {
		global $wgOut;

		$title = ShortUrlUtils::decodeURL( $par );
		if ( $title !== false ) {
			$wgOut->redirect( $title->getFullURL(), '301' );
		} else {
			$wgOut->showErrorPage( 'shorturl-not-found-title', 'shorturl-not-found-message', array( $par ) );
		}
	}
}
