#
# Table structure for table `assignment`
#

CREATE TABLE `prefix_assignment` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `format` tinyint(2) unsigned NOT NULL default '0',
  `resubmit` tinyint(2) unsigned NOT NULL default '0',
  `type` int(10) unsigned NOT NULL default '1',
  `maxbytes` int(10) unsigned NOT NULL default '100000',
  `timedue` int(10) unsigned NOT NULL default '0',
  `grade` int(10) NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
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
  `grade` int(11) NOT NULL default '0',
  `comment` text NOT NULL,
  `teacher` int(10) unsigned NOT NULL default '0',
  `timemarked` int(10) unsigned NOT NULL default '0',
  `mailed` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) COMMENT='Info about submitted assignments';
# --------------------------------------------------------


INSERT INTO prefix_log_display VALUES ('assignment', 'view', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'add', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'update', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'view submission', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'upload', 'assignment', 'name');

