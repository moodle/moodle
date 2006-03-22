

-- 
-- Table structure for table `prefix_question_multichoice`
-- 

CREATE TABLE prefix_question_multichoice (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  layout tinyint(4) NOT NULL default '0',
  answers varchar(255) NOT NULL default '',
  single tinyint(4) NOT NULL default '0',
  shuffleanswers tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Options for multiple choice questions';

-- --------------------------------------------------------