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
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  name varchar(255) default NULL,
  intro text,
  introformat integer NOT NULL default '0',
  days integer NOT NULL default '7',
  assessed integer NOT NULL default '0',
  timemodified integer NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table `journal_entries`
#

CREATE TABLE prefix_journal_entries (
  id SERIAL PRIMARY KEY,
  journal integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  modified integer NOT NULL default '0',
  text text NOT NULL default '',
  format integer NOT NULL default '0',
  rating integer default '0',
  comment text,
  teacher integer NOT NULL default '0',
  timemarked integer NOT NULL default '0',
  mailed integer NOT NULL default '0'
);

#
# Dumping data for table `log_display`
#

INSERT INTO prefix_log_display VALUES ('journal', 'view', 'journal', 'name');
INSERT INTO prefix_log_display VALUES ('journal', 'add entry', 'journal', 'name');
INSERT INTO prefix_log_display VALUES ('journal', 'update entry', 'journal', 'name');
INSERT INTO prefix_log_display VALUES ('journal', 'view responses', 'journal', 'name');
