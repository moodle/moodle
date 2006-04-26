CREATE TABLE `prefix_enrol_authorize` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cclastfour` int(4) unsigned NOT NULL default '0',
  `ccexp` varchar(6) default '',
  `cvv` varchar(4) default '',
  `ccname` varchar(255) NOT NULL default '',
  `courseid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `avscode` char(1) default 'P',
  `transid` varchar(255) default '',
  PRIMARY KEY  (`id`),
  KEY `courseid` (`courseid`),
  KEY `userid` (`userid`)
) TYPE=MyISAM COMMENT='Holds all known information about creditcard transactions';
