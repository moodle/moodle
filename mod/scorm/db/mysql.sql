#
# Table structure for table `scorm`
#
 
CREATE TABLE prefix_scorm (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  summary text NOT NULL default '',
  reference varchar(255) NOT NULL default '',
  version varchar(9) NOT NULL default '',
  maxgrade float(3) NOT NULL default '0',
  grademethod tinyint(2) NOT NULL default '0',
  maxattempt int(10) NOT NULL default '1',
  launch int(10) unsigned NOT NULL default '0',
  skipview tinyint(1) unsigned NOT NULL default '1',
  hidebrowse tinyint(1) NOT NULL default '0',
  hideexit tinyint(1) NOT NULL default '0',
  hideabandon tinyint(1) NOT NULL default '0',
  hidetoc tinyint(1) NOT NULL default '0',
  hidenav tinyint(1) NOT NULL default '0',
  auto tinyint(1) unsigned NOT NULL default '0',
  popup tinyint(1) unsigned NOT NULL default '0',
  options varchar(255) NOT NULL default '',
  width int(10) unsigned NOT NULL default '100',
  height int(10) unsigned NOT NULL default '600',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY course (course)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_scoes (
  id int(10) unsigned NOT NULL auto_increment,
  scorm int(10) unsigned NOT NULL default '0',
  manifest varchar(255) NOT NULL default '',
  organization varchar(255) NOT NULL default '',
  parent varchar(255) NOT NULL default '',
  identifier varchar(255) NOT NULL default '',
/*  launch varchar(255) NOT NULL default '', */
  launch int(10) NOT NULL default '0', 
  scormtype varchar(5) NOT NULL default '',
  title varchar(255) NOT NULL default '',
/*  parameters varchar(255) NOT NULL default '',
  prerequisites varchar(200) NOT NULL default '',
  maxtimeallowed varchar(19) NOT NULL default '',
  timelimitaction varchar(19) NOT NULL default '',
  datafromlms varchar(255) NOT NULL default '',
  masteryscore varchar(200) NOT NULL default '',
  next tinyint(1) unsigned NOT NULL default '0',
  previous tinyint(1) unsigned NOT NULL default '0', */
  PRIMARY KEY (id),
  KEY scorm (scorm)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_scoes_data (
  id int(10) unsigned NOT NULL auto_increment,
  scoid int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  value text NOT NULL default '',
  PRIMARY KEY (id),
  KEY scoid (scoid)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_scoes_track (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  scormid int(10) NOT NULL default '0',
  scoid int(10) unsigned NOT NULL default '0',
  attempt int(10) unsigned NOT NULL default '1',
  element varchar(255) NOT NULL default '',
  value longtext NOT NULL default '',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  KEY userid (userid),
  KEY scormid (scormid),
  KEY scoid (scoid),
  KEY element (element),
  UNIQUE track (userid, scormid, scoid, attempt, element)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_sequencing_ruleconditions (
  id int(10) unsigned NOT NULL auto_increment,
  scormid int(10) unsigned NOT NULL default '0',
  scoid int(10) unsigned NOT NULL default '0',
  conditioncombination varchar(3) NOT NULL default 'all',
  ruletype tinyint(2) unsigned NOT NULL default '0',
  action varchar(25) NOT NULL default '',
  PRIMARY KEY (id),
  UNIQUE (scormid, scoid,id),
  KEY scormid (scormid),
  KEY scoid (scoid)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_sequencing_rulecondition (
  id int(10) unsigned NOT NULL auto_increment,
  scormid int(10) unsigned NOT NULL default '0',
  scoid int(10) unsigned NOT NULL default '0',
  ruleconditionsid int(10) unsigned NOT NULL default '0',
  refrencedobjective varchar(255) NOT NULL default '',
  measurethreshold  float(11,4) NOT NULL default '0.0000',
  operator varchar(5) NOT NULL default 'noOp',
  condition varchar(30) NOT NULL default 'always',
  PRIMARY KEY (id),
  UNIQUE (scormid, scoid,id,ruleconditionsid),
  KEY ruleconditionsid (ruleconditionsid),
  KEY scormid (scormid),
  KEY scoid (scoid)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_sequencing_rolluprules (
  id int(10) unsigned NOT NULL auto_increment,
  scormid int(10) unsigned NOT NULL default '0',
  scoid int(10) unsigned NOT NULL default '0',
  rollupobjectivesatisfied  TINYINT(1) unsigned NOT NULL default '1',
  rollupprogresscompletion  TINYINT(1) unsigned NOT NULL default '1',
  objectivemeasureweight  float(11,4) NOT NULL default '1.0000',
  PRIMARY KEY (id),
  KEY scormid (scormid),
  KEY scoid (scoid)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_sequencing_rolluprule (
  id int(10) unsigned NOT NULL auto_increment,
  scormid int(10) unsigned NOT NULL default '0',
  scoid int(10) unsigned NOT NULL default '0',
  rolluprulesid int(10) unsigned NOT NULL default '0',
  childactivityset varchar(15) NOT NULL default '',
  minimumcount int(10) unsigned NOT NULL default '0',
  minimumpercent float(11,4) unsigned NOT NULL default '0.0000',
  conditioncombination varchar(3) NOT NULL default 'all',
  action varchar(15) NOT NULL default '',
  PRIMARY KEY (id),
  UNIQUE (scormid, scoid, rolluprulesid, id),
  KEY scormid (scormid),
  KEY scoid (scoid)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_sequencing_rolluprulecondition (
  id int(10) unsigned NOT NULL auto_increment,
  scormid int(10) unsigned NOT NULL default '0',
  scoid int(10) unsigned NOT NULL default '0',
  rollupruleid int(10) unsigned NOT NULL default '0',
  operator varchar(5) NOT NULL default 'noOp',
  condition varchar(25) NOT NULL default '',
  PRIMARY KEY (id),
  UNIQUE (scormid, scoid, rollupruleid, id),
  KEY scormid (scormid),
  KEY scoid (scoid)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_sequencing_objectives (
  id int(10) unsigned NOT NULL auto_increment,
  scormid int(10) unsigned NOT NULL default '0',
  scoid int(10) unsigned NOT NULL default '0',
  primary tinyint(1) NOT NULL default '0',
  objectiveid int(10) unsigned NOT NULL default '0',
  satisfiedbymeasure tinyint(1) NOT NULL default '1',
  minnormalizedmeasure float(11,4) unsigned NOT NULL default '1.0',
  PRIMARY KEY (id),
  UNIQUE (scormid, scoid, id),
  KEY scormid (scormid),
  KEY scoid (scoid)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_sequencing_objective (
  id int(10) unsigned NOT NULL auto_increment,
  scormid int(10) unsigned NOT NULL default '0',
  scoid int(10) unsigned NOT NULL default '0',
  objectiveid int(10) unsigned NOT NULL default '0',
  targetobjectiveid int(10) unsigned NOT NULL default '0',
  readsatisfiedstatus tinyint(1) NOT NULL default '1',
  readnormalizedmeasure tinyint(1) NOT NULL default '1',
  writesatisfiedstatus tinyint(1) NOT NULL default '0',
  writenormalizedmeasure tinyint(1) NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE (scormid, scoid, id, objectiveid),
  KEY scormid (scormid),
  KEY scoid (scoid)
) TYPE=MyISAM;

#
# Dumping data for table log_display
#

INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('scorm', 'view', 'scorm', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('scorm', 'review', 'scorm', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('scorm', 'update', 'scorm', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('scorm', 'add', 'scorm', 'name');
