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

CREATE INDEX prefix_attendance_course_idx ON prefix_attendance (course);

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

CREATE INDEX prefix_attendance_roll_dayid_idx ON prefix_attendance_roll (dayid);
CREATE INDEX prefix_attendance_roll_userid_idx ON prefix_attendance_roll (userid);

INSERT INTO prefix_log_display VALUES ('attendance', 'view', 'attendance', 'name');
INSERT INTO prefix_log_display VALUES ('attendance', 'view', 'attendance', 'name');
INSERT INTO prefix_log_display VALUES ('attendance', 'viewall', 'attendance', 'name');

