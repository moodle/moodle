<?PHP // $Id$

////////////////////////////////////////////////////////////////////////////////
//  Code fragment to define the module version etc.
//  This fragment is called by /admin/index.php
////////////////////////////////////////////////////////////////////////////////

$module->version  = 2002080500;
$module->cron     = 60;

function assignment_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    if ($oldversion < 2002080500) {

        execute_sql("
        CREATE TABLE `assignment` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `course` int(10) unsigned NOT NULL default '0',
          `name` varchar(255) NOT NULL default '',
          `description` text NOT NULL,
          `type` int(10) unsigned NOT NULL default '1',
          `maxbytes` int(10) unsigned NOT NULL default '100000',
          `timedue` int(10) unsigned NOT NULL default '0',
          `grade` int(10) NOT NULL default '0',
          `timemodified` int(10) unsigned NOT NULL default '0',
          PRIMARY KEY  (`id`)
        ) COMMENT='Defines assignments'
        ");
        
        execute_sql("
        CREATE TABLE `assignment_submissions` (
          `id` int(10) unsigned NOT NULL default '0',
          `assignment` int(10) unsigned NOT NULL default '0',
          `user` int(10) unsigned NOT NULL default '0',
          `timecreated` int(10) unsigned NOT NULL default '0',
          `timemodified` int(10) unsigned NOT NULL default '0',
          `numfiles` int(10) unsigned NOT NULL default '0',
          `grade` int(11) NOT NULL default '0',
          `comment` text NOT NULL,
          `teacher` int(10) unsigned NOT NULL default '0',
          `timemarked` int(10) unsigned NOT NULL default '0',
          `mailed` tinyint(1) unsigned NOT NULL default '0',
          PRIMARY KEY  (`id`)
        ) COMMENT='Info about submitted assignments'
        ");
        
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'view', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'add', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'update', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'view submissions', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'upload', 'assignment', 'name') ");

    }
    return true;
}


?>

