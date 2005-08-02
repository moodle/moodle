CREATE TABLE prefix_config (
   id SERIAL PRIMARY KEY,
   name varchar(255) NOT NULL default '',
   value text NOT NULL default '',
   CONSTRAINT prefix_config_name_uk UNIQUE (name)
);

CREATE TABLE prefix_config_plugins (
   id     SERIAL PRIMARY KEY,
   plugin varchar(100) NOT NULL default 'core',
   name   varchar(100) NOT NULL default '',
   value  text NOT NULL default '',
   CONSTRAINT prefix_config_plugins_plugin_name_uk UNIQUE (plugin, name)
);

CREATE TABLE prefix_course (
   id SERIAL PRIMARY KEY,
   category integer NOT NULL default '0',
   sortorder integer NOT NULL default '0',
   password varchar(50) NOT NULL default '',
   fullname varchar(254) NOT NULL default '',
   shortname varchar(15) NOT NULL default '',
   idnumber varchar(100) NOT NULL default '',
   summary text NOT NULL default '',
   format varchar(10) NOT NULL default 'topics',
   showgrades integer NOT NULL default '1',
   modinfo text NOT NULL default '',
   newsitems integer NOT NULL default '1',
   teacher varchar(100) NOT NULL default 'Teacher',
   teachers varchar(100) NOT NULL default 'Teachers',
   student varchar(100) NOT NULL default 'Student',
   students varchar(100) NOT NULL default 'Students',
   guest integer NOT NULL default '0',
   startdate integer NOT NULL default '0',
   enrolperiod integer NOT NULL default '0',
   numsections integer NOT NULL default '1',
   marker integer NOT NULL default '0',
   maxbytes integer NOT NULL default '0',
   showreports integer NOT NULL default '0',
   visible integer NOT NULL default '1',
   hiddensections integer NOT NULL default '0',
   groupmode integer NOT NULL default '0',
   groupmodeforce integer NOT NULL default '0',
   lang varchar(10) NOT NULL default '',
   theme varchar(50) NOT NULL default '',
   cost varchar(10) NOT NULL default '',
   timecreated integer NOT NULL default '0',
   timemodified integer NOT NULL default '0',
   metacourse integer NOT NULL default '0'
);

CREATE UNIQUE INDEX prefix_course_category_sortorder_uk ON prefix_course (category,sortorder);
CREATE INDEX prefix_course_idnumber_idx ON prefix_course (idnumber);
CREATE INDEX prefix_course_shortname_idx ON prefix_course (shortname);

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

CREATE TABLE prefix_course_meta (
	id SERIAL primary key,
	parent_course integer NOT NULL,
	child_course integer NOT NULL
);

CREATE INDEX prefix_course_meta_parent_idx ON prefix_course_meta (parent_course);
CREATE INDEX prefix_course_meta_child_idx ON prefix_course_meta (child_course);

CREATE TABLE prefix_course_modules (
   id SERIAL PRIMARY KEY,
   course integer NOT NULL default '0',
   module integer NOT NULL default '0',
   instance integer NOT NULL default '0',
   section integer NOT NULL default '0',
   added integer NOT NULL default '0',
   score integer NOT NULL default '0',
   indent integer NOT NULL default '0',
   visible integer NOT NULL default '1',
   groupmode integer NOT NULL default '0'
);

CREATE INDEX prefix_course_modules_visible_idx ON prefix_course_modules (visible);
CREATE INDEX prefix_course_modules_course_idx ON prefix_course_modules (course);
CREATE INDEX prefix_course_modules_module_idx ON prefix_course_modules (module);
CREATE INDEX prefix_course_modules_instance_idx ON prefix_course_modules (instance);

CREATE TABLE prefix_course_sections (
   id SERIAL PRIMARY KEY,
   course integer NOT NULL default '0',
   section integer NOT NULL default '0',
   summary text NOT NULL default '',
   sequence text NOT NULL default '',
   visible integer NOT NULL default '1'
);

CREATE INDEX prefix_course_sections_coursesection_idx ON prefix_course_sections (course,section);

