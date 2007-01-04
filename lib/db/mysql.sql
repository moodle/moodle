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
# Table structure for table `config_plugins`
#

CREATE TABLE `prefix_config_plugins` (
  `id`         int(10) unsigned NOT NULL auto_increment,
  `plugin`     varchar(100) NOT NULL default 'core',
  `name`       varchar(100) NOT NULL default '',
  `value`      text NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `plugin_name` (`plugin`, `name`)
) TYPE=MyISAM COMMENT='Moodle modules and plugins configuration variables';
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
  `shortname` varchar(100) NOT NULL default '',
  `idnumber` varchar(100) NOT NULL default '',
  `summary` text NOT NULL default '',
  `format` varchar(10) NOT NULL default 'topics',
  `showgrades` smallint(2) unsigned NOT NULL default '1',
  `modinfo` longtext,
  `newsitems` smallint(5) unsigned NOT NULL default '1',
  `teacher` varchar(100) NOT NULL default 'Teacher',
  `teachers` varchar(100) NOT NULL default 'Teachers',
  `student` varchar(100) NOT NULL default 'Student',
  `students` varchar(100) NOT NULL default 'Students',
  `guest` tinyint(2) unsigned NOT NULL default '0',
  `startdate` int(10) unsigned NOT NULL default '0',
  `enrolperiod` int(10) unsigned NOT NULL default '0',
  `numsections` smallint(5) unsigned NOT NULL default '1',
  `marker` int(10) unsigned NOT NULL default '0',
  `maxbytes` int(10) unsigned NOT NULL default '0',
  `showreports` int(4) unsigned NOT NULL default '0',
  `visible` int(1) unsigned NOT NULL default '1',
  `hiddensections` int(2) unsigned NOT NULL default '0',
  `groupmode` int(4) unsigned NOT NULL default '0',
  `groupmodeforce` int(4) unsigned NOT NULL default '0',
  `lang` varchar(10) NOT NULL default '',
  `theme` varchar(50) NOT NULL default '',
  `cost` varchar(10) NOT NULL default '',
  `currency` char(3) NOT NULL default 'USD',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `metacourse` int(1) unsigned NOT NULL default '0',
  `requested` int(1) unsigned NOT NULL default '0',
  `restrictmodules` int(1) unsigned NOT NULL default '0',
  `expirynotify` tinyint(1) unsigned NOT NULL default '0',
  `expirythreshold` int(10) unsigned NOT NULL default '0',
  `notifystudents` tinyint(1) unsigned NOT NULL default '0',  
  `enrollable` tinyint(1) unsigned NOT NULL default '1',
  `enrolstartdate` int(10) unsigned NOT NULL default '0',
  `enrolenddate` int(10) unsigned NOT NULL default '0',
  `enrol` varchar(20) NOT NULL default '',
  `defaultrole` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `category` (`category`),
  KEY `idnumber` (`idnumber`),
  KEY `shortname` (`shortname`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `course_categories`
#

CREATE TABLE `prefix_course_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text,
  `parent` int(10) unsigned NOT NULL default '0',
  `sortorder` int(10) unsigned NOT NULL default '0',
  `coursecount` int(10) unsigned NOT NULL default '0',
  `visible` tinyint(1) NOT NULL default '1',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `depth` int(10) unsigned NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
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
# Table structure for table `course_meta`
#

CREATE TABLE `prefix_course_meta` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `parent_course` int(10) NOT NULL default 0,
 `child_course` int(10) NOT NULL default 0,
 PRIMARY KEY (`id`),
 KEY `parent_course` (parent_course),
 KEY `child_course` (child_course)
);
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
  `score` tinyint(4) NOT NULL default '0',
  `indent` int(5) unsigned NOT NULL default '0',
  `visible` tinyint(1) NOT NULL default '1',
  `visibleold` tinyint(1) NOT NULL default '1',
  `groupmode` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `visible` (`visible`),
  KEY `course` (`course`),
  KEY `module` (`module`),
  KEY `instance` (`instance`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `course_sections`
#

CREATE TABLE `prefix_course_sections` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `section` int(10) unsigned NOT NULL default '0',
  `summary` text,
  `sequence` text,
  `visible` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `coursesection` (course,section)
) TYPE=MyISAM;
# --------------------------------------------------------

# 
# Table structure for table `course_request`
#

CREATE TABLE `prefix_course_request`  (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fullname` varchar(254) NOT NULL default '',
  `shortname` varchar(15) NOT NULL default '',
  `summary` text NOT NULL default '',
  `reason` text NOT NULL default '',
  `requester` int(10) NOT NULL default 0,
  `password` varchar(50) NOT NULL default '',
  PRIMARY KEY (`id`),
  KEY `shortname` (`shortname`)
) TYPE=MyISAM;
# ---------------------------------------------------------

