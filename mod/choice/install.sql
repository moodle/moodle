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

CREATE TABLE choice (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  text text NOT NULL,
  answer1 varchar(255) NOT NULL default 'Yes',
  answer2 varchar(255) NOT NULL default 'No',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `choice_answers`
#

CREATE TABLE choice_answers (
  id int(10) unsigned NOT NULL auto_increment,
  choice int(10) unsigned NOT NULL default '0',
  user int(10) unsigned NOT NULL default '0',
  answer tinyint(4) NOT NULL default '0',
  timemodified int(10) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;

