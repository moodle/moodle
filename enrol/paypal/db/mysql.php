<?PHP  //$Id$

// MySQL commands for upgrading this enrolment module

function paypal_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($oldversion == 0) {
        modify_database("$CFG->dirroot/enrol/paypal/db/mysql.sql");
    }

    return $result;

}

?>
