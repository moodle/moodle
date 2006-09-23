
CREATE TABLE prefix_data (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  intro text NOT NULL default '',
  comments integer NOT NULL default '0',
  timeavailablefrom integer NOT NULL default '0',
  timeavailableto integer NOT NULL default '0',
  timeviewfrom integer NOT NULL default '0',
  timeviewto integer NOT NULL default '0',
  requiredentries integer NOT NULL default '0',
  requiredentriestoview integer NOT NULL default '0',
  maxentries integer NOT NULL default '0',
  rssarticles integer NOT NULL default '0',
  singletemplate text,
  listtemplate text,
  listtemplateheader text,
  listtemplatefooter text,
  addtemplate text,
  rsstemplate text,
  rsstitletemplate text,
  csstemplate text,
  jstemplate text,
  approval integer NOT NULL default '0',
  scale integer NOT NULL default '0',
  assessed integer NOT NULL default '0',
  defaultsort integer NOT NULL default '0',
  defaultsortdir integer NOT NULL default '0',
  editany integer NOT NULL default '0'
);



CREATE TABLE prefix_data_content (
  id SERIAL PRIMARY KEY,
  fieldid integer NOT NULL default '0',
  recordid integer NOT NULL default '0',
  content text,
  content1 text,
  content2 text,
  content3 text,
  content text4
);


CREATE TABLE prefix_data_fields (
  id SERIAL PRIMARY KEY,
  dataid integer NOT NULL default '0',
  type varchar(255) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  description text NOT NULL default '',
  param1 text,
  param2 text,
  param3 text,
  param4 text,
  param5 text,
  param6 text,
  param7 text,
  param8 text,
  param9 text,
  param10 text
);

CREATE TABLE prefix_data_records (
  id SERIAL PRIMARY KEY,
  userid integer NOT NULL default '0',
  groupid integer NOT NULL default '0',
  dataid integer NOT NULL default '0',
  timecreated integer NOT NULL default '0',
  timemodified integer NOT NULL default '0',
  approved integer NOT NULL default '0'
);


CREATE TABLE prefix_data_comments (
  id SERIAL PRIMARY KEY,
  userid integer NOT NULL default '0',
  recordid integer NOT NULL default '0',
  content text NOT NULL default '',
  created integer NOT NULL default '0',
  modified integer NOT NULL default '0'
);


CREATE TABLE prefix_data_ratings (
  id SERIAL PRIMARY KEY,
  userid integer NOT NULL default '0',
  recordid integer NOT NULL default '0',
  rating integer NOT NULL default '0'
);

INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'view', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'add', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'update', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'record delete', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'fields add', 'data_fields', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'fields update', 'data_fields', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'templates saved', 'data', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'templates def', 'data', 'name');
