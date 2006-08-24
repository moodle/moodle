
# --------------------------------------------------------

#
# Table structure for table prefix_question_multichoice
#

CREATE TABLE prefix_question_multichoice (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  layout integer NOT NULL default '0',
  answers varchar(255) NOT NULL default '',
  single integer NOT NULL default '0',
  shuffleanswers integer NOT NULL default '1',
  correctfeedback text NOT NULL default '',
  partiallycorrectfeedback text NOT NULL default '',
  incorrectfeedback text NOT NULL default ''
);

CREATE INDEX prefix_question_multichoice_question_idx ON prefix_question_multichoice (question);

