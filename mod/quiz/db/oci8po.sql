rem
rem Oracle - draft draft draft untested untested untested
rem

set echo on pagesize 60
spool t.lst

rem
rem Table structure for table quiz
rem

drop table prefix_quiz;
CREATE TABLE prefix_quiz (
  id number(10) primary key,
  course number(10)  default '0' not null,
  name varchar2(255) default '' not null,
  intro varchar2(255) NOT NULL,
  timeopen number(10) default '0' not null,
  timeclose number(10) default '0' not null,
  attempts number(6) default '0' not null,
  feedback number(4) default '0' not null,
  correctanswers number(4) default '1' not null,
  grademethod number(4) default '1' not null,
  review number(4) default '0' not null,
  questions varchar2(255) NOT NULL,
  sumgrades number(10) default '0' not null,
  grade number(10) default '0' not null,
  timecreated number(10) default '0' not null,
  timemodified number(10) default '0' not null
);

comment on table prefix_quiz is 'Main information about each quiz';

drop sequence pqs;
create sequence pqs;

create or replace trigger pqt
  before insert on prefix_quiz
  referencing new as new_row
  for each row
  begin
    select pqs.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_quiz (course,name,intro,questions) values 
  (101,'Fundamentals of Testing','Test intro','question 1, question 2');

insert into prefix_quiz (course,name,intro,questions) values 
  (102,'Fundamentals of Testing 2','Test intro','question 3, question 4');

insert into prefix_quiz (course,name,intro,questions) values 
  (101,'Fundamentals of Testing','Test intro','question 1, question 2');

insert into prefix_quiz (course,name,intro,questions) values 
  (102,'Fundamentals of Testing 2','Test intro','question 3, question 4');

select id,course,substr(name,1,20) name,
	substr(intro,1,12) intro,
	substr(questions,1,22) questions
  from prefix_quiz order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_answers
rem

drop TABLE prefix_quiz_answers;
CREATE TABLE prefix_quiz_answers (
  id number(10)  NOT NULL  PRIMARY KEY,
  question number(10)  default '0' not null,
  answer varchar(255) default '' not null,
  fraction varchar(10) default '0.0',
  feedback varchar2(255) NOT NULL
);

COMMENT on table prefix_quiz_answers is 'Answers, with a fractional grade (0-1) and feedback';

drop sequence pqas;
create sequence pqas;

create or replace trigger pqat
  before insert on prefix_quiz_answers
  referencing new as new_row
  for each row
  begin
    select pqas.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_quiz_answers (question,answer,fraction,feedback) values 
  (1,'Answer #1','fraction1','feedback1');

insert into prefix_quiz_answers (question,answer,fraction,feedback) values 
  (13,'Answer #13','fraction13','feedback13');

insert into prefix_quiz_answers (question,answer,fraction,feedback) values 
  (31,'Answer #31','fraction31','feedback31');

insert into prefix_quiz_answers (question,answer,fraction,feedback) values 
  (3,'Answer #3','fraction3','feedback3');

select id,substr(question,1,12),
	substr(answer,1,12),
	substr(fraction,1,12),
	substr(feedback,1,12)
  from prefix_quiz_answers
 order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_attempts
rem

drop table prefix_quiz_attempts;
CREATE TABLE prefix_quiz_attempts (
  id number(10)  primary key,
  quiz number(10)  default '0' not null,
  userid number(10)  default '0' not null,
  attempt number(6) default '0' not null,
  sumgrades varchar(10) default '0.0' not null,
  timestart number(10)  default '0' not null,
  timefinish number(10)  default '0' not null,
  timemodified number(10)  default '0' not null
);

COMMENT on table prefix_quiz_attempts is 'Stores various attempts on a quiz';

drop sequence pq_attempts_seq;
create sequence pq_attempts_seq;

create or replace trigger pq_attempts_trig
  before insert on prefix_quiz_attempts
  referencing new as new_row
  for each row
  begin
    select pq_attempts_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_quiz_attempts (
       quiz,userid,attempt,sumgrades,timestart,timefinish,timemodified) 
values (1,1,1,'sumgrades',1400,1405,1404);

select 
	id,
	quiz,
	userid,
	attempt,
	substr(sumgrades,1,12) sumgrades,
	timestart,
	timefinish,
	timemodified
  from prefix_quiz_attempts
 order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_categories
rem

drop table prefix_quiz_categories;
CREATE TABLE prefix_quiz_categories (
  id number(10) primary key,
  course number(10)  default '0' not null,
  name varchar(255) default '' not null,
  info varchar2(255) not null,
  publish number(4) default '0' not null
);

COMMENT on table prefix_quiz_categories is 'Categories are for grouping questions';

drop sequence pq_categories_seq;
create sequence pq_categories_seq;

create or replace trigger pq_categories_trig
  before insert on prefix_quiz_categories
  referencing new as new_row
  for each row
  begin
    select pq_categories_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_quiz_categories (course,name,info,publish) values (1,1,1,1);
