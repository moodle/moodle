-- phpMyAdmin SQL Dump
-- version 2.8.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 24, 2006 at 05:23 PM
-- Server version: 5.0.21
-- PHP Version: 4.4.2-pl1
-- 
-- Database: `moodle`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_course`
-- 

CREATE TABLE `mdl_course` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `category` int(10) unsigned NOT NULL default '0',
  `sortorder` int(10) unsigned NOT NULL default '0',
  `password` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `fullname` varchar(254) collate utf8_unicode_ci NOT NULL default '',
  `shortname` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `idnumber` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `summary` text collate utf8_unicode_ci NOT NULL,
  `format` varchar(10) collate utf8_unicode_ci NOT NULL default 'topics',
  `showgrades` smallint(2) unsigned NOT NULL default '1',
  `modinfo` longtext collate utf8_unicode_ci NOT NULL,
  `newsitems` smallint(5) unsigned NOT NULL default '1',
  `teacher` varchar(100) collate utf8_unicode_ci NOT NULL default 'Teacher',
  `teachers` varchar(100) collate utf8_unicode_ci NOT NULL default 'Teachers',
  `student` varchar(100) collate utf8_unicode_ci NOT NULL default 'Student',
  `students` varchar(100) collate utf8_unicode_ci NOT NULL default 'Students',
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
  `lang` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  `theme` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `cost` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  `currency` char(3) collate utf8_unicode_ci NOT NULL default 'USD',
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
  `enrol` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `category` (`category`),
  KEY `idnumber` (`idnumber`),
  KEY `shortname` (`shortname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_course_modules`
-- 

CREATE TABLE `mdl_course_modules` (
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
  `groupingid` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `visible` (`visible`),
  KEY `course` (`course`),
  KEY `module` (`module`),
  KEY `instance` (`instance`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_groups_courses_groupings`
-- 

CREATE TABLE `mdl_groups_courses_groupings` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned NOT NULL default '0',
  `groupingid` mediumint(9) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseid` (`courseid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=87 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_groups_courses_groups`
-- 

CREATE TABLE `mdl_groups_courses_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned NOT NULL default '0',
  `groupid` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseid` (`courseid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=132 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_groups_groupings`
-- 

CREATE TABLE `mdl_groups_groupings` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(254) character set latin1 collate latin1_general_ci NOT NULL default '',
  `description` text character set latin1 collate latin1_general_ci NOT NULL,
  `timecreated` int(10) unsigned NOT NULL default '0',
  `viewowngroup` tinyint(1) NOT NULL,
  `viewallgroupsmembers` tinyint(1) NOT NULL,
  `viewallgroupsactivities` tinyint(1) NOT NULL,
  `teachersgroupmark` tinyint(1) NOT NULL,
  `teachersgroupview` binary(1) NOT NULL,
  `teachersoverride` binary(1) NOT NULL,
  `teacherdeletable` binary(1) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=87 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_groups_groupings_groups`
-- 

CREATE TABLE `mdl_groups_groupings_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `groupingid` int(10) unsigned default '0',
  `groupid` int(10) NOT NULL,
  `timecreated` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseid` (`groupingid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=67 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_groups_groups`
-- 

CREATE TABLE `mdl_groups_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(254) character set latin1 collate latin1_general_ci NOT NULL default '',
  `description` text character set latin1 collate latin1_general_ci NOT NULL,
  `enrolmentkey` varchar(50) character set latin1 collate latin1_general_ci NOT NULL default '',
  `lang` varchar(10) character set latin1 collate latin1_general_ci NOT NULL default 'en',
  `theme` varchar(50) character set latin1 collate latin1_general_ci NOT NULL default '',
  `picture` int(10) unsigned NOT NULL default '0',
  `hidepicture` int(1) unsigned NOT NULL default '0',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=132 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_groups_groups_users`
-- 

CREATE TABLE `mdl_groups_groups_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `groupid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `timeadded` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `groupid` (`groupid`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=175 ;
