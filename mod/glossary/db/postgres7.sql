# This file contains a complete database schema for all the 
# tables used by this module, written in SQL

# It may also contain INSERT statements for particular data 
# that may be used, especially new entries in the table log_display

#
# Table structure for table `glossary`
#

CREATE TABLE prefix_glossary (
     id SERIAL,
     course int4 NOT NULL default '0',
     name varchar(255) NOT NULL default '',
     studentcanpost int2 NOT NULL default '0',
     allowduplicatedentries int2 NOT NULL default '0',
     displayformat int2 NOT NULL default '0',
     mainglossary int2 NOT NULL default '0',
     showspecial int2 NOT NULL default '1',
     showalphabet int2 NOT NULL default '1',
     showall int2 NOT NULL default '1',
     timecreated int4 NOT NULL default '0',
     timemodified int4 NOT NULL default '0',
     PRIMARY KEY  (id)
);

#
# Table structure for table `glossary_entries`
#

CREATE TABLE prefix_glossary_entries (
     id SERIAL,
     glossaryid int4 NOT NULL default '0',
     userid int4 NOT NULL default '0',
     concept varchar(255) NOT NULL default '',
     definition text NOT NULL,
     format int2 NOT NULL default '0',
     attachment VARCHAR(100) NOT NULL default '',
     timecreated int4 NOT NULL default '0',
     timemodified int4 NOT NULL default '0',
     teacherentry int2 NOT NULL default '0',
     PRIMARY KEY(id)
);

#
# Table structure for table `glossary_cageories`
#

CREATE TABLE prefix_glossary_categories (
     id SERIAL,
     glossaryid int4 NOT NULL default '0',
     name varchar(255) NOT NULL default '',
     PRIMARY KEY  (id)
);

#
# Table structure for table `glossary_entries_category`
#

CREATE TABLE prefix_glossary_entries_categories (
     id SERIAL,
     categoryid int4 NOT NULL default '0',
     entryid int4 NOT NULL default '0',
     PRIMARY KEY  (categoryid, entryid)
);

#
# Dumping data for table `log_display`
#

INSERT INTO prefix_log_display VALUES ('glossary', 'add', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'update', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'view', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'view all', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'add entry', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'update entry', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'add category', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'update category', 'glossary', 'name');
INSERT INTO prefix_log_display VALUES ('glossary', 'delete category', 'glossary', 'name');