insert into prefix_quiz_categories (course,name,info,publish) values (2,2,2,2);
insert into prefix_quiz_categories (course,name,info,publish) values (3,3,3,3);
insert into prefix_quiz_categories (course,name,info,publish) values (4,4,4,4);

select id,
	substr(course,1,4) course,
	substr(name,1,4) name,
	substr(info,1,4) info,
	substr(publish,1,4) publish
  from prefix_quiz_categories
 order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_grades
rem

drop TABLE prefix_quiz_grades;
CREATE TABLE prefix_quiz_grades (
  id number(10)  NOT NULL ,
  quiz number(10)  default '0' not null,
  userid number(10) default '0' not null,
  grade varchar(10) default '0.0' not null,
  timemodified number(10) default '0' not null
);

COMMENT on table prefix_quiz_grades is 'Final quiz grade (may be best of several attempts)';

drop sequence pq_grades_seq;
create sequence pq_grades_seq;

create or replace trigger pq_grades_trig
  before insert on prefix_quiz_grades
  referencing new as new_row
  for each row
  begin
    select pq_grades_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_quiz_grades (quiz,userid,grade,timemodified) values (1,1,1,1);
insert into prefix_quiz_grades (quiz,userid,grade,timemodified) values (2,2,2,2);
insert into prefix_quiz_grades (quiz,userid,grade,timemodified) values (3,3,3,3);
insert into prefix_quiz_grades (quiz,userid,grade,timemodified) values (4,4,4,4);

select id,
	substr(quiz,1,4) quiz,
	substr(userid,1,4) userid,
	substr(grade,1,4) grade,
	substr(timemodified,1,4) timemodified
  from prefix_quiz_grades
 order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_multichoice
rem

drop TABLE prefix_quiz_multichoice;
CREATE TABLE prefix_quiz_multichoice (
  id number(10) primary key,
  question number(10) default '0' not null,
  layout number(4) default '0' not null,
  answers varchar(255) default '' not null,
  single number(4) default '0' not null
);

COMMENT on table prefix_quiz_multichoice is 'Options for multiple choice questions';

drop sequence pq_multichoice_seq;
create sequence pq_multichoice_seq;

create or replace trigger pq_multichoice_trig
  before insert on prefix_quiz_multichoice
  referencing new as new_row
  for each row
  begin
    select pq_multichoice_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_quiz_multichoice (question,layout,answers,single) values (1,1,1,1);
insert into prefix_quiz_multichoice (question,layout,answers,single) values (2,2,2,2);
insert into prefix_quiz_multichoice (question,layout,answers,single) values (3,3,3,3);
insert into prefix_quiz_multichoice (question,layout,answers,single) values (4,4,4,4);

select id,
	substr(question,1,4) question,
	substr(layout,1,4) layout,
	substr(answers,1,4) answers,
	substr(single,1,4) single
  from prefix_quiz_multichoice
 order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_question_grades
rem

drop TABLE prefix_quiz_question_grades;
CREATE TABLE prefix_quiz_question_grades (
  id number(10)  NOT NULL ,
  quiz number(10)  default '0',
  question number(10) default '0',
  grade number(6) default '0'
);

COMMENT on table prefix_quiz_question_grades is 'The grade for a question in a quiz';

drop sequence pq_question_grades_seq;
create sequence pq_question_grades_seq;

create or replace trigger pq_question_grades_trig
  before insert on prefix_quiz_question_grades
  referencing new as new_row
  for each row
  begin
    select pq_question_grades_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_quiz_question_grades (quiz,question,grade) values (1,1,1);
insert into prefix_quiz_question_grades (quiz,question,grade) values (2,2,2);
insert into prefix_quiz_question_grades (quiz,question,grade) values (3,3,3);
insert into prefix_quiz_question_grades (quiz,question,grade) values (4,4,4);

select id,
	substr(quiz,1,4) quiz,
	substr(question,1,4) question,
	substr(grade,1,4) grade
  from prefix_quiz_question_grades
 order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_randommatch
rem

drop TABLE prefix_quiz_randommatch;
CREATE TABLE prefix_quiz_randommatch (
  id number(10) primary key,
  question number(10)  default '0' not null,
  choose INT DEFAULT '4' NOT NULL
);

COMMENT on table prefix_quiz_randommatch is 'Info about a random matching question';

drop sequence pq_randommatch_seq;
create sequence pq_randommatch_seq;

create or replace trigger pq_randommatch_trig
  before insert on prefix_quiz_randommatch
  referencing new as new_row
  for each row
  begin
    select pq_randommatch_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_quiz_randommatch (question,choose) values (1,1);
insert into prefix_quiz_randommatch (question,choose) values (2,2);
insert into prefix_quiz_randommatch (question,choose) values (3,3);
insert into prefix_quiz_randommatch (question,choose) values (4,4);

select id,
	substr(question,1,4) question,
	substr(choose,1,4) choose
  from prefix_quiz_randommatch
 order by 1,2;

rem
rem Table structure for table quiz_questions
rem

drop TABLE prefix_quiz_questions;
CREATE TABLE prefix_quiz_questions (
  id number(10) primary key,
  category number(10) default '0' not null,
  name varchar(255) default '' not null,
  questiontext varchar2(255) NOT NULL,
  image varchar(255) default '' not null,
  qtype number(6) default '0' not null
);

