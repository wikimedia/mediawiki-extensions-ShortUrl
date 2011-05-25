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

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "not a valid entry point.\n" );
	die( 1 );
}

require_once "ShortUrl.functions.php";

/**
 * Provides the contact form
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
        global $wgOut, $wgRequest;
        
        $title = shorturlDecode( $par );
        if ( $title ) {
            $wgOut->redirect( $title->getFullURL(), '301' );
            return;
        }
		// Wrong ID
		$notfound = Html::element( 'p', array(), wfMsg ( 'shorturl-not-found', $id ) );
		$wgOut->addHTML( $notfound );
	}
}