#
# Table structure for table `coursre_allowed_modules`
# 

CREATE TABLE `prefix_course_allowed_modules` (
   `id` int(10) unsigned NOT NULL auto_increment,
   `course` int(10) unsigned NOT NULL default 0,
   `module` int(10) unsigned NOT NULL default 0,
   PRIMARY KEY (`id`),
   KEY `course` (`course`),
   KEY `module` (`module`)
) TYPE=MyISAM;

------------------------------------------------------------

#
# Table structure for table `event`
#

CREATE TABLE `prefix_event` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL default '',
  `format` int(4) unsigned NOT NULL default '0',
  `courseid` int(10) unsigned NOT NULL default '0',
  `groupid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `repeatid` int(10) unsigned NOT NULL default '0',
  `modulename` varchar(20) NOT NULL default '',
  `instance` int(10) unsigned NOT NULL default '0',
  `eventtype` varchar(20) NOT NULL default '',
  `timestart` int(10) unsigned NOT NULL default '0',
  `timeduration` int(10) unsigned NOT NULL default '0',
  `visible` tinyint(4) NOT NULL default '1',
  `uuid` char(36) NOT NULL default '',
  `sequence` int(10) unsigned NOT NULL default '1',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseid` (`courseid`),
  KEY `userid` (`userid`),
  KEY `timestart` (`timestart`),
  KEY `timeduration` (`timeduration`)
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
  `rawtext` text NOT NULL default '',
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
  `formattedtext` longtext NOT NULL default '',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `md5key` (`md5key`)
) TYPE=MyISAM COMMENT='For storing temporary copies of processed texts';
# --------------------------------------------------------


# 
# Table structure for table `grade_category`
# 

CREATE TABLE `prefix_grade_category` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `courseid` int(10) unsigned NOT NULL default '0',
  `drop_x_lowest` int(10) unsigned NOT NULL default '0',
  `bonus_points` int(10) unsigned NOT NULL default '0',
  `hidden` int(10) unsigned NOT NULL default '0',
  `weight` decimal(5,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`),
  KEY `courseid` (`courseid`)
) TYPE=MyISAM ;

# --------------------------------------------------------

# 
# Table structure for table `grade_exceptions`
# 

CREATE TABLE `prefix_grade_exceptions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned NOT NULL default '0',
  `grade_itemid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `courseid` (`courseid`)
) TYPE=MyISAM ;

# --------------------------------------------------------

# 
# Table structure for table `grade_item`
# 

CREATE TABLE `prefix_grade_item` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned NOT NULL default '0',
  `category` int(10) unsigned NOT NULL default '0',
  `modid` int(10) unsigned NOT NULL default '0',
  `cminstance` int(10) unsigned NOT NULL default '0',
  `scale_grade` float(11,10) NOT NULL default '1.0000000000',
  `extra_credit` int(10) unsigned NOT NULL default '0',
  `sort_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `courseid` (`courseid`)
) TYPE=MyISAM ;

# --------------------------------------------------------

# 
# Table structure for table `grade_letter`
# 

