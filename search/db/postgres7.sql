CREATE TABLE prefix_search_documents (
   id SERIAL8 PRIMARY KEY,
   "type" varchar(12) NOT NULL DEFAULT 'none', 
   title varchar(100) NOT NULL default '', 
   url varchar(100) NOT NULL default '', 
   updated timestamp NOT NULL DEFAULT NOW(), 
   courseid int4, 
   userid int4, 
   groupid int4
);

--DELETE FROM prefix_search_documents;
--SELECT setval('public.prefix_search_documents_id_seq', 1);