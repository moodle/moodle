<?PHP // $Id$

function quiz_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2002110100) {

        echo "The quiz module is under heavy development ... at this stage you should delete all existing quiz tables, as well as the quiz entry in the 'modules' table, then come back here to rebuild them.";

    }

    return true;
}

?>
