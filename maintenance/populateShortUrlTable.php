<?php

use MediaWiki\Maintenance\Maintenance;

// @codeCoverageIgnoreStart
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../..';
}
require_once "$IP/maintenance/Maintenance.php";
// @codeCoverageIgnoreEnd

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
		$dbw = $this->getPrimaryDB();
		$dbw->newInsertQueryBuilder()
			->insertInto( 'shorturls' )
			->ignore()
			->rows( $a )
			->caller( __METHOD__ )
			->execute();
	}

	/**
	 * @todo FIXME: Refactor out code in ShortUrlUtils.php so it can be used here
	 */
	public function execute() {
		$rowCount = 0;
		$dbr = $this->getReplicaDB();

		$last_processed_id = 0;

		while ( true ) {
			$insertBuffer = [];
			$res = $dbr->newSelectQueryBuilder()
				->select( [ 'page_id', 'page_namespace', 'page_title' ] )
				->from( 'page' )
				->where( $dbr->expr( 'page_id', '>', $last_processed_id ) )
				->limit( 100 )
				->orderBy( 'page_id' )
				->caller( __METHOD__ )
				->fetchResultSet();
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
			$this->waitForReplication();
			$this->output( $rowCount . " titles done\n" );
		}
		$this->output( "Done\n" );
	}
}

// @codeCoverageIgnoreStart
$maintClass = PopulateShortUrlTable::class;
require_once RUN_MAINTENANCE_IF_MAIN;
// @codeCoverageIgnoreEnd
