# phpMyAdmin MySQL-Dump
# version 2.3.2-dev
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Oct 16, 2002 at 01:12 AM
# Server version: 3.23.49
# PHP Version: 4.2.3
# Database : moodle
# --------------------------------------------------------

#
# Table structure for table prefix_quiz
#

CREATE TABLE prefix_quiz (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  intro text NOT NULL default '',
  timeopen integer NOT NULL default '0',
  timeclose integer NOT NULL default '0',
  optionflags integer NOT NULL default '0',
  penaltyscheme integer NOT NULL default '0',
  attempts integer NOT NULL default '0',
  attemptonlast integer NOT NULL default '0',
  grademethod integer NOT NULL default '1',
  decimalpoints integer NOT NULL default '2',
  review integer NOT NULL default '0',
  questionsperpage integer NOT NULL default '0',
  shufflequestions integer NOT NULL default '0',
  shuffleanswers integer NOT NULL default '0',
  questions text NOT NULL default '',
  sumgrades integer NOT NULL default '0',
  grade integer NOT NULL default '0',
  timecreated integer NOT NULL default '0',
  timemodified integer NOT NULL default '0',
  timelimit integer NOT NULL default '0',
  password varchar(255) NOT NULL default '',
  subnet varchar(255) NOT NULL default '',
  popup integer NOT NULL default '0'
);

CREATE INDEX prefix_quiz_course_idx ON prefix_quiz (course);

# --------------------------------------------------------

#
# Table structure for table prefix_quiz_answers
#

CREATE TABLE prefix_quiz_answers (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  answer text NOT NULL default '',
  fraction varchar(10) NOT NULL default '0.0',
  feedback text NOT NULL default ''
);

CREATE INDEX prefix_quiz_answers_question_idx ON prefix_quiz_answers (question);


# --------------------------------------------------------
#
# Table structure for table prefix_quiz_attemptonlast_datasets
#

CREATE TABLE prefix_quiz_attemptonlast_datasets (
    id SERIAL8 PRIMARY KEY,
    category INT8  NOT NULL default '0',
    userid INT8  NOT NULL default '0',
    datasetnumber INT8  NOT NULL default '0',
    CONSTRAINT  prefix_quiz_attemptonlast_datasets_category_userid UNIQUE (category,userid)
);


# --------------------------------------------------------
#
# Table structure for table prefix_quiz_attempts
#

CREATE TABLE prefix_quiz_attempts (
  id SERIAL PRIMARY KEY,
  uniqueid integer NOT NULL default '0',
  quiz integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  attempt integer NOT NULL default '0',
  sumgrades varchar(10) NOT NULL default '0.0',
  timestart integer NOT NULL default '0',
  timefinish integer NOT NULL default '0',
  timemodified integer NOT NULL default '0',
  layout text NOT NULL default '',
  preview integer NOT NULL default '0'
);

CREATE INDEX prefix_quiz_attempts_quiz_idx ON prefix_quiz_attempts (quiz);
CREATE INDEX prefix_quiz_attempts_userid_idx ON prefix_quiz_attempts (userid);

# --------------------------------------------------------
#
# Table structure for table prefix_quiz_calculated
#

CREATE TABLE prefix_quiz_calculated (
    id SERIAL8 PRIMARY KEY,
    question INT8  NOT NULL default '0',
    answer INT8  NOT NULL default '0',
    tolerance varchar(20) NOT NULL default '0.0',
    tolerancetype INT8 NOT NULL default '1',
    correctanswerlength INT8 NOT NULL default '2',
    correctanswerformat INT8 NOT NULL default '2'
);

CREATE INDEX prefix_quiz_calculated_question_idx ON prefix_quiz_calculated (question);
CREATE INDEX prefix_quiz_calculated_answer_idx ON prefix_quiz_calculated (answer);


# --------------------------------------------------------

#
# Table structure for table prefix_quiz_categories
#

CREATE TABLE prefix_quiz_categories (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  info text NOT NULL default '',
  publish integer NOT NULL default '0',
  stamp varchar(255) NOT NULL default '',
  parent integer NOT NULL default '0',
  sortorder integer NOT NULL default '999'
);

CREATE INDEX prefix_quiz_categories_course_idx ON prefix_quiz_categories (course);

# --------------------------------------------------------
#
# Table structure for table prefix_quiz_dataset_definitions
#


CREATE TABLE prefix_quiz_dataset_definitions (
    id SERIAL8 PRIMARY KEY,
    category INT8  NOT NULL default '0',
    name varchar(255) NOT NULL default '',
    type INT8 NOT NULL default '0',
    options varchar(255) NOT NULL default '',
    itemcount INT8  NOT NULL default '0'
);

