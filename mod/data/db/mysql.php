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
        modify_database('', "INSERT INTO prefix_log_display VALUES ('data', 'add', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display VALUES ('data', 'update', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display VALUES ('data', 'record delete', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display VALUES ('data', 'fields add', 'data_fields', 'name')");
        modify_database('', "INSERT INTO prefix_log_display VALUES ('data', 'fields update', 'data_fields', 'name')");
        modify_database('', "INSERT INTO prefix_log_display VALUES ('data', 'templates saved', 'data', 'name')");
        modify_database('', "INSERT INTO prefix_log_display VALUES ('data', 'templates def', 'data', 'name')");
    }
    
    if ($oldversion < 2006032700) {
        table_column("data", "", "defaultsort", "integer", "10", "signed");
        table_column("data", "", "editany", "tinyint", "4");
    }
    
    return true;
}

?>
