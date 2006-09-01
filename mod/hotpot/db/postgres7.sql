#
# Table structure for table `hotpot`
#

CREATE TABLE prefix_hotpot (
    id  SERIAL PRIMARY KEY,
    course          INT4 NOT NULL default '0',
    name            VARCHAR(255) NOT NULL default '',
    summary         TEXT,
    timeopen        INT4 NOT NULL default '0',
    timeclose       INT4 NOT NULL default '0',
    location        INT2 NOT NULL default '0',
    reference       VARCHAR(255) NOT NULL default '',
    outputformat    INT2 NOT NULL default '1',
    navigation      INT2 NOT NULL default '1',
    studentfeedback INT2 NOT NULL default '0',
    studentfeedbackurl VARCHAR(255) NOT NULL default '',
    forceplugins    INT2 NOT NULL default '0',
    shownextquiz    INT2 NOT NULL default '0',
    review          INT2 NOT NULL default '0',
    grade           INT4 NOT NULL default '0',
    grademethod     INT2 NOT NULL default '1',
    attempts        INT2 NOT NULL default '0',
    password        VARCHAR(255) NOT NULL default '',
    subnet          VARCHAR(255) NOT NULL default '',
    clickreporting  INT2 NOT NULL default '0',
    timecreated     INT4 NOT NULL default '0',
    timemodified    INT4 NOT NULL default '0'
);
COMMENT ON TABLE prefix_hotpot IS 'details about Hot Potatoes quizzes';

#
# Table structure for table `hotpot_attempts`
#

CREATE TABLE prefix_hotpot_attempts (
    id  SERIAL PRIMARY KEY,
    hotpot        INT4 NOT NULL default '0',
    userid        INT4 NOT NULL default '0',
    starttime     INT4 NOT NULL default '0',
    endtime       INT4 NOT NULL default '0',
    score         INT2 NOT NULL default '0',
    penalties     INT2 NOT NULL default '0',
    attempt       INT2 NOT NULL default '0',
    timestart     INT4 NOT NULL default '0',
    timefinish    INT4 NOT NULL default '0',
    status        INT2 NOT NULL default '1',
    clickreportid INT4 NOT NULL default '0'
);
COMMENT ON TABLE prefix_hotpot IS 'details about Hot Potatoes quiz attempts';

CREATE INDEX prefix_hotpot_attempts_hotpot_idx ON prefix_hotpot_attempts (hotpot);
CREATE INDEX prefix_hotpot_attempts_userid_idx ON prefix_hotpot_attempts (userid);

#
# Table structure for table `prefix_hotpot_details`
#

CREATE TABLE prefix_hotpot_details (
    id  SERIAL PRIMARY KEY,
    attempt  INT4 NOT NULL default '0',
    details  TEXT NOT NULL default ''
);
COMMENT ON TABLE prefix_hotpot_details IS 'raw details (as XML) of Hot Potatoes quiz attempts';

CREATE INDEX prefix_hotpot_details_attempt_idx ON prefix_hotpot_details (attempt);

#
# Table structure for table `hotpot_questions`
#

CREATE TABLE prefix_hotpot_questions (
    id  SERIAL PRIMARY KEY,
    name   TEXT NOT NULL default '',
    type   INT2 NOT NULL default '0',
    text   INT4 NOT NULL default '0',
    hotpot INT4 NOT NULL default '0',
    md5key VARCHAR(32) NOT NULL default ''
);
COMMENT ON TABLE prefix_hotpot_questions IS 'details about questions in Hot Potatoes quiz attempts';

CREATE INDEX prefix_hotpot_questions_hotpot_idx ON prefix_hotpot_questions (hotpot);
CREATE INDEX prefix_hotpot_questions_md5key_idx ON prefix_hotpot_questions (md5key);

#
# Table structure for table `hotpot_responses`
#

CREATE TABLE prefix_hotpot_responses (
    id  SERIAL PRIMARY KEY,
    attempt   INT4 NOT NULL default '0',
    question  INT4 NOT NULL default '0',
    score     INT2 NOT NULL default '0',
    weighting INT2 NOT NULL default '0',
    correct   VARCHAR(255) NOT NULL default '',
    wrong     VARCHAR(255) NOT NULL default '',
    ignored   VARCHAR(255) NOT NULL default '',
    hints     INT2 NOT NULL default '0',
    clues     INT2 NOT NULL default '0',
    checks    INT2 NOT NULL default '0'
);
COMMENT ON TABLE prefix_hotpot_responses IS 'details about responses in Hot Potatoes quiz attempts';

CREATE INDEX prefix_hotpot_responses_attempt_idx ON prefix_hotpot_responses (attempt);
CREATE INDEX prefix_hotpot_responses_question_idx ON prefix_hotpot_responses (question);

#
# Table structure for table `hotpot_strings`
#

CREATE TABLE prefix_hotpot_strings (
    id  SERIAL PRIMARY KEY,
    string TEXT NOT NULL default '',
    md5key VARCHAR(32) NOT NULL default ''
);
COMMENT ON TABLE prefix_hotpot_strings IS 'strings used in Hot Potatoes questions and responses';

CREATE INDEX prefix_hotpot_strings_md5key_idx ON prefix_hotpot_strings (md5key);
