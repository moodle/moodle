rem
rem Table structure for table choice
rem

drop TABLE prefix_choice;
CREATE TABLE prefix_choice (
  id number(10) primary key,
  course number(10) default '0' not null,
  name varchar2(255) default '' not null,
  text varchar2(1024) NOT NULL,
  format number(2) default '0' not null,
  answer1 varchar2(255) default 'Yes' not null,
  answer2 varchar2(255) default 'No' not null,
  answer3 varchar2(255) default NULL,
  answer4 varchar2(255) default NULL,
  answer5 varchar2(255) default NULL,
  answer6 varchar2(255) default NULL,
  publish number(2) default '0' not null,
  timemodified number(10) default '0' not null
);

COMMENT on table prefix_choice is 'Available choices are stored here.';

drop sequence p_choice_seq;
create sequence p_choice_seq;

create or replace trigger p_choice_trig
  before insert on prefix_choice
  referencing new as new_row
  for each row
  begin
    select p_choice_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_choice(course,name,text,format,answer1,answer2,answer3,answer4,answer5,answer6,publish,timemodified) values(1,'name1','text1',1,'1','1','1','1','1','1',1,1);
insert into prefix_choice(course,name,text,format,answer1,answer2,answer3,answer4,answer5,answer6,publish,timemodified) values(2,'name2','text2',2,'2','2','2','2','2','2',2,2);
insert into prefix_choice(course,name,text,format,answer1,answer2,answer3,answer4,answer5,answer6,publish,timemodified) values(3,'name3','text3',3,'3','3','3','3','3','3',3,3);
insert into prefix_choice(course,name,text,format,answer1,answer2,answer3,answer4,answer5,answer6,publish,timemodified) values(4,'name4','text4',4,'4','4','4','4','4','4',4,4);

select * from prefix_choice order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table choice_answers
rem

drop TABLE prefix_choice_answers;
CREATE TABLE prefix_choice_answers (
  id number(10) primary key,
  choice number(10) default '0' not null,
  userid number(10) default '0' not null,
  answer number(4) default '0' not null,
  timemodified number(10) default '0' not null
);

comment on table prefix_choice_answers is 'Answers for each choice';

drop sequence p_choice_answers_seq;
create sequence p_choice_answers_seq;

create or replace trigger p_choice_answers_trig
  before insert on prefix_choice_answers
  referencing new as new_row
  for each row
  begin
    select p_choice_answers_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_choice_answers (choice,userid,answer,timemodified) values(1,1,1,1);
insert into prefix_choice_answers (choice,userid,answer,timemodified) values(2,2,2,2);
insert into prefix_choice_answers (choice,userid,answer,timemodified) values(3,3,3,3);
insert into prefix_choice_answers (choice,userid,answer,timemodified) values(4,4,4,4);

select * from prefix_choice_answers order by 1,2;

rem
rem Dumping data for table log_display
rem

delete from prefix_log_display where module = 'choice';
INSERT INTO prefix_log_display VALUES ('choice', 'view', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'update', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'add', 'choice', 'name');
INSERT INTO prefix_log_display VALUES ('choice', 'report', 'choice', 'name');

    


