-- This file is automatically generated using maintenance/generateSchemaSql.php.
-- Source: ./tables.json
-- Do not modify this file directly.
-- See https://www.mediawiki.org/wiki/Manual:Schema_changes
CREATE TABLE /*_*/shorturls (
  su_id INT AUTO_INCREMENT NOT NULL,
  su_namespace INT NOT NULL,
  su_title VARCHAR(255) NOT NULL,
  UNIQUE INDEX shorturls_ns_title (su_namespace, su_title),
  PRIMARY KEY(su_id)
) /*$wgDBTableOptions*/;
