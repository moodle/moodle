# THIS FILE IS UNTESTED!!!  
# PLEASE HELP TEST/FIX IT AND CONTACT MARTIN OR ELOY!

#
# Table structure for table prefix_backup_files
#

CREATE TABLE prefix_backup_files (
  backup_code integer NOT NULL default '0',
  file_type varchar(10) NOT NULL default '',
  path varchar(255) NOT NULL default '',
  old_id integer default NULL,
  new_id integer default NULL
);

# --------------------------------------------------------

CREATE INDEX prefix_backup_codetypepath_idx ON prefix_backup_files (backup_code,file_type,path);

#
# Table structure for table prefix_backup_ids
#

CREATE TABLE prefix_backup_ids (
  backup_code int8 NOT NULL default '0',
  table_name varchar(30) NOT NULL default '',
  old_id int8 NOT NULL default '0',
  new_id int8 default NULL,
  info text
);

CREATE INDEX prefix_backup_codenameid_idx ON prefix_backup_ids (backup_code,table_name,old_id);

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
    courseid int8 UNIQUE NOT NULL default '0',
    laststarttime int8 NOT NULL default '0',
    lastendtime int8 NOT NULL default '0',
    laststatus varchar(1) NOT NULL default '0',
    nextstarttime int8 NOT NULL default '0'
);

#
# Table structure for table prefix_backup_log
#

CREATE TABLE prefix_backup_log (
    id SERIAL PRIMARY KEY,
    courseid int8 NOT NULL default '0',
    time int8 NOT NULL default '0',
    laststarttime int8 NOT NULL default '0',
    info varchar(255) NOT NULL default ''
);
