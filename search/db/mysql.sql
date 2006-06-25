CREATE TABLE IF NOT EXISTS `search_documents` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(12) NOT NULL default 'none',
  `title` varchar(100) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `courseid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `groupid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DELETE FROM `search_documents` WHERE 1;
ALTER TABLE `search_documents` AUTO_INCREMENT =1;

