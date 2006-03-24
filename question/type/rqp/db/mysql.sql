-- 
-- Table structure for table `prefix_question_rqp`
-- 

CREATE TABLE prefix_question_rqp (
  id int(10) unsigned NOT NULL auto_increment,
  question int(10) unsigned NOT NULL default '0',
  type int(10) unsigned NOT NULL default '0',
  source longblob NOT NULL default '',
  format varchar(255) NOT NULL default '',
  flags tinyint(3) unsigned NOT NULL default '0',
  maxscore int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (id),
  KEY question (question)
) TYPE=MyISAM COMMENT='Options for RQP questions';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_question_rqp_servers`
-- 

CREATE TABLE prefix_question_rqp_servers (
  id int(10) unsigned NOT NULL auto_increment,
  typeid int(10) unsigned NOT NULL default '0',
  url varchar(255) NOT NULL default '',
  can_render tinyint(2) unsigned NOT NULL default '0',
  can_author tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='Information about RQP servers';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_question_rqp_states`
-- 

CREATE TABLE prefix_question_rqp_states (
  id int(10) unsigned NOT NULL auto_increment,
  stateid int(10) unsigned NOT NULL default '0',
  responses text NOT NULL default '',
  persistent_data text NOT NULL default '',
  template_vars text NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='RQP question type specific state information';

-- --------------------------------------------------------

-- 
-- Table structure for table `prefix_question_rqp_types`
-- 

CREATE TABLE prefix_question_rqp_types (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY name (name)
) TYPE=MyISAM COMMENT='RQP question types';

-- --------------------------------------------------------