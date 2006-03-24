
-- 
-- Table structure for table `prefix_question_truefalse`
-- 

CREATE TABLE prefix_question_truefalse (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  trueanswer int(10) unsigned NOT NULL default '0',
  falseanswer int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Options for True-False questions';