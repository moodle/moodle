# phpMyAdmin MySQL-Dump
# version 2.3.2-dev
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Oct 16, 2002 at 01:12 AM
# Server version: 3.23.49
# PHP Version: 4.2.3
# Database : `moodle`
# --------------------------------------------------------

#
# Table structure for table `quiz`
#

CREATE TABLE `quiz` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `intro` text NOT NULL,
  `timeopen` int(10) unsigned NOT NULL default '0',
  `timeclose` int(10) unsigned NOT NULL default '0',
  `attempts` smallint(6) NOT NULL default '0',
  `feedback` tinyint(4) NOT NULL default '0',
  `correctanswers` tinyint(4) NOT NULL default '1',
  `grademethod` tinyint(4) NOT NULL default '1',
  `questions` text NOT NULL,
  `sumgrades` int(10) NOT NULL default '0',
  `grade` int(10) NOT NULL default '0',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Main information about each quiz';
# --------------------------------------------------------

#
# Table structure for table `quiz_answers`
#

CREATE TABLE `quiz_answers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `answer` varchar(255) NOT NULL default '',
  `fraction` varchar(10) NOT NULL default '0.0',
  `feedback` varchar(255) NOT NULL default '',

  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Answers, with a fractional grade (0-1) and feedback';
# --------------------------------------------------------

#
# Table structure for table `quiz_attempts`
#

CREATE TABLE `quiz_attempts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `quiz` int(10) unsigned NOT NULL default '0',
  `user` int(10) unsigned NOT NULL default '0',
  `attempt` smallint(6) NOT NULL default '0',
  `sumgrades` varchar(10) NOT NULL default '0.0',
  `timestart` int(10) unsigned NOT NULL default '0',
  `timefinish` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Stores various attempts on a quiz';
# --------------------------------------------------------

#
# Table structure for table `quiz_categories`
#

CREATE TABLE `quiz_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `info` text NOT NULL,
  `publish` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Categories are for grouping questions';
# --------------------------------------------------------

#
# Table structure for table `quiz_grades`
#

CREATE TABLE `quiz_grades` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `quiz` int(10) unsigned NOT NULL default '0',
  `user` int(10) unsigned NOT NULL default '0',
  `grade` varchar(10) NOT NULL default '0.0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Final quiz grade (may be best of several attempts)';
# --------------------------------------------------------

#
# Table structure for table `quiz_multichoice`
#

CREATE TABLE `quiz_multichoice` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `layout` tinyint(4) NOT NULL default '0',
  `answers` varchar(255) NOT NULL default '',
  `single` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Options for multiple choice questions';
# --------------------------------------------------------

#
# Table structure for table `quiz_question_grades`
#

CREATE TABLE `quiz_question_grades` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `quiz` int(10) unsigned NOT NULL default '0',
  `question` int(10) unsigned NOT NULL default '0',
  `grade` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='The grade for a question in a quiz';
# --------------------------------------------------------

#
# Table structure for table `quiz_questions`
#

CREATE TABLE `quiz_questions` (
  `id` int(10) NOT NULL auto_increment,
  `category` int(10) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `questiontext` text NOT NULL,
  `image` varchar(255) NOT NULL default '',
  `type` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='The quiz questions themselves';
# --------------------------------------------------------

#
# Table structure for table `quiz_responses`
#

CREATE TABLE `quiz_responses` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `attempt` int(10) unsigned NOT NULL default '0',
  `question` int(10) unsigned NOT NULL default '0',
  `answer` varchar(255) NOT NULL default '',
  `grade` varchar(10) NOT NULL default '0.0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Stores user responses to a quiz, and percentage grades';
# --------------------------------------------------------

#
# Table structure for table `quiz_shortanswer`
#

CREATE TABLE `quiz_shortanswer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `answers` varchar(255) NOT NULL default '',
  `usecase` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Options for short answer questions';
# --------------------------------------------------------

#
# Table structure for table `quiz_truefalse`
#

CREATE TABLE `quiz_truefalse` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `true` int(10) unsigned NOT NULL default '0',
  `false` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Options for True-False questions';


INSERT INTO log_display VALUES ('quiz', 'view', 'quiz', 'name');
INSERT INTO log_display VALUES ('quiz', 'report', 'quiz', 'name');
INSERT INTO log_display VALUES ('quiz', 'attempt', 'quiz', 'name');
INSERT INTO log_display VALUES ('quiz', 'submit', 'quiz', 'name');

