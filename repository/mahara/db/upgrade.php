<?php

function xmldb_repository_mahara_upgrade($oldversion) {

    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

/// And upgrade begins here. For each one, you'll need one
/// block of code similar to the next one. Please, delete
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of database_manager methods
/// }

    return $result;
}
