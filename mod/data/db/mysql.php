<?php

function data_upgrade($oldversion) {
/// This function does anything necessary to upgrade
/// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2006011900) {
        table_column("data_content", "", "content1", "longtext", "", "", "", "not null");
        table_column("data_content", "", "content2", "longtext", "", "", "", "not null");
        table_column("data_content", "", "content3", "longtext", "", "", "", "not null");
        table_column("data_content", "", "content4", "longtext", "", "", "", "not null");
    }

    if ($oldversion < 2006011901) {
        table_column("data", "", "approval", "tinyint", "4");
        table_column("data_records", "", "approved", "tinyint", "4");
    }

    if ($oldversion < 2006020801) {
        table_column("data", "", "scale", "integer", "10", "signed");
        table_column("data", "", "assessed", "integer", "10");
        table_column("data", "", "assesspublic", "integer", "4");
    }
    
    if ($oldversion < 2006022700) {
        table_column("data_comments", "", "created", "integer", "10");
        table_column("data_comments", "", "modified", "integer", "10");
    }
    
    if ($oldversion < 2006030700) {
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'add', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'update', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'record delete', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'fields add', 'data_fields', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'fields update', 'data_fields', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'templates saved', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'templates def', 'data', 'name')");
    }
    
    if ($oldversion < 2006032700) {
        table_column('data', '', 'defaultsort', 'integer', '10', 'unsigned', '0');
        table_column('data', '', 'defaultsortdir', 'tinyint', '4', 'unsigned', '0', 'not null', 'defaultsort');
        table_column('data', '', 'editany', 'tinyint', '4', 'unsigned', '0', 'not null', 'defaultsortdir');
    }

    if ($oldversion < 2006032900) {
        table_column('data', '', 'csstemplate', 'text', '', '', '', 'not null', 'rsstemplate');
    }
    
    if ($oldversion < 2006050500) { // 2 fields have got no default null values
        table_column('data_comments','content','content','text','','','','not null');
        table_column('data_fields','description','description','text','','','','not null');
        table_column('data_fields','param1','param1','text','','','','not null');
        table_column('data_fields','param2','param2','text','','','','not null');
        table_column('data_fields','param3','param3','text','','','','not null');
        table_column('data_fields','param4','param4','text','','','','not null');
        table_column('data_fields','param5','param5','text','','','','not null');
        table_column('data_fields','param6','param6','text','','','','not null');
        table_column('data_fields','param7','param7','text','','','','not null');
        table_column('data_fields','param8','param8','text','','','','not null');
        table_column('data_fields','param9','param9','text','','','','not null');
        table_column('data_fields','param10','param10','text','','','','not null');
    }
    
    if ($oldversion < 2006052400) {
        table_column('data','','rsstitletemplate','text','','','','not null','rsstemplate');
    }

    if ($oldversion < 2006081700) {
        table_column('data', '', 'jstemplate', 'text', '', '', '', 'not null', 'csstemplate');
    }
    /*
    if ($oldversion < 2006092000) {
        // Upgrades for new roles and capabilities support.
        require_once($CFG->dirroot.'/mod/data/lib.php');

        $datamod = get_record('modules', 'name', 'data');

        if ($data = get_records('data')) {

            if (!$teacherroles = get_roles_with_capability('moodle/legacy:teacher', CAP_ALLOW)) {
                notify('Default teacher role was not found. Roles and permissions '.
                       'for all your forums will have to be manually set after '.
                       'this upgrade.');
            }
            if (!$studentroles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW)) {
                notify('Default student role was not found. Roles and permissions '.
                       'for all your forums will have to be manually set after '.
                       'this upgrade.');
            }
            foreach ($data as $d) {
                if (!data_convert_to_roles($d, $teacherroles, $studentroles)) {
                    notify('Data with id '.$d->id.' was not upgraded');
                }
            }
            // We need to rebuild all the course caches to refresh the state of
            // the forum modules.
            include_once( "$CFG->dirroot/course/lib.php" );
            rebuild_course_cache();

        } // End if.

        modify_database('', 'ALTER TABLE prefix_data DROP COLUMN participants;');
        modify_database('', 'ALTER TABLE prefix_data DROP COLUMN assesspublic;');
        modify_database('', 'ALTER TABLE prefix_data DROP COLUMN groupmode;');
        
    }
    */
    return true;
}

?>
