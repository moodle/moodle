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

CREATE TABLE `prefix_quiz` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `intro` text NOT NULL,
  `timeopen` int(10) unsigned NOT NULL default '0',
  `timeclose` int(10) unsigned NOT NULL default '0',
  `attempts` smallint(6) NOT NULL default '0',
  `attemptonlast` tinyint(4) NOT NULL default '0',
  `feedback` tinyint(4) NOT NULL default '0',
  `correctanswers` tinyint(4) NOT NULL default '1',
  `grademethod` tinyint(4) NOT NULL default '1',
  `review` tinyint(4) NOT NULL default '0',
  `shufflequestions` tinyint(4) NOT NULL default '0',
  `shuffleanswers` tinyint(4) NOT NULL default '0',
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

CREATE TABLE `prefix_quiz_answers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `answer` varchar(255) NOT NULL default '',
  `fraction` varchar(10) NOT NULL default '0.0',
  `feedback` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Answers, with a fractional grade (0-1) and feedback';
# --------------------------------------------------------

#
# Table structure for table `quiz_attempts`
#

CREATE TABLE `prefix_quiz_attempts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `quiz` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `attempt` smallint(6) NOT NULL default '0',
  `sumgrades` varchar(10) NOT NULL default '0.0',
  `timestart` int(10) unsigned NOT NULL default '0',
  `timefinish` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `quiz` (`quiz`),
  KEY `userid` (`userid`)
) TYPE=MyISAM COMMENT='Stores various attempts on a quiz';
# --------------------------------------------------------

#
# Table structure for table `quiz_categories`
#

CREATE TABLE `prefix_quiz_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `info` text NOT NULL,
  `publish` tinyint(4) NOT NULL default '0',
  `stamp` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Categories are for grouping questions';
# --------------------------------------------------------

#
# Table structure for table `quiz_grades`
#

CREATE TABLE `prefix_quiz_grades` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `quiz` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `grade` varchar(10) NOT NULL default '0.0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `quiz` (`quiz`),
  KEY `userid` (`userid`)
) TYPE=MyISAM COMMENT='Final quiz grade (may be best of several attempts)';
# --------------------------------------------------------

#
# Table structure for table `quiz_match`
#

CREATE TABLE `prefix_quiz_match` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `subquestions` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Defines fixed matching questions';
# --------------------------------------------------------

#
# Table structure for table `quiz_match_sub`
#

CREATE TABLE `prefix_quiz_match_sub` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `questiontext` text NOT NULL,
  `answertext` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Defines the subquestions that make up a matching question';
# --------------------------------------------------------

#
# Table structure for table `quiz_multichoice`
#

CREATE TABLE `prefix_quiz_multichoice` (
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

CREATE TABLE `prefix_quiz_question_grades` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `quiz` int(10) unsigned NOT NULL default '0',
  `question` int(10) unsigned NOT NULL default '0',
  `grade` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `quiz` (`quiz`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='The grade for a question in a quiz';
# --------------------------------------------------------

#
# Table structure for table `quiz_questions`
#

CREATE TABLE `prefix_quiz_questions` (
  `id` int(10) NOT NULL auto_increment,
  `category` int(10) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `questiontext` text NOT NULL,
  `questiontextformat` tinyint(2) NOT NULL default '0',
  `image` varchar(255) NOT NULL default '',
  `defaultgrade` INT UNSIGNED DEFAULT '1' NOT NULL,
  `qtype` smallint(6) NOT NULL default '0',
  `stamp` varchar(255) NOT NULL default '',
  `version` int(10) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='The quiz questions themselves';
# --------------------------------------------------------

#
# Table structure for table `quiz_randomsamatch`
#

CREATE TABLE `prefix_quiz_randomsamatch` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `choose` INT UNSIGNED DEFAULT '4' NOT NULL,
  PRIMARY KEY ( `id` ),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Info about a random short-answer matching question';
# --------------------------------------------------------

#
# Table structure for table `quiz_responses`
#

CREATE TABLE `prefix_quiz_responses` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `attempt` int(10) unsigned NOT NULL default '0',
  `question` int(10) unsigned NOT NULL default '0',
  `answer` varchar(255) NOT NULL default '',
  `grade` varchar(10) NOT NULL default '0.0',
  PRIMARY KEY  (`id`),
  KEY `attempt` (`attempt`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Stores user responses to a quiz, and percentage grades';
# --------------------------------------------------------

#
# Table structure for table `quiz_shortanswer`
#

CREATE TABLE `prefix_quiz_shortanswer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `answers` varchar(255) NOT NULL default '',
  `usecase` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Options for short answer questions';
# --------------------------------------------------------

#
# Table structure for table `quiz_numerical`
#

CREATE TABLE `prefix_quiz_numerical` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `answer` int(10) unsigned NOT NULL default '0',
  `min` varchar(255) NOT NULL default '',
  `max` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `answer` (`answer`)
) TYPE=MyISAM COMMENT='Options for numerical questions';
# --------------------------------------------------------

#
# Table structure for table `quiz_truefalse`
#

CREATE TABLE `prefix_quiz_truefalse` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `trueanswer` int(10) unsigned NOT NULL default '0',
  `falseanswer` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Options for True-False questions';
# --------------------------------------------------------

#
# Table structure for table `quiz_multianswers`
#

CREATE TABLE `prefix_quiz_multianswers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `answers` varchar(255) NOT NULL default '',
  `positionkey` varchar(255) NOT NULL default '',
  `answertype` smallint(6) NOT NULL default '0',
  `norm` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Options for multianswer questions';
# --------------------------------------------------------

INSERT INTO prefix_log_display VALUES ('quiz', 'add', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'update', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'view', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'report', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'attempt', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'submit', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'review', 'quiz', 'name');

