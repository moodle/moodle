#
# Table structure for table `hotpot`
#

CREATE TABLE prefix_hotpot (
  id SERIAL PRIMARY KEY,
  course       INT4 NOT NULL default '0',
  name         VARCHAR(255) NOT NULL default '',
  summary      TEXT,
  timeopen     INT4 NOT NULL default '0',
  timeclose    INT4 NOT NULL default '0',
  location     INT2 NOT NULL default '0',
  reference    VARCHAR(255) NOT NULL default '',
  grade        INT4 NOT NULL default '0',
  grademethod  INT2 NOT NULL default '1',
  attempts     INT2 NOT NULL default '0',
  review       INT2 NOT NULL default '0',
  navigation   INT2 NOT NULL default '1',
  outputformat INT2 NOT NULL default '1',
  shownextquiz INT2 NOT NULL default '0',
  forceplugins INT2 NOT NULL default '0',
  password     VARCHAR(255) NOT NULL default '',
  subnet       VARCHAR(255) NOT NULL default '',
  timecreated  INT4 NOT NULL default '0',
  timemodified INT4 NOT NULL default '0'
);

#
# Table structure for table `hotpot_attempts`
#

CREATE TABLE prefix_hotpot_attempts (
  id SERIAL PRIMARY KEY,
  hotpot     INT4 NOT NULL default '0',
  userid     INT4 NOT NULL default '0',
  attempt    INT2 NOT NULL default '0',
  score      INT2,
  penalties  INT2,
  details    TEXT,
  starttime  INT4,
  endtime    INT4,
  timestart  INT4 NOT NULL default '0',
  timefinish INT4 NOT NULL default '0'
);
CREATE INDEX prefix_hotpot_attempts_hotpot_idx ON prefix_hotpot_attempts (hotpot);
CREATE INDEX prefix_hotpot_attempts_userid_idx ON prefix_hotpot_attempts (userid);


#
# Table structure for table `hotpot_questions`
#

CREATE TABLE prefix_hotpot_questions (
  id SERIAL PRIMARY KEY,
  hotpot INT4 NOT NULL default '0',
  name   VARCHAR(255) NOT NULL default '',
  type   INT2 NOT NULL default '0',
  text   TEXT
);
CREATE INDEX prefix_hotpot_questions_hotpot_idx ON prefix_hotpot_questions (hotpot);

#
# Table structure for table `hotpot_responses`
#

CREATE TABLE prefix_hotpot_responses (
  id SERIAL PRIMARY KEY,
  attempt   INT4 NOT NULL default '0',
  question  INT4 NOT NULL default '0',
  score     INT2,
  weighting INT2,
  correct   VARCHAR(255),
  wrong     VARCHAR(255),
  ignored   VARCHAR(255),
  hints     INT2,
  clues     INT2,
  checks    INT2
);
CREATE INDEX prefix_hotpot_responses_attempt_idx ON prefix_hotpot_responses (attempt);
CREATE INDEX prefix_hotpot_responses_question_idx ON prefix_hotpot_responses (question);

#
# Table structure for table `hotpot_strings`
#

CREATE TABLE prefix_hotpot_strings (
  id SERIAL PRIMARY KEY,
  string TEXT NOT NULL
);
    