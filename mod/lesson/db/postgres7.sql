# This file contains a complete database schema for all the 
# tables used by the mlesson module, written in SQL

# It may also contain INSERT statements for particular data 
# that may be used, especially new entries in the table log_display

CREATE TABLE prefix_lesson (
  id SERIAL8 PRIMARY KEY,
  course INT8  NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  grade INT NOT NULL default '0',
  usemaxgrade INT NOT NULL default '0',
  maxanswers INT  NOT NULL default '4',
  maxattempts INT NOT NULL default '0',
  nextpagedefault INT NOT NULL default '0',
  maxpages INT NOT NULL default '0',
  retake INT  NOT NULL default '1',
  available INT8  NOT NULL default '0',
  deadline INT8  NOT NULL default '0',
  timemodified INT8  NOT NULL default '0'
);
# --------------------------------------------------------

CREATE TABLE prefix_lesson_pages (
  id SERIAL8 PRIMARY KEY,
  lessonid INT8  NOT NULL default '0',
  prevpageid INT8  NOT NULL default '0',
  nextpageid INT8  NOT NULL default '0',
  qtype INT8  NOT NULL default '0',
  qoption INT8  NOT NULL default '0',
  timecreated INT8  NOT NULL default '0',
  timemodified INT8  NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  contents text NOT NULL default ''
); 
# COMMENT='Defines lesson_pages';
# --------------------------------------------------------

CREATE TABLE prefix_lesson_answers (
  id SERIAL8 PRIMARY KEY,
  lessonid INT8  NOT NULL default '0',
  pageid INT8  NOT NULL default '0',
  jumpto INT8 NOT NULL default '0',
  grade INT8  NOT NULL default '0',
  flags INT8  NOT NULL default '0',
  timecreated INT8  NOT NULL default '0',
  timemodified INT8  NOT NULL default '0',
  answer text NOT NULL default '',
  response text NOT NULL default ''
);
# COMMENT='Defines lesson_answers';
# --------------------------------------------------------

CREATE TABLE prefix_lesson_attempts (
  id SERIAL8 PRIMARY KEY,
  lessonid INT8  NOT NULL default '0',
  pageid INT8  NOT NULL default '0',
  userid INT8  NOT NULL default '0',
  answerid INT8  NOT NULL default '0',
  retry INT  NOT NULL default '0',
  correct INT8  NOT NULL default '0',
  timeseen INT8  NOT NULL default '0'
); 
#COMMENT='Defines lesson_attempts';
# --------------------------------------------------------

CREATE TABLE prefix_lesson_grades (
  id SERIAL8 PRIMARY KEY,
  lessonid INT8  NOT NULL default '0',
  userid INT8  NOT NULL default '0',
  grade INT  NOT NULL default '0',
  late INT  NOT NULL default '0',
  completed INT8  NOT NULL default '0'
);
# COMMENT='Defines lesson_grades';
# --------------------------------------------------------


INSERT INTO prefix_log_display VALUES ('lesson', 'start', 'lesson', 'name');
INSERT INTO prefix_log_display VALUES ('lesson', 'end', 'lesson', 'name');
INSERT INTO prefix_log_display VALUES ('lesson', 'view', 'lesson_pages', 'title');
