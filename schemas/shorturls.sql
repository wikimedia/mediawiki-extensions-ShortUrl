-- Replace /*_*/ with the proper prefix
-- Replace /*$wgDBTableOptions*/ with the correct options

CREATE TABLE IF NOT EXISTS /*_*/shorturls (    
    su_id integer NOT NULL AUTO_INCREMENT,
    su_namespace integer NOT NULL,
    su_title varchar(255) NOT NULL,
    PRIMARY KEY (su_id)
) /*$wgDBTableOptions*/;

CREATE INDEX /*i*/su_id ON /*_*/shorturls (su_id);
CREATE INDEX /*i*/su_title ON /*_*/shorturls (su_namespace, su_title);
