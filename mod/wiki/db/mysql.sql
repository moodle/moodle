# This file contains a complete database schema for all the
# tables used by this module, written in SQL

# It may also contain INSERT statements for particular data
# that may be used, especially new entries in the table log_display


CREATE TABLE `prefix_wiki` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `summary` text NOT NULL,
  `pagename` varchar(255) default NULL,
  `wtype` enum('teacher','group','student') NOT NULL default 'group',
  `ewikiprinttitle` tinyint(4) NOT NULL default '1',
  `htmlmode` tinyint(4) NOT NULL default '0',
  `ewikiacceptbinary` tinyint(4) NOT NULL default '0',
  `disablecamelcase` tinyint(4) NOT NULL default '0',
  `setpageflags` tinyint(4) NOT NULL default '1',
  `strippages` tinyint(4) NOT NULL default '1',
  `removepages` tinyint(4) NOT NULL default '1',
  `revertchanges` tinyint(4) NOT NULL default '1',
  `initialcontent` varchar(255) default NULL,
  `timemodified` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Main wiki table';


#
# Table structure for table `mdl_wiki_entries`
#

CREATE TABLE `prefix_wiki_entries` (
  `id` int(10) NOT NULL auto_increment,
  `wikiid` int(10) NOT NULL default '0',
  `course` int(10) NOT NULL default '0',
  `groupid` int(10) NOT NULL default '0',
  `userid` int(10) NOT NULL default '0',
  `pagename` varchar(255) NOT NULL default '',
  `timemodified` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Holds entries for each wiki start instance.';


CREATE TABLE `prefix_wiki_pages` (
  `pagename` VARCHAR(160) NOT NULL,
  `version` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `flags` INTEGER UNSIGNED DEFAULT 0,
  `content` MEDIUMTEXT,
  `author` VARCHAR(100) DEFAULT 'ewiki',
  `created` INTEGER UNSIGNED DEFAULT 0,
  `lastmodified` INTEGER UNSIGNED DEFAULT 0,
  `refs` MEDIUMTEXT,
  `meta` MEDIUMTEXT,
  `hits` INTEGER UNSIGNED DEFAULT 0,
  `wiki` int(10) unsigned NOT NULL,
  PRIMARY KEY id (pagename, version, wiki)
) TYPE=MyISAM COMMENT='Holds the Wiki-Pages';