CREATE TABLE `prefix_grade_letter` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned NOT NULL default '0',
  `letter` varchar(8) NOT NULL default 'NA',
  `grade_high` decimal(5,2) NOT NULL default '100.00',
  `grade_low` decimal(5,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`),
  KEY `courseid` (`courseid`)
) TYPE=MyISAM ;

# --------------------------------------------------------

# 
# Table structure for table `grade_preferences`
# 

CREATE TABLE `prefix_grade_preferences` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned NOT NULL default '0',
  `preference` int(10) NOT NULL default '0',
  `value` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `courseidpreference` (`courseid`,`preference`)
) TYPE=MyISAM;

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
  `module` varchar(20) NOT NULL default '',
  `cmid` int(10) unsigned NOT NULL default '0',
  `action` varchar(40) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `timecoursemoduleaction` (time,course,module,action),
  KEY `coursemoduleaction` (course,module,action),
  KEY `courseuserid` (course,userid),
  KEY `userid` (userid),
  KEY `info` (info)
) TYPE=MyISAM COMMENT='Every action is logged as far as possible.';
# --------------------------------------------------------

#
# Table structure for table `log_display`
#

CREATE TABLE `prefix_log_display` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `module` varchar(20) NOT NULL default '',
  `action` varchar(40) NOT NULL default '',
  `mtable` varchar(30) NOT NULL default '',
  `field` varchar(200) NOT NULL default '',
   PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='For a particular module/action, specifies a moodle table/field.';
ALTER TABLE prefix_log_display ADD UNIQUE `moduleaction`(`module` , `action`);
# --------------------------------------------------------

#
# Table structure for table `message`
#

CREATE TABLE `prefix_message` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `useridfrom` int(10) NOT NULL default '0',
  `useridto` int(10) NOT NULL default '0',
  `message` text NOT NULL default '',
  `format` int(4) unsigned NOT NULL default '0',
  `timecreated` int(10) NOT NULL default '0',
  `messagetype` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `useridfrom` (`useridfrom`),
  KEY `useridto` (`useridto`)
) TYPE=MyISAM COMMENT='Stores all unread messages';
# --------------------------------------------------------

#
# Table structure for table `message_read`
#

CREATE TABLE `prefix_message_read` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `useridfrom` int(10) NOT NULL default '0',
  `useridto` int(10) NOT NULL default '0',
  `message` text NOT NULL default '',
  `format` int(4) unsigned NOT NULL default '0',
  `timecreated` int(10) NOT NULL default '0',
  `timeread` int(10) NOT NULL default '0',
  `messagetype` varchar(50) NOT NULL default '',
  `mailed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `useridfrom` (`useridfrom`),
  KEY `useridto` (`useridto`)
) TYPE=MyISAM COMMENT='Stores all messages that have been read';
# --------------------------------------------------------

#
# Table structure for table `message_contacts`
#