CREATE TABLE prefix_event (
   id SERIAL PRIMARY KEY,
   name varchar(255) NOT NULL default '',
   description text,
   format integer NOT NULL default '0',
   courseid integer NOT NULL default '0',
   groupid integer NOT NULL default '0',
   userid integer NOT NULL default '0',
   repeatid integer NOT NULL default '0',
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
CREATE INDEX prefix_event_timestart_idx ON prefix_event (timestart);
CREATE INDEX prefix_event_timeduration_idx ON prefix_event (timeduration);


CREATE TABLE prefix_grade_category (
  id SERIAL PRIMARY KEY,
  name varchar(64) default NULL,
  courseid integer NOT NULL default '0',
  drop_x_lowest integer NOT NULL default '0',
  bonus_points integer NOT NULL default '0',
  hidden integer NOT NULL default '0',
  weight decimal(5,2) default '0.00'
);

CREATE INDEX prefix_grade_category_courseid_idx ON prefix_grade_category (courseid);

CREATE TABLE prefix_grade_exceptions (
  id SERIAL PRIMARY KEY,
  courseid integer  NOT NULL default '0',
  grade_itemid integer  NOT NULL default '0',
  userid integer  NOT NULL default '0'
);

CREATE INDEX prefix_grade_exceptions_courseid_idx ON prefix_grade_exceptions (courseid);


CREATE TABLE prefix_grade_item (
  id SERIAL PRIMARY KEY,
  courseid integer default NULL,
  category integer default NULL,
  modid integer default NULL,
  cminstance integer default NULL,
  scale_grade float(11) default '1.0000000000',
  extra_credit integer NOT NULL default '0',
  sort_order integer  NOT NULL default '0'
);

CREATE INDEX prefix_grade_item_courseid_idx ON prefix_grade_item (courseid);

CREATE TABLE prefix_grade_letter (
  id SERIAL PRIMARY KEY,
  courseid integer NOT NULL default '0',
  letter varchar(8) NOT NULL default 'NA',
  grade_high decimal(6,2) NOT NULL default '100.00',
  grade_low decimal(6,2) NOT NULL default '0.00'
);

CREATE INDEX prefix_grade_letter_courseid_idx ON prefix_grade_letter (courseid);

CREATE TABLE prefix_grade_preferences (
  id SERIAL PRIMARY KEY,
  courseid integer default NULL,
  preference integer NOT NULL default '0',
  value integer NOT NULL default '0'
);

CREATE UNIQUE INDEX prefix_grade_prefs_courseidpref_uk ON prefix_grade_preferences (courseid,preference);

CREATE TABLE prefix_groups (
   id SERIAL PRIMARY KEY,
   courseid integer NOT NULL default '0',
   name varchar(255) NOT NULL default '',
   description text,
   password varchar(50) NOT NULL default '',
   lang varchar(10) NOT NULL default '',
   theme varchar(50) NOT NULL default '',
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
CREATE INDEX prefix_groups_members_userid_idx ON prefix_groups_members (userid);

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

CREATE TABLE prefix_message (
   id SERIAL PRIMARY KEY,
   useridfrom integer NOT NULL default '0',
   useridto integer NOT NULL default '0',
   message text,
   format integer NOT NULL default '0',
   timecreated integer NOT NULL default '0',
   messagetype varchar(50) NOT NULL default ''
);

CREATE INDEX prefix_message_useridfrom_idx ON prefix_message (useridfrom);
CREATE INDEX prefix_message_useridto_idx ON prefix_message (useridto);

CREATE TABLE prefix_message_read (
   id SERIAL PRIMARY KEY,
   useridfrom integer NOT NULL default '0',
   useridto integer NOT NULL default '0',
   message text,
   format integer NOT NULL default '0',
   timecreated integer NOT NULL default '0',
   timeread integer NOT NULL default '0',
   messagetype varchar(50) NOT NULL default '',
   mailed integer NOT NULL default '0'
);

CREATE INDEX prefix_message_read_useridfrom_idx ON prefix_message_read (useridfrom);
CREATE INDEX prefix_message_read_useridto_idx ON prefix_message_read (useridto);

CREATE TABLE prefix_message_contacts (
   id SERIAL PRIMARY KEY,
   userid integer NOT NULL default '0',
   contactid integer NOT NULL default '0',
   blocked integer NOT NULL default '0'
);

CREATE INDEX prefix_message_contacts_useridcontactid_idx ON prefix_message_contacts (userid,contactid);

CREATE TABLE prefix_modules (
   id SERIAL PRIMARY KEY,
   name varchar(20) NOT NULL default '',
   version integer NOT NULL default '0',
   cron integer NOT NULL default '0',
   lastcron integer NOT NULL default '0',
   search varchar(255) NOT NULL default '',
   visible integer NOT NULL default '1'
);

CREATE INDEX prefix_modules_name_idx ON prefix_modules (name);

CREATE TABLE prefix_scale (
   id SERIAL PRIMARY KEY,
   courseid integer NOT NULL default '0',
   userid integer NOT NULL default '0',
   name varchar(255) NOT NULL default '',
   scale text,
   description text,
   timemodified integer NOT NULL default '0'
);

CREATE TABLE prefix_sessions (
  sesskey char(32) PRIMARY KEY,
  expiry integer NOT null,
  expireref varchar(64),
  data text NOT null
);

CREATE INDEX prefix_sessions_expiry_idx ON prefix_sessions (expiry);

CREATE TABLE prefix_timezone (
  id SERIAL PRIMARY KEY,
  name varchar(100) NOT NULL default '',
  year integer NOT NULL default '0',
  rule varchar(20) NOT NULL default '',
  gmtoff integer NOT NULL default '0',
  dstoff integer NOT NULL default '0',
  dst_month integer NOT NULL default '0',
  dst_startday integer NOT NULL default '0',
  dst_weekday integer NOT NULL default '0',
  dst_skipweeks integer NOT NULL default '0',
  dst_time varchar(5) NOT NULL default '00:00',
  std_month integer NOT NULL default '0',
  std_startday integer NOT NULL default '0',
  std_weekday integer NOT NULL default '0',
  std_skipweeks integer NOT NULL default '0',
  std_time varchar(5) NOT NULL default '00:00'
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

CREATE INDEX prefix_scale_courseid_idx ON prefix_scale (courseid);


CREATE TABLE prefix_cache_text (
   id SERIAL PRIMARY KEY,
   md5key varchar(32) NOT NULL default '',
   formattedtext text,
   timemodified integer NOT NULL default '0'
);

CREATE INDEX prefix_cache_text_md5key_idx ON prefix_cache_text (md5key);

#
# Table structure for table `user`
#
# When updating field length, modify
# truncate_userinfo() in moodlelib.php
#
CREATE TABLE prefix_user (
   id SERIAL PRIMARY KEY,
   auth varchar(20) NOT NULL default 'manual',
   confirmed integer NOT NULL default '0',
   policyagreed integer NOT NULL default '0',
   deleted integer NOT NULL default '0',
   username varchar(100) NOT NULL default '',
   password varchar(32) NOT NULL default '',
   idnumber varchar(64) default NULL,
   firstname varchar(20) NOT NULL default '',
   lastname varchar(20) NOT NULL default '',
   email varchar(100) NOT NULL default '',
   emailstop integer NOT NULL default '0',
   icq varchar(15) default NULL,
   skype varchar(50) default NULL,
   yahoo varchar(50) default NULL,
   aim varchar(50) default NULL,
   msn varchar(50) default NULL,
   phone1 varchar(20) default NULL,
   phone2 varchar(20) default NULL,
   institution varchar(40) default NULL,
   department varchar(30) default NULL,
   address varchar(70) default NULL,
   city varchar(20) default NULL,
   country char(2) default NULL,
   lang varchar(10) NOT NULL default '',
   theme varchar(50) NOT NULL default '',
   timezone varchar(100) NOT NULL default '99',
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
   maildigest integer NOT NULL default '0',
   maildisplay integer NOT NULL default '2',
   htmleditor integer NOT NULL default '1',
   autosubscribe integer NOT NULL default '1',
   trackforums integer NOT NULL default '0',
   timemodified integer NOT NULL default '0'
);

CREATE UNIQUE INDEX prefix_user_username_uk ON prefix_user (username);
CREATE INDEX prefix_user_idnumber_idx ON prefix_user (idnumber);
CREATE INDEX prefix_user_auth_idx ON prefix_user (auth);
CREATE INDEX prefix_user_deleted_idx ON prefix_user (deleted);
CREATE INDEX prefix_user_confirmed_idx ON prefix_user (confirmed);
CREATE INDEX prefix_user_firstname_idx ON prefix_user (firstname);
CREATE INDEX prefix_user_lastname_idx ON prefix_user (lastname);
CREATE INDEX prefix_user_city_idx ON prefix_user (city);
CREATE INDEX prefix_user_country_idx ON prefix_user (country);
CREATE INDEX prefix_user_lastaccess_idx ON prefix_user (lastaccess);
CREATE INDEX prefix_user_email_idx ON prefix_user (email);

CREATE TABLE prefix_user_admins (
   id SERIAL PRIMARY KEY,
   userid integer NOT NULL default '0'
);

CREATE INDEX prefix_user_admins_userid_idx ON prefix_user_admins (userid);

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
   timeaccess integer NOT NULL default '0',
   enrol varchar (20) NOT NULL default ''
);

CREATE UNIQUE INDEX prefix_user_students_courseuserid_uk ON prefix_user_students (course,userid);
CREATE INDEX prefix_user_students_userid_idx ON prefix_user_students (userid);
CREATE INDEX prefix_user_students_enrol_idx ON prefix_user_students (enrol);

CREATE TABLE prefix_user_teachers (
   id SERIAL PRIMARY KEY,
   userid integer NOT NULL default '0',
   course integer NOT NULL default '0',
   authority integer NOT NULL default '3',
   role varchar(40) NOT NULL default '',
   editall integer NOT NULL default '1',
   timestart integer NOT NULL default '0',
   timeend integer NOT NULL default '0',
   timemodified integer NOT NULL default '0',
   timeaccess integer NOT NULL default '0',
   enrol varchar (20) NOT NULL default ''
);

CREATE UNIQUE INDEX prefix_user_teachers_courseuserid_uk ON prefix_user_teachers (course,userid);
CREATE INDEX prefix_user_teachers_userid_idx ON prefix_user_teachers (userid);
CREATE INDEX prefix_user_teachers_enrol_idx ON prefix_user_teachers (enrol);

CREATE TABLE prefix_user_coursecreators (
   id SERIAL8 PRIMARY KEY,
   userid int8  NOT NULL default '0'
);

CREATE TABLE adodb_logsql (
   created timestamp NOT NULL,
   sql0 varchar(250) NOT NULL,
   sql1 text NOT NULL,
   params text NOT NULL,
   tracer text NOT NULL,
   timer decimal(16,6) NOT NULL
);


INSERT INTO prefix_log_display VALUES ('user', 'view', 'user', 'firstname||\' \'||lastname');
INSERT INTO prefix_log_display VALUES ('course', 'user report', 'user', 'firstname||\' \'||lastname');
INSERT INTO prefix_log_display VALUES ('course', 'view', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'update', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('course', 'enrol', 'course', 'fullname');
INSERT INTO prefix_log_display VALUES ('message', 'write', 'user', 'firstname||\' \'||lastname');
INSERT INTO prefix_log_display VALUES ('message', 'read', 'user', 'firstname||\' \'||lastname');
INSERT INTO prefix_log_display VALUES ('message', 'add contact', 'user', 'firstname||\' \'||lastname');
INSERT INTO prefix_log_display VALUES ('message', 'remove contact', 'user', 'firstname||\' \'||lastname');
INSERT INTO prefix_log_display VALUES ('message', 'block contact', 'user', 'firstname||\' \'||lastname');
INSERT INTO prefix_log_display VALUES ('message', 'unblock contact', 'user', 'firstname||\' \'||lastname');
