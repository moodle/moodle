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
  `shortname` varchar(15) NOT NULL default '',
  `idnumber` varchar(100) NOT NULL default '',
  `summary` text NOT NULL,
  `format` varchar(10) NOT NULL default 'topics',
  `showgrades` smallint(2) unsigned NOT NULL default '1',
  `modinfo` longtext NOT NULL,
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
  `description` text NOT NULL,
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
  `summary` text NOT NULL,
  `sequence` text NOT NULL default '',
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
  `summary` text NOT NULL,
  `reason` text NOT NULL,
  `requester` int(10) NOT NULL default 0,
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
  `description` text NOT NULL,
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
# Table structure for table `grade_category`
# 

CREATE TABLE `prefix_grade_category` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) default NULL,
  `courseid` int(10) unsigned NOT NULL default '0',
  `drop_x_lowest` int(10) unsigned NOT NULL default '0',
  `bonus_points` int(10) unsigned NOT NULL default '0',
  `hidden` int(10) unsigned NOT NULL default '0',
  `weight` decimal(4,2) default '0.00',
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
  `courseid` int(10) unsigned default NULL,
  `category` int(10) unsigned default NULL,
  `modid` int(10) unsigned default NULL,
  `cminstance` int(10) unsigned default NULL,
  `scale_grade` float(11,10) default '1.0000000000',
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
  `grade_high` decimal(4,2) NOT NULL default '100.00',
  `grade_low` decimal(4,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`),
  KEY `courseid` (`courseid`)
) TYPE=MyISAM ;

# --------------------------------------------------------

# 
# Table structure for table `grade_preferences`
# 

CREATE TABLE `prefix_grade_preferences` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned default NULL,
  `preference` int(10) NOT NULL default '0',
  `value` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `courseidpreference` (`courseid`,`preference`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `group`
#

CREATE TABLE `prefix_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned NOT NULL default '0',
  `name` varchar(254) NOT NULL default '',
  `description` text NOT NULL,
  `password` varchar(50) NOT NULL default '',
  `lang` varchar(10) NOT NULL default 'en',
  `theme` varchar(50) NOT NULL default '',
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
  KEY `groupid` (`groupid`),
  KEY `userid` (`userid`)
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
  `module` varchar(20) NOT NULL default '',
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
# Table structure for table `message`
#

CREATE TABLE `prefix_message` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `useridfrom` int(10) NOT NULL default '0',
  `useridto` int(10) NOT NULL default '0',
  `message` text NOT NULL,
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
  `message` text NOT NULL,
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
  `scale` text NOT NULL,
  `description` text NOT NULL,
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY `courseid` (`courseid`)
) TYPE=MyISAM COMMENT='Defines grading scales';
# --------------------------------------------------------


#
# Table structure for table `sessions`
#

CREATE TABLE `prefix_sessions` (
  `sesskey` char(32) NOT null,
  `expiry` int(11) unsigned NOT null,
  `expireref` varchar(64),
  `data` text NOT null,
  PRIMARY KEY (`sesskey`), 
  KEY (`expiry`) 
) TYPE=MyISAM COMMENT='Optional database session storage, not used by default';
# --------------------------------------------------------


#
# Table structure for table `timezone`
#

CREATE TABLE `prefix_timezone` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `year` int(11) NOT NULL default '0',
  `rule` varchar(20) NOT NULL default '',
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
  `idnumber` varchar(64) default NULL,
  `firstname` varchar(20) NOT NULL default '',
  `lastname` varchar(20) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `emailstop` tinyint(1) unsigned NOT NULL default '0',
  `icq` varchar(15) default NULL,
  `skype` varchar(50) default NULL,
  `yahoo` varchar(50) default NULL,
  `aim` varchar(50) default NULL,
  `msn` varchar(50) default NULL,
  `phone1` varchar(20) default NULL,
  `phone2` varchar(20) default NULL,
  `institution` varchar(40) default NULL,
  `department` varchar(30) default NULL,
  `address` varchar(70) default NULL,
  `city` varchar(20) default NULL,
  `country` char(2) default NULL,
  `lang` varchar(10) default 'en',
  `theme` varchar(50) NOT NULL default '',
  `timezone` varchar(100) NOT NULL default '99',
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
  `maildigest` tinyint(1) unsigned NOT NULL default '0',
  `maildisplay` tinyint(2) unsigned NOT NULL default '2',
  `htmleditor` tinyint(1) unsigned NOT NULL default '1',
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
 `created` datetime NOT NULL,
 `sql0` varchar(250) NOT NULL,
 `sql1` text NOT NULL,
 `params` text NOT NULL,
 `tracer` text NOT NULL,
 `timer` decimal(16,6) NOT NULL
);

