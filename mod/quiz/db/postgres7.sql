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
# Table structure for table quiz
#

CREATE TABLE prefix_quiz (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  intro text NOT NULL default '',
  timeopen integer NOT NULL default '0',
  timeclose integer NOT NULL default '0',
  attempts integer NOT NULL default '0',
  feedback integer NOT NULL default '0',
  correctanswers integer NOT NULL default '1',
  grademethod integer NOT NULL default '1',
  questions text NOT NULL default '',
  sumgrades integer NOT NULL default '0',
  grade integer NOT NULL default '0',
  timecreated integer NOT NULL default '0',
  timemodified integer NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table quiz_answers
#

CREATE TABLE prefix_quiz_answers (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  answer varchar(255) NOT NULL default '',
  fraction varchar(10) NOT NULL default '0.0',
  feedback text NOT NULL default ''
);
# --------------------------------------------------------

#
# Table structure for table quiz_attempts
#

CREATE TABLE prefix_quiz_attempts (
  id SERIAL PRIMARY KEY,
  quiz integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  attempt integer NOT NULL default '0',
  sumgrades varchar(10) NOT NULL default '0.0',
  timestart integer NOT NULL default '0',
  timefinish integer NOT NULL default '0',
  timemodified integer NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table quiz_categories
#

CREATE TABLE prefix_quiz_categories (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  info text NOT NULL default '',
  publish integer NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table quiz_grades
#

CREATE TABLE prefix_quiz_grades (
  id SERIAL PRIMARY KEY,
  quiz integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  grade varchar(10) NOT NULL default '0.0',
  timemodified integer NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table quiz_multichoice
#

CREATE TABLE prefix_quiz_multichoice (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  layout integer NOT NULL default '0',
  answers varchar(255) NOT NULL default '',
  single integer NOT NULL default '0'
);
# --------------------------------------------------------
CREATE INDEX question_quiz_multichoice_idx ON prefix_quiz_multichoice (question);

#
# Table structure for table quiz_question_grades
#

CREATE TABLE prefix_quiz_question_grades (
  id SERIAL PRIMARY KEY,
  quiz integer NOT NULL default '0',
  question integer NOT NULL default '0',
  grade integer NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table quiz_questions
#

CREATE TABLE prefix_quiz_questions (
  id SERIAL PRIMARY KEY,
  category integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  questiontext text NOT NULL default '',
  image varchar(255) NOT NULL default '',
  type integer NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table quiz_responses
#

CREATE TABLE prefix_quiz_responses (
  id SERIAL PRIMARY KEY,
  attempt integer NOT NULL default '0',
  question integer NOT NULL default '0',
  answer varchar(255) NOT NULL default '',
  grade varchar(10) NOT NULL default '0.0'
);
# --------------------------------------------------------

#
# Table structure for table quiz_shortanswer
#

CREATE TABLE prefix_quiz_shortanswer (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  answers varchar(255) NOT NULL default '',
  usecase integer NOT NULL default '0'
);
# --------------------------------------------------------
CREATE INDEX question_prefix_quiz_shortanswer_idx ON prefix_quiz_shortanswer (question);

#
# Table structure for table quiz_truefalse
#

CREATE TABLE prefix_quiz_truefalse (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  "true" integer NOT NULL default '0',
  "false" integer NOT NULL default '0'
);
CREATE INDEX question_prefix_quiz_truefalse_idx ON prefix_quiz_truefalse (question);


INSERT INTO prefix_log_display VALUES ('quiz', 'view', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'report', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'attempt', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'submit', 'quiz', 'name');