CREATE TABLE `prefix_message_contacts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `contactid` int(10) unsigned NOT NULL default '0',
  `blocked` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `usercontact` (`userid`,`contactid`)
) TYPE=MyISAM COMMENT='Maintains lists of relationships between users';
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
  UNIQUE KEY `id` (`id`),
  KEY `name` (`name`)
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
  `scale` text NOT NULL default '',
  `description` text NOT NULL default '',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY `courseid` (`courseid`)
) TYPE=MyISAM COMMENT='Defines grading scales';
# --------------------------------------------------------


#
# Table structure for table `sessions2`
#
CREATE TABLE prefix_sessions2 (
    sesskey VARCHAR(64) NOT NULL default '',
    expiry DATETIME NOT NULL,
    expireref VARCHAR(250),
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    sessdata LONGTEXT,
CONSTRAINT  PRIMARY KEY (sesskey)
) COMMENT='Optional database session storage in new format, not used by default';

CREATE INDEX prefix_sess_exp_ix ON prefix_sessions2 (expiry);
CREATE INDEX prefix_sess_exp2_ix ON prefix_sessions2 (expireref);
# --------------------------------------------------------

#
# Table structure for table `timezone`
#

CREATE TABLE `prefix_timezone` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `year` int(11) NOT NULL default '0',
  `tzrule` varchar(20) NOT NULL default '',
  `gmtoff` int(11) NOT NULL default '0',
  `dstoff` int(11) NOT NULL default '0',
  `dst_month` tinyint(2) NOT NULL default '0',
  `dst_startday` tinyint(3) NOT NULL default '0',
  `dst_weekday` tinyint(3) NOT NULL default '0',
  `dst_skipweeks` tinyint(3) NOT NULL default '0',
  `dst_time` varchar(5) NOT NULL default '00:00',
  `std_month` tinyint(2) NOT NULL default '0',
  `std_startday` tinyint(3) NOT NULL default '0',
  `std_weekday` tinyint(3) NOT NULL default '0',
  `std_skipweeks` tinyint(3) NOT NULL default '0',
  `std_time` varchar(5) NOT NULL default '00:00',
  PRIMARY KEY (`id`)
) TYPE=MyISAM COMMENT='Rules for calculating local wall clock time for users';


#
# Table structure for table `user`
#
# When changing prefix_user, you may need to update
# truncate_userinfo() in moodlelib.php
#
CREATE TABLE `prefix_user` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `auth` varchar(20) NOT NULL default 'manual',
  `confirmed` tinyint(1) NOT NULL default '0',
  `policyagreed` tinyint(1) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  `username` varchar(100) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `idnumber` varchar(64) NOT NULL default '',
  `firstname` varchar(100) NOT NULL default '',
  `lastname` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `emailstop` tinyint(1) unsigned NOT NULL default '0',
  `icq` varchar(15) NOT NULL default '',
  `skype` varchar(50) NOT NULL default '',
  `yahoo` varchar(50) NOT NULL default '',
  `aim` varchar(50) NOT NULL default '',
  `msn` varchar(50) NOT NULL default '',
  `phone1` varchar(20) NOT NULL default '',
  `phone2` varchar(20) NOT NULL default '',
  `institution` varchar(40) NOT NULL default '',
  `department` varchar(30) NOT NULL default '',
  `address` varchar(70) NOT NULL default '',
  `city` varchar(20) NOT NULL default '',
  `country` char(2) NOT NULL default '',
  `lang` varchar(10) NOT NULL default 'en',
  `theme` varchar(50) NOT NULL default '',
  `timezone` varchar(100) NOT NULL default '99',
  `firstaccess` int(10) unsigned NOT NULL default '0',
  `lastaccess` int(10) unsigned NOT NULL default '0',
  `lastlogin` int(10) unsigned NOT NULL default '0',
  `currentlogin` int(10) unsigned NOT NULL default '0',
  `lastip` varchar(15) NOT NULL default '',
  `secret` varchar(15) NOT NULL default '',
  `picture` tinyint(1) NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `description` text,
  `mailformat` tinyint(1) unsigned NOT NULL default '1',
  `maildigest` tinyint(1) unsigned NOT NULL default '0',
  `maildisplay` tinyint(2) unsigned NOT NULL default '2',
  `htmleditor` tinyint(1) unsigned NOT NULL default '1',
  `ajax` tinyint(1) unsigned NOT NULL default '1',
  `autosubscribe` tinyint(1) unsigned NOT NULL default '1',
  `trackforums` tinyint(1) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `user_deleted` (`deleted`),
  KEY `user_confirmed` (`confirmed`),
  KEY `user_firstname` (`firstname`),
  KEY `user_lastname` (`lastname`),
  KEY `user_city` (`city`),
  KEY `user_country` (`country`),
  KEY `user_lastaccess` (`lastaccess`),
  KEY `user_email` (`email`)
) TYPE=MyISAM COMMENT='One record for each person';

ALTER TABLE `prefix_user` ADD INDEX `auth` (`auth`);
ALTER TABLE `prefix_user` ADD INDEX `idnumber` (`idnumber`);
# --------------------------------------------------------

#
# Table structure for table `user_admins`
#

CREATE TABLE `prefix_user_admins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `userid` (`userid`)
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
  `enrol` varchar(20) NOT NULL default '',  
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `courseuserid` (course,userid),
  KEY `userid` (userid),
  KEY `enrol` (enrol),
  KEY `timeaccess` (timeaccess)
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
  `timestart` int(10) unsigned NOT NULL default '0',
  `timeend` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `timeaccess` int(10) unsigned NOT NULL default '0',
  `enrol` varchar(20) NOT NULL default '',  
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `courseuserid` (course,userid),
  KEY `userid` (userid),
  KEY `enrol` (enrol)
) TYPE=MyISAM COMMENT='One record per teacher per course';

#
# Table structure for table `user_admins`
#

CREATE TABLE `prefix_user_coursecreators` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `userid` (`userid`)
) TYPE=MyISAM COMMENT='One record per course creator';


#
# For debugging puposes, see admin/dbperformance.php
#

CREATE TABLE `adodb_logsql` (
 `created` datetime NOT NULL default '0000-00-00 00:00:00',
 `sql0` varchar(250) NOT NULL default '',
 `sql1` text NOT NULL default '',
 `params` text NOT NULL default '',
 `tracer` text NOT NULL default '',
 `timer` decimal(16,6) NOT NULL default '0'
);

CREATE TABLE `prefix_stats_daily` (
   `id` int(10) unsigned NOT NULL auto_increment,
   `courseid` int(10) unsigned NOT NULL default 0,
   `roleid` int(10) unsigned NOT NULL default 0,
   `timeend` int(10) unsigned NOT NULL default 0,
   `stattype` enum('enrolments', 'activity', 'logins') NOT NULL default 'activity',
   `stat1` int(10) unsigned NOT NULL default 0,
   `stat2` int(10) unsigned NOT NULL default 0,
   PRIMARY KEY (`id`),
   KEY `courseid` (`courseid`),
   KEY `timeend` (`timeend`)
);

