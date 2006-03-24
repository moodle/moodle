
# --------------------------------------------------------
#
# Table structure for table prefix_question_truefalse
#

CREATE TABLE prefix_question_truefalse (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  trueanswer integer NOT NULL default '0',
  falseanswer integer NOT NULL default '0'
);
CREATE INDEX prefix_question_truefalse_question_idx ON prefix_question_truefalse (question);
