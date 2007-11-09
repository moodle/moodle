
# --------------------------------------------------------

#
# Table structure for table prefix_question_rqp
#

CREATE TABLE prefix_question_rqp (
  id SERIAL PRIMARY KEY,
  question integer NOT NULL default '0',
  type integer NOT NULL default '0',
  source text NOT NULL,
  format varchar(255) NOT NULL default '',
  flags integer NOT NULL default '0',
  maxscore integer NOT NULL default '1'
);

CREATE INDEX prefix_question_rqp_question_idx ON prefix_question_rqp (question);

# --------------------------------------------------------

#
# Table structure for table prefix_question_rqp_servers
#

CREATE TABLE prefix_question_rqp_servers (
  id SERIAL PRIMARY KEY,
  typeid integer NOT NULL default '0',
  url varchar(255) NOT NULL default '',
  can_render INT4 NOT NULL default '0',
  can_author INT4 NOT NULL default '0'
);

# --------------------------------------------------------

#
# Table structure for table prefix_question_rqp_states
#

CREATE TABLE prefix_question_rqp_states (
  id SERIAL PRIMARY KEY,
  stateid integer NOT NULL default '0',
  responses text NOT NULL,
  persistent_data text NOT NULL,
  template_vars text NOT NULL
);

# --------------------------------------------------------

#
# Table structure for table prefix_question_rqp_type
#

CREATE TABLE prefix_question_rqp_types (
  id SERIAL PRIMARY KEY,
  name varchar(255) NOT NULL default '',
  rendering_server varchar(255) NOT NULL default '',
  cloning_server varchar(255) NOT NULL default '',
  flags integer NOT NULL default '0'
);

CREATE UNIQUE INDEX prefix_question_rqp_types_name_uk ON prefix_question_rqp_types (name);

