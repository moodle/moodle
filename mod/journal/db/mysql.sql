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

CREATE TABLE journal (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) default NULL,
  intro text,
  days smallint(5) unsigned NOT NULL default '7',
  assessed tinyint(1) NOT NULL default '1',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `journal_entries`
#

CREATE TABLE journal_entries (
  id int(10) unsigned NOT NULL auto_increment,
  journal int(10) unsigned NOT NULL default '0',
  user int(10) unsigned NOT NULL default '0',
  modified int(10) unsigned NOT NULL default '0',
  text text NOT NULL,
  rating tinyint(4) default '0',
  comment text,
  teacher int(10) unsigned NOT NULL default '0',
  timemarked int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='All the journal entries of all people';

