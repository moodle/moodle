rem
rem Table structure for table chat
rem

drop TABLE prefix_chat;
CREATE TABLE prefix_chat (
  id number(10) primary key,
  course number(10) default '0' not null,
  name varchar2(255) default '' not null,
  intro varchar2(1024) NOT NULL,
  keepdays number(11) default '0' not null,
  studentlogs number(4) default '0' not null,
  chattime number(10) default '0' not null,
  schedule number(4) default '0' not null,
  timemodified number(10) default '0' not null
);


COMMENT on table prefix_chat is 'Each of these is a chat room';

drop sequence p_chat_seq;
create sequence p_chat_seq;

create or replace trigger p_chat_trig
  before insert on prefix_chat
  referencing new as new_row
  for each row
  begin
    select p_chat_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_chat(course,name,intro,keepdays,studentlogs,chattime,schedule,timemodified) values(1,'name 1','intro 1',1,1,1,1,1);
insert into prefix_chat(course,name,intro,keepdays,studentlogs,chattime,schedule,timemodified) values(2,'name 2','intro 2',2,2,2,2,2);
insert into prefix_chat(course,name,intro,keepdays,studentlogs,chattime,schedule,timemodified) values(3,'name 3','intro 3',3,3,3,3,3);
insert into prefix_chat(course,name,intro,keepdays,studentlogs,chattime,schedule,timemodified) values(4,'name 4','intro 4',4,4,4,4,4);

select * from prefix_chat;

rem --------------------------------------------------------
rem
rem Table structure for table chat_messages
rem

drop TABLE prefix_chat_messages;
CREATE TABLE prefix_chat_messages (
  id number(10) primary key,
  chatid number(10) default '0' not null,
  userid number(10) default '0' not null,
  system number(1) default '0' not null,
  message varchar2(1024) NOT NULL,
  timestamp number(10) default '0' not null
);

COMMENT on table prefix_chat_messages is 'Stores all the actual chat messages';

create index timemodifiedchat on prefix_chat_messages(timestamp,chatid);

drop sequence p_chat_messages_seq;
create sequence p_chat_messages_seq;

create or replace trigger p_chat_messages_trig
  before insert on prefix_chat_messages
  referencing new as new_row
  for each row
  begin
    select p_chat_messages_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_chat_messages (chatid,userid,system,message,timestamp) values(1,1,1,'message1',1);
insert into prefix_chat_messages (chatid,userid,system,message,timestamp) values(2,2,2,'message2',2);
insert into prefix_chat_messages (chatid,userid,system,message,timestamp) values(3,3,3,'message3',3);
insert into prefix_chat_messages (chatid,userid,system,message,timestamp) values(4,4,4,'message4',4);

select * from prefix_chat_messages;

rem --------------------------------------------------------

rem
rem Table structure for table chat_users
rem

drop TABLE prefix_chat_users;
CREATE TABLE prefix_chat_users (
  id number(10) primary key,
  chatid number(11) default '0' not null,
  userid number(11) default '0' not null,
  version varchar2(16) default '' not null,
  ip varchar2(15) default '' not null,
  firstping number(10) default '0' not null,
  lastping number(10) default '0' not null,
  lastmessageping number(10) default '0' not null,
  sid varchar2(32) default '' not null
);

create index userid on prefix_chat_users(userid);
create index lastping on prefix_chat_users(lastping);

drop sequence p_chat_users_seq;
create sequence p_chat_users_seq;

create or replace trigger p_chat_users_trig
  before insert on prefix_chat_users
  referencing new as new_row
  for each row
  begin
    select p_chat_users_seq.nextval into :new_row.id from dual;
  end;
.
/

COMMENT on table prefix_chat_users is 'Keeps track of which users are in which chat rooms';

insert into prefix_chat_users (chatid,userid,version,ip,firstping,lastping,lastmessageping,sid) values(1,1,'version1','ip1',1,1,1,'sid1');
insert into prefix_chat_users (chatid,userid,version,ip,firstping,lastping,lastmessageping,sid) values(2,2,'version2','ip2',2,2,2,'sid2');
insert into prefix_chat_users (chatid,userid,version,ip,firstping,lastping,lastmessageping,sid) values(3,3,'version3','ip3',3,3,3,'sid3');
insert into prefix_chat_users (chatid,userid,version,ip,firstping,lastping,lastmessageping,sid) values(4,4,'version4','ip4',4,4,4,'sid4');

select * from prefix_chat_users;

delete from prefix_log_display where module='chat';

INSERT INTO prefix_log_display VALUES ('chat', 'view', 'chat', 'name');
INSERT INTO prefix_log_display VALUES ('chat', 'add', 'chat', 'name');
INSERT INTO prefix_log_display VALUES ('chat', 'update', 'chat', 'name');
INSERT INTO prefix_log_display VALUES ('chat', 'report', 'chat', 'name');

select * from prefix_log_display where module='chat' order by 1,2,3,4;
