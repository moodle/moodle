rem
rem Table structure for table forum
rem

drop TABLE prefix_forum;
CREATE TABLE prefix_forum (
  id number(10) primary key,
  course number(10)  default '0' NOT NULL,
  type varchar2(64) default 'general' not null,
    constraint type_check CHECK (type IN (
    'single','news','general','social','eachuser','teacher')), 
  name varchar2(255) default '' not null,
  intro varchar2(1024) NOT NULL,
  open number(2)  default '2' not null,
  assessed number(10)  default '0' NOT NULL,
  scale number(10)  default '0' NOT NULL,
  maxbytes number(10)  default '0' NOT NULL,
  forcesubscribe number(1)  default '0' NOT NULL,
  rsstype number(2) default '0' NOT NULL,
  rssarticles number(2) default '0' NOT NULL,
  timemodified number(10)  default '0' NOT NULL
);

COMMENT on table prefix_forum is 'Forums contain and structure discussion';

drop sequence p_forum_seq;
create sequence p_forum_seq;

create or replace trigger p_forum_trig
  before insert on prefix_forum
  referencing new as new_row
  for each row
  begin
    select p_forum_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_forum(course,type,name,intro,open,assessed,scale,forcesubscribe,timemodified) values(1,'single','1','1',1,1,1,1,1);
insert into prefix_forum(course,type,name,intro,open,assessed,scale,forcesubscribe,timemodified) values(2,'general','2','2',2,2,2,2,2);
insert into prefix_forum(course,type,name,intro,open,assessed,scale,forcesubscribe,timemodified) values(3,'eachuser','3','3',3,3,3,3,3);
rem should fail the check constraint
insert into prefix_forum(course,type,name,intro,open,assessed,scale,forcesubscribe,timemodified) values(4,'4','4','4',4,4,4,4,4);

select * from prefix_forum order by 1,2;

rem --------------------------------------------------------
rem
rem Table structure for table forum_discussions
rem

drop TABLE prefix_forum_discussions;
CREATE TABLE prefix_forum_discussions (
  id number(10) primary key,
  course number(10)  default '0' NOT NULL,
  forum number(10)  default '0' NOT NULL,
  name varchar2(255) default '' not null,
  firstpost number(10)  default '0' NOT NULL,
  assessed number(1) default '1' not null,
  timemodified number(10)  default '0' NOT NULL
);

COMMENT on table prefix_forum_discussions is 'Forums are composed of discussions';

drop sequence p_forum_disc_seq;
create sequence p_forum_disc_seq;

create or replace trigger p_forum_disc_trig
  before insert on prefix_forum_discussions
  referencing new as new_row
  for each row
  begin
    select p_forum_disc_seq.nextval into :new_row.id from dual;
  end;
.
/

insert into prefix_forum_discussions(course,forum,name,firstpost,assessed,timemodified) values(1,1,'1',1,1,1);
insert into prefix_forum_discussions(course,forum,name,firstpost,assessed,timemodified) values(2,2,'2',2,2,2);
insert into prefix_forum_discussions(course,forum,name,firstpost,assessed,timemodified) values(3,3,'3',3,3,3);
insert into prefix_forum_discussions(course,forum,name,firstpost,assessed,timemodified) values(4,4,'4',4,4,4);

select * from prefix_forum_discussions order by 1,2;

rem --------------------------------------------------------
rem
rem Table structure for table forum_posts
rem

drop TABLE prefix_forum_posts;
CREATE TABLE prefix_forum_posts (
  id number(10) primary key,
  discussion number(10)  default '0' NOT NULL,
  parent number(10)  default '0' NOT NULL,
  userid number(10)  default '0' NOT NULL,
  created number(10)  default '0' NOT NULL,
  modified number(10)  default '0' NOT NULL,
  mailed number(2)  default '0' NOT NULL,
  subject varchar2(255) default '' not null,
  message varchar2(1024) NOT NULL,
  format number(2) default '0' NOT NULL,
  attachment varchar2(100) default '' not null,
  totalscore number(4) default '0' NOT NULL
);

drop sequence p_forum_posts_seq;
create sequence p_forum_posts_seq;

