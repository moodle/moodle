<?php  //$Id$

// This file keeps track of upgrades to 
// the glossary module
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

function xmldb_glossary_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of "/lib/ddllib.php" function calls
/// }
    
    if ($result && $oldversion < 2006111400) {

    /// MDL-10475, set override for legacy:student before dropping studentcanpost
    /// if the glossary disables student postings
    
        if ($glossaries = get_records('glossary', 'studentcanpost', '0')) {
            foreach ($glossaries as $glossary) {
                if ($cm = get_coursemodule_from_instance('glossary', $glossary->id)) {
                    // add student override in this instance
                    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
                    // find all roles with legacy:student
                    if ($studentroles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW)) {
                        foreach ($studentroles as $studentrole) {
                            assign_capability('mod/glossary:write', CAP_PREVENT, $studentrole->id, $context->id);
                        }
                    }
                }
            }
        }

    /// Define field studentcanpost to be dropped from glossary
        $table = new XMLDBTable('glossary');
        $field = new XMLDBField('studentcanpost');

    /// Launch drop field studentcanpost
        $result = $result && drop_field($table, $field);
    }  

    if ($result && $oldversion < 2007060600) {
        require_once($CFG->dirroot.'/mod/glossary/lib.php');
        // too much debug output
        $db->debug = false;
        glossary_update_grades();
        $db->debug = true;
    }  
    
    return $result;
}

?>
