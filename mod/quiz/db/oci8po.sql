rem
rem Oracle - draft draft draft untested untested untested
rem

set echo on pagesize 60

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
  attemptonlast number(1) default '0',
  feedback number(4) default '0' not null,
  correctanswers number(4) default '1' not null,
  grademethod number(4) default '1' not null,
  review number(4) default '0' not null,
  shufflequestions number(4) default '0' not null,
  shuffleanswers number(4) default '0' not null,
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
insert into prefix_quiz(course,name,intro, timeopen,timeclose,attempts,attemptonlast,feedback,correctanswers,grademethod,review,shufflequestions,shuffleanswers,questions,sumgrades,grade,timecreated,timemodified) values(1,'1','1',1,1,1,1,1,1,1,1,1,1,'1',1,1,1,1);
insert into prefix_quiz(course,name,intro, timeopen,timeclose,attempts,attemptonlast,feedback,correctanswers,grademethod,review,shufflequestions,shuffleanswers,questions,sumgrades,grade,timecreated,timemodified) values(2,'2','2',2,2,2,2,2,2,2,2,2,2,'2',2,2,2,2);
insert into prefix_quiz(course,name,intro, timeopen,timeclose,attempts,attemptonlast,feedback,correctanswers,grademethod,review,shufflequestions,shuffleanswers,questions,sumgrades,grade,timecreated,timemodified) values(3,'3','3',3,3,3,3,3,3,3,3,3,3,'3',3,3,3,3);
insert into prefix_quiz(course,name,intro, timeopen,timeclose,attempts,attemptonlast,feedback,correctanswers,grademethod,review,shufflequestions,shuffleanswers,questions,sumgrades,grade,timecreated,timemodified) values(4,'4','4',4,4,4,4,4,4,4,4,4,4,'4',4,4,4,4);

select * from prefix_quiz order by 1,2;

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

create index question on prefix_quiz_answers(question);

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

insert into prefix_quiz_answers(question,answer,fraction,feedback) values(1,'1','1','1');
insert into prefix_quiz_answers(question,answer,fraction,feedback) values(2,'2','2','2');
insert into prefix_quiz_answers(question,answer,fraction,feedback) values(3,'3','3','3');
insert into prefix_quiz_answers(question,answer,fraction,feedback) values(4,'4','4','4');

col feedback format a10
select * from prefix_quiz_answers order by 1,2;

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

create index quiz on prefix_quiz_attempts(quiz);
create index userid0 on prefix_quiz_attempts(userid);

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

insert into prefix_quiz_attempts(quiz,userid,attempt,sumgrades,timestart,timefinish,timemodified) values(1,1,1,'1',1,1,1);
insert into prefix_quiz_attempts(quiz,userid,attempt,sumgrades,timestart,timefinish,timemodified) values(2,2,2,'2',2,2,2);
insert into prefix_quiz_attempts(quiz,userid,attempt,sumgrades,timestart,timefinish,timemodified) values(3,3,3,'3',3,3,3);
insert into prefix_quiz_attempts(quiz,userid,attempt,sumgrades,timestart,timefinish,timemodified) values(4,4,4,'4',4,4,4);

select * from prefix_quiz_attempts order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_categories
rem