create or replace trigger p_forum_posts_trig
  before insert on prefix_forum_posts
  referencing new as new_row
  for each row
  begin
    select p_forum_posts_seq.nextval into :new_row.id from dual;
  end;
.
/

COMMENT on table prefix_forum_posts is 'All posts are stored in this table';

insert into prefix_forum_posts (discussion,parent,userid,created,modified,mailed,subject,message,format,attachment,totalscore) values(1,1,1,1,1,1,'1','1',1,'1',1);
insert into prefix_forum_posts (discussion,parent,userid,created,modified,mailed,subject,message,format,attachment,totalscore) values(2,2,2,2,2,2,'2','2',2,'2',2);
insert into prefix_forum_posts (discussion,parent,userid,created,modified,mailed,subject,message,format,attachment,totalscore) values(3,3,3,3,3,3,'3','3',3,'3',3);
insert into prefix_forum_posts (discussion,parent,userid,created,modified,mailed,subject,message,format,attachment,totalscore) values(4,4,4,4,4,4,'4','4',4,'4',4);

select * from prefix_forum_posts order by 1,2;

rem --------------------------------------------------------
rem
rem Table structure for table forum_ratings
rem

drop TABLE prefix_forum_ratings;
CREATE TABLE prefix_forum_ratings (
  id number(10) primary key,
  userid number(10)  default '0' NOT NULL,
  post number(10)  default '0' NOT NULL,
  time number(10)  default '0' NOT NULL,
  rating number(4) default '0' NOT NULL
);

drop sequence p_forum_ratings_seq;
create sequence p_forum_ratings_seq;

create or replace trigger p_forum_ratings_trig
  before insert on prefix_forum_ratings
  referencing new as new_row
  for each row
  begin
    select p_forum_ratings_seq.nextval into :new_row.id from dual;
  end;
.
/

COMMENT on table prefix_forum_ratings is 'Contains user ratings for individual posts';

insert into prefix_forum_ratings(userid,post,time,rating) values(1,1,1,1);
insert into prefix_forum_ratings(userid,post,time,rating) values(2,2,2,2);
insert into prefix_forum_ratings(userid,post,time,rating) values(3,3,3,3);
insert into prefix_forum_ratings(userid,post,time,rating) values(4,4,4,4);

select * from prefix_forum_ratings order by 1,2;

rem --------------------------------------------------------
rem
rem Table structure for table forum_subscriptions
rem

drop TABLE prefix_forum_subscriptions;
CREATE TABLE prefix_forum_subscriptions (
  id number(10) primary key,
  userid number(10)  default '0' NOT NULL,
  forum number(10)  default '0' NOT NULL
);

drop sequence p_forum_subscrip_seq;
create sequence p_forum_subscrip_seq;

create or replace trigger p_forum_subscrip_trig
  before insert on prefix_forum_subscriptions
  referencing new as new_row
  for each row
  begin
    select p_forum_subscrip_seq.nextval into :new_row.id from dual;
  end;
.
/

COMMENT on table prefix_forum_subscriptions is 'Keeps track of who is subscribed to what forum';

insert into prefix_forum_subscriptions(userid,forum) values(1,1);
insert into prefix_forum_subscriptions(userid,forum) values(2,2);
insert into prefix_forum_subscriptions(userid,forum) values(3,3);
insert into prefix_forum_subscriptions(userid,forum) values(4,4);

select * from prefix_forum_subscriptions order by 1,2;


rem --------------------------------------------------------

rem
rem Dumping data for table log_display
rem
delete from prefix_log_display where module = 'forum';
INSERT INTO prefix_log_display VALUES ('forum', 'add', 'forum', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'update', 'forum', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'add discussion', 'forum_discussions', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'add post', 'forum_posts', 'subject');
INSERT INTO prefix_log_display VALUES ('forum', 'update post', 'forum_posts', 'subject');
INSERT INTO prefix_log_display VALUES ('forum', 'move discussion', 'forum_discussions', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'view subscribers', 'forum', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'view discussion', 'forum_discussions', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'view forum', 'forum', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'subscribe', 'forum', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'unsubscribe', 'forum', 'name');

select * from prefix_log_display where module = 'forum';
