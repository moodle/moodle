#
# Table structure for table workshop
#
BEGIN;
CREATE TABLE prefix_workshop (
  id SERIAL8 PRIMARY KEY,
  course INT8  NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  description text NOT NULL,
  nelements INT  NOT NULL default '10',
  phase INT2  NOT NULL default '0',
  format INT2  NOT NULL default '0',
  gradingstrategy INT2  NOT NULL default '1',
  resubmit INT2  NOT NULL default '0',
  graded INT2  NOT NULL default '1',
  showgrades INT2  NOT NULL default '0',
  anonymous INT2  NOT NULL default '0',
  includeself INT2  NOT NULL default '0',
  maxbytes INT8  NOT NULL default '100000',
  deadline INT8  NOT NULL default '0',
  grade INT8 NOT NULL default '0',
  ntassessments INT  NOT NULL default '0',
  nsassessments INT  NOT NULL default '0',
  timemodified INT8  NOT NULL default '0',
  mergegrades INT  NOT NULL default '0',
  teacherweight INT  NOT NULL default '5',
  peerweight INT  NOT NULL default '5',
  includeteachersgrade INT  NOT NULL default '0',
  biasweight INT  NOT NULL default '5',
  reliabilityweight INT  NOT NULL default '5',
  gradingweight INT  NOT NULL default '5'
);
# --------------------------------------------------------

#
# Table structure for table workshop_submissions
#

CREATE TABLE prefix_workshop_submissions (
  id SERIAL8 PRIMARY KEY,
  workshopid INT8  NOT NULL default '0',
  userid INT8  NOT NULL default '0',
  title varchar(100) NOT NULL default '',
  timecreated INT8  NOT NULL default '0',
  mailed INT2  NOT NULL default '0',
  teachergrade INT  NOT NULL default '0',
  peergrade INT  NOT NULL default '0',
  biasgrade INT  NOT NULL default '0',
  reliabilitygrade INT  NOT NULL default '0',
  gradinggrade INT  NOT NULL default '0',
  finalgrade INT  NOT NULL default '0'
);
CREATE INDEX prefix_workshop_submissions_title_idx on prefix_workshop_submissions (title);
# --------------------------------------------------------

#
# Table structure for table workshop_assessments
#

CREATE TABLE prefix_workshop_assessments (
  id SERIAL8 PRIMARY KEY,
  workshopid INT8  NOT NULL default '0',
  submissionid INT8  NOT NULL default '0',
  userid INT8  NOT NULL default '0',
  timecreated INT8  NOT NULL default '0',
  timegraded INT8  NOT NULL default '0',
  grade float NOT NULL default '0',
  gradinggrade INT NOT NULL default '0',
  mailed INT2  NOT NULL default '0',
  generalcomment text NOT NULL,
  teachercomment text NOT NULL
  );
# --------------------------------------------------------

#
# Table structure for table workshop_elements
#

CREATE TABLE prefix_workshop_elements (
  id SERIAL8 PRIMARY KEY,
  workshopid INT8  NOT NULL default '0',
  elementno INT  NOT NULL default '0',
  description text NOT NULL,
  scale INT  NOT NULL default '0',
  maxscore INT  NOT NULL default '1',
  weight float NOT NULL default '1.0'
);
# --------------------------------------------------------

#
# Table structure for table workshop_grades
#

CREATE TABLE prefix_workshop_grades (
  id SERIAL8 PRIMARY KEY,
  workshopid INT8  NOT NULL default '0', 
  assessmentid INT8  NOT NULL default '0',
  elementno INT8  NOT NULL default '0',
  feedback text NOT NULL default '',
  grade INT NOT NULL default '0'
);
# --------------------------------------------------------

INSERT INTO prefix_log_display VALUES ('workshop', 'assess', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'close', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'display grades', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'grade', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'hide grades', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'open', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'submit', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'view', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'update', 'workshop', 'name');
COMMIT;
