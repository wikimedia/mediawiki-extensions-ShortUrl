<?php

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = dirname( __FILE__ ) . '/../..';
}
require( "$IP/maintenance/Maintenance.php" );

class PopulateShortUrlsTable extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Populates ShortUrls Table with all existing articles";
	}

	private function insertRows($a) {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->insert( 
			'shorturls', 
			$a,
			__METHOD__
		);

    }

    //FIXME: Refactor out code in ShortUrl.functions.php so it can be used here
	public function execute() {
		$rowCount = 1;
		$dbr = wfGetDB( DB_SLAVE );
        $all_titles = $dbr->select(
			"page",
			array( "page_namespace", "page_title" )
		);
		$insert_buffer = array();
		$row = $dbr->fetchRow( $all_titles );

		while( $row ) {
			$row_data = array(
				'su_namespace' => $row['page_namespace'],
				'su_title' => $row['page_title']
			);
			array_push( $insert_buffer, $row_data );
			if( count( $insert_buffer ) % 100 == 0) {
				$this->insertRows( $insert_buffer );
				$insert_buffer = array();
			}
			$this->output( $rowCount . " titles done\n" );
			
			$row = $dbr->fetchRow( $all_titles );
			$rowCount++;
		}
		if( count( $insert_buffer ) > 0 ) {
			$this->insertRows( $insert_buffer );
		}
	}
}

$maintClass = "PopulateShortUrlsTable";
require_once( DO_MAINTENANCE );
