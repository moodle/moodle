#
# Table structure for table `exercise`
#

CREATE TABLE `prefix_exercise` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `nelements` tinyint(3) unsigned NOT NULL default '1',
  `phase` tinyint(3) unsigned NOT NULL default '0',
  `gradingstrategy` tinyint(3) unsigned NOT NULL default '1',
  `usemaximum` tinyint(3) unsigned NOT NULL default '0',
  `anonymous` tinyint(3) unsigned NOT NULL default '0',
  `maxbytes` int(10) unsigned NOT NULL default '100000',
  `deadline` int(10) unsigned NOT NULL default '0',
  `grade` tinyint(3) NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `teacherweight` tinyint(3) unsigned NOT NULL default '5',
  `gradingweight` tinyint(3) unsigned NOT NULL default '5',
  `showleaguetable` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) COMMENT='Defines exercise';
# --------------------------------------------------------

#
# Table structure for table `exercise_submissions`
#

CREATE TABLE `prefix_exercise_submissions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `exerciseid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `resubmit` tinyint(3) unsigned NOT NULL default '0',
  `mailed` tinyint(3) unsigned NOT NULL default '0',
  `isexercise` tinyint(3) unsigned NOT NULL default '0',
  `late` tinyint(3) unsigned NOT NULL default '0',
   PRIMARY KEY  (`id`),
   INDEX `userid` (`userid`) 
) COMMENT='Info about submitted work from teacher and students';
# --------------------------------------------------------

#
# Table structure for table `exercise_assessments`
#

CREATE TABLE `prefix_exercise_assessments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `exerciseid` int(10) unsigned NOT NULL default '0',
  `submissionid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timegraded` int(10) unsigned NOT NULL default '0',
  `grade` float NOT NULL default '0',
  `gradinggrade` int(3) NOT NULL default '0',
  `mailed` tinyint(2) unsigned NOT NULL default '0',
  `generalcomment` text NOT NULL,
  `teachercomment` text NOT NULL,
   PRIMARY KEY  (`id`),
   INDEX (`submissionid`),
   INDEX (`userid`) 
  ) COMMENT='Info about assessments by teacher and students';
# --------------------------------------------------------

#
# Table structure for table `exercise_elements`
#

CREATE TABLE `prefix_exercise_elements` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `exerciseid` int(10) unsigned NOT NULL default '0',
  `elementno` tinyint(3) unsigned NOT NULL default '0',
  `description` text NOT NULL,
  `scale` tinyint(3) unsigned NOT NULL default '0',
  `maxscore` tinyint(3) unsigned NOT NULL default '1',
  `weight` tinyint(3) unsigned NOT NULL default '11',
  PRIMARY KEY  (`id`)
) COMMENT='Info about marking scheme of assignment';
# --------------------------------------------------------


#
# Table structure for table `exercise_rubrics`
#

CREATE TABLE `prefix_exercise_rubrics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `exerciseid` int(10) unsigned NOT NULL default '0',
  `elementno` int(10) unsigned NOT NULL default '0',
  `rubricno` tinyint(3) unsigned NOT NULL default '0',
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) COMMENT='Info about the rubrics marking scheme';
# --------------------------------------------------------

#
# Table structure for table `exercise_grades`
#

CREATE TABLE `prefix_exercise_grades` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `exerciseid` int(10) unsigned NOT NULL default '0', 
  `assessmentid` int(10) unsigned NOT NULL default '0',
  `elementno` int(10) unsigned NOT NULL default '0',
  `feedback` text NOT NULL default '',
  `grade` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  INDEX (`assessmentid`)
) COMMENT='Info about individual grades given to each element';
# --------------------------------------------------------

        

INSERT INTO `prefix_log_display` VALUES ('exercise', 'close', 'exercise', 'name');
INSERT INTO `prefix_log_display` VALUES ('exercise', 'open', 'exercise', 'name');
INSERT INTO `prefix_log_display` VALUES ('exercise', 'submit', 'exercise', 'name');
INSERT INTO `prefix_log_display` VALUES ('exercise', 'view', 'exercise', 'name');
INSERT INTO `prefix_log_display` VALUES ('exercise', 'update', 'exercise', 'name');

