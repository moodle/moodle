#
# Table structure for table `prefix_attendance`
#

CREATE TABLE prefix_attendance (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  course int(10) NOT NULL default '0',
  day int(10) unsigned NOT NULL default '0',
  hours tinyint(1) NOT NULL default '0',
  roll tinyint(1) NOT NULL default '0',
  notes varchar(64) NOT NULL default '',
  timemodified int(10) unsigned NOT NULL default '0',
  dynsection tinyint(1) NOT NULL default '0',
  edited tinyint(1) NOT NULL default '0',
  autoattend tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Table structure for table `prefix_attendance_roll`
#

CREATE TABLE prefix_attendance_roll (
  id int(11) NOT NULL auto_increment,
  dayid int(10) unsigned NOT NULL default '0',
  userid int(11) NOT NULL default '0',
  hour tinyint(1) unsigned NOT NULL default '0',
  status int(11) NOT NULL default '0',
  notes varchar(64) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
