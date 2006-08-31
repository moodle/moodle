CREATE TABLE `prefix_enrol_authorize` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `paymentmethod` enum('cc', 'echeck') NOT NULL default 'cc',
  `cclastfour` int(4) unsigned NOT NULL default '0',
  `ccname` varchar(255) NOT NULL default '',
  `courseid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `transid` int(10) unsigned NOT NULL default '0',
  `status` int(10) unsigned NOT NULL default '0',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `settletime` int(10) unsigned NOT NULL default '0',
  `amount` varchar(10) NOT NULL default '',
  `currency` varchar(3) NOT NULL default 'USD',
  PRIMARY KEY  (`id`),
  KEY `courseid` (`courseid`),
  KEY `userid` (`userid`),
  KEY `status` (`status`),
  KEY `transid` (`transid`)
) TYPE=MyISAM COMMENT='Holds all known information about authorize.net transactions';

CREATE TABLE `prefix_enrol_authorize_refunds` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `orderid` int(10) unsigned NOT NULL default '0',
  `status` int(1) unsigned NOT NULL default '0',
  `amount` varchar(10) NOT NULL default '',
  `transid` int(10) unsigned default '0',
  `settletime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `orderid` (`orderid`),
  KEY `transid` (`transid`)
) TYPE=MyISAM COMMENT='Authorize.net refunds';
