# $Id$
# 
# Table structure for table `blocks`
# 

CREATE TABLE `prefix_block` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(40) NOT NULL default '',
  `version` int(10) NOT NULL default '0',
  `cron` int(10) unsigned NOT NULL default '0',
  `lastcron` int(10) unsigned NOT NULL default '0',
  `visible` tinyint(1) NOT NULL default '1',
  `multiple` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `prefix_block_instance` (
  `id` int(10) not null auto_increment,
  `blockid` int(10) not null default '0',
  `pageid` int(10) not null default '0',
  `pagetype` varchar(20) not null default '',
  `position` varchar(10) not null default '',
  `weight` tinyint(3) not null default '0',
  `visible` tinyint(1) not null default '0',
  `configdata` text not null default '',
  PRIMARY KEY(`id`),
  INDEX pageid(`pageid`)
) TYPE=MyISAM;

CREATE TABLE `prefix_block_pinned` (
  `id` int(10) not null auto_increment,
  `blockid` int(10) not null default '0',
  `pagetype` varchar(20) not null default '',
  `position` varchar(10) not null default '',
  `weight` tinyint(3) not null default '0',
  `visible` tinyint(1) not null default '0',
  `configdata` text not null default '',
  PRIMARY KEY(`id`)
) TYPE=MyISAM;



# --------------------------------------------------------