COMMENT on table prefix_quiz_questions is 'The quiz questions themselves';

drop sequence pq_questions_seq;
create sequence pq_questions_seq;

create or replace trigger pq_questions_trig
  before insert on prefix_quiz_questions
  referencing new as new_row
  for each row
  begin
    select pq_questions_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_quiz_questions (category,name,questiontext,image,qtype) values (1,'name1','questiontext1','image1',1);
insert into prefix_quiz_questions (category,name,questiontext,image,qtype) values (2,'name2','questiontext2','image2',2);
insert into prefix_quiz_questions (category,name,questiontext,image,qtype) values (3,'name3','questiontext3','image3',3);
insert into prefix_quiz_questions (category,name,questiontext,image,qtype) values (4,'name4','questiontext4','image4',4);

select id,
	substr(category,1,4) category,
	substr(name,1,4) name,
	substr(questiontext,1,4) questiontext,
	substr(image,1,4) image,
	substr(qtype,1,4) qtype
  from prefix_quiz_questions
 order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_responses
rem

drop TABLE prefix_quiz_responses;
CREATE TABLE prefix_quiz_responses (
  id number(10)  primary key,
  attempt number(10) default '0' not null,
  question number(10) default '0' not null,
  answer varchar(255) default '' not null,
  grade varchar(10) default '0.0' not null
);

COMMENT on table prefix_quiz_questions is 'Stores user responses to a quiz, and percentage grades';

drop sequence pq_responses_seq;
create sequence pq_responses_seq;

create or replace trigger pq_responses_trig
  before insert on prefix_quiz_responses
  referencing new as new_row
  for each row
  begin
    select pq_responses_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_quiz_responses (attempt,question,answer,grade) 
values (1,1,'answer1','grade1');
insert into prefix_quiz_responses (attempt,question,answer,grade) 
values (2,2,'answer2','grade2');
insert into prefix_quiz_responses (attempt,question,answer,grade) 
values (3,3,'answer3','grade3');
insert into prefix_quiz_responses (attempt,question,answer,grade) 
values (4,4,'answer4','grade4');

select id,
	substr(attempt,1,4) attempt,
	substr(question,1,14) question,
	substr(answer,1,14) answer,
	substr(grade,1,14) grade
  from prefix_quiz_responses
 order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_shortanswer
rem

drop TABLE prefix_quiz_shortanswer;
CREATE TABLE prefix_quiz_shortanswer (
  id number(10) primary key,
  question number(10) default '0' not null,
  answers varchar(255) default '' not null,
  usecase number(2) default '0' not null
);

COMMENT on table prefix_quiz_shortanswer is 'Options for short answer questions';

drop sequence pq_shortanswer_seq;
create sequence pq_shortanswer_seq;

create or replace trigger pq_shortanswer_trig
  before insert on prefix_quiz_shortanswer
  referencing new as new_row
  for each row
  begin
    select pq_shortanswer_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_quiz_shortanswer (question,answers,usecase) values (1,'answer1',1);
insert into prefix_quiz_shortanswer (question,answers,usecase) values (2,'answer2',2);
insert into prefix_quiz_shortanswer (question,answers,usecase) values (3,'answer3',3);
insert into prefix_quiz_shortanswer (question,answers,usecase) values (4,'answer4',4);

select id,
	substr(question,1,14) question,
	substr(answers,1,14) answers,
	substr(usecase,1,14) usecase
  from prefix_quiz_shortanswer
 order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_truefalse
rem

drop TABLE prefix_quiz_truefalse;
CREATE TABLE prefix_quiz_truefalse (
  id number(10)  primary key,
  question number(10)  default '0' not null,
  trueanswer number(10)  default '0' not null,
  falseanswer number(10) default '0' not null
);

COMMENT on table prefix_quiz_truefalse is 'Options for True-False questions';


drop sequence pq_truefalse_seq;
create sequence pq_truefalse_seq;

create or replace trigger pq_truefalse_trig
  before insert on prefix_quiz_truefalse
  referencing new as new_row
  for each row
  begin
    select pq_truefalse_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_quiz_truefalse (question,trueanswer,falseanswer) values (1,1,1);
insert into prefix_quiz_truefalse (question,trueanswer,falseanswer) values (2,2,2);
insert into prefix_quiz_truefalse (question,trueanswer,falseanswer) values (3,3,3);
insert into prefix_quiz_truefalse (question,trueanswer,falseanswer) values (4,4,4);

select id,
	substr(question,1,14) question,
	substr(trueanswer,1,14) trueanswer,
	substr(falseanswer,1,14) falseanswer
  from prefix_quiz_truefalse
 order by 1,2;

select 
	substr(table_name,1,28) table_name,
	substr(comments,1,54) comments 
  from all_tab_comments
 where owner = 'SCOTT'
   and table_name like 'PREFIX%'
/

INSERT INTO prefix_log_display VALUES ('quiz', 'view', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'report', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'attempt', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'submit', 'quiz', 'name');

spool off
