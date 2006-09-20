# -- phpMyAdmin SQL Dump
# -- version 2.6.2
# -- http://www.phpmyadmin.net
# -- 
# -- Host: localhost
# -- Generation Time: Aug 25, 2005 at 03:52 PM
# -- Server version: 3.23.54
# -- PHP Version: 4.2.2
# -- 

# -- --------------------------------------------------------

CREATE TABLE prefix_data (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  intro text NOT NULL default '',
  ratings int(10) NOT NULL default '0',
  comments int(4) unsigned NOT NULL default '0',
  timeavailablefrom int(10) unsigned NOT NULL default '0',
  timeavailableto int(10) unsigned NOT NULL default '0',
  timeviewfrom int(10) unsigned NOT NULL default '0',
  timeviewto int(10) unsigned NOT NULL default '0',
  requiredentries int(8) unsigned NOT NULL default '0',
  requiredentriestoview int(8) unsigned NOT NULL default '0',
  maxentries int(8) unsigned NOT NULL default '0',
  rssarticles int(4) unsigned NOT NULL default '0',
  singletemplate text NOT NULL default '',
  listtemplate text NOT NULL default '',
  listtemplateheader text NOT NULL default '',
  listtemplatefooter text NOT NULL default '',
  addtemplate text NOT NULL default '',
  rsstemplate text NOT NULL default '',
  rsstitletemplate text NOT NULL default '',
  csstemplate text NOT NULL default '',
  jstemplate text NOT NULL default '',
  approval tinyint(4) unsigned NOT NULL default '0',
  scale int(10) NOT NULL default '0',
  assessed int(10) unsigned NOT NULL default '0',
  defaultsort int(10) unsigned NOT NULL default '0',
  defaultsortdir tinyint(4) unsigned NOT NULL default '0',
  editany tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='Defines settings for each Database activity';


CREATE TABLE prefix_data_content (
  id int(10) unsigned NOT NULL auto_increment,
  fieldid int(10) unsigned NOT NULL default '0',
  recordid int(10) unsigned NOT NULL default '0',
  content longtext NOT NULL default '',
  content1 longtext NOT NULL default '',
  content2 longtext NOT NULL default '',
  content3 longtext NOT NULL default '',
  content4 longtext NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;


CREATE TABLE prefix_data_fields (
  id int(10) unsigned NOT NULL auto_increment,
  dataid int(10) unsigned NOT NULL default '0',
  type varchar(255) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  description text NOT NULL default '',
  param1  text NOT NULL default '',
  param2  text NOT NULL default '',
  param3  text NOT NULL default '',
  param4  text NOT NULL default '',
  param5  text NOT NULL default '',
  param6  text NOT NULL default '',
  param7  text NOT NULL default '',
  param8  text NOT NULL default '',
  param9  text NOT NULL default '',
  param10 text NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;


CREATE TABLE prefix_data_records (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  groupid int(10) unsigned NOT NULL default '0',
  dataid int(10) unsigned NOT NULL default '0',
  timecreated int(10) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  approved tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;


CREATE TABLE prefix_data_comments (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  recordid int(10) unsigned NOT NULL default '0',
  content text NOT NULL default '',
  created int(10) unsigned NOT NULL default '0',
  modified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;


CREATE TABLE prefix_data_ratings (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  recordid int(10) unsigned NOT NULL default '0',
  rating int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;


INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'view', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'add', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'update', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'record delete', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'fields add', 'data_fields', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'fields update', 'data_fields', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'templates saved', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'templates def', 'data', 'name');
