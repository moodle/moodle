CREATE TABLE prefix_config (
   id SERIAL PRIMARY KEY,
   name varchar(255) NOT NULL default '',
   value text NOT NULL default '',
   CONSTRAINT prefix_config_name_uk UNIQUE (name)
);

CREATE TABLE prefix_course (
   id SERIAL PRIMARY KEY,
   category integer NOT NULL default '0',
   sortorder integer NOT NULL default '0',
   password varchar(50) NOT NULL default '',
   fullname varchar(254) NOT NULL default '',
   shortname varchar(15) NOT NULL default '',
   summary text NOT NULL default '',
   format varchar(10) NOT NULL default 'topics',
   showgrades integer NOT NULL default '1',
   modinfo text NOT NULL default '',
   blockinfo varchar(255) NOT NULL default '',
   newsitems integer NOT NULL default '1',
   teacher varchar(100) NOT NULL default 'Teacher',
   teachers varchar(100) NOT NULL default 'Teachers',
   student varchar(100) NOT NULL default 'Student',
   students varchar(100) NOT NULL default 'Students',
   guest integer NOT NULL default '0',
   startdate integer NOT NULL default '0',
   numsections integer NOT NULL default '1',
   marker integer NOT NULL default '0',
   maxbytes integer NOT NULL default '0',
   showreports integer NOT NULL default '0',
   visible integer NOT NULL default '1',
   hiddensections integer NOT NULL default '0',
   groupmode integer NOT NULL default '0',
   groupmodeforce integer NOT NULL default '0',
   lang varchar(10) NOT NULL default '',
   timecreated integer NOT NULL default '0',
   timemodified integer NOT NULL default '0'
);

CREATE INDEX prefix_course_category_idx ON prefix_course (category);

CREATE TABLE prefix_course_categories (
   id SERIAL PRIMARY KEY,
   name varchar(255) NOT NULL default '',
   description text NOT NULL default '',
   parent integer NOT NULL default '0',
   sortorder integer NOT NULL default '0',
   coursecount integer NOT NULL default '0',
   visible integer NOT NULL default '1',
   timemodified integer NOT NULL default '0'
);

CREATE TABLE prefix_course_display (
   id SERIAL PRIMARY KEY,
   course integer NOT NULL default '0',
   userid integer NOT NULL default '0',
   display integer NOT NULL default '0'
);

CREATE INDEX prefix_course_display_courseuserid_idx ON prefix_course_display (course,userid);

CREATE TABLE prefix_course_modules (
   id SERIAL PRIMARY KEY,
   course integer NOT NULL default '0',
   module integer NOT NULL default '0',
   instance integer NOT NULL default '0',
   section integer NOT NULL default '0',
   added integer NOT NULL default '0',
   deleted integer NOT NULL default '0',
   score integer NOT NULL default '0',
   indent integer NOT NULL default '0',
   visible integer NOT NULL default '1',
   groupmode integer NOT NULL default '0'
);

CREATE TABLE prefix_course_sections (
   id SERIAL PRIMARY KEY,
   course integer NOT NULL default '0',
   section integer NOT NULL default '0',
   summary text NOT NULL default '',
   sequence text NOT NULL default '',
   visible integer NOT NULL default '1'
);

CREATE TABLE prefix_event (
   id SERIAL PRIMARY KEY,
   name varchar(255) NOT NULL default '',
   description text,
   format integer NOT NULL default '0',
   courseid integer NOT NULL default '0',
   groupid integer NOT NULL default '0',
   userid integer NOT NULL default '0',
   modulename varchar(20) NOT NULL default '',
   instance integer NOT NULL default '0',
   eventtype varchar(20) NOT NULL default '',
   timestart integer NOT NULL default '0',
   timeduration integer NOT NULL default '0',
   visible integer NOT NULL default '1',
   timemodified integer NOT NULL default '0'
);

CREATE INDEX prefix_event_courseid_idx ON prefix_event (courseid);
CREATE INDEX prefix_event_userid_idx ON prefix_event (userid);

CREATE TABLE prefix_groups (
   id SERIAL PRIMARY KEY,
   courseid integer NOT NULL default '0',
   name varchar(255) NOT NULL default '',
   description text,
   lang varchar(10) NOT NULL default '',
   picture integer NOT NULL default '0',
   hidepicture integer NOT NULL default '0',
   timecreated integer NOT NULL default '0',
   timemodified integer NOT NULL default '0'
);

CREATE INDEX prefix_groups_idx ON prefix_groups (courseid);

CREATE TABLE prefix_groups_members (
   id SERIAL PRIMARY KEY,
   groupid integer NOT NULL default '0',
   userid integer NOT NULL default '0',
   timeadded integer NOT NULL default '0'
);

CREATE INDEX prefix_groups_members_idx ON prefix_groups_members (groupid);

CREATE TABLE prefix_log (
   id SERIAL PRIMARY KEY,
   time integer NOT NULL default '0',
   userid integer NOT NULL default '0',
   ip varchar(15) NOT NULL default '',
   course integer NOT NULL default '0',
   module varchar(20) NOT NULL default '',
   cmid integer NOT NULL default '0',
   action varchar(20) NOT NULL default '',
   url varchar(100) NOT NULL default '',
   info varchar(255) NOT NULL default ''
);

