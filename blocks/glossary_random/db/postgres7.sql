#
# Table structure for table `block_glossary_random`
#

CREATE TABLE prefix_block_glossary_random (
  id SERIAL8 PRIMARY KEY,
  course int8 NOT NULL default '0',
  title varchar(50) default NULL,
  glossary int8 NOT NULL default '0',
  previous int8 NOT NULL default '0',
  type int4 NOT NULL default '0',
  addentry varchar(255) default NULL,
  viewglossary varchar(255) default NULL,
  invisible varchar(255) default NULL,
  cache text NOT NULL,
  refresh int8 NOT NULL default'0',	
  nexttime int8 NOT NULL default '0'
) ;
