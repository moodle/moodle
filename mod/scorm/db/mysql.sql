#
# Table structure for table `scorm`
#
 
CREATE TABLE prefix_scorm (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  reference varchar(255) NOT NULL default '',
  datadir varchar(255) NOT NULL default '',
  launch int(10) unsigned NOT NULL default 0,
  summary text NOT NULL,
  auto tinyint(1) unsigned NOT NULL default '0',
  popup varchar(255) NOT NULL default '',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_scoes (
  id int(10) unsigned NOT NULL auto_increment,
  scorm int(10) unsigned NOT NULL default '0',
  parent varchar(255) NOT NULL default '',
  identifier varchar(255) NOT NULL default '',
  launch varchar(255) NOT NULL default '',
  type varchar(5) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  datafromlms longtext,
  next tinyint(1) unsigned NOT NULL default '0',
  previous tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;

CREATE TABLE prefix_scorm_sco_users (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  scormid int(10) NOT NULL default '0',
  scoid int(10) unsigned NOT NULL default '0',
  cmi_core_lesson_location varchar(255) NOT NULL default '',
  cmi_core_lesson_status varchar(30) NOT NULL default '',
  cmi_core_exit varchar(30) NOT NULL default '',
  cmi_core_total_time varchar(13) NOT NULL default '00:00:00',
  cmi_core_session_time varchar(13) NOT NULL default '00:00:00',
  cmi_core_score_raw float(3) NOT NULL default '0',
  cmi_suspend_data longtext,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table log_display
#

INSERT INTO prefix_log_display VALUES ('scorm', 'view', 'scorm', 'name');
INSERT INTO prefix_log_display VALUES ('scorm', 'update', 'scorm', 'name');
INSERT INTO prefix_log_display VALUES ('scorm', 'add', 'scorm', 'name');