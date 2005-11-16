<?PHP

function lams_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2005062800) {

       # Do something ...

    }

    return true;
}

?>
