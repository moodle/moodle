rem
rem Table structure for table workshop
rem

drop TABLE prefix_workshop;
CREATE TABLE prefix_workshop (
  id number(10) primary key,
  course number(10)  default '0' not null,
  name varchar2(255) default '' not null,
  description varchar2(255) NOT NULL,
  nelements number(3)  default '1' not null,
  phase number(2)  default '0' not null,
  format number(2)  default '0' not null,
  gradingstrategy number(2)  default '1' not null,
  resubmit number(2)  default '0' not null,
  agreeassessments number(2)  default '0' not null,
  hidegrades number(2)  default '0' not null,
  anonymous number(2)  default '0' not null,
  includeself number(2)  default '0' not null,
  maxbytes number(10)  default '100000' not null,
  deadline number(10)  default '0' not null,
  grade number(10) default '0' not null,
  ntassessments number(3)  default '0' not null,
  nsassessments number(3)  default '0' not null,
  overallocation number(3)  default '0' not null,
  timemodified number(10)  default '0' not null,
  mergegrades number(3)  default '0' not null,
  teacherweight number(3)  default '5' NOT NULL,
  peerweight number(3)  default '5' NOT NULL,
  includeteachersgrade number(3)  default '0' not null,
  biasweight number(3)  default '5' NOT NULL,
  reliabilityweight number(3)  default '5' NOT NULL,
  gradingweight number(3)  default '5' NOT NULL,
  showleaguetable number(3)  default '0' not null
);

COMMENT on table prefix_workshop is 'Defines workshop';

drop sequence p_workshop_seq;
create sequence p_workshop_seq;

