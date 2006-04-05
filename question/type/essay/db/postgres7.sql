
# --------------------------------------------------------

#
# Table structure for table prefix_question_essay
#

CREATE TABLE mdl_question_essay (
    id serial NOT NULL,
    question integer NOT NULL DEFAULT 0,
    answer varchar(255) NOT NULL DEFAULT ''
);


# --------------------------------------------------------

#
# Table structure for table prefix_question_essay_states
#

CREATE TABLE mdl_question_essay_states (
    id serial NOT NULL,
    stateid integer NOT NULL DEFAULT 0,
    graded integer NOT NULL DEFAULT 0,
    response text NOT NULL DEFAULT '',
    fraction real NOT NULL DEFAULT 0
);