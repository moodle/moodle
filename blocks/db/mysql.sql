# $Id$
# 
# Table structure for table `blocks`
# 

CREATE TABLE `prefix_blocks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(40) NOT NULL default '',
  `version` int(10) NOT NULL default '0',
  `cron` int(10) unsigned NOT NULL default '0',
  `lastcron` int(10) unsigned NOT NULL default '0',
  `visible` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------
