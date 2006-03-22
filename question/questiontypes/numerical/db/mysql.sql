

-- 
-- Table structure for table `prefix_question_numerical`
-- 

CREATE TABLE prefix_question_numerical (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  answer int(10) unsigned NOT NULL default '0',
  tolerance varchar(255) NOT NULL default '0.0',
  PRIMARY KEY  (id),
  KEY answer (answer),
  KEY question (question)
) TYPE=MyISAM COMMENT='Options for numerical questions';

-- --------------------------------------------------------