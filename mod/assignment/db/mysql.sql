#
# Table structure for table `assignment`
#

CREATE TABLE `assignment` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
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

CREATE TABLE `assignment_submissions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `assignment` int(10) unsigned NOT NULL default '0',
  `user` int(10) unsigned NOT NULL default '0',
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


INSERT INTO log_display VALUES ('assignment', 'view', 'assignment', 'name');
INSERT INTO log_display VALUES ('assignment', 'add', 'assignment', 'name');
INSERT INTO log_display VALUES ('assignment', 'update', 'assignment', 'name');
INSERT INTO log_display VALUES ('assignment', 'view submissions', 'assignment', 'name');
INSERT INTO log_display VALUES ('assignment', 'upload', 'assignment', 'name');

