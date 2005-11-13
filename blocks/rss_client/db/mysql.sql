# $Id$
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
 `title` text NOT NULL default '',
 `preferredtitle` varchar(64) NOT NULL default '',
 `description` text NOT NULL default '',
 `url` varchar(255) NOT NULL default '',
PRIMARY KEY  (`id`)
) TYPE=MyISAM  COMMENT='Remote news feed information. Contains the news feed id, the userid of the user who added the feed, the title of the feed itself and a description of the feed contents along with the url used to access the remote feed. Preferredtitle is a field for future use - intended to allow for custom titles rather than those found in the feed.';