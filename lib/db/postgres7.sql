CREATE TABLE config (
   id SERIAL PRIMARY KEY,
   name varchar(255) NOT NULL default '',
   value varchar(255) NOT NULL default '',
   CONSTRAINT config_name_uk UNIQUE (name)
);

CREATE TABLE course (
   id SERIAL PRIMARY KEY,
   category integer NOT NULL default '0',
   password varchar(50) NOT NULL default '',
   fullname varchar(254) NOT NULL default '',
   shortname varchar(15) NOT NULL default '',
   summary text NOT NULL default '',
   format varchar(10) CHECK (format IN ('weeks','social','topics')) NOT NULL default 'weeks',
   modinfo text NOT NULL default '',
   newsitems integer NOT NULL default '1',
   teacher varchar(100) NOT NULL default 'Teacher',
   teachers varchar(100) NOT NULL default 'Teachers',
   student varchar(100) NOT NULL default 'Student',
   students varchar(100) NOT NULL default 'Students',
   guest integer NOT NULL default '0',
   startdate integer NOT NULL default '0',
   numsections integer NOT NULL default '1',
   marker integer NOT NULL default '0',
   timecreated integer NOT NULL default '0',
   timemodified integer NOT NULL default '0'
);

CREATE TABLE course_categories (
   id SERIAL PRIMARY KEY,
   name varchar(255) NOT NULL default ''
);

CREATE TABLE course_modules (
   id SERIAL PRIMARY KEY,
   course integer NOT NULL default '0',
   module integer NOT NULL default '0',
   instance integer NOT NULL default '0',
   section integer NOT NULL default '0',
   added integer NOT NULL default '0',
   deleted integer NOT NULL default '0',
   score integer NOT NULL default '0'
);

CREATE TABLE course_sections (
   id SERIAL PRIMARY KEY,
   course integer NOT NULL default '0',
   section integer NOT NULL default '0',
   summary text NOT NULL default '',
   sequence varchar(255) NOT NULL default ''
);

CREATE TABLE log (
   id SERIAL PRIMARY KEY,
   time integer NOT NULL default '0',
   "user" integer NOT NULL default '0',
   ip varchar(15) NOT NULL default '',
   course integer NOT NULL default '0',
   module varchar(10) NOT NULL default '',
   action varchar(15) NOT NULL default '',
   url varchar(100) NOT NULL default '',
   info varchar(255) NOT NULL default ''
);

CREATE TABLE log_display (
   module varchar(20) NOT NULL default '',
   action varchar(20) NOT NULL default '',
   mtable varchar(20) NOT NULL default '',
   field varchar(40) NOT NULL default ''
);

CREATE TABLE modules (
   id SERIAL PRIMARY KEY,
   name varchar(20) NOT NULL default '',
   version integer NOT NULL default '0',
   cron integer NOT NULL default '0',
   lastcron integer NOT NULL default '0',
   search varchar(255) NOT NULL default ''
);

CREATE TABLE "user" (
   id SERIAL PRIMARY KEY,
   confirmed integer NOT NULL default '0',
   deleted integer NOT NULL default '0',
   username varchar(100) NOT NULL default '',
   password varchar(32) NOT NULL default '',
   idnumber varchar(12) default NULL,
   firstname varchar(20) NOT NULL default '',
   lastname varchar(20) NOT NULL default '',
   email varchar(100) NOT NULL default '',
   icq varchar(15) default NULL,
   phone1 varchar(20) default NULL,
   phone2 varchar(20) default NULL,
   institution varchar(40) default NULL,
   department varchar(30) default NULL,
   address varchar(70) default NULL,
   city varchar(20) default NULL,
   country char(2) default NULL,
   timezone float NOT NULL default '99',
   firstaccess integer NOT NULL default '0',
   lastaccess integer NOT NULL default '0',
   lastlogin integer NOT NULL default '0',
   currentlogin integer NOT NULL default '0',
   lastIP varchar(15) default NULL,
   secret varchar(15) default NULL,
   picture integer default NULL,
   url varchar(255) default NULL,
   description text,
   mailformat integer NOT NULL default '1',
   maildisplay integer NOT NULL default '2',
   timemodified integer NOT NULL default '0',
   CONSTRAINT user_username_uk UNIQUE (username)
);

CREATE TABLE user_admins (
   id SERIAL PRIMARY KEY,
   "user" integer NOT NULL default '0'
);

CREATE TABLE user_students (
   id SERIAL PRIMARY KEY,
   "user" integer NOT NULL default '0',
   course integer NOT NULL default '0',
   "start" integer NOT NULL default '0',
   "end" integer NOT NULL default '0',
   time integer NOT NULL default '0'
);

CREATE TABLE user_teachers (
   id SERIAL PRIMARY KEY,
   "user" integer NOT NULL default '0',
   course integer NOT NULL default '0',
   authority integer NOT NULL default '3',
   role varchar(40) NOT NULL default ''
);
