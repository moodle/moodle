CREATE TABLE prefix_label (
  id SERIAL PRIMARY KEY,
  name varchar(255) default NULL,
  content text,
  timemodified integer NOT NULL default '0'
);
