

-- 
-- Table structure for table `prefix_question_match`
-- 

CREATE TABLE prefix_question_match (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  subquestions varchar(255) NOT NULL default '',
  shuffleanswers tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Defines fixed matching questions';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_question_match_sub`
-- 

CREATE TABLE prefix_question_match_sub (
  id int(10) unsigned NOT NULL auto_increment,
  code int(10) unsigned NOT NULL default '0',
  question int(10) unsigned NOT NULL default '0',
  questiontext text NOT NULL default '',
  answertext varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Defines the subquestions that make up a matching question';

-- --------------------------------------------------------