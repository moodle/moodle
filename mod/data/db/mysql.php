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
    return true;
}

?>
