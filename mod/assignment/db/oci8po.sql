rem
rem Table structure for table assignment
rem

drop TABLE prefix_assignment;
CREATE TABLE prefix_assignment (
 id number(10) primary key,
 course number(10) default '0' not null,
 name varchar(255) default '' not null,
 description varchar2(255) NOT NULL,
 format number(2) default '0' not null,
 resubmit number(2) default '0' not null,
 type number(10) default '1' not null,
 maxbytes number(10) default '100000' not null,
 timedue number(10) default '0' not null,
 grade number(10) default '0' not null, 
 timemodified number(10) default '0' not null
);

COMMENT on table prefix_assignment is 'Defines assignments';

drop sequence p_assignment_seq;
create sequence p_assignment_seq;

create or replace trigger p_assignment_trig
  before insert on prefix_assignment
  referencing new as new_row
  for each row
  begin
    select p_assignment_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_assignment (course,name,description,format,resubmit,type,maxbytes,timedue,grade,timemodified) values (1,'name1','description1','1','1','1','111111','1','1','1');
insert into prefix_assignment (course,name,description,format,resubmit,type,maxbytes,timedue,grade,timemodified) values (2,'name2','description2','2','2','2','222222','2','2','2');
insert into prefix_assignment (course,name,description,format,resubmit,type,maxbytes,timedue,grade,timemodified) values (3,'name3','description3','3','3','3','333333','3','3','3');
insert into prefix_assignment (course,name,description,format,resubmit,type,maxbytes,timedue,grade,timemodified) values (4,'name4','description4','4','4','4','444444','4','4','4');

col format format 99
select * from prefix_assignment order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table assignment_submissions
rem

drop TABLE prefix_assignment_submissions;
CREATE TABLE prefix_assignment_submissions (
 id number(10) primary key ,
 assignment number(10) default '0' not null,
 userid number(10) default '0' not null,
 timecreated number(10) default '0' not null,
 timemodified number(10) default '0' not null,
 numfiles number(10) default '0' not null,
 grade number(11) default '0' not null,
 commentt varchar2(255) not null,
 teacher number(10) default '0' not null,
 timemarked number(10) default '0' not null,
 mailed number(1) default '0' not null
);


COMMENT on table prefix_assignment_submissions is 'Info about submitted assignments';

drop sequence p_assignment_sub_seq;
create sequence p_assignment_sub_seq;

create or replace trigger p_assignment_sub_trig
  before insert on prefix_assignment_submissions
  referencing new as new_row
  for each row
  begin
    select p_assignment_sub_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_assignment_submissions(
 assignment,userid,timecreated,timemodified,numfiles,grade,commentt,teacher,timemarked,mailed) values ('1','1','1','1','1','1','comment1','1','1','1');
insert into prefix_assignment_submissions(assignment,userid,timecreated,timemodified,numfiles,grade,commentt,teacher,timemarked,mailed) values ('2','2','2','2','2','2','comment2','2','2','2');
insert into prefix_assignment_submissions(assignment,userid,timecreated,timemodified,numfiles,grade,commentt,teacher,timemarked,mailed) values ('3','3','3','3','3','3','comment3','3','3','3');
insert into prefix_assignment_submissions(assignment,userid,timecreated,timemodified,numfiles,grade,commentt,teacher,timemarked,mailed) values ('4','4','4','4','4','4','comment4','4','4','4');

col teacher format 99
select * from prefix_assignment_submissions order by 1,2;

rem --------------------------------------------------------

delete from prefix_log_display where module='assignment';
INSERT INTO prefix_log_display VALUES ('assignment', 'view', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'add', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'update', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'view submissions', 'assignment', 'name');
INSERT INTO prefix_log_display VALUES ('assignment', 'upload', 'assignment', 'name');

select * from prefix_log_display where module='assignment' order by 1,2;