CREATE TABLE prefix_stats_weekly (
   `id` int(10) unsigned NOT NULL auto_increment,
   `courseid` int(10) unsigned NOT NULL default 0,
   `roleid` int(10) unsigned NOT NULL default 0,
   `timeend` int(10) unsigned NOT NULL default 0,
   `stattype` enum('enrolments', 'activity', 'logins') NOT NULL default 'activity',
   `stat1` int(10) unsigned NOT NULL default 0,
   `stat2` int(10) unsigned NOT NULL default 0,
   PRIMARY KEY (`id`),
   KEY `courseid` (`courseid`),
   KEY `timeend` (`timeend`)
);

CREATE TABLE prefix_stats_monthly (
   `id` int(10) unsigned NOT NULL auto_increment,
   `courseid` int(10) unsigned NOT NULL default 0,
   `roleid` int(10) unsigned NOT NULL default 0,
   `timeend` int(10) unsigned NOT NULL default 0,
   `stattype` enum('enrolments', 'activity', 'logins') NOT NULL default 'activity',
   `stat1` int(10) unsigned NOT NULL default 0,
   `stat2` int(10) unsigned NOT NULL default 0,
   PRIMARY KEY (`id`),
   KEY `courseid` (`courseid`),
   KEY `timeend` (`timeend`)
);

CREATE TABLE prefix_stats_user_daily (
   `id` int(10) unsigned NOT NULL auto_increment,
   `courseid` int(10) unsigned NOT NULL default 0,
   `userid` int(10) unsigned NOT NULL default 0,
   `roleid` int(10) unsigned NOT NULL default 0,
   `timeend` int(10) unsigned NOT NULL default 0,
   `statsreads` int(10) unsigned NOT NULL default 0,
   `statswrites` int(10) unsigned NOT NULL default 0,
   `stattype` varchar(30) NOT NULL default '',
   PRIMARY KEY (`id`),
   KEY `courseid` (`courseid`),
   KEY `userid` (`userid`),
   KEY `roleid` (`roleid`),
   KEY `timeend` (`timeend`)
);

CREATE TABLE prefix_stats_user_weekly (
   `id` int(10) unsigned NOT NULL auto_increment,
   `courseid` int(10) unsigned NOT NULL default 0,
   `userid` int(10) unsigned NOT NULL default 0,
   `roleid` int(10) unsigned NOT NULL default 0,
   `timeend` int(10) unsigned NOT NULL default 0,
   `statsreads` int(10) unsigned NOT NULL default 0,
   `statswrites` int(10) unsigned NOT NULL default 0,
   `stattype` varchar(30) NOT NULL default '',
   PRIMARY KEY (`id`),
   KEY `courseid` (`courseid`),
   KEY `userid` (`userid`),
   KEY `roleid` (`roleid`),
   KEY `timeend` (`timeend`)
);

CREATE TABLE prefix_stats_user_monthly (
   `id` int(10) unsigned NOT NULL auto_increment,
   `courseid` int(10) unsigned NOT NULL default 0,
   `userid` int(10) unsigned NOT NULL default 0,
   `roleid` int(10) unsigned NOT NULL default 0,
   `timeend` int(10) unsigned NOT NULL default 0,
   `statsreads` int(10) unsigned NOT NULL default 0,
   `statswrites` int(10) unsigned NOT NULL default 0,
   `stattype` varchar(30) NOT NULL default '',
   PRIMARY KEY (`id`),
   KEY `courseid` (`courseid`),
   KEY `userid` (`userid`),
   KEY `roleid` (`roleid`),
   KEY `timeend` (`timeend`)
);

