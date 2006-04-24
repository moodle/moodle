
CREATE TABLE prefix_data (
  id SERIAL PRIMARY KEY,
  course int4 NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  intro text NOT NULL default '',
  ratings int4 NOT NULL default '0',
  comments int NOT NULL default '0',
  timedue int NOT NULL default '0',
  timeavailable integer NOT NULL default '0',
  participants integer NOT NULL default '0',
  required integer NOT NULL default '0',
  rsstype integer NOT NULL default '0',
  rssarticles integer NOT NULL default '0',
  singletemplate text NOT NULL default '',
  listtemplate text NOT NULL default '',
  listtemplateheader text NOT NULL default '',
  listtemplatefooter text NOT NULL default '',
  addtemplate text NOT NULL default '',
  rsstemplate text NOT NULL default '',
  csstemplate text NOT NULL default '',
  approval int NOT NULL default '0',
  scale integer NOT NULL default '0',
  assessed integer NOT NULL default '0',
  assesspublic integer NOT NULL default '0',
  defaultsort integer NOT NULL default '0',
  defaultsortdir integer NOT NULL default '0',
  editany integer NOT NULL default '0'
);



CREATE TABLE prefix_data_content (
  id SERIAL PRIMARY KEY,
  fieldid int4 NOT NULL default '0',
  recordid int4 NOT NULL default '0',
  content text NOT NULL,
  content1 text NOT NULL,
  content2 text NOT NULL,
  content3 text NOT NULL,
  content4 text NOT NULL
);


CREATE TABLE prefix_data_fields (
  id SERIAL PRIMARY KEY,
  dataid int4 NOT NULL default '0',
  type varchar(255) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  description text,
  param1  text,
  param2  text,
  param3  text,
  param4  text,
  param5  text,
  param6  text,
  param7  text,
  param8  text,
  param9  text,
  param10 text
);


CREATE TABLE prefix_data_records (
  id SERIAL PRIMARY KEY,
  userid int4 NOT NULL default '0',
  groupid int4 NOT NULL default '0',
  dataid int4 NOT NULL default '0',
  timecreated int4 NOT NULL default '0',
  timemodified int4 NOT NULL default '0',
  approved int NOT NULL default '0'
);


CREATE TABLE prefix_data_comments (
  id SERIAL PRIMARY KEY,
  userid int4 NOT NULL default '0',
  recordid int4 NOT NULL default '0',
  content text,
  created integer NOT NULL default '0',
  modified integer NOT NULL default '0'
);


CREATE TABLE prefix_data_ratings (
  id SERIAL PRIMARY KEY,
  userid int4 NOT NULL default '0',
  recordid int4 NOT NULL default '0',
  rating int4 NOT NULL default '0'
);

INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'view', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'add', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'update', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'record delete', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'fields add', 'data_fields', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'fields update', 'data_fields', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'templates saved', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'templates def', 'data', 'name');
