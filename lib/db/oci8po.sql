rem   DRAFT DRAFT DRAFT DRAFT - untested

rem
rem Table structure for table config
rem

drop TABLE prefix_config;
CREATE TABLE prefix_config (
  id number(10) primary key,
  name varchar2(255) default '' not null constraint unq_name unique,
  value varchar2(255) default '' not null
);

COMMENT on table prefix_config is 'Moodle configuration variables';

drop sequence p_config_seq;
create sequence p_config_seq;

create or replace trigger p_config_trig
  before insert on prefix_config
  referencing new as new_row
  for each row
  begin
    select p_config_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_config (name,value) values ('Name1','Value1');
insert into prefix_config (name,value) values ('Name2','Value2');
insert into prefix_config (name,value) values ('Name3','Value3');

rem testing unique column constraint on name: this should fail
insert into prefix_config (name,value) values ('Name1','Value4');


select * from prefix_config order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table course
rem

drop TABLE prefix_course;
CREATE TABLE prefix_course (
  id number(10) primary key,
  category number(10) default '0' not null,
  sortorder number(10) default '0' not null,
  password varchar2(50) default '' not null,
  fullname varchar2(254) default '' not null,
  shortname varchar2(15) default '' not null,
  summary varchar2(254) not null,
  format varchar2(10) default 'topics' not null,
  showgrades number(2) default '1' not null,
  modinfo varchar2(1024)  not null,
  newsitems number(5) default '1' not null,
  teacher varchar2(100) default 'Teacher' not null,
  teachers varchar2(100) default 'Teachers' not null,
  student varchar2(100) default 'Student' not null,
  students varchar2(100) default 'Students' not null,
  guest number(2) default '0' not null,
  startdate number(10) default '0' not null,
  numsections number(5) default '1' not null,
  marker number(10) default '0' not null,
  visible number(10) default '1' not null,
  timecreated number(10) default '0' not null,
  timemodified number(10) default '0' not null
);

create index category on prefix_course(category);

COMMENT on table prefix_course is 'Moodle prefix course table';

drop sequence p_course_seq;
create sequence p_course_seq;

create or replace trigger p_course_trig
  before insert on prefix_course
  referencing new as new_row
  for each row
  begin
    select p_course_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_course ( category,password,fullname,shortname,summary,modinfo) values ( 1,'password1','fullname1','shortname1','summary1','modinfo1');
insert into prefix_course ( category,password,fullname,shortname,summary,format,modinfo) values ( 2,'password2','fullname2','shortname2','summary2','social','modinfo2');
insert into prefix_course ( category,password,fullname,shortname,summary,format,modinfo) values ( 2,'password2','fullname2','shortname2','summary2','topics','modinfo2');

select * from prefix_course order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table course_categories
rem

drop TABLE prefix_course_categories;
CREATE TABLE prefix_course_categories (
  id number(10) primary key,
  name varchar2(255) default '' not null,
  description varchar2(1024),
  parent number(10) default '0' not null,
  sortorder number(10) default '0' not null,
  coursecount number(10) default '0' not null,
  visible number(1) default '1' not null,
  timemodified number(10) default '0' not null
);

COMMENT on table prefix_course_categories is 'Course categories';

drop sequence p_course_categories_seq;
create sequence p_course_categories_seq;

create or replace trigger p_course_categories_trig
  before insert on prefix_course_categories
  referencing new as new_row
  for each row
  begin
    select p_course_categories_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_course_categories (name) values ('name1');
insert into prefix_course_categories (name) values ('name2');
insert into prefix_course_categories (name) values ('name3');
insert into prefix_course_categories (name) values ('name4');


select * from prefix_course_categories;

rem
rem Table structure for table course_display
rem

drop TABLE prefix_course_display;
CREATE TABLE prefix_course_display (
  id number(10) primary key,
  course number(10) default '0' not null,
  userid number(10) default '0' not null,
  display number(10) default '0' not null
);

drop sequence p_course_display_seq;
create sequence p_course_display_seq;

create or replace trigger p_course_display_trig
  before insert on prefix_course_display
  referencing new as new_row
  for each row
  begin
    select p_course_display_seq.nextval into :new_row.id from dual;
  end;
