<?php
/**
 * Schema hooks for ShortUrl
 *
 * @file
 * @ingroup Extensions
 * @license BSD-3-Clause
 */

namespace MediaWiki\Extension\ShortUrl;

use MediaWiki\Installer\DatabaseUpdater;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;

class SchemaHooks implements LoadExtensionSchemaUpdatesHook {
	/**
	 * @param DatabaseUpdater $updater
	 */
	public function onLoadExtensionSchemaUpdates( $updater ) {
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
