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

