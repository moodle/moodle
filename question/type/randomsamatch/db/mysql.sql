-- 
-- Table structure for table `prefix_question_randomsamatch`
-- 

CREATE TABLE prefix_question_randomsamatch (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  choose int(10) unsigned NOT NULL default '4',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Info about a random short-answer matching question';

-- --------------------------------------------------------