-- phpMyAdmin SQL Dump
-- version 2.6.0-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jun 05, 2005 at 04:32 PM
-- Server version: 4.0.15
-- PHP Version: 4.3.3
-- 
-- Database: `moodle15`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz`
-- 

CREATE TABLE prefix_quiz (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  intro text NOT NULL,
  timeopen int(10) unsigned NOT NULL default '0',
  timeclose int(10) unsigned NOT NULL default '0',
  optionflags int(10) unsigned NOT NULL default '0',
  penaltyscheme int(4) unsigned NOT NULL default '0',
  attempts smallint(6) NOT NULL default '0',
  attemptonlast tinyint(4) NOT NULL default '0',
  grademethod tinyint(4) NOT NULL default '1',
  decimalpoints int(4) NOT NULL default '2',
  review int(10) unsigned NOT NULL default '0',
  questionsperpage int(10) NOT NULL default '0',
  shufflequestions tinyint(4) NOT NULL default '0',
  shuffleanswers tinyint(4) NOT NULL default '0',
  questions text NOT NULL,
  sumgrades int(10) NOT NULL default '0',
  grade int(10) NOT NULL default '0',
  timecreated int(10) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  timelimit int(2) unsigned NOT NULL default '0',
  password varchar(255) NOT NULL default '',
  subnet varchar(255) NOT NULL default '',
  popup tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY course (course)
) TYPE=MyISAM COMMENT='Main information about each quiz';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_answers`
-- 

