#
# Table structure for table `prefix_backup_files`
#

CREATE TABLE `prefix_backup_files` (
  `backup_code` int(10) unsigned NOT NULL default '0',
  `file_type` varchar(10) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `old_id` int(10) unsigned default NULL,
  `new_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`backup_code`,`file_type`,`path`)
) TYPE=MyISAM COMMENT='To store and recode ids to user and course files.';
# --------------------------------------------------------

#
# Table structure for table `prefix_backup_ids`
#

CREATE TABLE `prefix_backup_ids` (
  `backup_code` int(12) unsigned NOT NULL default '0',
  `table_name` varchar(30) NOT NULL default '',
  `old_id` int(10) unsigned NOT NULL default '0',
  `new_id` int(10) unsigned default NULL,
  `info` mediumtext,
  PRIMARY KEY  (`backup_code`,`table_name`,`old_id`)
) TYPE=MyISAM COMMENT='To store and convert ids in backup/restore';
# --------------------------------------------------------

#
# Table structure for table `prefix_backup_config`
#

CREATE TABLE `prefix_backup_config` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM COMMENT='To store backup configuration variables';
# --------------------------------------------------------

#
# Table structure for table `prefix_backup_courses`
#

CREATE TABLE `prefix_backup_courses` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `courseid` int(10) unsigned NOT NULL default '0',
  `laststarttime` int(10) unsigned NOT NULL default '0',
  `lastendtime` int(10) unsigned NOT NULL default '0',
  `laststatus` varchar(1) NOT NULL default '0',
  `nextstarttime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `courseid` (`courseid`)
) TYPE=MyISAM COMMENT='To store every course backup status';

# --------------------------------------------------------

#
# Table structure for table `prefix_backup_log`
#

CREATE TABLE `prefix_backup_log` (
  `id` int(10) unsigned NOT NULL auto_increment, 
  `courseid` int(10) unsigned NOT NULL default '0',
  `time` int(10) unsigned NOT NULL default '0',
  `laststarttime` int(10) unsigned NOT NULL default '0',
  `info` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='To store every course backup log info';
# --------------------------------------------------------
