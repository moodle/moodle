rem
rem Table structure for table prefix_backup_files
rem

drop table prefix_backup_files;

CREATE TABLE prefix_backup_files (
  backup_code number(10) default '0' NOT NULL,
  file_type varchar2(10) default '' NOT NULL,
  path varchar2(255) default '' NOT NULL,
  old_id number(10) default NULL,
  new_id number(10) default NULL
);

ALTER TABLE prefix_backup_files
  ADD CONSTRAINT pbf_pk 
  PRIMARY KEY (backup_code,file_type,path);

COMMENT on table prefix_backup_files is 'To store and recode ids to user and course files.';

rem --------------------------------------------------------
rem
rem Table structure for table prefix_backup_ids
rem

drop table prefix_backup_ids;

CREATE TABLE prefix_backup_ids (
  backup_code number(12) default '0' NOT NULL,
  table_name varchar2(30) default '' NOT NULL,
  old_id number(10) default '0' NOT NULL,
  new_id number(10) default NULL,
  info varchar2(20)
);

ALTER TABLE prefix_backup_ids
  ADD CONSTRAINT pbi_pk 
  PRIMARY KEY (backup_code,table_name,old_id);

COMMENT on table prefix_backup_ids is 'To store and convert ids in backup/restore';

rem --------------------------------------------------------
rem
rem Table structure for table prefix_backup_ids
rem

drop TABLE prefix_backup_config;

CREATE TABLE prefix_backup_config (
  id number(10) not null,
  name varchar2(255) not null,
  value varchar2(255) not null,
  constraint pk_baco primary key (id),
  constraint uk_baco_name unique (name)
);

COMMENT on table prefix_backup_config is 'To store backup configuration variables';

drop sequence p_backup_config_seq;
create sequence p_backup_config_seq;

create or replace trigger p_backup_config_trig
  before insert on prefix_backup_config
  referencing new as new_row
  for each row
  begin
    select p_backup_config_seq.nextval into :new_row.id from dual;
  end;
/
