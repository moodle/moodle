rem
rem Table structure for table journal
rem

drop TABLE prefix_journal;
CREATE TABLE prefix_journal (
 id number(10) primary key,
 course number(10) default '0' not null,
 name varchar(255) default NULL,
 intro varchar2(1024),
 days number(5) default '7' not null,
 assessed number(10) default '0' not null,
 timemodified number(10) default '0' not null
);

drop sequence p_journal_seq;
create sequence p_journal_seq;

create or replace trigger p_journal_trig
  before insert on prefix_journal
  referencing new as new_row
  for each row
  begin
    select p_journal_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_journal(course,name,intro,days,assessed,timemodified) values(1,'1','1',1,1,1);
insert into prefix_journal(course,name,intro,days,assessed,timemodified) values(2,'2','2',2,2,2);
insert into prefix_journal(course,name,intro,days,assessed,timemodified) values(3,'3','3',3,3,3);
insert into prefix_journal(course,name,intro,days,assessed,timemodified) values(4,'4','4',4,4,4);

select * from prefix_journal order by 1,2;

rem --------------------------------------------------------

rem
rem Table structure for table journal_entries
rem

drop TABLE prefix_journal_entries;
CREATE TABLE prefix_journal_entries (
 id number(10) primary key,
 journal number(10) default '0' not null,
 userid number(10) default '0' not null,
 modified number(10) default '0' not null,
 text varchar2(1024) NOT NULL,
 format number(2) default '0' not null,
 rating number(10) default '0',
 commentt varchar2(1024),
 teacher number(10) default '0' not null,
 timemarked number(10) default '0' not null,
 mailed number(1) default '0' not null
);

comment on table prefix_journal_entries is 'All the journal entries of all people';

drop sequence p_journal_entries_seq;
create sequence p_journal_entries_seq;

create or replace trigger p_journal_entries_trig
  before insert on prefix_journal_entries
  referencing new as new_row
  for each row
  begin
    select p_journal_entries_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_journal_entries(journal,userid,modified,text,format,rating,commentt,teacher,timemarked,mailed) values(1,1,1,'1',1,1,'1',1,1,1);
insert into prefix_journal_entries(journal,userid,modified,text,format,rating,commentt,teacher,timemarked,mailed) values(2,2,2,'2',2,2,'2',2,2,2);
insert into prefix_journal_entries(journal,userid,modified,text,format,rating,commentt,teacher,timemarked,mailed) values(3,3,3,'3',3,3,'3',3,3,3);
insert into prefix_journal_entries(journal,userid,modified,text,format,rating,commentt,teacher,timemarked,mailed) values(4,4,4,'4',4,4,'4',4,4,4);

select * from prefix_journal_entries order by 1,2;

rem
rem Dumping data for table log_display
rem
delete from prefix_log_display where module = 'journal';
INSERT INTO prefix_log_display VALUES ('journal', 'view', 'journal', 'name');
INSERT INTO prefix_log_display VALUES ('journal', 'add entry', 'journal', 'name');
INSERT INTO prefix_log_display VALUES ('journal', 'update entry', 'journal', 'name');
INSERT INTO prefix_log_display VALUES ('journal', 'view responses', 'journal', 'name');

col module format a10
select * from prefix_log_display where module = 'journal';