.
/

create index courseuserid on prefix_course_display(course,userid);

COMMENT on table prefix_course_display is 'Stores info about how to display the course';

insert into prefix_course_display (course,userid,display) values (1,1,1);
insert into prefix_course_display (course,userid,display) values (2,2,2);
insert into prefix_course_display (course,userid,display) values (3,3,3);
insert into prefix_course_display (course,userid,display) values (4,4,4);

select * from prefix_course_display;

rem --------------------------------------------------------

rem
rem Table structure for table course_modules
rem

drop TABLE prefix_course_modules;
CREATE TABLE prefix_course_modules (
  id number(10) primary key,
  course number(10) default '0' not null,
  module number(10) default '0' not null,
  instance number(10) default '0' not null,
  section number(10) default '0' not null,
  added number(10) default '0' not null,
  deleted number(1) default '0' not null,
  score number(4) default '0' not null,
  indent number(5) default '0' not null,
  visible number(1) default '1' not null
);

COMMENT on table prefix_course_modules is 'prefix_course_modules';

drop sequence p_course_modules_seq;
create sequence p_course_modules_seq;

create or replace trigger p_course_modules_trig
  before insert on prefix_course_modules
  referencing new as new_row
  for each row
  begin
    select p_course_modules_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_course_modules (course,module,instance,section,added,deleted,score,indent,visible) values (1,1,1,1,1,1,1,1,1);
insert into prefix_course_modules (course,module,instance,section,added,deleted,score,indent,visible) values (2,2,2,2,2,2,2,2,2);
insert into prefix_course_modules (course,module,instance,section,added,deleted,score,indent,visible) values (3,3,3,3,3,3,3,3,3);
insert into prefix_course_modules (course,module,instance,section,added,deleted,score,indent,visible) values (4,4,4,4,4,4,4,4,4);

select * from prefix_course_modules;

rem --------------------------------------------------------
rem
rem Table structure for table course_sections
rem

drop TABLE prefix_course_sections;
CREATE TABLE prefix_course_sections (
  id number(10) primary key,
  course number(10) default '0' not null,
  section number(10) default '0' not null,
  summary varchar2(254) NOT NULL,
  sequence varchar2(255) default '' not null,
  visible number(1) default '1' not null
);

COMMENT on table prefix_course_sections is 'prefix_course_sections';

drop sequence p_course_sections_seq;
create sequence p_course_sections_seq;

create or replace trigger p_course_sections_trig
  before insert on prefix_course_sections
  referencing new as new_row
  for each row
  begin
    select p_course_sections_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_course_sections (course,section,summary,sequence) values (1,1,1,1);
insert into prefix_course_sections (course,section,summary,sequence) values (2,2,2,2);
insert into prefix_course_sections (course,section,summary,sequence) values (3,3,3,3);
insert into prefix_course_sections (course,section,summary,sequence) values (4,4,4,4);

select * from prefix_course_sections;

rem --------------------------------------------------------

rem
rem Table structure for table log
rem

drop TABLE prefix_log;
CREATE TABLE prefix_log (
  id number(10) primary key,
  time number(10) default '0' not null,
  userid number(10) default '0' not null,
  ip varchar2(15) default '' not null,
  course number(10) default '0' not null,
  module varchar2(10) default '' not null,
  action varchar2(15) default '' not null,
  url varchar2(100) default '' not null,
  info varchar2(255) default '' not null
);

col module format a10

create index timecoursemoduleaction on prefix_log(time,course,module,action);

create index coursemoduleaction on prefix_log(course,module,action);

create index courseuserid0 on prefix_log(course,userid);

COMMENT on table prefix_log is 'Every action is logged as far as possible.';

drop sequence p_log_seq;
create sequence p_log_seq;

create or replace trigger p_log_trig
  before insert on prefix_log
  referencing new as new_row
  for each row
  begin
    select p_log_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_log (time,userid,ip,course,module,action,url,info) values (1,1,'ip1',1,'module1','action1','url1','info1');
