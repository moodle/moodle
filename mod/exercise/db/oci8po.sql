rem
rem Table structure for table exercise
rem

drop TABLE prefix_exercise;
CREATE TABLE prefix_exercise (
  id number(10) primary key,
  course number(10) default '0' not null,
  name varchar2(255) default '' not null,
  nelements number(3) default '1' not null,
  phase number(3) default '0' not null,
  gradingstrategy number(3) default '1' not null,
  usemaximum number(3) default '0' not null,
  anonymous number(3) default '0' not null,
  maxbytes number(10) default '100000' not null,
  deadline number(10) default '0' not null,
  grade number(3) default '0' not null,
  timemodified number(10) default '0' not null,
  teacherweight number(3) default '5' not null,
  gradingweight number(3) default '5' not null,
  showleaguetable number(3) default '0' not null
);

COMMENT on table prefix_exercise is 'Defines exercise';

drop sequence prefix_exercise_seq;
create sequence prefix_exercise_seq;

create or replace trigger p_exercise_trig
before insert on prefix_exercise
referencing new as new_row
for each row
begin
select prefix_exercise_seq.nextval into :new_row.id from dual;
end;
.
/

rem --------------------------------------------------------

rem
rem Table structure for table exercise_submissions
rem

drop TABLE prefix_exercise_submissions;
CREATE TABLE prefix_exercise_submissions (
  id number(10) primary key,
  exerciseid number(10) default '0',
  userid number(10) default '0',
  title varchar2(100) default '',
  timecreated number(10) default '0',
  resubmit number(3) default '0',
  mailed number(3) default '0',
  isexercise number(3) default '0',
  late number(3) default '0'
);

COMMENT on table prefix_exercise_submissions is 'Info about submitted work from teacher and students';

create INDEX pes_ix on prefix_exercise_submissions(userid);

drop sequence prefix_exercise_submissions_sq;
create sequence prefix_exercise_submissions_sq;

create or replace trigger p_exercise_submissions_trig
before insert on prefix_exercise_submissions
referencing new as new_row
for each row
begin
select prefix_exercise_submissions_sq.nextval into :new_row.id from dual;
end;
.
/

rem --------------------------------------------------------

rem
rem Table structure for table exercise_assessments
rem

drop TABLE prefix_exercise_assessments;
CREATE TABLE prefix_exercise_assessments (
  id number(10) primary key,
  exerciseid number(10) default '0',
  submissionid number(10) default '0',
  userid number(10) default '0',
  timecreated number(10) default '0',
  timegraded number(10) default '0',
  grade float default '0',
  gradinggrade number(3) default '0',
  mailed number(2) default '0',
  generalcomment varchar2(1024),
  teachercomment varchar2(1024)
  );

COMMENT on table prefix_exercise_assessments is 'Info about assessments by teacher and students';

drop sequence prefix_exercise_assessments_sq;
create sequence prefix_exercise_assessments_sq;

create or replace trigger p_exercise_assessments_trig
before insert on prefix_exercise_assessments
referencing new as new_row
for each row
begin
select prefix_exercise_assessments_sq.nextval into :new_row.id from dual;
end;
.
/


create INDEX eas_ix on prefix_exercise_assessments(submissionid);
create INDEX eau_ix on prefix_exercise_assessments (userid);
rem --------------------------------------------------------

rem
rem Table structure for table exercise_elements
rem

drop TABLE prefix_exercise_elements;
CREATE TABLE prefix_exercise_elements (
  id number(10) primary key,
  exerciseid number(10) default '0',
  elementno number(3) default '0',
  description varchar2(1024),
  scale number(3) default '0',
  maxscore number(3) default '1',
  weight number(3) default '11'
);

COMMENT on table prefix_exercise_elements is 'Info about marking scheme of assignment';

drop sequence prefix_exercise_elements_seq;
create sequence prefix_exercise_elements_seq;

create or replace trigger p_exercise_elements_trig
before insert on prefix_exercise_elements
referencing new as new_row
for each row
begin
select prefix_exercise_elements_seq.nextval into :new_row.id from dual;
end;
.
/

rem --------------------------------------------------------


rem
rem Table structure for table exercise_rubrics
rem

drop TABLE prefix_exercise_rubrics;
CREATE TABLE prefix_exercise_rubrics (
  id number(10) primary key,
  exerciseid number(10) default '0',
  elementno number(10) default '0',
  rubricno number(3) default '0',
  description varchar2(1024)
);

COMMENT on table prefix_exercise_rubrics is 'Info about the rubrics marking scheme';

drop sequence prefix_exercise_rubrics_seq;
create sequence prefix_exercise_rubrics_seq;

create or replace trigger p_exercise_rubrics_trig
before insert on prefix_exercise_rubrics
referencing new as new_row
for each row
begin
select prefix_exercise_rubrics_seq.nextval into :new_row.id from dual;
end;
.
/

rem --------------------------------------------------------

rem
rem Table structure for table exercise_grades
rem

drop TABLE prefix_exercise_grades;
CREATE TABLE prefix_exercise_grades (
  id number(10) primary key,
  exerciseid number(10) default '0', 
  assessmentid number(10) default '0',
  elementno number(10) default '0',
  feedback varchar2(1024) default '',
  grade number(3) default '0'
);

COMMENT on table prefix_exercise_grades is 'Info about individual grades given to each element';

drop sequence prefix_exercise_grades_seq;
create sequence prefix_exercise_grades_seq;

create or replace trigger p_exercise_grades_trig
before insert on prefix_exercise_grades
referencing new as new_row
for each row
begin
select prefix_exercise_grades_seq.nextval into :new_row.id from dual;
end;
.
/


create INDEX ega_idx on prefix_exercise_grades (assessmentid);

rem --------------------------------------------------------

INSERT INTO prefix_log_display VALUES ('exercise', 'close', 'exercise', 'name');
INSERT INTO prefix_log_display VALUES ('exercise', 'open', 'exercise', 'name');
INSERT INTO prefix_log_display VALUES ('exercise', 'submit', 'exercise', 'name');
INSERT INTO prefix_log_display VALUES ('exercise', 'view', 'exercise', 'name');
INSERT INTO prefix_log_display VALUES ('exercise', 'update', 'exercise', 'name');