CREATE TABLE prefix_quiz_answers (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  answer text NOT NULL,
  fraction varchar(10) NOT NULL default '0.0',
  feedback text NOT NULL,
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Answers, with a fractional grade (0-1) and feedback';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_attemptonlast_datasets`
-- 

CREATE TABLE prefix_quiz_attemptonlast_datasets (
  id int(10) unsigned NOT NULL auto_increment,
  category int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  datasetnumber int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY category (category,userid)
) TYPE=MyISAM COMMENT='Dataset number for attemptonlast attempts per user';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_attempts`
-- 

CREATE TABLE prefix_quiz_attempts (
  id int(10) unsigned NOT NULL auto_increment,
  uniqueid int(10) unsigned NOT NULL default '0',
  quiz int(10) unsigned NOT NULL default '0', 
  userid int(10) unsigned NOT NULL default '0',
  attempt smallint(6) NOT NULL default '0',
  sumgrades varchar(10) NOT NULL default '0.0',
  timestart int(10) unsigned NOT NULL default '0',
  timefinish int(10) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  layout text NOT NULL,
  preview tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY quiz (quiz),
  KEY userid (userid)
) TYPE=MyISAM COMMENT='Stores various attempts on a quiz';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_calculated`
-- 

CREATE TABLE prefix_quiz_calculated (
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

-- 
-- Table structure for table `prefix_quiz_categories`
-- 

CREATE TABLE prefix_quiz_categories (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  info text NOT NULL,
  publish tinyint(4) NOT NULL default '0',
  stamp varchar(255) NOT NULL default '',
  parent int(10) unsigned NOT NULL default '0',
  sortorder int(10) unsigned NOT NULL default '999',
  PRIMARY KEY  (id),
  KEY course (course)
) TYPE=MyISAM COMMENT='Categories are for grouping questions';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_dataset_definitions`
-- 

CREATE TABLE prefix_quiz_dataset_definitions (
  id int(10) unsigned NOT NULL auto_increment,
  category int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  type int(10) NOT NULL default '0',
  options varchar(255) NOT NULL default '',
  itemcount int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY category (category)
) TYPE=MyISAM COMMENT='Organises and stores properties for dataset items';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_dataset_items`
-- 

CREATE TABLE prefix_quiz_dataset_items (
  id int(10) unsigned NOT NULL auto_increment,
  definition int(10) unsigned NOT NULL default '0',
  number int(10) unsigned NOT NULL default '0',
  value varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY definition (definition)
) TYPE=MyISAM COMMENT='Individual dataset items';

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_quiz_essay`
-- 

CREATE TABLE `prefix_quiz_essay` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `answer` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Options for essay questions';

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_quiz_essay_states`
-- 

CREATE TABLE `prefix_quiz_essay_states` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `stateid` int(10) unsigned NOT NULL default '0',
  `graded` tinyint(4) unsigned NOT NULL default '0',
  `fraction` varchar(10) NOT NULL default '0.0',
  `response` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='essay question type specific state information';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_grades`
-- 

CREATE TABLE prefix_quiz_grades (
  id int(10) unsigned NOT NULL auto_increment,
  quiz int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  grade double NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY quiz (quiz),
  KEY userid (userid)
) TYPE=MyISAM COMMENT='Final quiz grade (may be best of several attempts)';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_match`
-- 

CREATE TABLE prefix_quiz_match (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  subquestions varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Defines fixed matching questions';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_match_sub`
-- 

CREATE TABLE prefix_quiz_match_sub (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  questiontext text NOT NULL,
  answertext varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Defines the subquestions that make up a matching question';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_multianswers`
-- 

CREATE TABLE prefix_quiz_multianswers (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  sequence varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Options for multianswer questions';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_multichoice`
-- 

CREATE TABLE prefix_quiz_multichoice (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  layout tinyint(4) NOT NULL default '0',
  answers varchar(255) NOT NULL default '',
  single tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Options for multiple choice questions';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_newest_states`
-- 

CREATE TABLE prefix_quiz_newest_states (
  id int(10) unsigned NOT NULL auto_increment,
  attemptid int(10) unsigned NOT NULL default '0',
  questionid int(10) unsigned NOT NULL default '0',
  newest int(10) unsigned NOT NULL default '0',
  newgraded int(10) unsigned NOT NULL default '0',
  sumpenalty varchar(10) NOT NULL default '0.0',
  PRIMARY KEY  (id),
  UNIQUE KEY attemptid (attemptid,questionid)
) TYPE=MyISAM COMMENT='Gives ids of the newest open and newest graded states';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_numerical`
-- 

CREATE TABLE prefix_quiz_numerical (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  answer int(10) unsigned NOT NULL default '0',
  tolerance varchar(255) NOT NULL default '0.0',
  PRIMARY KEY  (id),
  KEY answer (answer),
  KEY question (question)
) TYPE=MyISAM COMMENT='Options for numerical questions';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_numerical_units`
-- 

CREATE TABLE prefix_quiz_numerical_units (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  multiplier decimal(40,20) NOT NULL default '1.00000000000000000000',
  unit varchar(50) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Optional unit options for numerical questions';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_question_datasets`
-- 

CREATE TABLE prefix_quiz_question_datasets (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  datasetdefinition int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY question (question,datasetdefinition)
) TYPE=MyISAM COMMENT='Many-many relation between questions and dataset definitions';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_question_instances`
-- 

CREATE TABLE prefix_quiz_question_instances (
  id int(10) unsigned NOT NULL auto_increment,
  quiz int(10) unsigned NOT NULL default '0',
  question int(10) unsigned NOT NULL default '0',
  grade smallint(6) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY quiz (quiz),
  KEY question (question)
) TYPE=MyISAM COMMENT='The grade for a question in a quiz';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_question_versions`
-- 

CREATE TABLE prefix_quiz_question_versions (
  id int(10) unsigned NOT NULL auto_increment,
  quiz int(10) unsigned NOT NULL default '0',
  oldquestion int(10) unsigned NOT NULL default '0',
  newquestion int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  timestamp int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='The mapping between old and new versions of a question';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_questions`
-- 

CREATE TABLE prefix_quiz_questions (
  id int(10) NOT NULL auto_increment,
  category int(10) NOT NULL default '0',
  parent int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  questiontext text NOT NULL,
  questiontextformat tinyint(2) NOT NULL default '0',
  image varchar(255) NOT NULL default '',
  defaultgrade int(10) unsigned NOT NULL default '1',
  penalty float NOT NULL default '0.1',
  qtype smallint(6) NOT NULL default '0',
  length int(10) unsigned NOT NULL default '1',
  stamp varchar(255) NOT NULL default '',
  version int(10) NOT NULL default '1',
  hidden int(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY category (category)
) TYPE=MyISAM COMMENT='The quiz questions themselves';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_randomsamatch`
-- 

CREATE TABLE prefix_quiz_randomsamatch (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  choose int(10) unsigned NOT NULL default '4',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Info about a random short-answer matching question';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_rqp`
-- 

CREATE TABLE prefix_quiz_rqp (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  type int(10) unsigned NOT NULL default '0',
  source longblob NOT NULL,
  format varchar(255) NOT NULL default '',
  flags tinyint(3) unsigned NOT NULL default '0',
  maxscore int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Options for RQP questions';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_rqp_servers`
-- 

CREATE TABLE prefix_quiz_rqp_servers (
  id int(10) unsigned NOT NULL auto_increment,
  typeid int(10) unsigned NOT NULL default '0',
  url varchar(255) NOT NULL default '',
  can_render tinyint(2) unsigned NOT NULL default '0',
  can_author tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='Information about RQP servers';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_rqp_states`
-- 

CREATE TABLE prefix_quiz_rqp_states (
  id int(10) unsigned NOT NULL auto_increment,
  stateid int(10) unsigned NOT NULL default '0',
  responses text NOT NULL,
  persistent_data text NOT NULL,
  template_vars text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='RQP question type specific state information';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_rqp_types`
-- 

CREATE TABLE prefix_quiz_rqp_types (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY name (name)
) TYPE=MyISAM COMMENT='RQP question types';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_shortanswer`
-- 

CREATE TABLE prefix_quiz_shortanswer (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  answers varchar(255) NOT NULL default '',
  usecase tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Options for short answer questions';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_states`
-- 

CREATE TABLE prefix_quiz_states (
  id int(10) unsigned NOT NULL auto_increment,
  attempt int(10) unsigned NOT NULL default '0',
  question int(10) unsigned NOT NULL default '0',
  originalquestion int(10) unsigned NOT NULL default '0',
  seq_number int(6) unsigned NOT NULL default '0',
  answer text NOT NULL,
  timestamp int(10) unsigned NOT NULL default '0',
  event int(4) unsigned NOT NULL default '0',
  grade varchar(10) NOT NULL default '0.0',
  raw_grade varchar(10) NOT NULL default '',
  penalty varchar(10) NOT NULL default '0.0',
  PRIMARY KEY  (id),
  KEY attempt (attempt),
  KEY question (question)
) TYPE=MyISAM COMMENT='Stores user responses to a quiz, and percentage grades';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_quiz_truefalse`
-- 

CREATE TABLE prefix_quiz_truefalse (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  trueanswer int(10) unsigned NOT NULL default '0',
  falseanswer int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Options for True-False questions';

INSERT INTO prefix_log_display VALUES ('quiz', 'add', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'update', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'view', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'report', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'attempt', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'submit', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'review', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'editquestions', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'preview', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'start attempt', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'close attempt', 'quiz', 'name');
