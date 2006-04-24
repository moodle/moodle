<?php

function data_upgrade($oldversion) {
/// This function does anything necessary to upgrade
/// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2006011900) {
        table_column("data_content", "", "content1", "text", "", "", "", "not null");
        table_column("data_content", "", "content2", "text", "", "", "", "not null");
        table_column("data_content", "", "content3", "text", "", "", "", "not null");
        table_column("data_content", "", "content4", "text", "", "", "", "not null");
    }

    if ($oldversion < 2006011901) {
        table_column("data", "", "approval", "integer", "4", "unsigned", "0", "not null");
        table_column("data_records", "", "approved", "integer", "4", "unsigned", "0", "not null");
    }
    
    if ($oldversion < 2006020801) {
        table_column("data", "", "scale", "integer");
        table_column("data", "", "assessed", "integer");
        table_column("data", "", "assesspublic", "integer");
    }

    if ($oldversion < 2006022700) {
        table_column("data_comments", "", "created", "integer");
        table_column("data_comments", "", "modified", "integer");
    }
    
    if ($oldversion < 2006030700) {
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'view', 'data', 'name');");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'add', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'update', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'record delete', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'fields add', 'data_fields', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'fields update', 'data_fields', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'templates saved', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('data', 'templates defaults', 'data', 'name')");
    }
    
    if ($oldversion < 2006032700) {
        table_column('data', '', 'defaultsort', 'integer', '10', 'unsigned', '0');
        table_column('data', '', 'defaultsortdir', 'tinyint', '4', 'unsigned', '0', 'not null', 'defaultsort');
        table_column('data', '', 'editany', 'tinyint', '4', 'unsigned', '0', 'not null', 'defaultsortdir');
    }

    if ($oldversion < 2006032900) {
        table_column('data', '', 'csstemplate', 'text', '', '', '', 'not null', 'rsstemplate');
    }
    
    return true;
}

?>
