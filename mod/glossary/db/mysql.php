<?PHP

function glossary_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2003091000) {

        execute_sql(" ALTER TABLE `{$CFG->prefix}glossary` ".
                    " ADD `allowduplicatedentries` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `studentcanpost` , ".
                    " ADD `displayformat` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `allowduplicatedentries` , ".
                    " ADD `mainglossary` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `displayformat` ");

        execute_sql(" ALTER TABLE `{$CFG->prefix}glossary_entries` ".
                    " ADD timecreated INT(10) UNSIGNED NOT NULL default '0' AFTER `format` , ".
                    " ADD timemodified INT(10) UNSIGNED NOT NULL default '0' AFTER `timecreated` , ".
			  " ADD teacherentry TINYINT(2) UNSIGNED NOT NULL default '0' AFTER `timemodified` ");

        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('glossary', 'delete', 'glossary', 'name') ");
        execute_sql(" INSERT INTO {$CFG->prefix}log_display VALUES ('glossary', 'delete entry', 'glossary', 'name') ");

    }

    return true;
}

?>

