<?PHP  //$Id$

// PostgreSQL commands for upgrading this enrolment module

function paypal_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($oldversion == 0) {
        $result = modify_database("$CFG->dirroot/enrol/paypal/db/postgres7.sql");
    }

    return $result;

}

?>
