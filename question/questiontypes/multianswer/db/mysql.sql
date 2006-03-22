

-- 
-- Table structure for table `prefix_question_multianswer`
-- 

CREATE TABLE prefix_question_multianswer (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  sequence text NOT NULL default '',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Options for multianswer questions';

-- --------------------------------------------------------