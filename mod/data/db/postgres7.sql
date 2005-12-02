
CREATE TABLE prefix_data (
  id SERIAL,
  course int4 unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  intro text NOT NULL default '',
  ratings int4 NOT NULL default '0',
  comments int unsigned NOT NULL default '0',
  timedue int unsigned NOT NULL default '0',
  timeavailable integer unsigned NOT NULL default '0',
  participants integer unsigned NOT NULL default '0',
  required integer unsigned NOT NULL default '0',
  rsstype integer unsigned NOT NULL default '0',
  rssarticles integer unsigned NOT NULL default '0',
  singletemplate text NOT NULL default '',
  listtemplate text NOT NULL default '',
  addtemplate text NOT NULL default '',
  rsstemplate text NOT NULL default '',
  listtemplateheader text NOT NULL default '',
  listtemplatefooter text NOT NULL default '',
  PRIMARY KEY  (id)
);



CREATE TABLE prefix_data_content (
  id SERIAL,
  fieldid int4 NOT NULL default '0',
  recordid int4 NOT NULL default '0',
  content longtext NOT NULL,
  PRIMARY KEY  (id)
);


CREATE TABLE prefix_data_fields (
  id SERIAL,
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
  param10 text,
  PRIMARY KEY  (id)
);


CREATE TABLE prefix_data_records (
  id SERIAL,
  userid int4 NOT NULL default '0',
  groupid int4 NOT NULL default '0',
  dataid int4 NOT NULL default '0',
  timecreated int4 NOT NULL default '0',
  timemodified int4 NOT NULL default '0',
  PRIMARY KEY  (id)
);


CREATE TABLE prefix_data_comments (
  id SERIAL,
  userid int4 NOT NULL default '0',
  recordid int4 NOT NULL default '0',
  content text,
  PRIMARY KEY  (id)
);


CREATE TABLE prefix_data_ratings (
  id SERIAL,
  userid int4 NOT NULL default '0',
  recordid int4 NOT NULL default '0',
  rating int4 NOT NULL default '0',
  PRIMARY KEY  (id)
);


