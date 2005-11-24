#
# Table structure for table `hotpot`
#
CREATE TABLE prefix_hotpot (
  id SERIAL PRIMARY KEY,
  course          INT4 NOT NULL default '0',
  name            VARCHAR(255) NOT NULL default '',
  summary         TEXT,
  timeopen        INT4 NOT NULL default '0',
  timeclose       INT4 NOT NULL default '0',
  location        INT2 NOT NULL default '0',
  reference       VARCHAR(255) NOT NULL default '',
  navigation      INT2 NOT NULL default '1',
  outputformat    INT2 NOT NULL default '1',
  forceplugins    INT2 NOT NULL default '0',
  shownextquiz    INT2 NOT NULL default '0',
  microreporting  INT2 NOT NULL default '0',
  studentfeedback VARCHAR(255) NOT NULL default '0',
  review          INT2 NOT NULL default '0',
  grade           INT4 NOT NULL default '0',
  grademethod     INT2 NOT NULL default '1',
  attempts        INT2 NOT NULL default '0',
  password        VARCHAR(255) NOT NULL default '',
  subnet          VARCHAR(255) NOT NULL default '',
  timecreated     INT4 NOT NULL default '0',
  timemodified    INT4 NOT NULL default '0'
);
COMMENT ON TABLE prefix_hotpot IS 'details about Hot Potatoes quizzes';
#
# Table structure for table `hotpot_attempts`
#
CREATE TABLE prefix_hotpot_attempts (
  id SERIAL PRIMARY KEY,
  hotpot        INT4 NOT NULL default '0',
  userid        INT4 NOT NULL default '0',
  groupid       INT4 NOT NULL default '0',
  attempt       INT2 NOT NULL default '0',
  score         INT2,
  penalties     INT2,
  starttime     INT4,
  endtime       INT4,
  timestart     INT4 NOT NULL default '0',
  timefinish    INT4 NOT NULL default '0',
  status        INT2 NOT NULL default '1',
  microreportid INT4
);
COMMENT ON TABLE prefix_hotpot IS 'details about Hot Potatoes quiz attempts';
CREATE INDEX prefix_hotpot_attempts_hotpot_idx ON prefix_hotpot_attempts (hotpot);
CREATE INDEX prefix_hotpot_attempts_userid_idx ON prefix_hotpot_attempts (userid);
#
# Table structure for table `prefix_hotpot_details`
#
CREATE TABLE prefix_hotpot_details (
  id SERIAL PRIMARY KEY,
  attempt  INT4 NOT NULL default '0',
  details  TEXT
);
COMMENT ON TABLE prefix_hotpot_details IS 'raw details (as XML) of Hot Potatoes quiz attempts';
CREATE INDEX prefix_hotpot_details_attempt_idx ON prefix_hotpot_details (attempt);
#
# Table structure for table `hotpot_questions`
#
CREATE TABLE prefix_hotpot_questions (
  id SERIAL PRIMARY KEY,
  name   TEXT,
  type   INT2 NOT NULL default '0',
  text   INT4 NULL,
  hotpot INT4 NOT NULL default '0'
);
COMMENT ON TABLE prefix_hotpot_questions IS 'details about questions in Hot Potatatoes quiz attempts';
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
COMMENT ON TABLE prefix_hotpot_responses IS 'details about responses in Hot Potatatoes quiz attempts';
CREATE INDEX prefix_hotpot_responses_attempt_idx ON prefix_hotpot_responses (attempt);
CREATE INDEX prefix_hotpot_responses_question_idx ON prefix_hotpot_responses (question);
#
# Table structure for table `hotpot_strings`
#
CREATE TABLE prefix_hotpot_strings (
  id SERIAL PRIMARY KEY,
  string TEXT NOT NULL
);
COMMENT ON TABLE prefix_hotpot_strings IS 'strings used in Hot Potatatoes questions and responses';