#
# Table structure for table `prefix_post`
#
CREATE TABLE prefix_post (
  `id` int(10) unsigned NOT NULL auto_increment,
  `module` varchar(20) NOT NULL default '',
  `userid` int(10) unsigned NOT NULL default '0',
  `courseid` int(10) unsigned NOT NULL default '0',
  `groupid` int(10) unsigned NOT NULL default '0',
  `moduleid` int(10) unsigned NOT NULL default '0',
  `coursemoduleid` int(10) unsigned NOT NULL default '0',
  `subject` varchar(128) NOT NULL default '',
  `summary` longtext,
  `content` longtext,
  `uniquehash` varchar(128) NOT NULL default '',
  `rating` int(10) unsigned NOT NULL default '0',
  `format` int(10) unsigned NOT NULL default '0',
  `publishstate` enum('draft','site','public') NOT NULL default 'draft',
  `lastmodified` int(10) unsigned NOT NULL default '0',
  `created` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_user_idx` (`id`, `userid`),
  KEY `post_lastmodified_idx` (`lastmodified`),
  KEY `post_module_idx` (`module`),
  KEY `post_subject_idx` (`subject`)
) TYPE=MyISAM  COMMENT='Generic post table to hold data blog entries etc in different modules.';


# tags are not limited to blogs
CREATE TABLE prefix_tags (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(255) NOT NULL default 'official',
  `userid` int(10) unsigned NOT NULL default '0',
  `text` varchar(20) NOT NULL default '',
  KEY `tags_typeuserid_idx` (`type`, `userid`),
  KEY `tags_text_idx` (`text`),
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT ='tags structure for moodle.';

# instance of a tag for a blog
CREATE TABLE prefix_blog_tag_instance (
  `id` int(10) unsigned NOT NULL auto_increment,
  `entryid` int(10) unsigned NOT NULL default '0',
  `tagid` int(10) unsigned NOT NULL default '0',
  `groupid` int(10) unsigned NOT NULL default '0',
  `courseid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  KEY `bti_entryid_idx` (`entryid`),
  KEY `bti_tagid_idx` (`tagid`),
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT ='tag instance for blogs.';

###################################
# Roles tables
###################################

CREATE TABLE prefix_role (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `shortname` varchar(100) NOT NULL default '',
  `description` text NOT NULL default '',
  `sortorder` int(10) unsigned NOT NULL default '0',
  KEY `sortorder` (`sortorder`),
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT ='moodle roles';

CREATE TABLE prefix_context (
  `id` int(10) unsigned NOT NULL auto_increment,
  `contextlevel` int(10) unsigned NOT NULL default '0',
  `instanceid` int(10) unsigned NOT NULL default '0',
  KEY `instanceid` (`instanceid`),
  UNIQUE KEY `contextlevel-instanceid` (`contextlevel`, `instanceid`),
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT ='one of these must be set';

CREATE TABLE prefix_role_assignments (
  `id` int(10) unsigned NOT NULL auto_increment,
  `roleid` int(10) unsigned NOT NULL default '0',
  `contextid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `hidden` int(1) unsigned NOT NULL default '0',
  `timestart` int(10) unsigned NOT NULL default '0',
  `timeend` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `modifierid` int(10) unsigned NOT NULL default '0',
  `enrol` varchar(20) NOT NULL default '',
  `sortorder` int(10) unsigned NOT NULL default '0',
  KEY `roleid` (`roleid`),
  KEY `contextid` (`contextid`),
  KEY `userid` (`userid`),
  UNIQUE KEY `contextid-roleid-userid` (`contextid`, `roleid`, `userid`),
  KEY `sortorder` (`sortorder`),
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT ='assigning roles to different context';

CREATE TABLE prefix_role_capabilities (
  `id` int(10) unsigned NOT NULL auto_increment,
  `contextid` int(10)unsigned NOT NULL default '0',
  `roleid` int(10) unsigned NOT NULL default '0',
  `capability` varchar(255) NOT NULL default '',
  `permission` int(10) NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `modifierid` int(10) unsigned NOT NULL default '0',
  KEY `roleid` (`roleid`),
  KEY `contextid` (`contextid`),
  KEY `modifierid` (`modifierid`),
  UNIQUE KEY `roleid-contextid-capability` (`roleid`, `contextid`, `capability`),  
  PRIMARY KEY (`id`)
) TYPE=MYISAM COMMENT ='permission has to be signed, overriding a capability for a particular role in a particular context';

CREATE TABLE prefix_role_allow_assign (
  `id` int(10) unsigned NOT NULL auto_increment,
  `roleid` int(10) unsigned NOT NULL default '0',
  `allowassign` int(10) unsigned NOT NULL default '0',
  KEY `roleid` (`roleid`),
  KEY `allowassign` (`allowassign`),
  UNIQUE KEY `roleid-allowassign` (`roleid`, `allowassign`),
  PRIMARY KEY (`id`)
) TYPE=MYISAM COMMENT ='this defines what role can assign what role';

CREATE TABLE prefix_role_allow_override (
  `id` int(10) unsigned NOT NULL auto_increment,
  `roleid` int(10) unsigned NOT NULL default '0',
  `allowoverride` int(10) unsigned NOT NULL default '0',
  KEY `roleid` (`roleid`),
  KEY `allowoverride` (`allowoverride`),
  UNIQUE KEY `roleid-allowoverride` (`roleid`, `allowoverride`),
  PRIMARY KEY (`id`)
) TYPE=MYISAM COMMENT ='this defines what role can override what role';

CREATE TABLE prefix_capabilities ( 
  `id` int(10) unsigned NOT NULL auto_increment, 
  `name` varchar(255) NOT NULL default '', 
  `captype` varchar(50) NOT NULL default '', 
  `contextlevel` int(10) unsigned NOT NULL default '0', 
  `component` varchar(100) NOT NULL default '', 
  `riskbitmask` int(10) unsigned NOT NULL default '0', 
  UNIQUE KEY `name` (`name`),
  PRIMARY KEY (`id`) 
) TYPE=MYISAM COMMENT ='this defines all capabilities';

CREATE TABLE prefix_role_names ( 
  `id` int(10) unsigned NOT NULL auto_increment, 
  `roleid` int(10) unsigned NOT NULL default '0',
  `contextid` int(10) unsigned NOT NULL default '0', 
  `text` text NOT NULL default '',
  KEY `roleid` (`roleid`),
  KEY `contextid` (`contextid`),
  UNIQUE KEY `roleid-contextid` (`roleid`, `contextid`),
  PRIMARY KEY (`id`) 
) TYPE=MYISAM COMMENT ='role names in native strings';

CREATE TABLE prefix_user_lastaccess ( 
  `id` int(10) unsigned NOT NULL auto_increment, 
  `userid` int(10) unsigned NOT NULL default '0',
  `courseid` int(10) unsigned NOT NULL default '0', 
  `timeaccess` int(10) unsigned NOT NULL default '0', 
  KEY `userid` (`userid`),
  KEY `courseid` (`courseid`),
  UNIQUE KEY `userid-courseid` (`userid`, `courseid`),
  PRIMARY KEY (`id`) 
) TYPE=MYISAM COMMENT ='time user last accessed any page in a course';

INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('user', 'view', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('course', 'user report', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('course', 'view', 'course', 'fullname');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('course', 'update', 'course', 'fullname');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('course', 'enrol', 'course', 'fullname');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('course', 'report log', 'course', 'fullname');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('course', 'report live', 'course', 'fullname');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('course', 'report outline', 'course', 'fullname');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('course', 'report participation', 'course', 'fullname');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('course', 'report stats', 'course', 'fullname');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('message', 'write', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('message', 'read', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('message', 'add contact', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('message', 'remove contact', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('message', 'block contact', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('message', 'unblock contact', 'user', 'CONCAT(firstname," ",lastname)');

CREATE TABLE prefix_user_info_field (
    id BIGINT(10) NOT NULL auto_increment,
    name VARCHAR(255) NOT NULL default '',
    datatype VARCHAR(255) NOT NULL default '',
    categoryid BIGINT(10) unsigned NOT NULL default 0,
    sortorder BIGINT(10) unsigned NOT NULL default 0,
    required TINYINT(2) unsigned NOT NULL default 0,
    locked TINYINT(2) unsigned NOT NULL default 0,
    visible SMALLINT(4) unsigned NOT NULL default 0,
    defaultdata LONGTEXT,
CONSTRAINT  PRIMARY KEY (id)
);

ALTER TABLE prefix_user_info_field COMMENT='Customisable user profile fields';

CREATE TABLE prefix_user_info_category (
    id BIGINT(10) NOT NULL auto_increment,
    name VARCHAR(255) NOT NULL default '',
    sortorder BIGINT(10) unsigned NOT NULL default 0,
CONSTRAINT  PRIMARY KEY (id)
);

ALTER TABLE prefix_user_info_category COMMENT='Customisable fields categories';

CREATE TABLE prefix_user_info_data (
    id BIGINT(10) NOT NULL auto_increment,
    userid BIGINT(10) unsigned NOT NULL default 0,
    fieldid BIGINT(10) unsigned NOT NULL default 0,
    data LONGTEXT NOT NULL,
CONSTRAINT  PRIMARY KEY (id)
);

ALTER TABLE prefix_user_info_data COMMENT='Data for the customisable user fields';
