#
# Table structure for table `prefix_attendance`
#

CREATE TABLE prefix_attendance (
  id SERIAL,
  name varchar(255) NOT NULL default '',
  course int4 NOT NULL default '0',
  day int4 NOT NULL default '0',
  hours int2 NOT NULL default '0',
  roll int2 NOT NULL default '0',
  notes varchar(64) NOT NULL default '',
  timemodified int4 NOT NULL default '0',
  dynsection int2 NOT NULL default '0',
  edited int2 NOT NULL default '0',
  autoattend int2 NOT NULL default '0',
  PRIMARY KEY(id)
);

#
# Table structure for table `prefix_attendance_roll`
#

CREATE TABLE prefix_attendance_roll (
  id SERIAL,
  dayid int4 NOT NULL default '0',
  userid int4 NOT NULL default '0',
  hour int2 NOT NULL default '0',
  status int4 NOT NULL default '0',
  notes varchar(64) NOT NULL default '',
  PRIMARY KEY  (id)
);
