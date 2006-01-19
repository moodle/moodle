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

    return true;
}

?>