insert into prefix_log (time,userid,ip,course,module,action,url,info) values (2,2,'ip2',2,'module2','action2','url2','info2');
insert into prefix_log (time,userid,ip,course,module,action,url,info) values (3,3,'ip3',3,'module3','action3','url3','info3');
insert into prefix_log (time,userid,ip,course,module,action,url,info) values (4,4,'ip4',4,'module4','action4','url4','info4');

select * from prefix_log;

rem --------------------------------------------------------

rem
rem Table structure for table log_display
rem

drop TABLE prefix_log_display;
CREATE TABLE prefix_log_display (
  module varchar2(20) default '' not null,
  action varchar2(20) default '' not null,
  mtable varchar2(20) default '' not null,
  field varchar2(40) default '' not null
) ;

COMMENT on table prefix_log_display is 'For a particular module/action, specifies a moodle table/field.';

rem for testing only
rem insert into prefix_log_display (module,action,mtable,field) values ('module1','action1','mtable1','field1');
rem insert into prefix_log_display (module,action,mtable,field) values ('module2','action2','mtable2','field2');
rem insert into prefix_log_display (module,action,mtable,field) values ('module3','action3','mtable3','field3');
rem insert into prefix_log_display (module,action,mtable,field) values ('module4','action4','mtable4','field4');

select * from prefix_log_display;

rem --------------------------------------------------------

rem
rem Table structure for table modules
rem

drop TABLE prefix_modules;
CREATE TABLE prefix_modules (
  id number(10) primary key,
  name varchar2(20) default '' not null,
  version number(10) default '0' not null,
  cron number(10) default '0' not null,
  lastcron number(10) default '0' not null,
  search varchar2(255) default '' not null,
  visible number(1) default '1' not null
);

drop sequence p_modules_seq;
create sequence p_modules_seq;

create or replace trigger p_modules_trig
  before insert on prefix_modules
  referencing new as new_row
  for each row
  begin
    select p_modules_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_modules (name,version,cron,lastcron,search) values ('name1',1,1,1,'search1');
insert into prefix_modules (name,version,cron,lastcron,search) values ('name2',2,2,2,'search2');
insert into prefix_modules (name,version,cron,lastcron,search) values ('name3',3,3,3,'search3');
insert into prefix_modules (name,version,cron,lastcron,search) values ('name4',4,4,4,'search4');

select * from prefix_modules;

rem --------------------------------------------------------

drop TABLE prefix_scale;
CREATE TABLE prefix_scale (
  id number(10) primary key,
  courseid number(10) default '0' not null,
  userid number(10) default '0' not null,
  name varchar2(255) default '' not null,
  scale varchar2(1024),
  description varchar2(1024),
  timemodified number(10) default '0' not null
);

COMMENT on table prefix_scale is 'Defines grading scales';

drop sequence p_scale_seq;
create sequence p_scale_seq;

create or replace trigger p_scale_trig
  before insert on prefix_scale
  referencing new as new_row
  for each row
  begin
    select p_scale_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_scale( courseid, userid, name, scale, description, timemodified) values(1,1,'1','1','1',1);
insert into prefix_scale( courseid, userid, name, scale, description, timemodified) values(2,2,'2','2','2',2);
insert into prefix_scale( courseid, userid, name, scale, description, timemodified) values(3,3,'3','3','3',3);
insert into prefix_scale( courseid, userid, name, scale, description, timemodified) values(4,4,'4','4','4',4);

select * from prefix_scale order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table user
rem

