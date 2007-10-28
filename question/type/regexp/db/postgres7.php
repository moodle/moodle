<?php  //$Id$

// PostgreSQL commands for upgrading this question type

function qtype_regexp_upgrade($oldversion=0) {
    global $CFG;


    if ($oldversion == 0) { // First time install
        $result = modify_database("$CFG->dirroot/question/type/regexp/db/postgres7.sql");
        return $result;
    }

    // Question type was installed before. Upgrades must be applied
    if ($oldversion < 2007012800) {
        $result = modify_database("$CFG->dirroot/question/type/regexp/db/postgres7_02.sql");
        return $result;
        
    }
    return true;
}

?>
