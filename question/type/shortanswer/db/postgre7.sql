
# --------------------------------------------------------

#
# Table structure for table prefix_question_shortanswer
#

CREATE TABLE prefix_question_shortanswer (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  answers varchar(255) NOT NULL default '',
  usecase integer NOT NULL default '0'
);
CREATE INDEX prefix_question_shortanswer_question_idx ON prefix_question_shortanswer (question);
