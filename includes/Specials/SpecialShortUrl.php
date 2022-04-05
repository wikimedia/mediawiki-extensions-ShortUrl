<?php
/**
 * A special page that provides redirects to articles via their page IDs
 *
 * @file
 * @ingroup Extensions
 * @author Yuvi Panda, http://yuvi.in
 * @copyright © 2011 Yuvaraj Pandian (yuvipanda@yuvi.in)
 * @license BSD-3-Clause
 */

namespace MediaWiki\Extension\ShortUrl\Specials;

use MediaWiki\Extension\ShortUrl\Utils;
use UnlistedSpecialPage;

/**
 * Provides the actual redirection
 *
 * @ingroup SpecialPage
 */
class SpecialShortUrl extends UnlistedSpecialPage {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'ShortUrl' );
	}

	/**
	 * Main execution function
	 *
	 * @param string|null $par Parameters passed to the page
	 */
	public function execute( $par ) {
		$out = $this->getOutput();

		$title = Utils::decodeURL( $par );
		if ( $title !== false ) {
			$out->redirect( $title->getFullURL(), '301' );
		} else {
			$parEsc = wfEscapeWikiText( $par );
			$out->showErrorPage( 'shorturl-not-found-title', 'shorturl-not-found-message', [ $parEsc ] );
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function getGroupName() {
		return 'pagetools';
	}
}