CREATE TABLE `prefix_stats_daily` (
   `id` int(10) unsigned NOT NULL auto_increment,
   `courseid` int(10) unsigned NOT NULL default 0,
   `timeend` int(10) unsigned NOT NULL default 0,
   `students` int(10) unsigned NOT NULL default 0,
   `teachers` int(10) unsigned NOT NULL default 0,
   `activestudents` int(10) unsigned NOT NULL default 0,
   `activeteachers` int(10) unsigned NOT NULL default 0,
   `studentreads` int(10) unsigned NOT NULL default 0,
   `studentwrites` int(10) unsigned NOT NULL default 0,
   `teacherreads` int(10) unsigned NOT NULL default 0,
   `teacherwrites` int(10) unsigned NOT NULL default 0,
   `logins` int(10) unsigned NOT NULL default 0,
   `uniquelogins` int(10) unsigned NOT NULL default 0,
   PRIMARY KEY (`id`),
   KEY `courseid` (`courseid`),
   KEY `timeend` (`timeend`)	 
);

CREATE TABLE prefix_stats_weekly (
   `id` int(10) unsigned NOT NULL auto_increment,
   `courseid` int(10) unsigned NOT NULL default 0,
   `timeend` int(10) unsigned NOT NULL default 0,
   `students` int(10) unsigned NOT NULL default 0,
   `teachers` int(10) unsigned NOT NULL default 0,
   `activestudents` int(10) unsigned NOT NULL default 0,
   `activeteachers` int(10) unsigned NOT NULL default 0,
   `studentreads` int(10) unsigned NOT NULL default 0,
   `studentwrites` int(10) unsigned NOT NULL default 0,
   `teacherreads` int(10) unsigned NOT NULL default 0,
   `teacherwrites` int(10) unsigned NOT NULL default 0,
   `logins` int(10) unsigned NOT NULL default 0,
   `uniquelogins` int(10) unsigned NOT NULL default 0,
   PRIMARY KEY (`id`),
   KEY `courseid` (`courseid`),
   KEY `timeend` (`timeend`)	 
);

CREATE TABLE prefix_stats_monthly (
   `id` int(10) unsigned NOT NULL auto_increment,
   `courseid` int(10) unsigned NOT NULL default 0,
   `timeend` int(10) unsigned NOT NULL default 0,
   `students` int(10) unsigned NOT NULL default 0,
   `teachers` int(10) unsigned NOT NULL default 0,
   `activestudents` int(10) unsigned NOT NULL default 0,
   `activeteachers` int(10) unsigned NOT NULL default 0,
   `studentreads` int(10) unsigned NOT NULL default 0,
   `studentwrites` int(10) unsigned NOT NULL default 0,
   `teacherreads` int(10) unsigned NOT NULL default 0,
   `teacherwrites` int(10) unsigned NOT NULL default 0,
   `logins` int(10) unsigned NOT NULL default 0,
   `uniquelogins` int(10) unsigned NOT NULL default 0,
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
   `reads` int(10) unsigned NOT NULL default 0,
   `writes` int(10) unsigned NOT NULL default 0,
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
   `reads` int(10) unsigned NOT NULL default 0,
   `writes` int(10) unsigned NOT NULL default 0,
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
   `reads` int(10) unsigned NOT NULL default 0,
   `writes` int(10) unsigned NOT NULL default 0,
   `stattype` varchar(30) NOT NULL default '',
   PRIMARY KEY (`id`),
   KEY `courseid` (`courseid`),
   KEY `userid` (`userid`),
   KEY `roleid` (`roleid`),
   KEY `timeend` (`timeend`)	
);


INSERT INTO prefix_log_display VALUES ('user', 'view', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display VALUES ('course', 'user report', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display VALUES ('course', 'view', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'update', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'enrol', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('message', 'write', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display VALUES ('message', 'read', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display VALUES ('message', 'add contact', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display VALUES ('message', 'remove contact', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display VALUES ('message', 'block contact', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display VALUES ('message', 'unblock contact', 'user', 'CONCAT(firstname," ",lastname)');
