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
# Table structure for table `choice`
#

CREATE TABLE prefix_choice (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  text text NOT NULL,
  format tinyint(2) unsigned NOT NULL default '0',
  publish tinyint(2) unsigned NOT NULL default '0',
  `release` tinyint(2) unsigned NOT NULL default '0',
  display tinyint(4) unsigned NOT NULL default '0',
  allowupdate tinyint(2) unsigned NOT NULL default '0',
  showunanswered tinyint(2) unsigned NOT NULL default '0',    limitanswers tinyint(2) unsigned NOT NULL default '0',
  timeopen int(10) unsigned NOT NULL default '0',
  timeclose int(10) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id),
  KEY course (course)
) TYPE=MyISAM COMMENT='Available choices are stored here.';


# --------------------------------------------------------

#
# Table structure for table `choice_answers`
#

CREATE TABLE prefix_choice_answers (
  id int(10) unsigned NOT NULL auto_increment,
  choiceid int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  optionid int(10) NOT NULL default '0',
  timemodified int(10) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id),
  KEY userid (userid),
  KEY choiceid (choiceid)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Table structure for table `choice_options`
#

CREATE TABLE prefix_choice_options (
  id int(10) unsigned NOT NULL auto_increment,
  choiceid int(10) unsigned NOT NULL default '0',
  `text` TEXT,    maxanswers int(10) unsigned NULL default '0',
  timemodified int(10) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id),
  KEY choiceid (choiceid)
) TYPE=MyISAM;

#
# Dumping data for table `log_display`
#

INSERT INTO prefix_log_display VALUES ('choice', 'view', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'update', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'add', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'report', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'choose', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'choose again', 'choice', 'name');