create or replace trigger p_workshop_trig
  before insert on prefix_workshop
  referencing new as new_row
  for each row
  begin
    select p_workshop_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_workshop( course,name,description,nelements,phase,format,gradingstrategy,resubmit,agreeassessments,hidegrades,anonymous,includeself,maxbytes,deadline,grade,ntassessments,nsassessments,timemodified,mergegrades,teacherweight,peerweight,includeteachersgrade,biasweight,reliabilityweight,gradingweight,showleaguetable) values (1,'1','1',1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
insert into prefix_workshop( course,name,description,nelements,phase,format,gradingstrategy,resubmit,agreeassessments,hidegrades,anonymous,includeself,maxbytes,deadline,grade,ntassessments,nsassessments,timemodified,mergegrades,teacherweight,peerweight,includeteachersgrade,biasweight,reliabilityweight,gradingweight,showleaguetable) values (2,'2','2',2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2);
insert into prefix_workshop( course,name,description,nelements,phase,format,gradingstrategy,resubmit,agreeassessments,hidegrades,anonymous,includeself,maxbytes,deadline,grade,ntassessments,nsassessments,timemodified,mergegrades,teacherweight,peerweight,includeteachersgrade,biasweight,reliabilityweight,gradingweight,showleaguetable) values (3,'3','3',3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3);
insert into prefix_workshop( course,name,description,nelements,phase,format,gradingstrategy,resubmit,agreeassessments,hidegrades,anonymous,includeself,maxbytes,deadline,grade,ntassessments,nsassessments,timemodified,mergegrades,teacherweight,peerweight,includeteachersgrade,biasweight,reliabilityweight,gradingweight,showleaguetable) values (4,'4','4',4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4);

col format format 9999
select * from prefix_workshop order by 1,2;
rem --------------------------------------------------------

rem
rem Table structure for table workshop_submissions
rem

drop TABLE prefix_workshop_submissions;
CREATE TABLE prefix_workshop_submissions (
  id number(10) primary key,
  workshopid number(10)  default '0' not null,
  userid number(10)  default '0' not null,
  title varchar2(100) default '' not null,
  timecreated number(10)  default '0' not null,
  mailed number(2)  default '0' not null,
  teachergrade number(3)  default '0' not null,
  peergrade number(3)  default '0' not null,
  biasgrade number(3)  default '0' not null,
  reliabilitygrade number(3)  default '0' not null,
  gradinggrade number(3)  default '0' not null,
  finalgrade number(3)  default '0' not null
);

CREATE INDEX title ON prefix_workshop_submissions(title);

comment on TABLE prefix_workshop_submissions is 'Info about submitted work from teacher and students';

drop sequence p_workshop_submissions_seq;
create sequence p_workshop_submissions_seq;

create or replace trigger p_workshop_submissions_trig
  before insert on prefix_workshop_submissions
  referencing new as new_row
  for each row
  begin
    select p_workshop_submissions_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_workshop_submissions ( workshopid,userid,title,timecreated,mailed,teachergrade,peergrade,biasgrade,reliabilitygrade,gradinggrade,finalgrade) values(1,1,'1',1,1,1,1,1,1,1,1);
insert into prefix_workshop_submissions ( workshopid,userid,title,timecreated,mailed,teachergrade,peergrade,biasgrade,reliabilitygrade,gradinggrade,finalgrade) values(2,2,'2',2,2,2,2,2,2,2,2);
insert into prefix_workshop_submissions ( workshopid,userid,title,timecreated,mailed,teachergrade,peergrade,biasgrade,reliabilitygrade,gradinggrade,finalgrade) values(3,3,'3',3,3,3,3,3,3,3,3);
insert into prefix_workshop_submissions ( workshopid,userid,title,timecreated,mailed,teachergrade,peergrade,biasgrade,reliabilitygrade,gradinggrade,finalgrade) values(4,4,'4',4,4,4,4,4,4,4,4);

select * from prefix_workshop_submissions order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table workshop_assessments
rem

drop TABLE prefix_workshop_assessments;
CREATE TABLE prefix_workshop_assessments (
  id number(10) primary key,
  workshopid number(10)  default '0' not null,
  submissionid number(10)  default '0' not null,
  userid number(10)  default '0' not null,
  timecreated number(10)  default '0' not null,
  timegraded number(10)  default '0' not null,
  timeagreed number(10)  default '0' not null,
  grade float default '0' not null,
  gradinggrade number(3) default '0' not null,
  mailed number(2)  default '0' not null,
  resubmission number(2)  default '0' not null,
  generalcomment varchar2(255) NOT NULL,
  teachercomment varchar2(255) NOT NULL
);

comment on TABLE prefix_workshop_assessments is 'Info about assessments by teacher and students';

drop sequence p_workshop_assessments_seq;
create sequence p_workshop_assessments_seq;

create or replace trigger p_workshop_assessments_trig
  before insert on prefix_workshop_assessments
  referencing new as new_row
  for each row
  begin
    select p_workshop_assessments_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_workshop_assessments (workshopid,submissionid,userid,timecreated,timegraded,timeagreed,grade,gradinggrade,mailed,generalcomment,teachercomment) values(1,1,1,1,1,1,1,1,1,'1','1'); 
insert into prefix_workshop_assessments (workshopid,submissionid,userid,timecreated,timegraded,timeagreed,grade,gradinggrade,mailed,generalcomment,teachercomment) values(2,2,2,2,2,2,2,2,2,'2','2'); 
insert into prefix_workshop_assessments (workshopid,submissionid,userid,timecreated,timegraded,timeagreed,grade,gradinggrade,mailed,generalcomment,teachercomment) values(3,3,3,3,3,3,3,3,3,'3','3'); 
insert into prefix_workshop_assessments (workshopid,submissionid,userid,timecreated,timegraded,timeagreed,grade,gradinggrade,mailed,generalcomment,teachercomment) values(4,4,4,4,4,4,4,4,4,'4','4'); 

select * from prefix_workshop_assessments order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table workshop_elements
rem

drop TABLE prefix_workshop_elements;
CREATE TABLE prefix_workshop_elements (
  id number(10) primary key,
  workshopid number(10)  default '0' not null,
  elementno number(3)  default '0' not null,
  description varchar2(255) NOT NULL,
  scale number(3)  default '0' not null,
  maxscore number(3)  default '1' not null,
  weight float default '1.0' not null
);

comment on TABLE prefix_workshop_elements is 'Info about marking scheme of assignment';

drop sequence p_workshop_elements_seq;
create sequence p_workshop_elements_seq;

create or replace trigger p_workshop_elements_trig
  before insert on prefix_workshop_elements
  referencing new as new_row
  for each row
  begin
    select p_workshop_elements_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_workshop_elements ( workshopid, elementno, description, scale, maxscore, weight) values(1,1,'1',1,1,1);
insert into prefix_workshop_elements ( workshopid, elementno, description, scale, maxscore, weight) values(2,2,'2',2,2,2);
insert into prefix_workshop_elements ( workshopid, elementno, description, scale, maxscore, weight) values(3,3,'3',3,3,3);
insert into prefix_workshop_elements ( workshopid, elementno, description, scale, maxscore, weight) values(4,4,'4',4,4,4);

select * from prefix_workshop_elements order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table workshop_rubrics
rem

drop TABLE prefix_workshop_rubrics;
CREATE TABLE prefix_workshop_rubrics (
  id number(10) primary key,
  workshopid number(10)  default '0' not null,
  elementno number(10)  default '0' not null,
  rubricno number(3)  default '0' not null,
  description varchar2(255) NOT NULL
  );

comment on TABLE prefix_workshop_rubrics is 'Info about the rubrics marking scheme';

drop sequence p_workshop_rubrics_seq;
create sequence p_workshop_rubrics_seq;

create or replace trigger p_workshop_rubrics_trig
  before insert on prefix_workshop_rubrics
  referencing new as new_row
  for each row
  begin
    select p_workshop_rubrics_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_workshop_rubrics ( workshopid,elementno,rubricno,description) values(1,1,1,'1');
insert into prefix_workshop_rubrics ( workshopid,elementno,rubricno,description) values(2,2,2,'2');
insert into prefix_workshop_rubrics ( workshopid,elementno,rubricno,description) values(3,3,3,'3');
insert into prefix_workshop_rubrics ( workshopid,elementno,rubricno,description) values(4,4,4,'4');

select * from prefix_workshop_rubrics order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table workshop_grades
rem

drop TABLE prefix_workshop_grades;
CREATE TABLE prefix_workshop_grades (
  id number(10) primary key,
  workshopid number(10)  default '0' not null, 
  assessmentid number(10)  default '0' not null,
  elementno number(10)  default '0' not null,
  feedback varchar2(255) default '' not null,
  grade number(3) default '0' not null
  );

comment on TABLE prefix_workshop_grades is 'Info about individual grades given to each element';

drop sequence p_workshop_grades_seq;
create sequence p_workshop_grades_seq;

create or replace trigger p_workshop_grades_trig
  before insert on prefix_workshop_grades
  referencing new as new_row
  for each row
  begin
    select p_workshop_grades_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_workshop_grades (workshopid,assessmentid,elementno,feedback,grade) values(1,1,1,'1',1);
insert into prefix_workshop_grades (workshopid,assessmentid,elementno,feedback,grade) values(2,2,2,'2',2);
insert into prefix_workshop_grades (workshopid,assessmentid,elementno,feedback,grade) values(3,3,3,'3',3);
insert into prefix_workshop_grades (workshopid,assessmentid,elementno,feedback,grade) values(4,4,4,'4',4);

col feedback format a10
select * from prefix_workshop_grades order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table workshop_comments
rem

drop TABLE prefix_workshop_comments;
CREATE TABLE prefix_workshop_comments (
  id number(10) primary key,
  workshopid number(10)  default '0' not null, 
  assessmentid number(10)  default '0' not null,
  userid number(10)  default '0' not null,
  timecreated number(10)  default '0' not null,
  mailed number(2)  default '0' not null,
  comments varchar2(255) NOT NULL
  );

comment on TABLE prefix_workshop_comments is 'Defines comments';

drop sequence p_workshop_comments_seq;
create sequence p_workshop_comments_seq;

create or replace trigger p_workshop_comments_trig
  before insert on prefix_workshop_comments
  referencing new as new_row
  for each row
  begin
    select p_workshop_comments_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_workshop_comments ( workshopid, assessmentid, userid, timecreated, mailed, comments) values(1,1,1,1,1,'1');
insert into prefix_workshop_comments ( workshopid, assessmentid, userid, timecreated, mailed, comments) values(2,2,2,2,2,'2');
insert into prefix_workshop_comments ( workshopid, assessmentid, userid, timecreated, mailed, comments) values(3,3,3,3,3,'3');
insert into prefix_workshop_comments ( workshopid, assessmentid, userid, timecreated, mailed, comments) values(4,4,4,4,4,'4');

select * from prefix_workshop_comments order by 1,2;

rem --------------------------------------------------------

delete from prefix_log_display where module='workshop';

INSERT INTO prefix_log_display VALUES ('workshop', 'assess', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'close', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'display grades', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'grade', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'hide grades', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'open', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'submit', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'view', 'workshop', 'name');
INSERT INTO prefix_log_display VALUES ('workshop', 'update', 'workshop', 'name');

select * from prefix_log_display where module='workshop' order by 1,2;
