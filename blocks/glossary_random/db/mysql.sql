#
# Table structure for table `block_glossary_random`
#

CREATE TABLE prefix_block_glossary_random (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  title varchar(50) default NULL,
  glossary int(10) unsigned NOT NULL default '0',
  previous int(10) unsigned NOT NULL default '0',
  type tinyint(4) NOT NULL default '0',
  addentry varchar(255) default NULL,
  viewglossary varchar(255) default NULL,
  invisible varchar(255) default NULL,
  cache text NOT NULL,
  refresh int(10) unsigned NOT NULL default'0',	
  nexttime int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;
