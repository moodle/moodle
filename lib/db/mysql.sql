# phpMyAdmin MySQL-Dump
# version 2.3.0-dev
# http://phpwizard.net/phpMyAdmin/
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Jun 25, 2002 at 05:04 PM
# Server version: 3.23.49
# PHP Version: 4.1.2
# Database : `moodle`
# --------------------------------------------------------

#
# Table structure for table `config`
#

CREATE TABLE `prefix_config` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM COMMENT='Moodle configuration variables';
# --------------------------------------------------------

#
# Table structure for table `course`
#

CREATE TABLE `prefix_course` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `category` int(10) unsigned NOT NULL default '0',
  `sortorder` int(10) unsigned NOT NULL default '0',
  `password` varchar(50) NOT NULL default '',
  `fullname` varchar(254) NOT NULL default '',
  `shortname` varchar(15) NOT NULL default '',
  `summary` text NOT NULL,
  `format` varchar(10) NOT NULL default 'topics',
  `showgrades` smallint(2) unsigned NOT NULL default '1',
  `modinfo` longtext NOT NULL,
  `blockinfo` varchar(255) NOT NULL default '',
  `newsitems` smallint(5) unsigned NOT NULL default '1',
  `teacher` varchar(100) NOT NULL default 'Teacher',
  `teachers` varchar(100) NOT NULL default 'Teachers',
  `student` varchar(100) NOT NULL default 'Student',
  `students` varchar(100) NOT NULL default 'Students',
  `guest` tinyint(2) unsigned NOT NULL default '0',
  `startdate` int(10) unsigned NOT NULL default '0',
  `numsections` smallint(5) unsigned NOT NULL default '1',
  `marker` int(10) unsigned NOT NULL default '0',
  `maxbytes` int(10) unsigned NOT NULL default '0',
  `showreports` int(4) unsigned NOT NULL default '0',
  `visible` int(1) unsigned NOT NULL default '1',
  `hiddensections` int(2) unsigned NOT NULL default '0',
  `groupmode` int(4) unsigned NOT NULL default '0',
  `groupmodeforce` int(4) unsigned NOT NULL default '0',
  `lang` varchar(10) NOT NULL default '',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `category` (`category`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `course_categories`
#

CREATE TABLE `prefix_course_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `parent` int(10) unsigned NOT NULL default '0',
  `sortorder` int(10) unsigned NOT NULL default '0',
  `coursecount` int(10) unsigned NOT NULL default '0',
  `visible` tinyint(1) NOT NULL default '1',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM COMMENT='Course categories';
# --------------------------------------------------------


#
# Table structure for table `course_display`
#

CREATE TABLE `prefix_course_display` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `display` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseuserid` (course,userid)
) TYPE=MyISAM COMMENT='Stores info about how to display the course';
# --------------------------------------------------------


#
# Table structure for table `course_modules`
#

CREATE TABLE `prefix_course_modules` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `module` int(10) unsigned NOT NULL default '0',
  `instance` int(10) unsigned NOT NULL default '0',
  `section` int(10) unsigned NOT NULL default '0',
  `added` int(10) unsigned NOT NULL default '0',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `score` tinyint(4) NOT NULL default '0',
  `indent` int(5) unsigned NOT NULL default '0',
  `visible` tinyint(1) NOT NULL default '1',
  `groupmode` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `course_sections`
#

CREATE TABLE `prefix_course_sections` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `section` int(10) unsigned NOT NULL default '0',
  `summary` text NOT NULL,
  `sequence` text NOT NULL default '',
  `visible` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `coursesection` (course,section)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `event`
#

CREATE TABLE `prefix_event` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `format` int(4) unsigned NOT NULL default '0',
  `courseid` int(10) unsigned NOT NULL default '0',
  `groupid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `modulename` varchar(20) NOT NULL default '',
  `instance` int(10) unsigned NOT NULL default '0',
  `eventtype` varchar(20) NOT NULL default '',
  `timestart` int(10) unsigned NOT NULL default '0',
  `timeduration` int(10) unsigned NOT NULL default '0',
  `visible` tinyint(4) NOT NULL default '1',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseid` (`courseid`),
  KEY `userid` (`userid`)
) TYPE=MyISAM COMMENT='For everything with a time associated to it';
# --------------------------------------------------------

#
# Table structure for table `cache_filters`
#

CREATE TABLE `prefix_cache_filters` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `filter` varchar(32) NOT NULL default '',
  `version` int(10) unsigned NOT NULL default '0',
  `md5key` varchar(32) NOT NULL default '',
  `rawtext` text NOT NULL,
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `filtermd5key` (filter,md5key)
) TYPE=MyISAM COMMENT='For keeping information about cached data';
# --------------------------------------------------------


#
# Table structure for table `cache_text`
#

CREATE TABLE `prefix_cache_text` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `md5key` varchar(32) NOT NULL default '',
  `formattedtext` longtext NOT NULL,
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `md5key` (`md5key`)
) TYPE=MyISAM COMMENT='For storing temporary copies of processed texts';
# --------------------------------------------------------



#
# Table structure for table `group`
#

