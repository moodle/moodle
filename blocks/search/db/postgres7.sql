CREATE TABLE prefix_search_documents (
   id SERIAL8 PRIMARY KEY,
   docid int4 NOT NULL,
   doctype varchar(12) NOT NULL default 'none',
   itemtype varchar(32) NOT NULL default 'standard',
   title varchar(255) NOT NULL default '',
   url varchar(255) NOT NULL default '',
   docdate timestamp NOT NULL,
   updated timestamp NOT NULL default NOW(),
   courseid int4,
   groupid int4
);
