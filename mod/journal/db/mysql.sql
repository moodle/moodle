# phpMyAdmin MySQL-Dump
# version 2.2.1
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/ (download page)
#
# Host: localhost
# Generation Time: Nov 14, 2001 at 04:44 PM
# Server version: 3.23.36
# PHP Version: 4.0.6
# Database : `moodle`
# --------------------------------------------------------

#
# Table structure for table `journal`
#

CREATE TABLE prefix_journal (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) default NULL,
  intro text,
  introformat tinyint(2) NOT NULL default '0',
  days smallint(5) unsigned NOT NULL default '7',
  assessed int(10) NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `journal_entries`
#

CREATE TABLE prefix_journal_entries (
  id int(10) unsigned NOT NULL auto_increment,
  journal int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  modified int(10) unsigned NOT NULL default '0',
  text text NOT NULL,
  format tinyint(2) NOT NULL default '0',
  rating int(10) default '0',
  comment text,
  teacher int(10) unsigned NOT NULL default '0',
  timemarked int(10) unsigned NOT NULL default '0',
  mailed int(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='All the journal entries of all people';

#
# Dumping data for table `log_display`
#

INSERT INTO prefix_log_display VALUES ('journal', 'view', 'journal', 'name');
INSERT INTO prefix_log_display VALUES ('journal', 'add entry', 'journal', 'name');
INSERT INTO prefix_log_display VALUES ('journal', 'update entry', 'journal', 'name');
INSERT INTO prefix_log_display VALUES ('journal', 'view responses', 'journal', 'name');
