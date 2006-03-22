
# --------------------------------------------------------

#
# Table structure for table prefix_question_multianswer
#

CREATE TABLE prefix_question_multianswer (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  sequence text NOT NULL default ''
);

CREATE INDEX prefix_question_multianswer_question_idx ON prefix_question_multianswer (question);
