<?php  //$Id$

// This file keeps track of upgrades to 
// the scorm module
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

function xmldb_scorm_upgrade($oldversion=0) {

    global $CFG, $THEME, $DB;

    $dbman = $DB->get_manager();

    $result = true;

//===== 1.9.0 upgrade line ======//

    // Adding missing 'whatgrade' field to table scorm
    if ($result && $oldversion < 2008073000) {
        $table = new xmldb_table('scorm');
        $field = new xmldb_field('whatgrade');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'grademethod');
        
        /// Launch add field whatgrade
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        
        upgrade_mod_savepoint($result, 2008073000, 'scorm');
    }
    
    return $result;
}

?>