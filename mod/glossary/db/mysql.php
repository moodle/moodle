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
    
    if ( $oldversion < 2003091500 ) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}glossary_entries` ".
                    " ADD attachment VARCHAR(100) NOT NULL default '' AFTER `format`");
    }

    if ( $oldversion < 2003091600 ) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}glossary` ".
                    " ADD `showspecial` TINYINT(2) UNSIGNED DEFAULT '1' NOT NULL AFTER `mainglossary` , ".
                    " ADD `showalphabet` TINYINT(2) UNSIGNED DEFAULT '1' NOT NULL AFTER `showspecial` , ".
                    " ADD `showall` TINYINT(2) UNSIGNED DEFAULT '1' NOT NULL AFTER `showalphabet` ");
    }
    
    if ( $oldversion < 2003091800 ) {

        execute_sql("CREATE TABLE `{$CFG->prefix}glossary_categories` (
                    `id` INT(10) unsigned NOT NULL auto_increment,
                    `glossaryid` INT(10) UNSIGNED NOT NULL default '0',
                    `name` VARCHAR(255) NOT NULL default '',
                    PRIMARY KEY  (`id`)
                    ) TYPE=MyISAM COMMENT='all categories for glossary entries'");

        execute_sql("CREATE TABLE `{$CFG->prefix}glossary_entries_categories` (
                    `categoryid` INT(10) UNSIGNED NOT NULL default '1',
                    `entryid` INT(10) UNSIGNED NOT NULL default '0',
                    PRIMARY KEY  (`categoryid`, `entryid`)
                    ) TYPE=MyISAM COMMENT='categories of each glossary entry'");
     }
     
     if ( $oldversion < 2003092100 ) {
          execute_sql("ALTER TABLE `{$CFG->prefix}glossary_entries_categories` CHANGE `categoryid` `categoryid` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL ");
     }

     if ( $oldversion < 2003092101 ) {
          execute_sql("ALTER TABLE `{$CFG->prefix}glossary_entries_categories` ADD `ID` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
     }

    return true;
}

?>



