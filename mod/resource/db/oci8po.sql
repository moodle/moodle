rem
rem Table structure for table resource
rem

drop TABLE prefix_resource;
CREATE TABLE prefix_resource (
  id number(10) primary key,
  course number(10)  default '0' not null,
  name varchar2(255) default '' not null,
  type number(4) default '0' not null,
  reference varchar2(255) default NULL,
  summary varchar2(1024) NOT NULL,
  alltext varchar2(1024) NOT NULL,
  timemodified number(10)  default '0' not null
);

drop sequence p_resource_seq;
create sequence p_resource_seq;

create or replace trigger p_resource_trig
  before insert on prefix_resource
  referencing new as new_row
  for each row
  begin
    select p_resource_seq.nextval into :new_row.id from dual;
  end;
.
/

comment on table prefix_resource is 'table of resources';

insert into prefix_resource(course,name,type,reference,summary,alltext,timemodified) values(1,'1',1,1,'1','1',1);
insert into prefix_resource(course,name,type,reference,summary,alltext,timemodified) values(2,'2',2,2,'2','2',2);
insert into prefix_resource(course,name,type,reference,summary,alltext,timemodified) values(3,'3',3,3,'3','3',3);
insert into prefix_resource(course,name,type,reference,summary,alltext,timemodified) values(4,'4',4,4,'4','4',4);

select * from prefix_resource order by 1,2;

rem
rem Dumping data for table log_display
rem

delete from prefix_log_display where module = 'resource';
INSERT INTO prefix_log_display VALUES ('resource', 'view', 'resource', 'name');
select * from prefix_log_display where module = 'resource';