CREATE TABLE `prefix_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned NOT NULL default '0',
  `name` varchar(254) NOT NULL default '',
  `description` text NOT NULL,
  `lang` varchar(10) NOT NULL default 'en',
  `picture` int(10) unsigned NOT NULL default '0',
  `hidepicture` int(2) unsigned NOT NULL default '0',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseid` (`courseid`)
) TYPE=MyISAM COMMENT='Each record is a group in a course.';
# --------------------------------------------------------

#
# Table structure for table `group_members`
#

CREATE TABLE `prefix_groups_members` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `groupid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `timeadded` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `groupid` (`groupid`)
) TYPE=MyISAM COMMENT='Lists memberships of users to groups';
# --------------------------------------------------------


#
# Table structure for table `log`
#

CREATE TABLE `prefix_log` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `time` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `ip` varchar(15) NOT NULL default '',
  `course` int(10) unsigned NOT NULL default '0',
  `module` varchar(10) NOT NULL default '',
  `cmid` int(10) unsigned NOT NULL default '0',
  `action` varchar(15) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `timecoursemoduleaction` (time,course,module,action),
  KEY `coursemoduleaction` (course,module,action),
  KEY `courseuserid` (course,userid)
) TYPE=MyISAM COMMENT='Every action is logged as far as possible.';
# --------------------------------------------------------

#
# Table structure for table `log_display`
#

CREATE TABLE `prefix_log_display` (
  `module` varchar(20) NOT NULL default '',
  `action` varchar(20) NOT NULL default '',
  `mtable` varchar(20) NOT NULL default '',
  `field` varchar(40) NOT NULL default ''
) TYPE=MyISAM COMMENT='For a particular module/action, specifies a moodle table/field.';
# --------------------------------------------------------

#
# Table structure for table `modules`
#

CREATE TABLE `prefix_modules` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL default '',
  `version` int(10) NOT NULL default '0',
  `cron` int(10) unsigned NOT NULL default '0',
  `lastcron` int(10) unsigned NOT NULL default '0',
  `search` varchar(255) NOT NULL default '',
  `visible` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM;
# --------------------------------------------------------


#
# Table structure for table `scale`
#

CREATE TABLE `prefix_scale` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `scale` text NOT NULL,
  `description` text NOT NULL,
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='Defines grading scales';
# --------------------------------------------------------


#
# Table structure for table `user`
#

CREATE TABLE `prefix_user` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `confirmed` tinyint(1) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  `username` varchar(100) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `idnumber` varchar(12) default NULL,
  `firstname` varchar(20) NOT NULL default '',
  `lastname` varchar(20) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `emailstop` tinyint(1) unsigned NOT NULL default '0',
  `icq` varchar(15) default NULL,
  `phone1` varchar(20) default NULL,
  `phone2` varchar(20) default NULL,
  `institution` varchar(40) default NULL,
  `department` varchar(30) default NULL,
  `address` varchar(70) default NULL,
  `city` varchar(20) default NULL,
  `country` char(2) default NULL,
  `lang` varchar(10) default 'en',
  `timezone` float NOT NULL default '99',
  `firstaccess` int(10) unsigned NOT NULL default '0',
  `lastaccess` int(10) unsigned NOT NULL default '0',
  `lastlogin` int(10) unsigned NOT NULL default '0',
  `currentlogin` int(10) unsigned NOT NULL default '0',
  `lastIP` varchar(15) default NULL,
  `secret` varchar(15) default NULL,
  `picture` tinyint(1) default NULL,
  `url` varchar(255) default NULL,
  `description` text,
  `mailformat` tinyint(1) unsigned NOT NULL default '1',
  `maildisplay` tinyint(2) unsigned NOT NULL default '2',
  `htmleditor` tinyint(1) unsigned NOT NULL default '1',
  `autosubscribe` tinyint(1) unsigned NOT NULL default '1',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `username` (`username`)
) TYPE=MyISAM COMMENT='One record for each person';
# --------------------------------------------------------

#
# Table structure for table `user_admins`
#

CREATE TABLE `prefix_user_admins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM COMMENT='One record per administrator user';
# --------------------------------------------------------



#
# Table structure for table `user_preferences`
#

CREATE TABLE `prefix_user_preferences` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `useridname` (userid,name)
) TYPE=MyISAM COMMENT='Allows modules to store arbitrary user preferences';
# --------------------------------------------------------



#
# Table structure for table `user_students`
#

CREATE TABLE `prefix_user_students` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `course` int(10) unsigned NOT NULL default '0',
  `timestart` int(10) unsigned NOT NULL default '0',
  `timeend` int(10) unsigned NOT NULL default '0',
  `time` int(10) unsigned NOT NULL default '0',
  `timeaccess` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseuserid` (course,userid)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `user_teachers`
#

CREATE TABLE `prefix_user_teachers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `course` int(10) unsigned NOT NULL default '0',
  `authority` int(10) NOT NULL default '3',
  `role` varchar(40) NOT NULL default '',
  `editall` int(1) unsigned NOT NULL default '1',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `timeaccess` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseuserid` (course,userid)
) TYPE=MyISAM COMMENT='One record per teacher per course';

#
# Table structure for table `user_admins`
#

CREATE TABLE `prefix_user_coursecreators` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM COMMENT='One record per course creator';

INSERT INTO prefix_log_display VALUES ('user', 'view', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display VALUES ('course', 'user report', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display VALUES ('course', 'view', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'update', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'enrol', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'update', 'course', 'fullname');
