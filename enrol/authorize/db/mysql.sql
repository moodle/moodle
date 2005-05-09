CREATE TABLE `prefix_enrol_authorize` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cclastfour` int(11) default '0',
  `ccexp` varchar(6) default '',
  `cvv` varchar(4) default '',
  `ccname` varchar(255) default '',
  `courseid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `avscode` char(1) default 'P',
  `transid` varchar(255) default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Holds all known information about creditcard transactions';
