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
  visible int NOT NULL default '1'
) ;
# --------------------------------------------------------
