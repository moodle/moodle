<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function resource_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2003082000) {
        table_column("resource", "course", "course", "integer", "10", "unsigned", "0");
    }

    if ($oldversion < 2004013101) {
        modify_database("", "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('resource', 'update', 'resource', 'name');");
        modify_database("", "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('resource', 'add', 'resource', 'name');");
    }

    if ($oldversion < 2004071000) {
        table_column("resource", "", "popup", "text", "", "", "", "", "alltext");
        if ($resources = get_records_select("resource", "type='3' OR type='5'", "", "id, alltext")) {
            foreach ($resources as $resource) {
                $resource->popup = addslashes($resource->alltext);
                $resource->alltext = "";
                if (!update_record("resource", $resource)) {
                    notify("Error updating popup field for resource id = $resource->id");
                }                
            }
        }
        require_once("$CFG->dirroot/course/lib.php");
        rebuild_course_cache();
    }

    if ($oldversion < 2004071300) {
        table_column("resource", "", "options", "varchar", "255", "", "", "", "popup");
    }

    if ($oldversion < 2004071303) {
        table_column("resource", "type", "type", "varchar", "30", "", "", "", "");

        modify_database("", "UPDATE prefix_resource SET type='reference' WHERE type='1';");
        modify_database("", "UPDATE prefix_resource SET type='file', options='frame' WHERE type='2';");
        modify_database("", "UPDATE prefix_resource SET type='file' WHERE type='3';");
        modify_database("", "UPDATE prefix_resource SET type='text', options='0' WHERE type='4';");
        modify_database("", "UPDATE prefix_resource SET type='file' WHERE type='5';");
        modify_database("", "UPDATE prefix_resource SET type='html' WHERE type='6';");
        modify_database("", "UPDATE prefix_resource SET type='file' WHERE type='7';");
        modify_database("", "UPDATE prefix_resource SET type='text', options='3' WHERE type='8';");
        modify_database("", "UPDATE prefix_resource SET type='directory' WHERE type='9';");
    }

    if ($oldversion < 2004080801) {
        modify_database("", "UPDATE prefix_resource SET alltext=reference,type='html' WHERE type='reference';");
        rebuild_course_cache();
    }

    if ($oldversion < 2004111200) { //drop first to avoid conflicts when upgrading
        execute_sql("ALTER TABLE {$CFG->prefix}resource DROP INDEX course;",false);

        modify_database('','ALTER TABLE prefix_resource ADD INDEX course (course);');
    }

    if ($oldversion < 2005041100) { // replace wiki-like with markdown
        include_once( "$CFG->dirroot/lib/wiki_to_markdown.php" );
        $wtm = new WikiToMarkdown();
        $wtm->update( 'resource','alltext','options' );
    }
    
    if ($oldversion < 2006042800) {
        execute_sql("UPDATE {$CFG->prefix}resource SET reference='' WHERE reference IS NULL");
        table_column('resource','reference','reference','varchar','255','','','not null');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}


?>
