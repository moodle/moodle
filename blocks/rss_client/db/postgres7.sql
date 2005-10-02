# $Id$
# This file contains a complete database schema for all the 
# tables used by this module, written in SQL

# It may also contain INSERT statements for particular data 
# that may be used, especially new entries in the table log_display

#
# Table structure for table `block_rss_client`
#

CREATE TABLE prefix_block_rss_client (
 id SERIAL PRIMARY KEY,
 userid INTEGER NOT NULL default '0',
 title varchar(64) NOT NULL default '',
 preferredtitle varchar(64) NOT NULL default '',
 description varchar(128) NOT NULL default '',
 url varchar(255) NOT NULL default ''
);
