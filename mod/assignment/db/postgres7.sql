#
# Table structure for table assignment
#

CREATE TABLE prefix_assignment (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  description text NOT NULL default '',
  format integer NOT NULL default '0',
  resubmit integer NOT NULL default '0',
  emailteachers integer NOT NULL default '0',
  type integer NOT NULL default '1',
  maxbytes integer NOT NULL default '100000',
  timedue integer NOT NULL default '0',
  grade integer NOT NULL default '0',
  timemodified integer NOT NULL default '0'
);

CREATE INDEX prefix_assignment_course_idx ON prefix_assignment (course);

# --------------------------------------------------------

#
# Table structure for table assignment_submissions
#

CREATE TABLE prefix_assignment_submissions (
  id SERIAL PRIMARY KEY,
  assignment integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  timecreated integer NOT NULL default '0',
  timemodified integer NOT NULL default '0',
  numfiles integer NOT NULL default '0',
  grade integer NOT NULL default '0',
  comment text NOT NULL default '',
  teacher integer NOT NULL default '0',
  timemarked integer NOT NULL default '0',
  mailed integer NOT NULL default '0'
);

CREATE INDEX prefix_assignment_submissions_assignment_idx ON prefix_assignment_submissions (assignment);
CREATE INDEX prefix_assignment_submissions_userid_idx ON prefix_assignment_submissions (userid);
CREATE INDEX prefix_assignment_submissions_mailed_idx ON prefix_assignment_submissions (mailed);
CREATE INDEX prefix_assignment_submissions_timemarked_idx ON prefix_assignment_submissions (timemarked);


# --------------------------------------------------------


INSERT INTO prefix_log_display VALUES ('assignment', 'view', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'add', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'update', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'view submission', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'upload', 'assignment', 'name');

