#
# Table structure for table `scorm`
#

CREATE TABLE prefix_scorm (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  reference varchar(255) NOT NULL default '',
  maxgrade real NOT NULL default '0',
  grademethod integer NOT NULL default '0',
  datadir varchar(255) NOT NULL default '',
  launch integer NOT NULL default '0',
  summary text NOT NULL default '',
  auto integer NOT NULL default '0',
  popup varchar(255) NOT NULL default '',
  timemodified integer NOT NULL default '0'
);

CREATE INDEX prefix_scorm_course_idx ON prefix_scorm (course);

CREATE TABLE prefix_scorm_scoes (
  id SERIAL PRIMARY KEY,
  scorm integer NOT NULL default '0',
  manifest varchar(255) NOT NULL default '',
  organization varchar(255) NOT NULL default '',
  parent varchar(255) NOT NULL default '',
  identifier varchar(255) NOT NULL default '',
  launch varchar(255) NOT NULL default '',
  type varchar(5) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  datafromlms text NOT NULL default '',
  next integer NOT NULL default '0',
  previous integer NOT NULL default '0'
);

CREATE INDEX prefix_scorm_scoes_scorm_idx ON prefix_scorm_scoes (scorm);

CREATE TABLE prefix_scorm_sco_users (
  id SERIAL PRIMARY KEY,
  userid integer NOT NULL default '0',
  scormid integer NOT NULL default '0',
  scoid integer NOT NULL default '0',
  cmi_core_lesson_location varchar(255) NOT NULL default '',
  cmi_core_lesson_status varchar(30) NOT NULL default '',
  cmi_core_exit varchar(30) NOT NULL default '',
  cmi_core_total_time varchar(13) NOT NULL default '00:00:00',
  cmi_core_session_time varchar(13) NOT NULL default '00:00:00',
  cmi_core_score_raw real NOT NULL default '0',
  cmi_suspend_data text NOT NULL default ''
);

CREATE INDEX prefix_scorm_sco_users_userid_idx ON  prefix_scorm_sco_users (userid);
CREATE INDEX prefix_scorm_sco_users_scormid_idx ON  prefix_scorm_sco_users (scormid);
CREATE INDEX prefix_scorm_sco_users_scoid_idx ON  prefix_scorm_sco_users (scoid);

#
# Dumping data for table `log_display`
#

INSERT INTO prefix_log_display VALUES ('resource', 'view', 'resource', 'name');
INSERT INTO prefix_log_display VALUES ('resource', 'update', 'resource', 'name');
INSERT INTO prefix_log_display VALUES ('resource', 'add', 'resource', 'name');
