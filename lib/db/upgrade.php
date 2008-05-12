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

    if ($result && $oldversion < 2008050700) {
    /// Fix minor problem caused by MDL-5482.
        require_once($CFG->dirroot . '/question/upgrade.php');
        $result = $result && question_fix_random_question_parents();
        upgrade_main_savepoint($result, 2008050700);
    }

    if ($result && $oldversion < 2008051200) {
        // if guest role used as default user role unset it and force admin to choose new setting
        if (!empty($CFG->defaultuserroleid)) {
            if ($role = get_record('role', 'id', $CFG->defaultuserroleid)) {
                if ($guestroles = get_roles_with_capability('moodle/legacy:guest', CAP_ALLOW)) {
                    if (isset($guestroles[$role->id])) {
                        set_config('defaultuserroleid', null);
                        notify('Guest role removed from "Default role for all users" setting, please select another role.', 'notifysuccess');
                    }
                }
            } else {
                set_config('defaultuserroleid', null);
            }
        }
    }

    if ($result && $oldversion < 2008051201) {
        notify('Increasing size of user idnumber field, this may take a while...', 'notifysuccess');

    /// Define index idnumber (not unique) to be dropped form user
        $table = new XMLDBTable('user');
        $index = new XMLDBIndex('idnumber');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('idnumber'));

    /// Launch drop index idnumber
        if (index_exists($table, $index)) {
            $result = $result && drop_index($table, $index);
        }

    /// Changing precision of field idnumber on table user to (255)
        $table = new XMLDBTable('user');
        $field = new XMLDBField('idnumber');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null, 'password');

    /// Launch change of precision for field idnumber
        $result = $result && change_field_precision($table, $field);

    /// Launch add index idnumber again
        $index = new XMLDBIndex('idnumber');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('idnumber'));
        $result = $result && add_index($table, $index);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008051201);
    }

    return $result;
}


?>
