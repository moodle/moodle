CREATE TABLE prefix_label (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  name varchar(255) default NULL,
  content text,
  timemodified integer NOT NULL default '0'
);

INSERT INTO prefix_log_display VALUES ('label', 'add', 'quiz', 'name');
INSERT INTO prefix_log_display VALUES ('label', 'update', 'quiz', 'name');
