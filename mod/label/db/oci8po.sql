drop TABLE prefix_label;
CREATE TABLE prefix_label (
  id number(10) primary key,
  course number(10) default '0' not null,
  name varchar2(255) default '' not null,
  content varchar2(1024) NOT NULL,
  timemodified number(10) default '0' not null
);

COMMENT on table prefix_label is 'Defines labels';

drop sequence p_label_seq;
create sequence p_label_seq;

create or replace trigger p_label_trig
  before insert on prefix_label
  referencing new as new_row
  for each row
  begin
    select p_label_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_label(course,name,content,timemodified) values(1,'1','1',1);
insert into prefix_label(course,name,content,timemodified) values(2,'2','2',2);
insert into prefix_label(course,name,content,timemodified) values(3,'3','3',3);
insert into prefix_label(course,name,content,timemodified) values(4,'4','4',4);

select * from prefix_label order by 1,2;
