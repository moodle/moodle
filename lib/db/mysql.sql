# phpMyAdmin MySQL-Dump
# version 2.3.0-dev
# http://phpwizard.net/phpMyAdmin/
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: May 29, 2002 at 05:19 PM
# Server version: 3.23.49
# PHP Version: 4.1.2
# Database : `moodle`
# --------------------------------------------------------

#
# Table structure for table `course`
#

CREATE TABLE course (
  id int(10) unsigned NOT NULL auto_increment,
  category int(10) unsigned NOT NULL default '0',
  password varchar(50) NOT NULL default '',
  fullname varchar(254) NOT NULL default '',
  shortname varchar(15) NOT NULL default '',
  summary text NOT NULL,
  format tinyint(4) NOT NULL default '1',
  teacher varchar(100) NOT NULL default 'Teacher',
  startdate int(10) unsigned NOT NULL default '0',
  enddate int(10) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `course_categories`
#

CREATE TABLE course_categories (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM COMMENT='Course categories';
# --------------------------------------------------------

#
# Table structure for table `course_modules`
#

CREATE TABLE course_modules (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  module int(10) unsigned NOT NULL default '0',
  instance int(10) unsigned NOT NULL default '0',
  week int(10) unsigned NOT NULL default '0',
  added int(10) unsigned NOT NULL default '0',
  deleted tinyint(1) unsigned NOT NULL default '0',
  score tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `course_weeks`
#

CREATE TABLE course_weeks (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  week int(10) unsigned NOT NULL default '0',
  summary varchar(255) NOT NULL default '',
  sequence varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `log`
#

CREATE TABLE log (
  id int(10) unsigned NOT NULL auto_increment,
  time int(10) unsigned NOT NULL default '0',
  user int(10) unsigned NOT NULL default '0',
  ip varchar(15) NOT NULL default '',
  course int(10) unsigned NOT NULL default '0',
  module varchar(10) NOT NULL default '',
  action varchar(15) NOT NULL default '',
  url varchar(100) NOT NULL default '',
  info varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='Every action is logged as far as possible.';
# --------------------------------------------------------

#
# Table structure for table `log_display`
#

CREATE TABLE log_display (
  module varchar(20) NOT NULL default '',
  action varchar(20) NOT NULL default '',
  table varchar(20) NOT NULL default '',
  field varchar(40) NOT NULL default ''
) TYPE=MyISAM COMMENT='For a particular module/action, specifies a table field.';
# --------------------------------------------------------

#
# Table structure for table `modules`
#

CREATE TABLE modules (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(20) NOT NULL default '',
  fullname varchar(255) NOT NULL default '',
  version int(10) NOT NULL default '0',
  cron int(10) unsigned NOT NULL default '0',
  lastcron int(10) unsigned NOT NULL default '0',
  search varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `user`
#

CREATE TABLE user (
  id int(10) unsigned NOT NULL auto_increment,
  confirmed tinyint(1) NOT NULL default '0',
  username varchar(100) NOT NULL default '',
  password varchar(32) NOT NULL default '',
  idnumber varchar(12) default NULL,
  firstname varchar(20) NOT NULL default '',
  lastname varchar(20) NOT NULL default '',
  email varchar(100) NOT NULL default '',
  icq varchar(15) default NULL,
  phone1 varchar(20) default NULL,
  phone2 varchar(20) default NULL,
  institution varchar(40) default NULL,
  department varchar(30) default NULL,
  address varchar(70) default NULL,
  city varchar(20) default NULL,
  country char(2) default NULL,
  firstaccess int(10) unsigned NOT NULL default '0',
  lastaccess int(10) unsigned NOT NULL default '0',
  lastlogin int(10) unsigned NOT NULL default '0',
  currentlogin int(10) unsigned NOT NULL default '0',
  lastIP varchar(15) default NULL,
  personality varchar(5) default NULL,
  picture tinyint(1) default NULL,
  url varchar(255) default NULL,
  description text,
  research tinyint(1) unsigned NOT NULL default '0',
  forwardmail tinyint(1) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id),
  UNIQUE KEY username (username)
) TYPE=MyISAM COMMENT='One record for each person';
# --------------------------------------------------------

#
# Table structure for table `user_admins`
#

CREATE TABLE user_admins (
  id int(10) unsigned NOT NULL auto_increment,
  user int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM COMMENT='One record per administrator user';
# --------------------------------------------------------

#
# Table structure for table `user_students`
#

CREATE TABLE user_students (
  id int(10) unsigned NOT NULL auto_increment,
  user int(10) unsigned NOT NULL default '0',
  course int(10) unsigned NOT NULL default '0',
  start int(10) unsigned NOT NULL default '0',
  end int(10) unsigned NOT NULL default '0',
  time int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `user_teachers`
#

CREATE TABLE user_teachers (
  id int(10) unsigned NOT NULL auto_increment,
  user int(10) unsigned NOT NULL default '0',
  course int(10) unsigned NOT NULL default '0',
  authority varchar(10) default NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM COMMENT='One record per teacher per course';

    


