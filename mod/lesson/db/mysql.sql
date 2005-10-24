# This file contains a complete database schema for all the 
# tables used by the mlesson module, written in SQL

# It may also contain INSERT statements for particular data 
# that may be used, especially new entries in the table log_display

CREATE TABLE `prefix_lesson` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `practice` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `modattempts` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `usepassword` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `password` VARCHAR(32) NOT NULL default '',
  `dependency` int(10) unsigned NOT NULL default '0',
  `conditions` text NOT NULL default '',
  `grade` tinyint(3) NOT NULL default '0',
  `custom` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `ongoing` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `usemaxgrade` tinyint(3) NOT NULL default '0',
  `maxanswers` int(3) unsigned NOT NULL default '4',
  `maxattempts` int(3) unsigned NOT NULL default '5',
  `review` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `nextpagedefault` int(3) unsigned NOT NULL default '0',
  `minquestions` int(3) unsigned NOT NULL default '0',
  `maxpages` int(3) unsigned NOT NULL default '0',
  `timed` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `maxtime` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `retake` int(3) unsigned NOT NULL default '1',
  `tree` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `mediafile` varchar(255) NOT NULL default '',
  `slideshow` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `width` INT(10) UNSIGNED NOT NULL DEFAULT '640',
  `height` INT(10) UNSIGNED NOT NULL DEFAULT '480',
  `bgcolor` CHAR(7) NOT NULL DEFAULT '#FFFFFF',
  `displayleft` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `displayleftif` INT(3) UNSIGNED NOT NULL DEFAULT '0',
  `progressbar` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `highscores` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `maxhighscores` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `available` int(10) unsigned NOT NULL default '0',
  `deadline` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
   PRIMARY KEY  (`id`),
   KEY `course` (`course`)
) COMMENT='Defines lesson';
# --------------------------------------------------------

CREATE TABLE `prefix_lesson_pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `lessonid` int(10) unsigned NOT NULL default '0',
  `prevpageid` int(10) unsigned NOT NULL default '0',
  `nextpageid` int(10) unsigned NOT NULL default '0',
  `qtype` tinyint(3) unsigned NOT NULL default '0',
  `qoption` tinyint(3) unsigned NOT NULL default '0',
  `layout` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
  `display` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `contents` text NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `lessonid` (`lessonid`)
) COMMENT='Defines lesson_pages';
# --------------------------------------------------------

CREATE TABLE `prefix_lesson_answers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `lessonid` int(10) unsigned NOT NULL default '0',
  `pageid` int(10) unsigned NOT NULL default '0',
  `jumpto` int(11) NOT NULL default '0',
  `grade` tinyint(3) unsigned NOT NULL default '0',
  `score` INT(10) NOT NULL DEFAULT '0',
  `flags` tinyint(3) unsigned NOT NULL default '0',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `answer` text NOT NULL default '',
  `response` text NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY (`pageid`),
  KEY `lessonid` (`lessonid`) 
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
  `useranswer` text NOT NULL default '',
  `timeseen` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY (`userid`),
  KEY `lessonid` (`lessonid`),
  KEY `pageid` (`pageid`)
) COMMENT='Defines lesson_attempts';
# --------------------------------------------------------

CREATE TABLE `prefix_lesson_grades` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `lessonid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `grade` float unsigned NOT NULL default '0',
  `late` int(3) unsigned NOT NULL default '0',
  `completed` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `lessonid` (`lessonid`), 
  KEY `userid` (`userid`)
) COMMENT='Defines lesson_grades';
# --------------------------------------------------------

CREATE TABLE `prefix_lesson_default` 
        ( `id` int(10) unsigned NOT NULL auto_increment,
          `course` int(10) unsigned NOT NULL default '0',
          `practice` tinyint(3) unsigned NOT NULL default '0',
          `modattempts` tinyint(3) unsigned NOT NULL default '0',
          `password` varchar(32) NOT NULL default '',
          `usepassword` int(3) unsigned NOT NULL default '0',
          `grade` tinyint(3) NOT NULL default '0',
          `custom` int(3) unsigned NOT NULL default '0',
          `ongoing` int(3) unsigned NOT NULL default '0',
          `usemaxgrade` tinyint(3) unsigned NOT NULL default '0',
          `maxanswers` int(3) unsigned NOT NULL default '4',
          `maxattempts` int(3) unsigned NOT NULL default '5',
          `review` tinyint(3) unsigned NOT NULL default '0',
          `nextpagedefault` int(3) unsigned NOT NULL default '0',
          `minquestions` tinyint(3) unsigned NOT NULL default '0',
          `maxpages` int(3) unsigned NOT NULL default '0',
          `timed` int(3) unsigned NOT NULL default '0',
          `maxtime` int(10) unsigned NOT NULL default '0',
          `retake` int(3) unsigned NOT NULL default '1',
          `tree` int(3) unsigned NOT NULL default '0',
          `slideshow` int(3) unsigned NOT NULL default '0',
          `width` int(10) unsigned NOT NULL default '640',
          `height` int(10) unsigned NOT NULL default '480',
          `bgcolor` varchar(7) default '#FFFFFF',
          `displayleft` int(3) unsigned NOT NULL default '0',
          `highscores` int(3) unsigned NOT NULL default '0',
          `maxhighscores` int(10) NOT NULL default '0',
          PRIMARY KEY  (`id`)
        ) COMMENT = 'Defines lesson_default';
# --------------------------------------------------------

CREATE TABLE `prefix_lesson_timer`
    ( `id` int(10) unsigned NOT NULL auto_increment,
        `lessonid` int(10) unsigned not null,
      `userid` int(10) unsigned not null,
      `starttime` int(10) unsigned not null,
        `lessontime` int(10) unsigned not null,
      PRIMARY KEY (`id`)
    );
# --------------------------------------------------------

CREATE TABLE `prefix_lesson_branch`
    ( `id` int(10) unsigned not null auto_increment,
      `lessonid` int(10) unsigned not null,
      `userid` int(10) unsigned not null,
      `pageid` int(10) unsigned not null,
      `retry` int(10) unsigned not null,
      `flag`  tinyint(3) unsigned not null,
      `timeseen` int(10) unsigned not null,
      PRIMARY KEY (`id`)
    );
# --------------------------------------------------------

CREATE TABLE `prefix_lesson_high_scores`
    ( `id` int(10) unsigned not null auto_increment,
      `lessonid` int(10) unsigned not null,
      `userid` int(10) unsigned not null,
      `gradeid` int(10) unsigned not null,
      `nickname` varchar(5) not null,
      PRIMARY KEY (`id`)
    );
# --------------------------------------------------------


INSERT INTO prefix_log_display VALUES ('lesson', 'start', 'lesson', 'name');
INSERT INTO prefix_log_display VALUES ('lesson', 'end', 'lesson', 'name');
INSERT INTO prefix_log_display VALUES ('lesson', 'view', 'lesson_pages', 'title');
