# This file contains a complete database schema for all the 
# tables used by this module, written in SQL

# It may also contain INSERT statements for particular data 
# that may be used, especially new entries in the table log_display

# --------------------------------------------------------

#
# Table structure for table `prefix_block_rss_client`
#

CREATE TABLE prefix_block_rss_client (
 `id` int(11) NOT NULL auto_increment,
 `userid` int(11) NOT NULL default '0',
 `title` varchar(64) NOT NULL default '',
 `description` varchar(128) NOT NULL default '',
 `url` varchar(255) NOT NULL default '',
 `type` char(1) NOT NULL default 'R',
PRIMARY KEY  (`id`)
) TYPE=MyISAM  COMMENT='Cached remote news feeds';