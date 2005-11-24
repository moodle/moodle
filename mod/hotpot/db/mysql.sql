#
# Table structure for table `hotpot`
#
CREATE TABLE prefix_hotpot (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  reference varchar(255) NOT NULL default '',
  summary text NOT NULL,
  timeopen int(10) unsigned NOT NULL default '0',
  timeclose int(10) unsigned NOT NULL default '0',
  attempts smallint(6) NOT NULL default '0',
  grademethod tinyint(4) NOT NULL default '1',
  review tinyint(4) NOT NULL default '0',
  grade int(10) NOT NULL default '0',
  timecreated int(10) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  location int(4) unsigned NOT NULL default '0',
  navigation int(4) unsigned NOT NULL default '1',
  outputformat int(4) unsigned NOT NULL default '1',
  shownextquiz int(4) unsigned NOT NULL default '0',
  forceplugins int(4) unsigned NOT NULL default '0',
  password varchar(255) NOT NULL default '',
  subnet varchar(255) NOT NULL default '',
  clickreporting tinyint(4) unsigned NOT NULL default '0',
  studentfeedback tinyint(4) unsigned NOT NULL default '0',
  studentfeedbackurl varchar(255) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='details about Hot Potatoes quizzes';
#
# Table structure for table `hotpot_attempts`
#
CREATE TABLE prefix_hotpot_attempts (
  id int(10) unsigned NOT NULL auto_increment,
  hotpot int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  starttime int(10) unsigned default NULL,
  endtime int(10) unsigned default NULL,
  score int(6) unsigned default NULL,
  penalties int(6) unsigned default NULL,
  attempt int(6) unsigned NOT NULL default '0',
  timestart int(10) unsigned default NULL,
  timefinish int(10) unsigned default NULL,
  status tinyint(4) unsigned NOT NULL default '1',
  clickreportid int(10) unsigned default NULL,
  PRIMARY KEY  (id),
  KEY prefix_hotpot_attempts_hotpot_idx (hotpot),
  KEY prefix_hotpot_attempts_userid_idx (userid)
) TYPE=MyISAM COMMENT='details about Hot Potatoes quiz attempts';
# 
# Table structure for table `hotpot_details`
#
CREATE TABLE prefix_hotpot_details (
  id int(10) unsigned NOT NULL auto_increment,
  attempt int(10) unsigned NOT NULL,
  details text,
  PRIMARY KEY  (id),
  KEY prefix_hotpot_details_attempt_idx (attempt)
) TYPE=MyISAM COMMENT='raw details (as XML) of Hot Potatoes quiz attempts';
#
# Table structure for table `hotpot_questions`
#
CREATE TABLE prefix_hotpot_questions (
  id int(10) unsigned NOT NULL auto_increment,
  name text NOT NULL,
  type tinyint(4) unsigned default NULL,
  text int(10) unsigned default NULL,
  hotpot int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY prefix_hotpot_questions_name_idx (name(20)),
  KEY prefix_hotpot_questions_hotpot_idx (hotpot)
) TYPE=MyISAM COMMENT='details about questions in Hot Potatatoes quiz attempts';
#
# Table structure for table `hotpot_responses`
#
CREATE TABLE prefix_hotpot_responses (
  id int(10) unsigned NOT NULL auto_increment,
  attempt int(10) unsigned NOT NULL default '0',
  question int(10) unsigned NOT NULL default '0',
  score smallint(8) default NULL,
  weighting smallint(8) default NULL,
  correct varchar(255) default NULL,
  wrong varchar(255) default NULL,
  ignored varchar(255) default NULL,
  hints smallint(6) default NULL,
  clues smallint(6) default NULL,
  checks smallint(6) default NULL,
  PRIMARY KEY  (id),
  KEY prefix_hotpot_responses_attempt_idx (attempt),
  KEY prefix_hotpot_responses_question_idx (question)
) TYPE=MyISAM COMMENT='details about responses in Hot Potatatoes quiz attempts';
#
# Table structure for table `hotpot_strings`
#
CREATE TABLE prefix_hotpot_strings (
  id int(10) unsigned NOT NULL auto_increment,
  string text NOT NULL,
  PRIMARY KEY  (id),
  KEY prefix_hotpot_strings_string_idx (string(20))
) TYPE=MyISAM COMMENT='strings used in Hot Potatatoes questions and responses';
    