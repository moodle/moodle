#
# Table structure for table `scorm`
#
 
CREATE TABLE prefix_scorm (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  reference varchar(255) NOT NULL default '',
  maxgrade float(3) NOT NULL default '0',
  grademethod tinyint(2) NOT NULL default '0',
  datadir varchar(255) NOT NULL default '',
  launch int(10) unsigned NOT NULL default 0,
  summary text NOT NULL,
  auto tinyint(1) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id),
  KEY course (course)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_scoes (
  id int(10) unsigned NOT NULL auto_increment,
  scorm int(10) unsigned NOT NULL default '0',
  manifest varchar(255) NOT NULL default '',
  organization varchar(255) NOT NULL default '',
  parent varchar(255) NOT NULL default '',
  identifier varchar(255) NOT NULL default '',
  launch varchar(255) NOT NULL default '',
  scormtype set('sco','asset') NOT NULL default '',
  title varchar(255) NOT NULL default '',
  prerequisites varchar(200) NOT NULL default '',
  maxtimeallowed set('exit,message','exit,no message','continue,message','continue,no message'),
  timelimitaction varchar(19) NOT NULL default '',
  datafromlms varchar(255) NOT NULL default '',
  masteryscore varchar(200) NOT NULL default '',
  next tinyint(1) unsigned NOT NULL default '0',
  previous tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY id (id),
  KEY scorm (scorm)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_scoes_track (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  scormid int(10) NOT NULL default '0',
  scoid int(10) unsigned NOT NULL default '0',
  element varchar(255) NOT NULL default '',
  value longtext NOT NULL default '',
  PRIMARY KEY  (userid, scormid, scoid, element),
  UNIQUE (userid, scormid, scoid, element),
  KEY userdata (userid, scormid, scoid)
) TYPE=MyISAM;

#
# Dumping data for table log_display
#

INSERT INTO prefix_log_display VALUES ('scorm', 'view', 'scorm', 'name');
INSERT INTO prefix_log_display VALUES ('scorm', 'review', 'scorm', 'name');
INSERT INTO prefix_log_display VALUES ('scorm', 'update', 'scorm', 'name');
INSERT INTO prefix_log_display VALUES ('scorm', 'add', 'scorm', 'name');