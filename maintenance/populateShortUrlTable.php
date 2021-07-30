<?php

use MediaWiki\MediaWikiServices;

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../..';
}
require_once "$IP/maintenance/Maintenance.php";

class PopulateShortUrlTable extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Populates ShortUrls Table with all existing articles' );
		$this->requireExtension( 'ShortUrl' );
	}

	/**
	 * @param mixed $a
	 */
	private function insertRows( $a ) {
		$dbw = wfGetDB( DB_PRIMARY );
		$dbw->insert(
			'shorturls',
			$a,
			__METHOD__,
			[ 'IGNORE' ]
		);
	}

	/**
	 * @todo FIXME: Refactor out code in ShortUrlUtils.php so it can be used here
	 */
	public function execute() {
		$rowCount = 0;
		$dbr = wfGetDB( DB_REPLICA );
		$lbFactory = MediaWikiServices::getInstance()->getDBLoadBalancerFactory();

		$last_processed_id = 0;

		while ( true ) {
			$insertBuffer = [];
			$res = $dbr->select(
				'page',
				[ 'page_id', 'page_namespace', 'page_title' ],
				[ 'page_id > ' . $last_processed_id ],
				__METHOD__,
				[ 'LIMIT' => 100, 'ORDER BY' => 'page_id' ]
			);
			if ( $res->numRows() == 0 ) {
				break;
			}

			foreach ( $res as $row ) {
				$rowCount++;

				$rowData = [
					'su_namespace' => $row->page_namespace,
					'su_title' => $row->page_title
				];
				$insertBuffer[] = $rowData;

				$last_processed_id = $row->page_id;
			}

			$this->insertRows( $insertBuffer );
			$lbFactory->waitForReplication();
			$this->output( $rowCount . " titles done\n" );
		}
		$this->output( "Done\n" );
	}
}

$maintClass = PopulateShortUrlTable::class;
require_once RUN_MAINTENANCE_IF_MAIN;
