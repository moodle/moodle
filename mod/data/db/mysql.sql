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
  intro text NOT NULL,
  ratings int(10) NOT NULL default '0',
  comments int(4) unsigned NOT NULL default '0',
  timeavailablefrom int(10) unsigned NOT NULL default '0',
  timeavailableto int(10) unsigned NOT NULL default '0',
  timeviewfrom int(10) unsigned NOT NULL default '0',
  timeviewto int(10) unsigned NOT NULL default '0',
  participants int(4) unsigned NOT NULL default '0',
  requiredentries int(8) unsigned NOT NULL default '0',
  requiredentriestoview int(8) unsigned NOT NULL default '0',
  maxentries int(8) unsigned NOT NULL default '0',
  rssarticles int(4) unsigned NOT NULL default '0',
  singletemplate text NOT NULL,
  listtemplate text NOT NULL,
  addtemplate text NOT NULL,
  rsstemplate text NOT NULL,
  listtemplateheader text NOT NULL,
  listtemplatefooter text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='Defines settings for each Database activity';


CREATE TABLE prefix_data_content (
  id int(10) unsigned NOT NULL auto_increment,
  fieldid int(10) unsigned NOT NULL default '0',
  recordid int(10) unsigned NOT NULL default '0',
  content longtext NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;


CREATE TABLE prefix_data_fields (
  id int(10) unsigned NOT NULL auto_increment,
  dataid int(10) unsigned NOT NULL default '0',
  type varchar(255) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  description text,
  param1  text,
  param2  text,
  param3  text,
  param4  text,
  param5  text,
  param6  text,
  param7  text,
  param8  text,
  param9  text,
  param10 text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;


CREATE TABLE prefix_data_records (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  groupid int(10) unsigned NOT NULL default '0',
  dataid int(10) unsigned NOT NULL default '0',
  timecreated int(10) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;


CREATE TABLE prefix_data_comments (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  recordid int(10) unsigned NOT NULL default '0',
  content text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;


CREATE TABLE prefix_data_ratings (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  recordid int(10) unsigned NOT NULL default '0',
  rating int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

