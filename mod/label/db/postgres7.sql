CREATE TABLE prefix_label (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  name varchar(255) default NULL,
  content text,
  timemodified integer NOT NULL default '0'
);
