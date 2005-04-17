#
# Table structure for table `assignment`
#

CREATE TABLE `prefix_assignment` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `format` tinyint(4) unsigned NOT NULL default '0',
  `assignmenttype` varchar(50) NOT NULL default '',
  `resubmit` tinyint(2) unsigned NOT NULL default '0',
  `emailteachers` tinyint(2) unsigned NOT NULL default '0',
  `var1` int(10) default '0',
  `var2` int(10) default '0',
  `var3` int(10) default '0',
  `var4` int(10) default '0',
  `var5` int(10) default '0',
  `maxbytes` int(10) unsigned NOT NULL default '100000',
  `timedue` int(10) unsigned NOT NULL default '0',
  `timeavailable` int(10) unsigned NOT NULL default '0',
  `grade` int(10) NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `course` (`course`)
) COMMENT='Defines assignments';
# --------------------------------------------------------

#
# Table structure for table `assignment_submissions`
#

CREATE TABLE `prefix_assignment_submissions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `assignment` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `timecreated` int(10) unsigned NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  `numfiles` int(10) unsigned NOT NULL default '0',
  `data1` text NOT NULL,
  `data2` text NOT NULL,
  `grade` int(11) NOT NULL default '0',
  `comment` text NOT NULL,
  `format` tinyint(4) unsigned NOT NULL default '0',
  `teacher` int(10) unsigned NOT NULL default '0',
  `timemarked` int(10) unsigned NOT NULL default '0',
  `mailed` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `assignment` (`assignment`),
  KEY `userid` (`userid`),
  KEY `mailed` (`mailed`),
  KEY `timemarked` (`timemarked`)
) COMMENT='Info about submitted assignments';
# --------------------------------------------------------


INSERT INTO prefix_log_display VALUES ('assignment', 'view', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'add', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'update', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'view submission', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'upload', 'assignment', 'name');

