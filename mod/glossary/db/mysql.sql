# This file contains a complete database schema for all the 
# tables used by this module, written in SQL

# It may also contain INSERT statements for particular data 
# that may be used, especially new entries in the table log_display

#
# Table structure for table `glossary`
#

CREATE TABLE prefix_glossary (
     id int(10) unsigned NOT NULL auto_increment,
     course int(10) unsigned NOT NULL default '0',
     name varchar(255) NOT NULL default '',
     intro text NOT NULL,
     studentcanpost tinyint(2) unsigned NOT NULL default '0',
     allowduplicatedentries tinyint(2) unsigned NOT NULL default '0',
     displayformat tinyint(2) unsigned NOT NULL default '0',
     mainglossary tinyint(2) unsigned NOT NULL default '0',
     showspecial tinyint(2) unsigned NOT NULL default '1',
     showalphabet tinyint(2) unsigned NOT NULL default '1',
     showall tinyint(2) unsigned NOT NULL default '1',
     allowcomments tinyint(2) unsigned NOT NULL default '0',
     usedynalink tinyint(2) unsigned NOT NULL default '1',
     defaultapproval tinyint(2) unsigned NOT NULL default '1',
     globalglossary tinyint(2) unsigned NOT NULL default '0',
     entbypage tinyint(3) unsigned NOT NULL default '10',
     rsstype tinyint(2) unsigned NOT NULL default '0',
     rssarticles tinyint(2) unsigned NOT NULL default '0',
     assessed int(10) unsigned NOT NULL default '0',
     assesstimestart int(10) unsigned NOT NULL default '0',
     assesstimefinish int(10) unsigned NOT NULL default '0',
     scale int(10) NOT NULL default '0',
     timecreated int(10) unsigned NOT NULL default '0',
     timemodified int(10) unsigned NOT NULL default '0',
     PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='all glossaries';

#
# Table structure for table `glossary_entries`
#

CREATE TABLE prefix_glossary_entries (
     id int(10) unsigned NOT NULL auto_increment,
     glossaryid int(10) unsigned NOT NULL default '0',
     userid int(10) unsigned NOT NULL default '0',
     concept varchar(255) NOT NULL default '',
     definition text NOT NULL,
     format tinyint(2) unsigned NOT NULL default '0',
     attachment VARCHAR(100) NOT NULL default '',
     timecreated int(10) unsigned NOT NULL default '0',
     timemodified int(10) unsigned NOT NULL default '0',
     teacherentry tinyint(2) unsigned NOT NULL default '0',
     sourceglossaryid int(10) unsigned NOT NULL default '0',
     usedynalink tinyint(2) unsigned NOT NULL default '1',
     casesensitive tinyint(2) unsigned NOT NULL default '0',
     fullmatch tinyint(2) unsigned NOT NULL default '1',
     approved tinyint(2) unsigned NOT NULL default '1',
     PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='all glossary entries';

#
# Table structure for table `glossary_alias`
#

CREATE TABLE prefix_glossary_alias (
     id int(10) unsigned NOT NULL auto_increment,
     entryid int(10) unsigned NOT NULL default '0',
     alias text NOT NULL,
     PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='entries alias';

#
# Table structure for table `glossary_cageories`
#

CREATE TABLE prefix_glossary_categories (
     id int(10) unsigned NOT NULL auto_increment,
     glossaryid int(10) unsigned NOT NULL default '0',
     name varchar(255) NOT NULL default '',
     usedynalink tinyint(2) unsigned NOT NULL default '1',
     PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='all categories for glossary entries';

#
# Table structure for table `glossary_entries_category`
#

CREATE TABLE prefix_glossary_entries_categories (
     id int(10) unsigned NOT NULL auto_increment,
     categoryid int(10) unsigned NOT NULL default '0',
     entryid int(10) unsigned NOT NULL default '0',
     PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='categories of each glossary entry';

CREATE TABLE prefix_glossary_comments (
     id int(10) unsigned NOT NULL auto_increment,
     entryid int(10) unsigned NOT NULL default '0',
     userid int(10) unsigned NOT NULL default '0',
     comment text NOT NULL,
     format tinyint(2) unsigned NOT NULL default '0',
     timemodified int(10) unsigned NOT NULL default '0',
	 
     PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='comments on glossary entries';

CREATE TABLE prefix_glossary_displayformats (
     id int(10) unsigned NOT NULL auto_increment,
     fid int(10) unsigned NOT NULL default '0',
     visible tinyint(2) unsigned NOT NULL default '1',

     relatedview tinyint(3) NOT NULL default '-1',
     showgroup tinyint(2) unsigned NOT NULL default '1',

     defaultmode varchar(50) NOT NULL default '',
     defaulthook varchar(50) NOT NULL default '',
	 
     sortkey varchar(50) NOT NULL default '',
     sortorder varchar(50) NOT NULL default '',
	 
     PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='Setting of the display formats';

#
# Table structure for table `forum_ratings`
#

CREATE TABLE prefix_glossary_ratings (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  entryid int(10) unsigned NOT NULL default '0',
  time int(10) unsigned NOT NULL default '0',
  rating tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (id)
) COMMENT='Contains user ratings for entries';
# --------------------------------------------------------

#
# Dumping data for table `log_display`
#

INSERT INTO prefix_log_display VALUES ('glossary', 'add', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'update', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'view', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'view all', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'add entry', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'update entry', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'add category', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'update category', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'delete category', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'add comment', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'update comment', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'delete comment', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'approve entry', 'glossary', 'name');

