<?PHP 

function quiz_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2004042501) {
        include_once("$CFG->dirroot/mod/quiz/lib.php");
        quiz_refresh_events();
    }

    return true;
}

?>
