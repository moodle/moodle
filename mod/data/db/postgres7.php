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

    return true;
}

?>