CREATE INDEX prefix_log_coursemoduleaction_idx ON prefix_log (course,module,action);
CREATE INDEX prefix_log_timecoursemoduleaction_idx ON prefix_log (time,course,module,action);
CREATE INDEX prefix_log_courseuserid_idx ON prefix_log (course,userid);

CREATE TABLE prefix_log_display (
   module varchar(20) NOT NULL default '',
   action varchar(20) NOT NULL default '',
   mtable varchar(20) NOT NULL default '',
   field varchar(40) NOT NULL default ''
);

CREATE TABLE prefix_modules (
   id SERIAL PRIMARY KEY,
   name varchar(20) NOT NULL default '',
   version integer NOT NULL default '0',
   cron integer NOT NULL default '0',
   lastcron integer NOT NULL default '0',
   search varchar(255) NOT NULL default '',
   visible integer NOT NULL default '1'
);

CREATE TABLE prefix_scale (
   id SERIAL PRIMARY KEY,
   courseid integer NOT NULL default '0',
   userid integer NOT NULL default '0',
   name varchar(255) NOT NULL default '',
   scale text,
   description text,
   timemodified integer NOT NULL default '0'
);


CREATE TABLE prefix_cache_filters (
   id SERIAL PRIMARY KEY,
   filter varchar(32) NOT NULL default '',
   version integer NOT NULL default '0',
   md5key varchar(32) NOT NULL default '',
   rawtext text,
   timemodified integer NOT NULL default '0'
);

CREATE INDEX prefix_cache_filters_filtermd5key_idx ON prefix_cache_filters (filter,md5key);


CREATE TABLE prefix_cache_text (
   id SERIAL PRIMARY KEY,
   md5key varchar(32) NOT NULL default '',
   formattedtext text,
   timemodified integer NOT NULL default '0'
);

CREATE INDEX prefix_cache_text_md5key_idx ON prefix_cache_text (md5key);


CREATE TABLE prefix_user (
   id SERIAL PRIMARY KEY,
   confirmed integer NOT NULL default '0',
   deleted integer NOT NULL default '0',
   username varchar(100) NOT NULL default '',
   password varchar(32) NOT NULL default '',
   idnumber varchar(12) default NULL,
   firstname varchar(20) NOT NULL default '',
   lastname varchar(20) NOT NULL default '',
   email varchar(100) NOT NULL default '',
   emailstop integer NOT NULL default '0',
   icq varchar(15) default NULL,
   phone1 varchar(20) default NULL,
   phone2 varchar(20) default NULL,
   institution varchar(40) default NULL,
   department varchar(30) default NULL,
   address varchar(70) default NULL,
   city varchar(20) default NULL,
   country char(2) default NULL,
   lang varchar(10) NOT NULL default '',
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
   htmleditor integer NOT NULL default '1',
   autosubscribe integer NOT NULL default '1',
   timemodified integer NOT NULL default '0',
   CONSTRAINT prefix_user_username_uk UNIQUE (username)
);

CREATE TABLE prefix_user_admins (
   id SERIAL PRIMARY KEY,
   userid integer NOT NULL default '0'
);

CREATE TABLE prefix_user_preferences (
   id SERIAL PRIMARY KEY,
   userid integer NOT NULL default '0',
   name varchar(50) NOT NULL default '',
   value varchar(255) NOT NULL default ''
);

CREATE INDEX prefix_user_preferences_useridname_idx ON prefix_user_preferences (userid,name);

CREATE TABLE prefix_user_students (
   id SERIAL PRIMARY KEY,
   userid integer NOT NULL default '0',
   course integer NOT NULL default '0',
   timestart integer NOT NULL default '0',
   timeend integer NOT NULL default '0',
   time integer NOT NULL default '0',
   timeaccess integer NOT NULL default '0'
);

CREATE INDEX prefix_user_students_courseuserid_idx ON prefix_user_students (course,userid);

CREATE TABLE prefix_user_teachers (
   id SERIAL PRIMARY KEY,
   userid integer NOT NULL default '0',
   course integer NOT NULL default '0',
   authority integer NOT NULL default '3',
   role varchar(40) NOT NULL default '',
   editall integer NOT NULL default '1',
   timemodified integer NOT NULL default '0',
   timeaccess integer NOT NULL default '0'
);

CREATE INDEX prefix_user_teachers_courseuserid_idx ON prefix_user_teachers (course,userid);

CREATE TABLE prefix_user_coursecreators (
   id SERIAL8 PRIMARY KEY,
   userid int8  NOT NULL default '0'
);

INSERT INTO prefix_log_display VALUES ('user', 'view', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display VALUES ('course', 'user report', 'user', 'CONCAT(firstname," ",lastname)');
INSERT INTO prefix_log_display VALUES ('course', 'view', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'update', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'enrol', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'update', 'course', 'fullname');
