<?PHP

function attendance_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2003091001) {

       # Do something ...

    }

    return true;
}

?>
