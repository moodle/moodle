
# --------------------------------------------------------

#
# Table structure for table prefix_question_randomsamatch
#

CREATE TABLE prefix_question_randomsamatch (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  choose integer NOT NULL default '4',
);

CREATE INDEX prefix_question_randomsamatch_question_idx ON prefix_question_randomsamatch (question);