CREATE INDEX prefix_quiz_dataset_definitions_category_idx ON prefix_quiz_dataset_definitions (category);

# --------------------------------------------------------
#
# Table structure for table prefix_quiz_dataset_items
#

CREATE TABLE prefix_quiz_dataset_items (
    id SERIAL8 PRIMARY KEY,
    definition INT8  NOT NULL default '0',
    number INT8  NOT NULL default '0',
    value varchar(255) NOT NULL default ''
);

CREATE INDEX prefix_quiz_dataset_items_definition_idx  ON prefix_quiz_dataset_items (definition);


# --------------------------------------------------------

#
# Table structure for table prefix_quiz_grades
#

CREATE TABLE prefix_quiz_grades (
  id SERIAL PRIMARY KEY,
  quiz integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  grade real NOT NULL default '0',
  timemodified integer NOT NULL default '0'
);

CREATE INDEX prefix_quiz_grades_quiz_idx ON prefix_quiz_grades (quiz);
CREATE INDEX prefix_quiz_grades_userid_idx ON prefix_quiz_grades (userid);

# --------------------------------------------------------

#
# Table structure for table prefix_quiz_match
#

CREATE TABLE prefix_quiz_match (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  subquestions varchar(255) NOT NULL default ''
);

CREATE INDEX prefix_quiz_match_question_idx ON prefix_quiz_match (question);

# --------------------------------------------------------

#
# Table structure for table prefix_quiz_match_sub
#

CREATE TABLE prefix_quiz_match_sub (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  questiontext text NOT NULL default '',
  answertext varchar(255) NOT NULL default ''
);
CREATE INDEX prefix_quiz_match_sub_question_idx ON prefix_quiz_match_sub (question);

# --------------------------------------------------------

#
# Table structure for table prefix_quiz_multianswers
#

CREATE TABLE prefix_quiz_multianswers (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  sequence varchar(255) NOT NULL default ''
);

CREATE INDEX prefix_quiz_multianswers_question_idx ON prefix_quiz_multianswers (question);

# --------------------------------------------------------

#
# Table structure for table prefix_quiz_multichoice
#

CREATE TABLE prefix_quiz_multichoice (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  layout integer NOT NULL default '0',
  answers varchar(255) NOT NULL default '',
  single integer NOT NULL default '0'
);

CREATE INDEX prefix_quiz_multichoice_question_idx ON prefix_quiz_multichoice (question);


# --------------------------------------------------------

#
# Table structure for table prefix_quiz_newest_states
#


CREATE TABLE prefix_quiz_newest_states (
  id SERIAL PRIMARY KEY,
  attemptid integer NOT NULL default '0',
  questionid integer NOT NULL default '0',
  newest integer NOT NULL default '0',
  newgraded integer NOT NULL default '0',
  sumpenalty varchar(10) NOT NULL default '0.0'
);

CREATE UNIQUE INDEX prefix_quiz_newest_states_attempt_idx ON prefix_quiz_newest_states (attemptid,questionid);

# --------------------------------------------------------

#
# Table structure for table prefix_quiz_numerical
#

CREATE TABLE prefix_quiz_numerical (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  answer integer NOT NULL default '0',
  tolerance varchar(255) NOT NULL default '0.0'
);

CREATE INDEX prefix_quiz_numerical_answer_idx ON prefix_quiz_numerical (answer);
CREATE INDEX prefix_quiz_numerical_question_idx ON prefix_quiz_numerical (question);

# --------------------------------------------------------
#
# Table structure for table prefix_quiz_numerical_units
#

CREATE TABLE prefix_quiz_numerical_units (
    id SERIAL8 PRIMARY KEY,
    question INT8  NOT NULL default '0',
    multiplier decimal(40,20) NOT NULL default '1.00000000000000000000',
    unit varchar(50) NOT NULL default ''
);

CREATE INDEX prefix_quiz_numerical_units_question_idx ON prefix_quiz_numerical_units (question);

# --------------------------------------------------------
#
# Table structure for table prefix_quiz_question_datasets
#

CREATE TABLE prefix_quiz_question_datasets (
    id SERIAL8 PRIMARY KEY,
    question INT8  NOT NULL default '0',
    datasetdefinition INT8  NOT NULL default '0'
);

CREATE INDEX prefix_quiz_question_datasets_question_datasetdefinition_idx  ON prefix_quiz_question_datasets (question,datasetdefinition);

# --------------------------------------------------------

#
# Table structure for table prefix_quiz_question_instances
#

CREATE TABLE prefix_quiz_question_instances (
  id SERIAL PRIMARY KEY,
  quiz integer NOT NULL default '0',
  question integer NOT NULL default '0',
  grade integer NOT NULL default '0'
);

