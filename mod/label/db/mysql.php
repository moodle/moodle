<?PHP

function label_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2003091400) {
        table_column("label", "", "course", "integer", "10", "unsigned", "0", "not null", "id");
    }

    return true;
}

?>
