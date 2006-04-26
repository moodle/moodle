CREATE TABLE prefix_enrol_authorize (
   id SERIAL PRIMARY KEY,
   cclastfour integer DEFAULT 0 NOT NULL,
   ccexp varchar(6) default '',
   cvv varchar(4) default '',
   ccname varchar(255) default '',
   courseid integer DEFAULT 0 NOT NULL,
   userid integer DEFAULT 0 NOT NULL,
   avscode varchar(1) default 'P',
   transid varchar(255) default ''
);

CREATE INDEX prefix_enrol_authorize_courseid_idx ON prefix_enrol_authorize(courseid);
CREATE INDEX prefix_enrol_authorize_userid_idx ON prefix_enrol_authorize(userid);
