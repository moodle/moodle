<?php  //$Id$

// PostgreSQL commands for upgrading this question type

function qtype_calculated_upgrade($oldversion=0) {
    global $CFG;
    require_once("$CFG->libdir/questionlib.php");

    if ($oldversion == 0) { // First time install
        $result = modify_database("$CFG->dirroot/question/questiontypes/calculated/db/postgres7.sql");
        return $result;
    }

    // Question type was installed before. Upgrades must be applied

//    if ($oldversion < 2005071600) {
//        
//    }

    return true;
}

?>
