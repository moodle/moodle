--probably a bit suspect, need to explicitly create
--id sequence (i.e. don't depend on postgres default seq naming)?
--not sure about table owner either

CREATE TABLE search_documents
(
   id serial, 
   "type" varchar(12) NOT NULL DEFAULT 'none', 
   title varchar(100) NOT NULL default '', 
   url varchar(100) NOT NULL default '', 
   updated timestamp NOT NULL DEFAULT NOW(), 
   courseid int4, 
   userid int4, 
   groupid int4, 
   CONSTRAINT id_pkey PRIMARY KEY (id)
) WITHOUT OIDS;

--ALTER TABLE search_documents OWNER TO postgres;

DELETE FROM search_documents;
SELECT setval('public.search_documents_id_seq', 1);
