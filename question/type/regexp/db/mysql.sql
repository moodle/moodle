

-- 
-- Table structure for table `prefix_question_regexp`
-- 

CREATE TABLE prefix_question_regexp (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  answers varchar(255) NOT NULL default '',
  usehint tinyint(2) NULL default '0',  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Options for regexp questions';

-- --------------------------------------------------------
