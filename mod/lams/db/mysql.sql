# This file contains a complete database schema for all the 
# tables used by this module, written in SQL

# It may also contain INSERT statements for particular data 
# that may be used, especially new entries in the table log_display

CREATE TABLE prefix_lams (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  introduction text NOT NULL,
  learning_session_id bigint(20),
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY course (course)
)COMMENT='LAMS activity';  

