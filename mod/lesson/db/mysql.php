<?PHP

function lesson_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2004012400) {

       # Do something ...

    }

    return true;
}

?>
