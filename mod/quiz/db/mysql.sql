-- --------------------------------------------------------
-- Quiz module and question bank table definitions.
--
-- The tables are grouped divided by:
-- quiz/questionbank and definition/runtime.
-- --------------------------------------------------------

-- --------------------------------------------------------
-- Quiz module, quiz definition data.
-- --------------------------------------------------------

CREATE TABLE prefix_quiz (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  intro text NOT NULL default '',
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
  questions text NOT NULL default '',
  sumgrades int(10) NOT NULL default '0',
  grade int(10) NOT NULL default '0',
  timecreated int(10) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  timelimit int(2) unsigned NOT NULL default '0',
  password varchar(255) NOT NULL default '',
  subnet varchar(255) NOT NULL default '',
  popup tinyint(4) NOT NULL default '0',
  delay1 int(10) NOT NULL default '0',
  delay2 int(10) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY course (course)
) TYPE=MyISAM COMMENT='Main information about each quiz';

CREATE TABLE prefix_quiz_question_instances (
  id int(10) unsigned NOT NULL auto_increment,
  quiz int(10) unsigned NOT NULL default '0',
  question int(10) unsigned NOT NULL default '0',
  grade smallint(6) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY quiz (quiz),
  KEY question (question)
) TYPE=MyISAM COMMENT='The grade for a question in a quiz';

CREATE TABLE prefix_quiz_question_versions (
  id int(10) unsigned NOT NULL auto_increment,
  quiz int(10) unsigned NOT NULL default '0',
  oldquestion int(10) unsigned NOT NULL default '0',
  newquestion int(10) unsigned NOT NULL default '0',
  originalquestion int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  timestamp int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='The mapping between old and new versions of a question';

CREATE TABLE prefix_quiz_feedback (
  id int(10) unsigned NOT NULL auto_increment,
  quizid int(10) unsigned NOT NULL default '0',
  feedbacktext text NOT NULL default '',
  mingrade double NOT NULL default '0',
  maxgrade double NOT NULL default '0',
  PRIMARY KEY (id),
  KEY quizid (quizid)
) TYPE=MyISAM COMMENT='Feedback given to students based on their overall score on the test';

-- --------------------------------------------------------
-- Quiz module, quiz runtime data.
-- --------------------------------------------------------

CREATE TABLE prefix_quiz_attempts (
  id int(10) unsigned NOT NULL auto_increment,
  uniqueid int(10) unsigned NOT NULL default '0',
  quiz int(10) unsigned NOT NULL default '0', 
  userid int(10) unsigned NOT NULL default '0',
  attempt smallint(6) NOT NULL default '0',
  sumgrades float NOT NULL default '0',
  timestart int(10) unsigned NOT NULL default '0',
  timefinish int(10) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  layout text NOT NULL default '',
  preview tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY `uniqueid` (`uniqueid`),
  KEY quiz (quiz),
  KEY userid (userid)
) TYPE=MyISAM COMMENT='Stores various attempts on a quiz';

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
-- Questionbank definition data.
-- 
-- TODO, these tables no longer belong to the quiz module.
-- They should be moved elsewhere when a good home for them
-- is found.
-- --------------------------------------------------------

CREATE TABLE prefix_question_categories (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  info text NOT NULL default '',
  publish tinyint(4) NOT NULL default '0',
  stamp varchar(255) NOT NULL default '',
  parent int(10) unsigned NOT NULL default '0',
  sortorder int(10) unsigned NOT NULL default '999',
  PRIMARY KEY  (id),
  KEY course (course)
) TYPE=MyISAM COMMENT='Categories are for grouping questions';

CREATE TABLE prefix_question (
  id int(10) NOT NULL auto_increment,
  category int(10) NOT NULL default '0',
  parent int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  questiontext text NOT NULL,
  questiontextformat tinyint(2) NOT NULL default '0',
  image varchar(255) NOT NULL default '',
  commentarytext text NOT NULL,
  defaultgrade int(10) unsigned NOT NULL default '1',
  penalty float NOT NULL default '0.1',
  qtype varchar(20) NOT NULL default '',
  length int(10) unsigned NOT NULL default '1',
  stamp varchar(255) NOT NULL default '',
  version varchar(255) NOT NULL default '',
  hidden int(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY category (category)
) TYPE=MyISAM COMMENT='The quiz questions themselves';

CREATE TABLE prefix_question_answers (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  answer text NOT NULL default '',
  fraction float NOT NULL default '0',
  feedback text NOT NULL default '',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Answers, with a fractional grade (0-1) and feedback';

CREATE TABLE prefix_question_numerical_units (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  multiplier decimal(40,20) NOT NULL default '1.00000000000000000000',
  unit varchar(50) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Optional unit options for numerical questions';

CREATE TABLE prefix_question_datasets (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  datasetdefinition int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY question (question,datasetdefinition)
) TYPE=MyISAM COMMENT='Many-many relation between questions and dataset definitions';

CREATE TABLE prefix_question_dataset_definitions (
  id int(10) unsigned NOT NULL auto_increment,
  category int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  type int(10) NOT NULL default '0',
  options varchar(255) NOT NULL default '',
  itemcount int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY category (category)
) TYPE=MyISAM COMMENT='Organises and stores properties for dataset items';

CREATE TABLE prefix_question_dataset_items (
  id int(10) unsigned NOT NULL auto_increment,
  definition int(10) unsigned NOT NULL default '0',
  itemnumber int(10) unsigned NOT NULL default '0',
  value varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY definition (definition)
) TYPE=MyISAM COMMENT='Individual dataset items';

-- --------------------------------------------------------
-- Questionbank runtime data.
-- See above TODO.
-- --------------------------------------------------------

CREATE TABLE prefix_question_attempts (
  id int(10) unsigned NOT NULL auto_increment,
  modulename varchar(20) NOT NULL default 'quiz',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='Student attempts. This table gets extended by the modules';

CREATE TABLE prefix_question_sessions (
  id int(10) unsigned NOT NULL auto_increment,
  attemptid int(10) unsigned NOT NULL default '0',
  questionid int(10) unsigned NOT NULL default '0',
  newest int(10) unsigned NOT NULL default '0',
  newgraded int(10) unsigned NOT NULL default '0',
  sumpenalty float NOT NULL default '0',
  manualcomment text NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY attemptid (attemptid,questionid)
) TYPE=MyISAM COMMENT='Gives ids of the newest open and newest graded states';

CREATE TABLE prefix_question_states (
  id int(10) unsigned NOT NULL auto_increment,
  attempt int(10) unsigned NOT NULL default '0',
  question int(10) unsigned NOT NULL default '0',
  originalquestion int(10) unsigned NOT NULL default '0',
  seq_number int(6) unsigned NOT NULL default '0',
  answer text NOT NULL default '',
  timestamp int(10) unsigned NOT NULL default '0',
  event int(4) unsigned NOT NULL default '0',
  grade float NOT NULL default '0',
  raw_grade float NOT NULL default '0',
  penalty float NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY attempt (attempt),
  KEY question (question)
) TYPE=MyISAM COMMENT='Stores user responses to a quiz, and percentage grades';

-- --------------------------------------------------------
-- Quiz log actions.
-- --------------------------------------------------------

INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'add', 'quiz', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'update', 'quiz', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'view', 'quiz', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'report', 'quiz', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'attempt', 'quiz', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'submit', 'quiz', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'review', 'quiz', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'editquestions', 'quiz', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'preview', 'quiz', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'start attempt', 'quiz', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'close attempt', 'quiz', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('quiz', 'continue attempt', 'quiz', 'name');
