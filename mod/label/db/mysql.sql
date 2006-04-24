CREATE TABLE `prefix_label` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY course (course)
) COMMENT='Defines labels';

INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('label', 'add', 'quiz', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('label', 'update', 'quiz', 'name');
