rem
rem Table structure for table survey
rem

drop TABLE prefix_survey;
CREATE TABLE prefix_survey (
  id number(10) primary key,
  course number(10) default '0' not null,
  template number(10) default '0' not null,
  days number(6) default '0' not null,
  timecreated number(10) default '0' not null,
  timemodified number(10) default '0' not null,
  name varchar2(255) default '' not null,
  intro varchar2(1024),
  questions varchar2(255) default NULL
);

drop sequence p_survey_seq;
create sequence p_survey_seq;

create or replace trigger p_survey_trig
  before insert on prefix_survey
  referencing new as new_row
  for each row
  begin
    select p_survey_seq.nextval into :new_row.id from dual;
  end;
.
/

COMMENT on table prefix_survey is 'all surveys';

INSERT INTO prefix_survey (course, template, days, timecreated, timemodified, name, intro, questions) VALUES (0, 0, 0, 985017600, 985017600, 'collesaname', 'collesaintro', '25,26,27,28,29,30,43,44');
INSERT INTO prefix_survey (course, template, days, timecreated, timemodified, name, intro, questions) VALUES (0, 0, 0, 985017600, 985017600, 'collespname', 'collespintro', '31,32,33,34,35,36,43,44');
INSERT INTO prefix_survey (course, template, days, timecreated, timemodified, name, intro, questions) VALUES (0, 0, 0, 985017600, 985017600, 'collesapname', 'collesapintro', '37,38,39,40,41,42,43,44');
INSERT INTO prefix_survey (course, template, days, timecreated, timemodified, name, intro, questions) VALUES (0, 0, 0, 985017600, 985017600, 'attlsname', 'attlsintro', '65,67,68');

select * from prefix_survey order by 1,2;

rem
rem Table structure for table survey_analysis
rem

drop TABLE prefix_survey_analysis;
CREATE TABLE prefix_survey_analysis (
id number(10) primary key,
survey number(10) default '0' not null,
userid number(10) default '0' not null,
notes varchar2(1024) NOT NULL

drop sequence p_survey_analysis_seq;
create sequence p_survey_analysis_seq;

create or replace trigger p_survey_analysis_trig
  before insert on prefix_survey_analysis
  referencing new as new_row
  for each row
  begin
    select p_survey_analysis_seq.nextval into :new_row.id from dual;
  end;
.
/

);

comment on table prefix_survey_analysis is 'Survey analysis';

rem
rem Dumping data for table survey_analysis
rem

rem --------------------------------------------------------

rem
rem Table structure for table survey_answers
rem

drop TABLE prefix_survey_answers;
CREATE TABLE prefix_survey_answers (
id number(10) primary key,
userid number(10) default '0' not null,
survey number(10) default '0' not null,
question number(10) default '0' not null,
time number(10) default NULL,
answer1 varchar2(255) default NULL,
answer2 varchar2(255) default NULL
);

drop sequence p_survey_answers_seq;
create sequence p_survey_answers_seq;

create or replace trigger p_survey_answers_trig
before insert on prefix_survey_answers
referencing new as new_row
for each row
begin
  select p_survey_answers_seq.nextval into :new_row.id from dual;
end;
.
/


rem
rem Dumping data for table survey_answers
rem

rem --------------------------------------------------------

rem
rem Table structure for table survey_questions
rem

drop TABLE prefix_survey_questions;
CREATE TABLE prefix_survey_questions (
id number(10) primary key,
text varchar2(255) default '' not null,
shorttext varchar2(30) default '' not null,
multi varchar2(100) default '' not null,
intro varchar2(50) default NULL,
type number(3) default '0' not null,
options varchar2(1024)
);

comment on table prefix_survey_questions is 'structure for survey_questions';

drop sequence p_survey_questions_seq;
create sequence p_survey_questions_seq;

create or replace trigger p_survey_questions_trig
  before insert on prefix_survey_questions
  referencing new as new_row
  for each row
  begin
    select p_survey_questions_seq.nextval into :new_row.id from dual;
  end;
.
/

rem
rem Dumping data for table survey_questions
rem

