#
# Table structure for table workshop
#
BEGIN;
CREATE TABLE prefix_workshop (
  id SERIAL PRIMARY KEY,
  course INT8  NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  description text NOT NULL default '',
  nelements INT  NOT NULL default '10',
  phase INT2  NOT NULL default '0',
  format INT2  NOT NULL default '0',
  gradingstrategy INT2  NOT NULL default '1',
  resubmit INT2  NOT NULL default '0',
  agreeassessments INT2  NOT NULL default '0',
  hidegrades INT2  NOT NULL default '0',
  anonymous INT2  NOT NULL default '0',
  includeself INT2  NOT NULL default '0',
  maxbytes INT8  NOT NULL default '100000',
  deadline INT8  NOT NULL default '0',
  grade INT8 NOT NULL default '0',
  ntassessments INT  NOT NULL default '0',
  nsassessments INT  NOT NULL default '0',
  overallocation INT  NOT NULL default '0',
  timemodified INT8  NOT NULL default '0',
  mergegrades INT  NOT NULL default '0',
  teacherweight INT  NOT NULL default '5',
  peerweight INT  NOT NULL default '5',
  includeteachersgrade INT  NOT NULL default '0',
  biasweight INT  NOT NULL default '5',
  reliabilityweight INT  NOT NULL default '5',
  gradingweight INT  NOT NULL default '5',
  timeagreed INT8 NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table workshop_submissions
#

CREATE TABLE prefix_workshop_submissions (
  id SERIAL PRIMARY KEY,
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
  id SERIAL PRIMARY KEY,
  workshopid INT8  NOT NULL default '0',
  submissionid INT8  NOT NULL default '0',
  userid INT8  NOT NULL default '0',
  timecreated INT8  NOT NULL default '0',
  timegraded INT8  NOT NULL default '0',
  grade float NOT NULL default '0',
  gradinggrade INT NOT NULL default '0',
  mailed INT2  NOT NULL default '0',
  resubmission INT2  NOT NULL default '0',
  generalcomment text NOT NULL default '',
  teachercomment text NOT NULL default ''
  );
# --------------------------------------------------------

#
# Table structure for table workshop_elements
#

CREATE TABLE prefix_workshop_elements (
  id SERIAL PRIMARY KEY,
  workshopid INT8  NOT NULL default '0',
  elementno INT  NOT NULL default '0',
  description text NOT NULL default '',
  scale INT  NOT NULL default '0',
  maxscore INT  NOT NULL default '1',
  weight float NOT NULL default '1.0'
);
# --------------------------------------------------------

#
# Table structure for table workshop_grades
#

CREATE TABLE prefix_workshop_grades (
  id SERIAL PRIMARY KEY,
  workshopid INT8  NOT NULL default '0', 
  assessmentid INT8  NOT NULL default '0',
  elementno INT8  NOT NULL default '0',
  feedback text NOT NULL default '',
  grade INT NOT NULL default '0'
);
# --------------------------------------------------------
CREATE TABLE prefix_workshop_comments (
  id SERIAL PRIMARY KEY,
  workshopid int8 NOT NULL default '0',
  assessmentid int8  NOT NULL default '0',
  userid int8 NOT NULL default '0',
  timecreated int8  NOT NULL default '0',
  mailed int2  NOT NULL default '0',
  comments text NOT NULL default ''
);


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