drop TABLE prefix_user;
CREATE TABLE prefix_user (
  id number(10) primary key,
  confirmed number(1) default '0' not null,
  deleted number(1) default '0' not null,
  username varchar2(100) default '' not null
    constraint unq_username unique,
  password varchar2(32) default '' not null,
  idnumber varchar2(12) default NULL,
  firstname varchar2(20) default '' not null,
  lastname varchar2(20) default '' not null,
  email varchar2(100) default '' not null,
  icq varchar2(15) default NULL,
  phone1 varchar2(20) default NULL,
  phone2 varchar2(20) default NULL,
  institution varchar2(40) default NULL,
  department varchar2(30) default NULL,
  address varchar2(70) default NULL,
  city varchar2(20) default NULL,
  country varchar2(2) default NULL,
  lang varchar2(5) default 'en',
  timezone float default '99' not null,
  firstaccess number(10) default '0' not null,
  lastaccess number(10) default '0' not null,
  lastlogin number(10) default '0' not null,
  currentlogin number(10) default '0' not null,
  lastIP varchar2(15) default NULL,
  secret varchar2(15) default NULL,
  picture number(1) default NULL,
  url varchar2(255) default NULL,
  description varchar2(255),
  mailformat number(1) default '1' not null,
  maildisplay number(2) default '2' not null,
  htmleditor number(1) default '1' not null,
  autosubscribe number(1) default '1' not null,
  timemodified number(10) default '0' not null
) ;

COMMENT on table prefix_user is 'One record for each person';

drop sequence p_user_seq;
create sequence p_user_seq;

create or replace trigger p_user_trig
  before insert on prefix_user
  referencing new as new_row
  for each row
  begin
    select p_user_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_user ( confirmed, deleted, username, password, idnumber, firstname, lastname, email, icq, phone1, phone2, institution, department, address, city, country, lang, timezone, firstaccess, lastaccess, lastlogin, currentlogin, lastIP, secret, picture, url, description, mailformat, maildisplay, htmleditor, timemodified ) values ( 1, 1, 'username1', 'password1', 'idnumber1', 'firstname1', 'lastname1', 'email1', 'icq1', 'phone11', 'phone21', 'institution1', 'department1', 'address1', 'city1', 'c1', 'lang1', 1, 1, 1, 1, 1, 'lastIP1', 'secret1', 1, 'url1', 'description1', 1, 1, 1, 1);
insert into prefix_user ( confirmed, deleted, username, password, idnumber, firstname, lastname, email, icq, phone1, phone2, institution, department, address, city, country, lang, timezone, firstaccess, lastaccess, lastlogin, currentlogin, lastIP, secret, picture, url, description, mailformat, maildisplay, htmleditor, timemodified )
values ( 2, 2, 'username2', 'password2', 'idnumber2', 'firstname2', 'lastname2', 'email2', 'icq2', 'phone12', 'phone22', 'institution2', 'department2', 'address2', 'city2', 'c2', 'lang2', 2, 2, 2, 2, 2, 'lastIP2', 'secret2', 2, 'url2', 'description2', 2, 2, 2, 2);
insert into prefix_user ( confirmed, deleted, username, password, idnumber, firstname, lastname, email, icq, phone1, phone2, institution, department, address, city, country, lang, timezone, firstaccess, lastaccess, lastlogin, currentlogin, lastIP, secret, picture, url, description, mailformat, maildisplay, htmleditor, timemodified ) values ( 3, 3, 'username3', 'password3', 'idnumber3', 'firstname3', 'lastname3', 'email3', 'icq3', 'phone13', 'phone23', 'institution3', 'department3', 'address3', 'city3', 'c3', 'lang3', 3, 3, 3, 3, 3, 'lastIP3', 'secret3', 3, 'url3', 'description3', 3, 3, 3, 3);

rem test unique constraint on username: this statement should fail
insert into prefix_user ( confirmed, deleted, username, password, idnumber, firstname, lastname, email, icq, phone1, phone2, institution, department, address, city, country, lang, timezone, firstaccess, lastaccess, lastlogin, currentlogin, lastIP, secret, picture, url, description, mailformat, maildisplay, htmleditor, timemodified ) values ( 4, 4, 'username1', 'password4', 'idnumber4', 'firstname4', 'lastname4', 'email4', 'icq4', 'phone14', 'phone24', 'institution4', 'department4', 'address4', 'city4', 'c4', 'lang4', 4, 4, 4, 4, 4, 'lastIP4', 'secret4', 4, 'url4', 'description4', 4, 4, 4, 4);

select * from prefix_user;

rem --------------------------------------------------------

rem
rem Table structure for table user_admins
rem

drop TABLE prefix_user_admins;
CREATE TABLE prefix_user_admins (
  id number(10) primary key,
  userid number(10) default '0' not null
);