INSERT INTO prefix_survey_questions ( text, shorttext, multi, intro, type, options) VALUES ('colles1', 'colles1short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles2', 'colles2short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles3', 'colles3short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles4', 'colles4short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles5', 'colles5short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles6', 'colles6short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles7', 'colles7short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles8', 'colles8short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles9', 'colles9short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ( 'colles10', 'colles10short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ( 'colles11', 'colles11short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ( 'colles12', 'colles12short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ( 'colles13', 'colles13short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles14', 'colles14short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles15', 'colles15short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles16', 'colles16short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles17', 'colles17short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles18', 'colles18short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles19', 'colles19short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles20', 'colles20short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles21', 'colles21short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles22', 'colles22short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles23', 'colles23short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('colles24', 'colles24short', '1', '1', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm1', 'collesm1short', '1,2,3,4', 'collesmintro', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm2', 'collesm2short', '5,6,7,8', 'collesmintro', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm3', 'collesm3short', '9,10,11,12', 'collesmintro', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm4', 'collesm4short', '13,14,15,16', 'collesmintro', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm5', 'collesm5short', '17,18,19,20', 'collesmintro', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm6', 'collesm6short', '21,22,23,24', 'collesmintro', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm1', 'collesm1short', '1,2,3,4', 'collesmintro', 2, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm2', 'collesm2short', '5,6,7,8', 'collesmintro', 2, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm3', 'collesm3short', '9,10,11,12', 'collesmintro', 2, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm4', 'collesm4short', '13,14,15,16', 'collesmintro', 2, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm5', 'collesm5short', '17,18,19,20', 'collesmintro', 2, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm6', 'collesm6short', '21,22,23,24', 'collesmintro', 2, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm1', 'collesm1short', '1,2,3,4', 'collesmintro', 3, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm2', 'collesm2short', '5,6,7,8', 'collesmintro', 3, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm3', 'collesm3short', '9,10,11,12', 'collesmintro', 3, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm4', 'collesm4short', '13,14,15,16', 'collesmintro', 3, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm5', 'collesm5short', '17,18,19,20', 'collesmintro', 3, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('collesm6', 'collesm6short', '21,22,23,24', 'collesmintro', 3, 'scaletimes5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('howlong', '1', '1', '1', 1, 'howlongoptions');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('othercomments', '1', '1', '1', 0, '');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls20', 'attls20short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls14', 'attls14short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls15', 'attls15short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls16', 'attls16short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls17', 'attls17short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls18', 'attls18short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls19', 'attls19short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls12', 'attls12short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls13', 'attls13short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls11', 'attls11short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls10', 'attls10short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls9', 'attls9short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls8', 'attls8short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls7', 'attls7short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls6', 'attls6short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls5', 'attls5short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls4', 'attls4short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls3', 'attls3short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls1', 'attls1short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attls2', 'attls2short', '1', '1', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attlsm1', 'attlsm1', '45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64', 'attlsmintro', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attlsm2', 'attlsm2', '63,62,59,57,55,49,52,50,48,47', 'attlsmintro', -1, 'scaleagree5');
INSERT INTO prefix_survey_questions (text, shorttext, multi, intro, type, options) VALUES ('attlsm3', 'attlsm3', '46,54,45,51,60,53,56,58,61,64', 'attlsmintro', -1, 'scaleagree5');

rem select * from prefix_survey_questions where text like 'colles%' or text like 'attlsm%'

col id format 99
select * from prefix_survey_questions;


rem
rem Dumping data for table log_display
rem

delete from prefix_log_display where module = 'survey';
INSERT INTO prefix_log_display VALUES ('survey', 'download', 'survey', 'name');
INSERT INTO prefix_log_display VALUES ('survey', 'view form', 'survey', 'name');
INSERT INTO prefix_log_display VALUES ('survey', 'view graph', 'survey', 'name');
INSERT INTO prefix_log_display VALUES ('survey', 'view report', 'survey', 'name');
INSERT INTO prefix_log_display VALUES ('survey', 'submit', 'survey', 'name');
select * from prefix_log_display where module = 'survey';