CREATE INDEX prefix_quiz_question_instances_quiz_idx ON prefix_quiz_question_instances (quiz);
CREATE INDEX prefix_quiz_question_instances_question_idx ON prefix_quiz_question_instances (question);

# --------------------------------------------------------

#
# Table structure for table prefix_quiz_question_versions
#

CREATE TABLE prefix_quiz_question_versions (
  id SERIAL PRIMARY KEY,
  quiz integer NOT NULL default '0',
  oldquestion integer NOT NULL default '0',
  newquestion integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  timestamp integer NOT NULL default '0'
);

# --------------------------------------------------------

#
# Table structure for table prefix_quiz_questions
#

CREATE TABLE prefix_quiz_questions (
  id SERIAL PRIMARY KEY,
  category integer NOT NULL default '0',
  parent integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  questiontext text NOT NULL default '',
  questiontextformat integer NOT NULL default '0',
  image varchar(255) NOT NULL default '',
  defaultgrade integer NOT NULL default '1',
  penalty real NOT NULL default '0.1',
  qtype integer NOT NULL default '0',
  length integer NOT NULL DEFAULT '1',
  stamp varchar(255) NOT NULL default '',
  version integer NOT NULL default '1',
  hidden integer NOT NULL default '0'
);

CREATE INDEX prefix_quiz_questions_category_idx ON prefix_quiz_questions (category);

# --------------------------------------------------------

#
# Table structure for table prefix_quiz_randomsamatch
#

CREATE TABLE prefix_quiz_randomsamatch (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  choose integer NOT NULL default '4'
);

CREATE INDEX prefix_quiz_randomsamatch_question_idx ON prefix_quiz_randomsamatch (question);

# --------------------------------------------------------

#
# Table structure for table prefix_quiz_rqp
#

CREATE TABLE prefix_quiz_rqp (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  type integer NOT NULL default '0',
  source text NOT NULL,
  format varchar(255) NOT NULL default '',
  flags integer NOT NULL default '0',
  maxscore integer NOT NULL default '1'
);

CREATE INDEX prefix_quiz_rqp_question_idx ON prefix_quiz_rqp (question);


# --------------------------------------------------------

#
# Table structure for table prefix_quiz_rqp_states
#

CREATE TABLE prefix_quiz_rqp_states (
  id SERIAL PRIMARY KEY,
  stateid integer NOT NULL default '0',
  responses text NOT NULL,
  persistent_data text NOT NULL,
  template_vars text NOT NULL
);

# --------------------------------------------------------

#
# Table structure for table prefix_quiz_rqp_type
#

CREATE TABLE prefix_quiz_rqp_types (
  id SERIAL PRIMARY KEY,
  name varchar(255) NOT NULL default '',
  rendering_server varchar(255) NOT NULL default '',
  cloning_server varchar(255) NOT NULL default '',
  flags integer NOT NULL default '0'
);

CREATE UNIQUE INDEX prefix_quiz_rqp_types_name_uk ON prefix_quiz_rqp_types (name);


# --------------------------------------------------------

#
# Table structure for table prefix_quiz_shortanswer
#

CREATE TABLE prefix_quiz_shortanswer (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  answers varchar(255) NOT NULL default '',
  usecase integer NOT NULL default '0'
);
CREATE INDEX prefix_quiz_shortanswer_question_idx ON prefix_quiz_shortanswer (question);


# --------------------------------------------------------

#
# Table structure for table prefix_quiz_states
#

CREATE TABLE prefix_quiz_states (
  id SERIAL PRIMARY KEY,
  attempt integer NOT NULL default '0',
  question integer NOT NULL default '0',
  originalquestion integer NOT NULL default '0',
  seq_number integer NOT NULL default '0',
  answer text NOT NULL default '',
  timestamp integer NOT NULL default '0',
  event integer NOT NULL default '0',
  grade varchar(10) NOT NULL default '0.0',
  raw_grade varchar(10) NOT NULL default '',
  penalty varchar(10) NOT NULL default '0.0'
);

CREATE INDEX prefix_quiz_states_attempt_idx ON prefix_quiz_states (attempt);
CREATE INDEX prefix_quiz_states_question_idx ON prefix_quiz_states (question);;


# --------------------------------------------------------
#
# Table structure for table prefix_quiz_truefalse
#

CREATE TABLE prefix_quiz_truefalse (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  trueanswer integer NOT NULL default '0',
  falseanswer integer NOT NULL default '0'
);
CREATE INDEX prefix_quiz_truefalse_question_idx ON prefix_quiz_truefalse (question);


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
