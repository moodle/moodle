<?php //$Id$

function rss_client_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2005111400) {
        // title and description should be TEXT as we don't have control over their length.
        table_column('block_rss_client','title','title','text');
        table_column('block_rss_client','description','description','text');
    }

    if ($oldversion < 2005090201) {
        modify_database('', 'ALTER TABLE prefix_block_rss_client
            ALTER COLUMN title SET DEFAULT \'\',
            ALTER COLUMN description SET DEFAULT \'\'');
    }

    return true;
}

?>