COMMENT on table prefix_user_admins is 'One record per administrator user';

drop sequence p_user_admins_seq;
create sequence p_user_admins_seq;

create or replace trigger p_user_admins_trig
  before insert on prefix_user_admins
  referencing new as new_row
  for each row
  begin
    select p_user_admins_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_user_admins (userid) values (1);
insert into prefix_user_admins (userid) values (2);
insert into prefix_user_admins (userid) values (3);
insert into prefix_user_admins (userid) values (4);

select * from prefix_user_admins;

rem --------------------------------------------------------

rem
rem Table structure for table user_students
rem

drop TABLE prefix_user_students;
CREATE TABLE prefix_user_students (
  id number(10) primary key,
  userid number(10) default '0' not null,
  course number(10) default '0' not null,
  timestart number(10) default '0' not null,
  timeend number(10) default '0' not null,
  time number(10) default '0' not null
);

create index courseuserid1 on prefix_user_students(course,userid);

drop sequence p_user_students_seq;
create sequence p_user_students_seq;

create or replace trigger p_user_students_trig
  before insert on prefix_user_students
  referencing new as new_row
  for each row
  begin
    select p_user_students_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_user_students (userid,course,timestart,timeend,time) values (1,1,1,1,1);
insert into prefix_user_students (userid,course,timestart,timeend,time) values (2,2,2,2,2);
insert into prefix_user_students (userid,course,timestart,timeend,time) values (3,3,3,3,3);
insert into prefix_user_students (userid,course,timestart,timeend,time) values (4,4,4,4,4);

select * from prefix_user_students;


rem --------------------------------------------------------

rem
rem Table structure for table user_teachers
rem

drop TABLE prefix_user_teachers;
CREATE TABLE prefix_user_teachers (
  id number(10) primary key,
  userid number(10) default '0' not null,
  course number(10) default '0' not null,
  authority number(10) default '3' not null,
  role varchar2(40) default '' not null,
  editall number(1) default '1' not null,
  timemodified number(10) default '0' not null
);

create index courseuserid2 on prefix_user_teachers(course,userid);

COMMENT on table prefix_user_teachers is 'One record per teacher per course';

drop sequence p_user_teachers_seq;
create sequence p_user_teachers_seq;

create or replace trigger p_user_teachers_trig
  before insert on prefix_user_teachers
  referencing new as new_row
  for each row
  begin
    select p_user_teachers_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_user_teachers (userid,course,authority,role) values (1,1,1,'role1');
insert into prefix_user_teachers (userid,course,authority,role) values (2,2,2,'role2');
insert into prefix_user_teachers (userid,course,authority,role) values (3,3,3,'role3');
insert into prefix_user_teachers (userid,course,authority,role) values (3,3,3,'role3');

select * from prefix_user_teachers;

rem
rem Table structure for table user_coursecreators
rem

drop TABLE prefix_user_coursecreators;
CREATE TABLE prefix_user_coursecreators (
  id number(10) primary key,
  userid number(10) default '0' not null
);

COMMENT on table prefix_user_coursecreators is 'One record per course creator';
drop sequence p_user_coursecreators_seq;
create sequence p_user_coursecreators_seq;

create or replace trigger p_user_coursecreators_trig
  before insert on prefix_user_coursecreators
  referencing new as new_row
  for each row
  begin
    select p_user_coursecreators_seq.nextval into :new_row.id from dual;
  end;
.
/
insert into prefix_user_coursecreators (userid) values (1);
insert into prefix_user_coursecreators (userid) values (2);
insert into prefix_user_coursecreators (userid) values (3);
insert into prefix_user_coursecreators (userid) values (4);

select * from prefix_user_coursecreators;

rem --------------------------------------------------------

INSERT INTO prefix_log_display VALUES ('user', 'view', 'user', 'firstname'||' '||'lastname');
INSERT INTO prefix_log_display VALUES ('course', 'view', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'update', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'enrol', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'update', 'course', 'fullname');

select * from prefix_log_display where module in ('user','course') order by 1,2;
