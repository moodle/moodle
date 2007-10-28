
# --------------------------------------------------------

#
# Table structure for table prefix_question_regexp
#

CREATE TABLE prefix_question_regexp (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  answers varchar(255) NOT NULL default '',
  usehint integer NULL default '0'
);
CREATE INDEX prefix_question_regexp_question_idx ON prefix_question_regexp (question);
