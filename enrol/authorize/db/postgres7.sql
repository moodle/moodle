CREATE TABLE prefix_enrol_authorize (
   id SERIAL PRIMARY KEY,
   cclastfour integer default '0',
   ccexp varchar(6) default '',
   cvv varchar(4) default '',
   ccname varchar(255) default '',
   courseid integer NOT NULL default '0',
   userid integer NOT NULL default '0',
   avscode varchar(1) default 'P',
   transid varchar(255) default ''
);