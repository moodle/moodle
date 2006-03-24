<?php  //$Id$

// MySQL commands for upgrading this question type

function qtype_rqp_upgrade($oldversion=0) {
    global $CFG;


    if ($oldversion == 0) { // First time install
        $result = modify_database("$CFG->dirroot/question/type/rqp/db/mysql.sql");
        return $result;
    }

    // Question type was installed before. Upgrades must be applied

//    if ($oldversion < 2005071600) {
//        
//    }

    return true;
}

?>
