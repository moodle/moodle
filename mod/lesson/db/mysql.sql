# This file contains a complete database schema for all the 
# tables used by the mlesson module, written in SQL

# It may also contain INSERT statements for particular data 
# that may be used, especially new entries in the table log_display

CREATE TABLE `prefix_lesson` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `grade` tinyint(3) NOT NULL default '0',
  `usemaxgrade` tinyint(3) NOT NULL default '0',
  `maxanswers` int(3) unsigned NOT NULL default '4',
  `maxattempts` int(3) unsigned NOT NULL default '5',
  `nextpagedefault` int(3) unsigned NOT NULL default '0',
  `minquestions` int(3) unsigned NOT NULL default '0',
  `maxpages` int(3) unsigned NOT NULL default '0',
  `retake` int(3) unsigned NOT NULL default '1',
  `available` int(10) unsigned NOT NULL default '0',
  `deadline` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
   PRIMARY KEY  (`id`)
) COMMENT='Defines lesson';
# --------------------------------------------------------

CREATE TABLE `prefix_lesson_pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `lessonid` int(10) unsigned NOT NULL default '0',
  `prevpageid` int(10) unsigned NOT NULL default '0',
  `nextpageid` int(10) unsigned NOT NULL default '0',
  `qtype` tinyint(3) unsigned NOT NULL default '0',
  `qoption` tinyint(3) unsigned NOT NULL default '0',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `contents` text NOT NULL default '',
  PRIMARY KEY  (`id`)
) COMMENT='Defines lesson_pages';
# --------------------------------------------------------

CREATE TABLE `prefix_lesson_answers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `lessonid` int(10) unsigned NOT NULL default '0',
  `pageid` int(10) unsigned NOT NULL default '0',
  `jumpto` int(11) NOT NULL default '0',
  `grade` tinyint(3) unsigned NOT NULL default '0',
  `flags` tinyint(3) unsigned NOT NULL default '0',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `answer` text NOT NULL default '',
  `response` text NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY (`pageid`)
) COMMENT='Defines lesson_answers';
# --------------------------------------------------------

CREATE TABLE `prefix_lesson_attempts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `lessonid` int(10) unsigned NOT NULL default '0',
  `pageid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `answerid` int(10) unsigned NOT NULL default '0',
  `retry` int(3) unsigned NOT NULL default '0',
  `correct` int(10) unsigned NOT NULL default '0',
  `timeseen` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY (`userid`)
) COMMENT='Defines lesson_attempts';
# --------------------------------------------------------

CREATE TABLE `prefix_lesson_grades` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `lessonid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `grade` int(3) unsigned NOT NULL default '0',
  `late` int(3) unsigned NOT NULL default '0',
  `completed` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) COMMENT='Defines lesson_grades';
# --------------------------------------------------------


INSERT INTO prefix_log_display VALUES ('lesson', 'start', 'lesson', 'name');
INSERT INTO prefix_log_display VALUES ('lesson', 'end', 'lesson', 'name');
INSERT INTO prefix_log_display VALUES ('lesson', 'view', 'lesson_pages', 'title');
