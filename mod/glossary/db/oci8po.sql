rem This file contains a complete database schema for all the 
rem tables used by this module, written in SQL

rem It may also contain INSERT statements for particular data 
rem that may be used, especially new entries in the table log_display

rem
rem Table structure for table glossary
rem

drop TABLE prefix_glossary;
CREATE TABLE prefix_glossary (
  id number(10) primary key,
  course number(10) default '0' not null,
  name varchar2(255) default '' not null,
  intro varchar2(255) default '' not null,
  studentcanpost number(2) default '0' not null,
  allowduplicatedentries number(2) default '0' not null,
  displayformat number(2) default '0' not null,
  mainglossary number(2) default '0' not null,
  showspecial number(2) default '1' not null,
  showall number(2) default '1' not null,
  showalphabet number(2) default '1' not null,
  rsstype number(2) default '0' NOT NULL,
  rssarticles number(2) default '0' NOT NULL,
  timecreated number(10) default '0' not null,
  timemodified number(10) default '0' not null
);

COMMENT on table prefix_glossary is 'all glossaries';

drop sequence p_glossary_seq;
create sequence p_glossary_seq;

create or replace trigger p_glossary_trig
  before insert on prefix_glossary
  referencing new as new_row
  for each row
  begin
    select p_glossary_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_glossary(course,name,intro,studentcanpost,allowduplicatedentries,displayformat,mainglossary,showspecial,showall,showalphabet,timecreated,timemodified) values(1,'1','1',1,1,1,1,1,1,1,1,1);
insert into prefix_glossary(course,name,intro,studentcanpost,allowduplicatedentries,displayformat,mainglossary,showspecial,showall,showalphabet,timecreated,timemodified) values(2,'2','2',2,2,2,2,2,2,2,2,2);
insert into prefix_glossary(course,name,intro,studentcanpost,allowduplicatedentries,displayformat,mainglossary,showspecial,showall,showalphabet,timecreated,timemodified) values(3,'3','3',3,3,3,3,3,3,3,3,3);
insert into prefix_glossary(course,name,intro,studentcanpost,allowduplicatedentries,displayformat,mainglossary,showspecial,showall,showalphabet,timecreated,timemodified) values(4,'4','4',4,4,4,4,4,4,4,4,4);

select * from prefix_glossary order by 1,2;

rem
rem Table structure for table glossary_entries
rem

drop TABLE prefix_glossary_entries;
CREATE TABLE prefix_glossary_entries (
  id number(10) primary key,
  glossaryid number(10) default '0' not null,
  userid number(10) default '0' not null,
  concept varchar2(255) default '' not null,
  definition varchar2(1024) NOT NULL,
  format number(2) default '0' not null,
  attachment varchar2(100) default '' not null,
  timecreated number(10) default '0' not null,
  timemodified number(10) default '0' not null,
  teacherentry number(2) default '0' not null,
  sourceglossaryid number(10) default '0' not null
);

COMMENT on table prefix_glossary_entries is 'all glossary entries';

drop sequence p_glossary_entries_seq;
create sequence p_glossary_entries_seq;

create or replace trigger p_glossary_entries_trig
  before insert on prefix_glossary_entries
  referencing new as new_row
  for each row
  begin
    select p_glossary_entries_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_glossary_entries(glossaryid,userid,concept,definition,format,attachment,timecreated,timemodified,teacherentry,sourceglossaryid) values(1,1,'1','1',1,'1',1,1,1,1);
insert into prefix_glossary_entries(glossaryid,userid,concept,definition,format,attachment,timecreated,timemodified,teacherentry,sourceglossaryid) values(2,2,'2','2',2,'2',2,2,2,2);
insert into prefix_glossary_entries(glossaryid,userid,concept,definition,format,attachment,timecreated,timemodified,teacherentry,sourceglossaryid) values(3,3,'3','3',3,'3',3,3,3,3);
insert into prefix_glossary_entries(glossaryid,userid,concept,definition,format,attachment,timecreated,timemodified,teacherentry,sourceglossaryid) values(4,4,'4','4',4,'4',4,4,4,4);

col format format 99
select * from prefix_glossary_entries order by 1,2;

rem
rem Table structure for table glossary_cageories
rem

drop TABLE prefix_glossary_categories;
CREATE TABLE prefix_glossary_categories (
     id number(10) primary key,
     glossaryid number(10) default '0' not null,
     name varchar(255) default '' not null
);

COMMENT on table prefix_glossary_categories is 'all categories for glossary entries';

drop sequence p_glossary_categories_seq;
create sequence p_glossary_categories_seq;

create or replace trigger p_glossary_categories_trig
  before insert on prefix_glossary_categories
  referencing new as new_row
  for each row
  begin
    select p_glossary_categories_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_glossary_categories(glossaryid,name) values(1,'1');
insert into prefix_glossary_categories(glossaryid,name) values(2,'2');
insert into prefix_glossary_categories(glossaryid,name) values(3,'3');
insert into prefix_glossary_categories(glossaryid,name) values(4,'4');

rem
rem Table structure for table glossary_entries_category
rem

drop TABLE prefix_glossary_entries_catego;
CREATE TABLE prefix_glossary_entries_catego (
     id number(10) primary key,
     categoryid number(10) default '0' not null,
     entryid number(10) default '0' not null
);

COMMENT on table prefix_glossary_entries_catego is 'categories of each glossary entry';

drop sequence pg_entries_catego_seq;
create sequence pg_entries_catego_seq;

create or replace trigger pg_entries_catego_trig
  before insert on prefix_glossary_entries_catego
  referencing new as new_row
  for each row
  begin
    select pg_entries_catego_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_glossary_entries_catego(categoryid,entryid) values(1,1);
insert into prefix_glossary_entries_catego(categoryid,entryid) values(2,2);
insert into prefix_glossary_entries_catego(categoryid,entryid) values(3,3);
insert into prefix_glossary_entries_catego(categoryid,entryid) values(4,4);

rem
rem Dumping data for table log_display
rem

INSERT INTO prefix_log_display VALUES ('glossary', 'add', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'update', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'delete', 'glossary', 'name');

INSERT INTO prefix_log_display VALUES ('glossary', 'view', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'view all', 'glossary', 'name');

INSERT INTO prefix_log_display VALUES ('glossary', 'add entry', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'update entry', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'delete entry', 'glossary', 'name');

INSERT INTO prefix_log_display VALUES ('glossary', 'add category', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'update category', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'delete category', 'glossary', 'name');
