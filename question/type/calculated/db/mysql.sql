-- 
-- Table structure for table `prefix_question_calculated`
-- 

CREATE TABLE prefix_question_calculated (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  answer int(10) unsigned NOT NULL default '0',
  tolerance varchar(20) NOT NULL default '0.0',
  tolerancetype int(10) NOT NULL default '1',
  correctanswerlength int(10) NOT NULL default '2',
  correctanswerformat int(10) NOT NULL default '2',
  PRIMARY KEY  (id),
  KEY question (question),
  KEY answer (answer)
) TYPE=MyISAM COMMENT='Options for questions of type calculated';

-- --------------------------------------------------------