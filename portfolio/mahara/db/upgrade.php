<?php

// This file keeps track of upgrades to
// the portfolio/mahara plugin
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

function xmldb_portfolio_mahara_upgrade($oldversion) {

    global $CFG, $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes
    $result = true;

    return $result;

}
