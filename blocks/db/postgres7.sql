# $Id$
# 
# Table structure for table blocks
# 

CREATE TABLE prefix_blocks (
  id SERIAL8 PRIMARY KEY,
  name varchar(40) NOT NULL default '',
  version INT8 NOT NULL default '0',
  cron INT8  NOT NULL default '0',
  lastcron INT8  NOT NULL default '0',
  visible int NOT NULL default '1',
  multiple int NOT NULL default '1'
) ;

CREATE TABLE prefix_block_instance (
  id SERIAL8 PRIMARY KEY,
  blockid INT8 not null default '0',
  pageid INT8 not null default '0',
  pagetype enum('course') not null,
  position enum('l', 'r') not null,
  weight int not null default '0',
  visible int not null default '0',
  configdata text not null default ''
) ;
      
# --------------------------------------------------------
