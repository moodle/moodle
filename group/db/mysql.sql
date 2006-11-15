# phpMyAdmin SQL Dump
# version 2.8.1
# http://www.phpmyadmin.net
# 
# Host: localhost
# Generation Time: Oct 24, 2006 at 05:23 PM
# Server version: 5.0.21
# PHP Version: 4.4.2-pl1
# 
# Database: `moodle`
# 

# --------------------------------------------------------

# 
# Table structure for table `mdl_groups_courses_groupings`
# 

CREATE TABLE `prefix_groups_courses_groupings` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned NOT NULL default '0',
  `groupingid` mediumint(9) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseid` (`courseid`)
) ENGINE=MyISAM ;

# --------------------------------------------------------

# 
# Table structure for table `prefix_groups_courses_groups`
# 

CREATE TABLE `prefix_groups_courses_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned NOT NULL default '0',
  `groupid` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseid` (`courseid`)
) ENGINE=MyISAM ;

# --------------------------------------------------------

# 
# Table structure for table `prefix_groups_groupings`
# 

CREATE TABLE `prefix_groups_groupings` (
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
) ENGINE=MyISAM ;

# --------------------------------------------------------

# 
# Table structure for table `prefix_groups_groupings_groups`
# 

CREATE TABLE `prefix_groups_groupings_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `groupingid` int(10) unsigned default '0',
  `groupid` int(10) NOT NULL,
  `timeadded` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseid` (`groupingid`)
) ENGINE=MyISAM  AUTO_INCREMENT=67 ;

# --------------------------------------------------------

# 
# Table structure for table `prefix_groups_groups`
# 

CREATE TABLE `prefix_groups_groups` (
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
) ENGINE=MyISAM ;

# --------------------------------------------------------

# 
# Table structure for table `prefix_groups_groups_users`
# 

CREATE TABLE `prefix_groups_groups_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `groupid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `timeadded` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `groupid` (`groupid`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM ;
