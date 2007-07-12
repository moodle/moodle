CREATE TABLE IF NOT EXISTS `prefix_search_documents` (
  `id` int(11) NOT NULL auto_increment,
  `docid` varchar(32) NOT NULL,
  `doctype` varchar(32) NOT NULL default 'none',
  `itemtype` varchar(32) NOT NULL default 'standard',
  `title` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `docdate` timestamp NOT NULL default 0,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `courseid` int(11) NOT NULL default 0,
  `groupid` int(11) NOT NULL default 0,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;
