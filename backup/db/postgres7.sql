#
# Table structure for table prefix_backup_files
#

CREATE TABLE prefix_backup_files (
  id SERIAL PRIMARY KEY,
  backup_code integer NOT NULL default '0',
  file_type varchar(10) NOT NULL default '',
  path varchar(255) NOT NULL default '',
  old_id integer default NULL,
  new_id integer default NULL,
  CONSTRAINT backup_files_uk UNIQUE (backup_code, file_type, path)
);


#
# Table structure for table prefix_backup_ids
#

CREATE TABLE prefix_backup_ids (
  id SERIAL PRIMARY KEY,
  backup_code integer NOT NULL default '0',
  table_name varchar(30) NOT NULL default '',
  old_id integer NOT NULL default '0',
  new_id integer default NULL,
  info text,
  CONSTRAINT backup_ids_uk UNIQUE (backup_code, table_name, old_id)
);


#
# Table structure for table prefix_backup_config
#

CREATE TABLE prefix_backup_config (
   id SERIAL PRIMARY KEY,
   name varchar(255) UNIQUE NOT NULL default '',
   value varchar(255) NOT NULL default ''
);


#
# Table structure for table prefix_backup_courses
#

CREATE TABLE prefix_backup_courses (
    id SERIAL PRIMARY KEY,
    courseid integer UNIQUE NOT NULL default '0',
    laststarttime integer NOT NULL default '0',
    lastendtime integer NOT NULL default '0',
    laststatus varchar(1) NOT NULL default '0',
    nextstarttime integer NOT NULL default '0'
);



#
# Table structure for table prefix_backup_log
#

CREATE TABLE prefix_backup_log (
    id SERIAL PRIMARY KEY,
    courseid integer NOT NULL default '0',
    time integer NOT NULL default '0',
    laststarttime integer NOT NULL default '0',
    info varchar(255) NOT NULL default ''
);
