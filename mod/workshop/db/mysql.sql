#
# Table structure for table `workshop`
#

CREATE TABLE `prefix_workshop` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `nelements` tinyint(3) unsigned NOT NULL default '1',
  `phase` tinyint(2) unsigned NOT NULL default '0',
  `format` tinyint(2) unsigned NOT NULL default '0',
  `gradingstrategy` tinyint(2) unsigned NOT NULL default '1',
  `resubmit` tinyint(2) unsigned NOT NULL default '0',
  `agreeassessments` tinyint(2) unsigned NOT NULL default '0',
  `hidegrades` tinyint(2) unsigned NOT NULL default '0',
  `anonymous` tinyint(2) unsigned NOT NULL default '0',
  `includeself` tinyint(2) unsigned NOT NULL default '0',
  `maxbytes` int(10) unsigned NOT NULL default '100000',
  `deadline` int(10) unsigned NOT NULL default '0',
  `grade` int(10) NOT NULL default '0',
  `ntassessments` tinyint(3) unsigned NOT NULL default '0',
  `nsassessments` tinyint(3) unsigned NOT NULL default '0',
  `overallocation` tinyint(3) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `mergegrades` tinyint(3) unsigned NOT NULL default '0',
  `teacherweight` tinyint(3) unsigned NOT NULL default '5',
  `peerweight` tinyint(3) unsigned NOT NULL default '5',
  `includeteachersgrade` tinyint(3) unsigned NOT NULL default '0',
  `biasweight` tinyint(3) unsigned NOT NULL default '5',
  `reliabilityweight` tinyint(3) unsigned NOT NULL default '5',
  `gradingweight` tinyint(3) unsigned NOT NULL default '5',
  `teacherloading` tinyint(3) unsigned NOT NULL default '5',
  `assessmentstodrop` tinyint(3) unsigned NOT NULL default '0',
  `showleaguetable` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) COMMENT='Defines workshop';
# --------------------------------------------------------

#
# Table structure for table `workshop_submissions`
#

CREATE TABLE `prefix_workshop_submissions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `workshopid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `title` varchar(100) NOT NULL default '',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `mailed` tinyint(2) unsigned NOT NULL default '0',
  `teachergrade` int(3) unsigned NOT NULL default '0',
  `peergrade` int(3) unsigned NOT NULL default '0',
  `biasgrade` int(3) unsigned NOT NULL default '0',
  `reliabilitygrade` int(3) unsigned NOT NULL default '0',
  `gradinggrade` int(3) unsigned NOT NULL default '0',
  `finalgrade` int(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  INDEX `title` (`title`) 
) COMMENT='Info about submitted work from teacher and students';
# --------------------------------------------------------

#
# Table structure for table `workshop_assessments`
#

CREATE TABLE `prefix_workshop_assessments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `workshopid` int(10) unsigned NOT NULL default '0',
  `submissionid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timegraded` int(10) unsigned NOT NULL default '0',
  `timeagreed` int(10) unsigned NOT NULL default '0',
  `grade` float NOT NULL default '0',
  `gradinggrade` int(3) NOT NULL default '0',
  `mailed` tinyint(3) unsigned NOT NULL default '0',
  `resubmission` tinyint(3) unsigned NOT NULL default '0',
  `donotuse` tinyint(3) unsigned NOT NULL default '0',
  `generalcomment` text NOT NULL,
  `teachercomment` text NOT NULL,
  PRIMARY KEY  (`id`)
  ) COMMENT='Info about assessments by teacher and students';
# --------------------------------------------------------

#
# Table structure for table `workshop_elements`
#

CREATE TABLE `prefix_workshop_elements` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `workshopid` int(10) unsigned NOT NULL default '0',
  `elementno` tinyint(3) unsigned NOT NULL default '0',
  `description` text NOT NULL,
  `scale` tinyint(3) unsigned NOT NULL default '0',
  `maxscore` tinyint(3) unsigned NOT NULL default '1',
  `weight` float NOT NULL default '1.0',
  PRIMARY KEY  (`id`)
) COMMENT='Info about marking scheme of assignment';
# --------------------------------------------------------


#
# Table structure for table `workshop_rubrics`
#

CREATE TABLE `prefix_workshop_rubrics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `workshopid` int(10) unsigned NOT NULL default '0',
  `elementno` int(10) unsigned NOT NULL default '0',
  `rubricno` tinyint(3) unsigned NOT NULL default '0',
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) COMMENT='Info about the rubrics marking scheme';
# --------------------------------------------------------

#
# Table structure for table `workshop_grades`
#

CREATE TABLE `prefix_workshop_grades` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `workshopid` int(10) unsigned NOT NULL default '0', 
  `assessmentid` int(10) unsigned NOT NULL default '0',
  `elementno` int(10) unsigned NOT NULL default '0',
  `feedback` text NOT NULL default '',
  `grade` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) COMMENT='Info about individual grades given to each element';
# --------------------------------------------------------

#
# Table structure for table `workshop_comments`
#

CREATE TABLE `prefix_workshop_comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `workshopid` int(10) unsigned NOT NULL default '0', 
  `assessmentid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `mailed` tinyint(2) unsigned NOT NULL default '0',
  `comments` text NOT NULL,
  PRIMARY KEY  (`id`)
) COMMENT='Defines comments';
# --------------------------------------------------------
        
        

INSERT INTO `prefix_log_display` VALUES ('workshop', 'assessments', 'workshop', 'name');
INSERT INTO `prefix_log_display` VALUES ('workshop', 'close', 'workshop', 'name');
INSERT INTO `prefix_log_display` VALUES ('workshop', 'display', 'workshop', 'name');
INSERT INTO `prefix_log_display` VALUES ('workshop', 'resubmit', 'workshop', 'name');
INSERT INTO `prefix_log_display` VALUES ('workshop', 'set up', 'workshop', 'name');
INSERT INTO `prefix_log_display` VALUES ('workshop', 'submissions', 'workshop', 'name');
INSERT INTO `prefix_log_display` VALUES ('workshop', 'view', 'workshop', 'name');
INSERT INTO `prefix_log_display` VALUES ('workshop', 'update', 'workshop', 'name');

