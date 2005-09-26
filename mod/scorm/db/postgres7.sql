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
  launch integer NOT NULL default '0',
  summary text NOT NULL default '',
  hidebrowse integer NOT NULL default '0',
  hidetoc integer NOT NULL default '0',
  hidenav integer NOT NULL default '0',
  auto integer NOT NULL default '0',
  popup integer NOT NULL default '0',
  options varchar(255) NOT NULL default '',
  width integer NOT NULL default '800',
  height integer NOT NULL default '600',
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
  parameters varchar(255) NOT NULL default '',
  scormtype varchar(5) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  prerequisites varchar(200) NOT NULL default '',
  maxtimeallowed varchar(19) NOT NULL default '',
  timelimitaction varchar(19) NOT NULL default '',
  datafromlms varchar(255) NOT NULL default '',
  masteryscore varchar(200) NOT NULL default '',
  next integer NOT NULL default '0',
  previous integer NOT NULL default '0'
);

CREATE INDEX prefix_scorm_scoes_scorm_idx ON prefix_scorm_scoes (scorm);

CREATE TABLE prefix_scorm_scoes_track (
  id SERIAL PRIMARY KEY,
  userid integer NOT NULL default '0',
  scormid integer NOT NULL default '0',
  scoid integer NOT NULL default '0',
  element varchar(255) NOT NULL default '',
  value text NOT NULL default '',
  timemodified integer NOT NULL default '0',
  UNIQUE (userid, scormid, scoid, element)
);

CREATE INDEX prefix_scorm_scoes_track_user_idx ON prefix_scorm_scoes_track (userid);
CREATE INDEX prefix_scorm_scoes_track_scorm_idx ON prefix_scorm_scoes_track (scormid);
CREATE INDEX prefix_scorm_scoes_track_sco_idx ON prefix_scorm_scoes_track (scoid);
CREATE INDEX prefix_scorm_scoes_track_element_idx ON prefix_scorm_scoes_track (element);

#
# Dumping data for table `log_display`
#

INSERT INTO prefix_log_display VALUES ('resource', 'view', 'resource', 'name');
INSERT INTO prefix_log_display VALUES ('resource', 'review', 'resource', 'name');
INSERT INTO prefix_log_display VALUES ('resource', 'update', 'resource', 'name');
INSERT INTO prefix_log_display VALUES ('resource', 'add', 'resource', 'name');
