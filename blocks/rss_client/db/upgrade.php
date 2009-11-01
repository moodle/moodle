<?php

// This file keeps track of upgrades to
// the rss_client block
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_block_rss_client_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

/// And upgrade begins here. For each one, you'll need one
/// block of code similar to the next one. Please, delete
/// this comment lines once this file start handling proper
/// upgrade code.

    if ($result && $oldversion < 2007080100) { //New version in version.php
    /// block_rss_timeout config setting must be block_rss_client_timeout
        $DB->set_field('config', 'name', 'block_rss_client_timeout', array('name'=>'block_rss_timeout'));

    /// rss_client savepoint reached
        upgrade_block_savepoint($result, 2007080100, 'rss_client');
    }

    if ($result && $oldversion < 2009072901) {
        // Remove config variable which is no longer used..
        $result = $result && $DB->delete_records('config', array('name' =>'block_rss_client_submitters'));
        upgrade_block_savepoint($result, 2009072901, 'rss_client');
    }


    return $result;
}


