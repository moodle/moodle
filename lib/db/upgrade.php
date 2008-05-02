<?PHP  //$Id$

// This file keeps track of upgrades to Moodle.
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
// using the functions defined in lib/ddllib.php


function xmldb_main_upgrade($oldversion=0) {

    global $CFG, $THEME, $USER, $db;

    $result = true;

    ////////////////////////////////////////
    ///upgrade supported only from 1.9.x ///
    ////////////////////////////////////////

    if ($result && $oldversion < 2008030700) {

    /// Define index contextid-lowerboundary (not unique) to be dropped form grade_letters
        $table = new XMLDBTable('grade_letters');
        $index = new XMLDBIndex('contextid-lowerboundary');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('contextid', 'lowerboundary'));

    /// Launch drop index contextid-lowerboundary
        $result = $result && drop_index($table, $index);

    /// Define index contextid-lowerboundary-letter (unique) to be added to grade_letters
        $table = new XMLDBTable('grade_letters');
        $index = new XMLDBIndex('contextid-lowerboundary-letter');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('contextid', 'lowerboundary', 'letter'));

    /// Launch add index contextid-lowerboundary-letter
        $result = $result && add_index($table, $index);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008030700);
    }

    if ($result && $oldversion < 2008050100) {
        // Update courses that used weekscss to weeks
        $result = set_field('course', 'format', 'weeks', 'format', 'weekscss');
        upgrade_main_savepoint($result, 2008050100);
    }

    if ($result && $oldversion < 2008050200) {
        // remove unused config options
        unset_config('statsrolesupgraded');
        upgrade_main_savepoint($result, 2008050200);
    }

    return $result;
}


?>
