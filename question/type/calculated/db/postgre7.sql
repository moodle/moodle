
# --------------------------------------------------------
#
# Table structure for table prefix_question_calculated
#

CREATE TABLE prefix_question_calculated (
    id SERIAL8 PRIMARY KEY,
    question INT8  NOT NULL default '0',
    answer INT8  NOT NULL default '0',
    tolerance varchar(20) NOT NULL default '0.0',
    tolerancetype INT8 NOT NULL default '1',
    correctanswerlength INT8 NOT NULL default '2',
    correctanswerformat INT8 NOT NULL default '2'
);

CREATE INDEX prefix_question_calculated_question_idx ON prefix_question_calculated (question);
CREATE INDEX prefix_question_calculated_answer_idx ON prefix_question_calculated (answer);

