
# --------------------------------------------------------

#
# Table structure for table prefix_question_match
#

CREATE TABLE prefix_question_match (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  subquestions varchar(255) NOT NULL default '',
  shuffleanswers integer NOT NULL default '1'
);

CREATE INDEX prefix_question_match_question_idx ON prefix_question_match (question);

# --------------------------------------------------------

#
# Table structure for table prefix_question_match_sub
#

CREATE TABLE prefix_question_match_sub (
  id SERIAL PRIMARY KEY,
  code integer NOT NULL default '0',
  question integer NOT NULL default '0',
  questiontext text NOT NULL default '',
  answertext varchar(255) NOT NULL default ''
);
CREATE INDEX prefix_question_match_sub_question_idx ON prefix_question_match_sub (question);
