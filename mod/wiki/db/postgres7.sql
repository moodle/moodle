# This file contains a complete database schema for all the
# tables used by this module, written in SQL

# It may also contain INSERT statements for particular data
# that may be used, especially new entries in the table log_display


CREATE TABLE prefix_wiki (
  id SERIAL8 PRIMARY KEY,
  course INT8  NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  summary text NOT NULL,
  pagename varchar(255) ,
  wtype VARCHAR default 'group' CHECK( wtype IN('teacher', 'group', 'student')),
  ewikiprinttitle INT NOT NULL default '1',
  htmlmode INT NOT NULL default '0',
  ewikiacceptbinary INT NOT NULL default '0',
  disablecamelcase INT NOT NULL default '0',
  setpageflags INT NOT NULL default '1',
  strippages INT NOT NULL default '1',
  removepages INT NOT NULL default '1',
  revertchanges INT NOT NULL default '1',
  initialcontent varchar(255) ,
  timemodified INT8 NOT NULL default '0'
) ;


#
# Table structure for table mdl_wiki_entries
#

CREATE TABLE prefix_wiki_entries (
  id SERIAL8 PRIMARY KEY,
  wikiid INT8 NOT NULL default '0',
  course INT8 NOT NULL default '0',
  groupid INT8 NOT NULL default '0',
  userid INT8 NOT NULL default '0',
  pagename varchar(255) NOT NULL default '',
  timemodified INT8 NOT NULL default '0'
) ;


CREATE TABLE prefix_wiki_pages (
  pagename VARCHAR(160) NOT NULL,
  version INTEGER  NOT NULL DEFAULT 0,
  flags INTEGER  DEFAULT 0,
  content TEXT,
  author VARCHAR(100) DEFAULT 'ewiki',
  userid INTEGER  NOT NULL DEFAULT 0,
  created INTEGER  DEFAULT 0,
  lastmodified INTEGER  DEFAULT 0,
  refs TEXT,
  meta TEXT,
  hits INTEGER  DEFAULT 0,
  wiki INT8  NOT NULL
) ;

ALTER TABLE ONLY prefix_wiki_pages
    ADD CONSTRAINT id PRIMARY KEY (pagename, version, wiki);

CREATE INDEX prefix_wiki_pages_pagename_version_wiki_idx ON prefix_wiki_pages (pagename, version, wiki) ;
