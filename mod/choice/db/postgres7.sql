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
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  text text NOT NULL default '',
  format integer NOT NULL default '0',
  answer1 varchar(255) NOT NULL default 'Yes',
  answer2 varchar(255) NOT NULL default 'No',
  answer3 varchar(255) default NULL,
  answer4 varchar(255) default NULL,
  answer5 varchar(255) default NULL,
  answer6 varchar(255) default NULL,
  showunanswered integer NOT NULL default '0',
  publish integer NOT NULL default '0',
  timemodified integer NOT NULL default '0'
);


# --------------------------------------------------------

#
# Table structure for table `choice_answers`
#

CREATE TABLE prefix_choice_answers (
  id SERIAL PRIMARY KEY,
  choice integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  answer integer NOT NULL default '0',
  timemodified integer NOT NULL default '0'
);

#
# Dumping data for table `log_display`
#

INSERT INTO prefix_log_display VALUES ('choice', 'view', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'update', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'add', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'report', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'choose', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'choose again', 'choice', 'name');

