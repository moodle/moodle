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
  `groupingid` mediumint(9) NOT NULL default '0',
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
  `groupid` int(11) NOT NULL default '0',
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
  `name` varchar(254) NOT NULL,
  `description` text NOT NULL default '',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `viewowngroup` tinyint(1) NOT NULL default 1,
  `viewallgroupsmembers` tinyint(1) NOT NULL default 0,
  `viewallgroupsactivities` tinyint(1) NOT NULL default 0,
  `teachersgroupmark` tinyint(1) NOT NULL default 0,
  `teachersgroupview` binary(1) NOT NULL default 0,
  `teachersoverride` binary(1) NOT NULL default 0,
  `teacherdeletable` binary(1) NOT NULL default 0,
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
  `groupid` int(10) NOT NULL default '0',
  `timeadded` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `courseid` (`groupingid`)
) ENGINE=MyISAM  AUTO_INCREMENT=67 ;

# --------------------------------------------------------

# 
# Table structure for table `prefix_groups`
# 

CREATE TABLE `prefix_groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL default '',
  `enrolmentkey` varchar(50) NOT NULL default '',
  `lang` varchar(10) NOT NULL default 'en',
  `theme` varchar(50) NOT NULL default '',
  `picture` int(10) unsigned NOT NULL default '0',
  `hidepicture` int(1) unsigned NOT NULL default '0',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM ;

# --------------------------------------------------------

# 
# Table structure for table `prefix_groups_members`
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
) ENGINE=MyISAM ;
