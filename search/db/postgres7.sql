CREATE TABLE prefix_search_documents (
   id SERIAL8 PRIMARY KEY,
   doctype varchar(12) NOT NULL DEFAULT 'none', 
   title varchar(100) NOT NULL default '', 
   url varchar(100) NOT NULL default '', 
   updated timestamp NOT NULL DEFAULT NOW(), 
   courseid int4,    
   groupid int4
);