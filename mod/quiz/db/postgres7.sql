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
  popup integer NOT NULL default '0',
  delay1 integer NOT NULL default '0',
  delay2 integer NOT NULL default '0'
);
CREATE INDEX prefix_quiz_course_idx ON prefix_quiz (course);

CREATE TABLE prefix_quiz_question_instances (
  id SERIAL PRIMARY KEY,
  quiz integer NOT NULL default '0',
  question integer NOT NULL default '0',
  grade integer NOT NULL default '0'
);
CREATE INDEX prefix_quiz_question_instances_quiz_idx ON prefix_quiz_question_instances (quiz);
CREATE INDEX prefix_quiz_question_instances_question_idx ON prefix_quiz_question_instances (question);

CREATE TABLE prefix_quiz_question_versions (
  id SERIAL PRIMARY KEY,
  quiz integer NOT NULL default '0',
  oldquestion integer NOT NULL default '0',
  newquestion integer NOT NULL default '0',
  originalquestion integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  timestamp integer NOT NULL default '0'
);

CREATE TABLE prefix_quiz_feedback (
  id SERIAL PRIMARY KEY,
  quizid integer NOT NULL default '0',
  feedbacktext text NOT NULL default '',
  mingrade real NOT NULL default '0',
  maxgrade real NOT NULL default '0'
);
CREATE INDEX prefix_quiz_feedback_quizid_idx ON prefix_quiz_feedback (quizid);

-- --------------------------------------------------------
-- Quiz module, quiz runtime data.
-- --------------------------------------------------------

CREATE TABLE prefix_quiz_attempts (
  id SERIAL PRIMARY KEY,
  uniqueid integer NOT NULL default '0',
  quiz integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  attempt integer NOT NULL default '0',
  sumgrades real NOT NULL default '0',
  timestart integer NOT NULL default '0',
  timefinish integer NOT NULL default '0',
  timemodified integer NOT NULL default '0',
  layout text NOT NULL default '',
  preview integer NOT NULL default '0'
);
CREATE INDEX prefix_quiz_attempts_quiz_idx ON prefix_quiz_attempts (quiz);
CREATE INDEX prefix_quiz_attempts_userid_idx ON prefix_quiz_attempts (userid);
CREATE UNIQUE INDEX prefix_quiz_attempts_uniqueid_uk ON prefix_quiz_attempts (uniqueid);

CREATE TABLE prefix_quiz_grades (
  id SERIAL PRIMARY KEY,
  quiz integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  grade real NOT NULL default '0',
  timemodified integer NOT NULL default '0'
);

CREATE INDEX prefix_quiz_grades_quiz_idx ON prefix_quiz_grades (quiz);
CREATE INDEX prefix_quiz_grades_userid_idx ON prefix_quiz_grades (userid);

-- --------------------------------------------------------
-- Questionbank definition data.
-- 
-- TODO, these tables no longer belong to the quiz module.
-- They should be moved elsewhere when a good home for them
-- is found.
-- --------------------------------------------------------

CREATE TABLE prefix_question_categories (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  info text NOT NULL default '',
  publish integer NOT NULL default '0',
  stamp varchar(255) NOT NULL default '',
  parent integer NOT NULL default '0',
  sortorder integer NOT NULL default '999'
);
CREATE INDEX prefix_question_categories_course_idx ON prefix_question_categories (course);

CREATE TABLE prefix_question (
  id SERIAL PRIMARY KEY,
  category integer NOT NULL default '0',
  parent integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  questiontext text NOT NULL default '',
  questiontextformat integer NOT NULL default '0',
  image varchar(255) NOT NULL default '',
  commentarytext text NOT NULL default '',
  defaultgrade integer NOT NULL default '1',
  penalty real NOT NULL default '0.1',
  qtype varchar(20) NOT NULL default '0',
  length integer NOT NULL DEFAULT '1',
  stamp varchar(255) NOT NULL default '',
  version varchar(255) NOT NULL default '',
  hidden integer NOT NULL default '0'
);
CREATE INDEX prefix_question_category_idx ON prefix_question (category);

CREATE TABLE prefix_question_answers (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  answer text NOT NULL default '',
  fraction real NOT NULL default '0',
  feedback text NOT NULL default ''
);
CREATE INDEX prefix_question_answers_question_idx ON prefix_question_answers (question);

CREATE TABLE prefix_question_numerical_units (
    id SERIAL8 PRIMARY KEY,
    question INT8  NOT NULL default '0',
    multiplier decimal(40,20) NOT NULL default '1.00000000000000000000',
    unit varchar(50) NOT NULL default ''
);
CREATE INDEX prefix_question_numerical_units_question_idx ON prefix_question_numerical_units (question);

CREATE TABLE prefix_question_datasets (
    id SERIAL8 PRIMARY KEY,
    question INT8  NOT NULL default '0',
    datasetdefinition INT8  NOT NULL default '0'
);
CREATE INDEX prefix_question_datasets_question_datasetdefinition_idx  ON prefix_question_datasets (question,datasetdefinition);

CREATE TABLE prefix_question_dataset_definitions (
    id SERIAL8 PRIMARY KEY,
    category INT8  NOT NULL default '0',
    name varchar(255) NOT NULL default '',
    type INT8 NOT NULL default '0',
    options varchar(255) NOT NULL default '',
    itemcount INT8  NOT NULL default '0'
);
CREATE INDEX prefix_question_dataset_definitions_category_idx ON prefix_question_dataset_definitions (category);

CREATE TABLE prefix_question_dataset_items (
    id SERIAL8 PRIMARY KEY,
    definition INT8  NOT NULL default '0',
    number INT8  NOT NULL default '0',
    value varchar(255) NOT NULL default ''
);
CREATE INDEX prefix_question_dataset_items_definition_idx  ON prefix_question_dataset_items (definition);

-- --------------------------------------------------------
-- Questionbank runtime data.
-- See above TODO.
-- --------------------------------------------------------

CREATE TABLE prefix_question_attempts (
  id SERIAL PRIMARY KEY,
  modulename varchar(20) NOT NULL default 'quiz'
);

CREATE TABLE prefix_question_sessions (
  id SERIAL PRIMARY KEY,
  attemptid integer NOT NULL default '0',
  questionid integer NOT NULL default '0',
  newest integer NOT NULL default '0',
  newgraded integer NOT NULL default '0',
  sumpenalty real NOT NULL default '0',
  manualcomment text NOT NULL default ''
);
CREATE UNIQUE INDEX prefix_question_sessions_attempt_idx ON prefix_question_sessions (attemptid,questionid);

CREATE TABLE prefix_question_states (
  id SERIAL PRIMARY KEY,
  attempt integer NOT NULL default '0',
  question integer NOT NULL default '0',
  originalquestion integer NOT NULL default '0',
  seq_number integer NOT NULL default '0',
  answer text NOT NULL default '',
  timestamp integer NOT NULL default '0',
  event integer NOT NULL default '0',
  grade real NOT NULL default '0',
  raw_grade real NOT NULL default '0',
  penalty real NOT NULL default '0'
);
CREATE INDEX prefix_question_states_attempt_idx ON prefix_question_states (attempt);
CREATE INDEX prefix_question_states_question_idx ON prefix_question_states (question);

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
