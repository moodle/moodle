#
# Table structure for table exercise
#

CREATE TABLE prefix_exercise (
  id SERIAL8 PRIMARY KEY,
  course INT8  NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  nelements INT  NOT NULL default '1',
  phase INT  NOT NULL default '0',
  gradingstrategy INT  NOT NULL default '1',
  usemaximum INT  NOT NULL default '0',
  anonymous INT  NOT NULL default '0',
  maxbytes INT8  NOT NULL default '100000',
  deadline INT8  NOT NULL default '0',
  grade INT NOT NULL default '0',
  timemodified INT8  NOT NULL default '0',
  teacherweight INT  NOT NULL default '5',
  gradingweight INT  NOT NULL default '5',
  showleaguetable INT  NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table exercise_submissions
#

CREATE TABLE prefix_exercise_submissions (
  id SERIAL8 PRIMARY KEY,
  exerciseid INT8  NOT NULL default '0',
  userid INT8  NOT NULL default '0',
  title varchar(100) NOT NULL default '',
  timecreated INT8  NOT NULL default '0',
  resubmit INT  NOT NULL default '0',
  mailed INT  NOT NULL default '0',
  isexercise INT  NOT NULL default '0'
);
CREATE INDEX prefix_exercise_submissions_userid_idx ON prefix_exercise_submissions (userid);
# --------------------------------------------------------

#
# Table structure for table exercise_assessments
#

CREATE TABLE prefix_exercise_assessments (
  id SERIAL8 PRIMARY KEY,
  exerciseid INT8  NOT NULL default '0',
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
CREATE INDEX prefix_exercise_assessments_submissionid_idx ON prefix_exercise_assessments (submissionid);
CREATE INDEX prefix_exercise_assessments_userid_idx ON prefix_exercise_assessments (userid);

# Table structure for table exercise_elements
#

CREATE TABLE prefix_exercise_elements (
  id SERIAL8 PRIMARY KEY,
  exerciseid INT8  NOT NULL default '0',
  elementno INT  NOT NULL default '0',
  description text NOT NULL,
  scale INT  NOT NULL default '0',
  maxscore INT  NOT NULL default '1',
  weight INT  NOT NULL default '11'
);
# --------------------------------------------------------


#
# Table structure for table exercise_rubrics
#

CREATE TABLE prefix_exercise_rubrics (
  id SERIAL8 PRIMARY KEY,
  exerciseid INT8  NOT NULL default '0',
  elementno INT8  NOT NULL default '0',
  rubricno INT  NOT NULL default '0',
  description text NOT NULL
);
# --------------------------------------------------------

#
# Table structure for table exercise_grades
#

CREATE TABLE prefix_exercise_grades (
  id SERIAL8 PRIMARY KEY,
  exerciseid INT8  NOT NULL default '0', 
  assessmentid INT8  NOT NULL default '0',
  elementno INT8  NOT NULL default '0',
  feedback text NOT NULL default '',
  grade INT NOT NULL default '0'
);

CREATE INDEX prefix_exercise_grades_assessmentid_idx ON prefix_exercise_grades (assessmentid);
# --------------------------------------------------------

        

INSERT INTO prefix_log_display VALUES ('exercise', 'close', 'exercise', 'name');
INSERT INTO prefix_log_display VALUES ('exercise', 'open', 'exercise', 'name');
INSERT INTO prefix_log_display VALUES ('exercise', 'submit', 'exercise', 'name');
INSERT INTO prefix_log_display VALUES ('exercise', 'view', 'exercise', 'name');
INSERT INTO prefix_log_display VALUES ('exercise', 'update', 'exercise', 'name');

