#
# Table structure for table `prefix_attendance`
#

CREATE TABLE prefix_attendance (
  id SERIAL8,
  name varchar(255) NOT NULL default '',
  course int8 NOT NULL default '0',
  day int8 NOT NULL default '0',
  hours int2 NOT NULL default '0',
  roll int2 NOT NULL default '0',
  notes varchar(64) NOT NULL default '',
  timemodified int8 NOT NULL default '0',
  dynsection int2 NOT NULL default '0',
  edited int2 NOT NULL default '0',
  autoattend int2 NOT NULL default '0',
  PRIMARY KEY(id)
);

#
# Table structure for table `prefix_attendance_roll`
#

CREATE TABLE prefix_attendance_roll (
  id SERIAL8,
  dayid int4 NOT NULL default '0',
  userid int8 NOT NULL default '0',
  hour int2 NOT NULL default '0',
  status int4 NOT NULL default '0',
  notes varchar(64) NOT NULL default '',
  PRIMARY KEY  (id)
);

INSERT INTO prefix_log_display VALUES ('attendance', 'view', 'attendance', 'name');
INSERT INTO prefix_log_display VALUES ('attendance', 'view', 'attendance', 'name');
INSERT INTO prefix_log_display VALUES ('attendance', 'viewall', 'attendance', 'name');

