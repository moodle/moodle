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
  new_id integer default NULL,
  PRIMARY KEY (backup_code, file_type, path)
);

# --------------------------------------------------------

CREATE INDEX prefix_backup_codetypepath_idx ON prefix_backup_files (backup_code,file_type,path);

#
# Table structure for table prefix_backup_ids
#

CREATE TABLE prefix_backup_ids (
  prefix_backup_codenameid_idx PRIMARY KEY,
  backup_code int(12) unsigned NOT NULL default '0',
  table_name varchar(30) NOT NULL default '',
  old_id int(10) unsigned NOT NULL default '0',
  new_id int(10) unsigned default NULL,
  info mediumtext,
  PRIMARY KEY (backup_code, table_name, old_id)
);

CREATE INDEX prefix_backup_codenameid_idx ON prefix_backup_ids (backup_code,table_name,old_id);


