rem
rem Table structure for table prefix_attendance
rem

drop TABLE prefix_attendance;
CREATE TABLE prefix_attendance (
 id number(10) primary key,
 name varchar2(255) default '' not null,
 course number(10) default '0' not null,
 day number(10) default '0' not null,
 hours number(1) default '0' not null,
 roll number(1) default '0' not null,
 notes varchar2(64) default '' not null,
 timemodified number(10) default '0' not null,
 dynsection number(1) default '0' not null,
 edited number(1) default '0' not null,
 autoattend number(1) default '0' not null
);

drop sequence p_attendance_seq;
create sequence p_attendance_seq;

create or replace trigger p_attendance_trig
  before insert on prefix_attendance
  referencing new as new_row
  for each row
  begin
    select p_attendance_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_attendance (name,course,day,hours,roll,notes,timemodified,dynsection,edited) values ('1',1,1,1,1,'1',1,1,1,1);
insert into prefix_attendance (name,course,day,hours,roll,notes,timemodified,dynsection,edited) values ('2',2,2,2,2,'2',2,2,2,2);
insert into prefix_attendance (name,course,day,hours,roll,notes,timemodified,dynsection,edited) values ('3',3,3,3,3,'3',3,3,3,3);
insert into prefix_attendance (name,course,day,hours,roll,notes,timemodified,dynsection,edited) values ('4',4,4,4,4,'4',4,4,4,4);

select * from prefix_attendance order by 1,2;


rem
rem Table structure for table prefix_attendance_roll
rem

drop TABLE prefix_attendance_roll;
CREATE TABLE prefix_attendance_roll (
 id number(11) primary key,
 dayid number(10) default '0' not null,
 userid number(11) default '0' not null,
 hour number(1) default '0' not null,
 status number(11) default '0' not null,
 notes varchar2(64) default '' not null
);

drop sequence p_attendance_roll_seq;
create sequence p_attendance_roll_seq;

create or replace trigger p_attendance_roll_trig
  before insert on prefix_attendance_roll
  referencing new as new_row
  for each row
  begin
    select p_attendance_roll_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_attendance_roll (dayid,userid,hour,status,notes) values (1,1,1,1,'1');
insert into prefix_attendance_roll (dayid,userid,hour,status,notes) values (2,2,2,2,'2');
insert into prefix_attendance_roll (dayid,userid,hour,status,notes) values (3,3,3,3,'3');
insert into prefix_attendance_roll (dayid,userid,hour,status,notes) values (4,4,4,4,'4');

col format format 99
select * from prefix_attendance_roll order by 1,2;