drop table prefix_quiz_categories;
CREATE TABLE prefix_quiz_categories (
  id number(10) primary key,
  course number(10)  default '0' not null,
  name varchar(255) default '' not null,
  info varchar2(1024) not null,
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

select * from prefix_quiz_categories order by 1,2;

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

create index quiz0 on prefix_quiz_grades(quiz);
create index userid1 on prefix_quiz_grades(userid);

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

select * from prefix_quiz_grades order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_match
rem

drop TABLE prefix_quiz_match;
CREATE TABLE prefix_quiz_match (
  id number(10) primary key,
  question number(10) default '0' not null,
  subquestions varchar2(255) default '' not null
);

COMMENT on table prefix_quiz_match is 'Defines fixed matching questions';

drop sequence pq_match_seq;
create sequence pq_match_seq;

create or replace trigger pq_match_trig
  before insert on prefix_quiz_match
  referencing new as new_row
  for each row
  begin
    select pq_match_seq.nextval into :new_row.id from dual;
  end;
.
/

create index question4 on prefix_quiz_match(question);

insert into prefix_quiz_match(question,subquestions) values(1,'1');
insert into prefix_quiz_match(question,subquestions) values(2,'2');
insert into prefix_quiz_match(question,subquestions) values(3,'3');
insert into prefix_quiz_match(question,subquestions) values(4,'4');

rem --------------------------------------------------------
rem
rem Table structure for table quiz_match_sub
rem

drop TABLE prefix_quiz_match_sub;
CREATE TABLE prefix_quiz_match_sub (
  id number(10)  primary key,
  question number(10)  default '0' not null,
  questiontext varchar2(1024) NOT NULL,
  answertext varchar2(255) default '' not null
);

COMMENT on table prefix_quiz_match_sub is 'Defines the subquestions that make up a matching question';

CREATE index question6 on prefix_quiz_match_sub (question);

drop sequence pq_match_sub_seq;
create sequence pq_match_sub_seq;

create or replace trigger pq_match_sub_trig
  before insert on prefix_quiz_match_sub
  referencing new as new_row
  for each row
  begin
    select pq_match_sub_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_quiz_match_sub (question,questiontext,answertext) values(1,'1','1');
insert into prefix_quiz_match_sub (question,questiontext,answertext) values(2,'2','2');
insert into prefix_quiz_match_sub (question,questiontext,answertext) values(3,'3','3');
insert into prefix_quiz_match_sub (question,questiontext,answertext) values(4,'4','4');

select * from prefix_quiz_match_sub order by 1,2;

rem --------------------------------------------------------
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

CREATE index question7 on prefix_quiz_multichoice(question);

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

select * from prefix_quiz_multichoice order by 1,2;

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

CREATE index quiz1 on prefix_quiz_question_grades(quiz);
CREATE index question8 on prefix_quiz_question_grades(question);

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

select * from prefix_quiz_question_grades order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table quiz_questions
rem

drop TABLE prefix_quiz_questions;
CREATE TABLE prefix_quiz_questions (
  id number(10) primary key,
  category number(10) default '0' not null,
  name varchar(255) default '' not null,
  questiontext varchar2(1024) NOT NULL,
  image varchar(255) default '' not null,
  defaultgrade number(4) default '1' not null,
  qtype number(6) default '0' not null,
  stamp varchar2(255) default '' not null,
  version number(10) default '1' not null
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

insert into prefix_quiz_questions (category,name,questiontext,image,defaultgrade,qtype,stamp,version) values(1,'1','1','1',1,1,'1',1);
insert into prefix_quiz_questions (category,name,questiontext,image,defaultgrade,qtype,stamp,version) values(2,'2','2','2',2,2,'2',2);
insert into prefix_quiz_questions (category,name,questiontext,image,defaultgrade,qtype,stamp,version) values(3,'3','3','3',3,3,'3',3);
insert into prefix_quiz_questions (category,name,questiontext,image,defaultgrade,qtype,stamp,version) values(4,'4','4','4',4,4,'4',4);

select * from prefix_quiz_questions order by 1,2;

rem --------------------------------------------------------
rem
rem Table structure for table quiz_randomsamatch
rem

drop TABLE prefix_quiz_randomsamatch;
CREATE TABLE prefix_quiz_randomsamatch (
  id number(10) primary key,
  question number(10) default '0' not null,
  choose number(4) DEFAULT '4' NOT NULL
);

COMMENT on table prefix_quiz_randomsamatch is 'Info about a random short-answer matching question';

drop sequence pq_quiz_randoms_seq;
create sequence pq_quiz_randoms_seq;

create or replace trigger pq_quiz_randoms_trig
  before insert on prefix_quiz_randomsamatch
  referencing new as new_row
  for each row
  begin
    select pq_quiz_randoms_seq.nextval into :new_row.id from dual;
  end;
.

create index question0 on prefix_quiz_randomsamatch (question);

insert into prefix_quiz_randomsamatch(id,question,choose) values(1,1,1);
insert into prefix_quiz_randomsamatch(id,question,choose) values(2,2,2);
insert into prefix_quiz_randomsamatch(id,question,choose) values(3,3,3);
insert into prefix_quiz_randomsamatch(id,question,choose) values(4,4,4);

select * from prefix_quiz_randomsamatch order by 1,2;

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

create index attempt on prefix_quiz_responses(attempt);
create index question1 on prefix_quiz_responses(question);

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
insert into prefix_quiz_responses (attempt,question,answer,grade) values (1,1,'answer1','grade1');
insert into prefix_quiz_responses (attempt,question,answer,grade) values (2,2,'answer2','grade2');
insert into prefix_quiz_responses (attempt,question,answer,grade) values (3,3,'answer3','grade3');
insert into prefix_quiz_responses (attempt,question,answer,grade) values (4,4,'answer4','grade4');

select * from prefix_quiz_responses order by 1,2;

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

create index question2 on prefix_quiz_shortanswer(question);

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

select * from prefix_quiz_shortanswer order by 1,2;

rem --------------------------------------------------------


rem
rem Table structure for table quiz_numerical
rem

drop TABLE prefix_quiz_numerical;
CREATE TABLE prefix_quiz_numerical (
  id number(10) primary key,
  question number(10) default '0' not null,
  answer number(10) default '0' not null,
  min varchar2(255) default '' not null,
  max varchar2(255) default '' not null
);

COMMENT on table prefix_quiz_numerical is 'Options for numerical questions';

create index answer on prefix_quiz_numerical(answer);

drop sequence pq_numerical_seq;
create sequence pq_numerical_seq;

create or replace trigger pq_numerical_trig
  before insert on prefix_quiz_numerical
  referencing new as new_row
  for each row
  begin
    select pq_numerical_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_quiz_numerical (question,answer,min,max) values (1,1,'1','1');
insert into prefix_quiz_numerical (question,answer,min,max) values (2,2,'2','2');
insert into prefix_quiz_numerical (question,answer,min,max) values (3,3,'3','3');
insert into prefix_quiz_numerical (question,answer,min,max) values (4,4,'4','4');

select * from prefix_quiz_numerical order by 1,2;

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

create index question3 on prefix_quiz_truefalse(question);

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

select * from prefix_quiz_truefalse order by 1,2;

rem --------------------------------------------------------
rem
rem Table structure for table quiz_multianswers
rem

drop TABLE prefix_quiz_multianswers;
CREATE TABLE prefix_quiz_multianswers (
  id number(10)  primary key,
  question number(10)  default '0' not null,
  trueanswer number(10)  default '0' not null,
  falseanswer number(10) default '0' not null
);

COMMENT on table prefix_quiz_multianswers is 'Options for True-False questions';

create index question5 on prefix_quiz_multianswers(question);

drop sequence pq_multianswers_seq;
create sequence pq_multianswers_seq;

create or replace trigger pq_multianswers_trig
  before insert on prefix_quiz_multianswers
  referencing new as new_row
  for each row
  begin
    select pq_multianswers_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_quiz_multianswers (question,trueanswer,falseanswer) values (1,1,1);
insert into prefix_quiz_multianswers (question,trueanswer,falseanswer) values (2,2,2);
insert into prefix_quiz_multianswers (question,trueanswer,falseanswer) values (3,3,3);
insert into prefix_quiz_multianswers (question,trueanswer,falseanswer) values (4,4,4);

select * from prefix_quiz_multianswers order by 1,2;

delete from prefix_log_display where module='quiz';
INSERT INTO prefix_log_display VALUES ('quiz', 'view', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'report', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'attempt', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('quiz', 'submit', 'quiz', 'name');

select * from prefix_log_display where module='quiz' order by 1,2;

