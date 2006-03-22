
# --------------------------------------------------------

#
# Table structure for table prefix_question_numerical
#

CREATE TABLE prefix_question_numerical (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  answer integer NOT NULL default '0',
  tolerance varchar(255) NOT NULL default '0.0'
);

CREATE INDEX prefix_question_numerical_answer_idx ON prefix_question_numerical (answer);
CREATE INDEX prefix_question_numerical_question_idx ON prefix_question_numerical (question);
