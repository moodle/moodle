CREATE TABLE `prefix_label` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) COMMENT='Defines labels';

INSERT INTO prefix_log_display VALUES ('label', 'add', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('label', 'update', 'quiz', 'name');
