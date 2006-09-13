<?PHP
function hotpot_upgrade($oldversion) {
    global $CFG;
    $ok = true;

    // if the version number indicates this could be an early HotPot v2.1 (Moodle 1.6),
    // check this is not actually HotPot v2.0 (Moodle 1.5) with an overly advanced version number
    if ($oldversion>2005031400 && $oldversion<=2006082899) {
        $columns = $db->MetaColumns($CFG->prefix.'hotpot_attempts');
        foreach ($columns as $column) {
            if ($column->name=='details') {
                // the "hotpot_attempts" table has a "details" field so this is actually HotPot v2.0
                // reset the version number in order to trigger the correct order of updates
                $oldversion = 2005031400;
                break;
            }
        }
    }

    // set path to update functions
    $update_to_v2 = "$CFG->dirroot/mod/hotpot/db/update_to_v2.php";

    // update from HotPot v1 to HotPot v2
    if ($oldversion < 2005031400) {
        require_once $update_to_v2;
        $ok = $ok && hotpot_update_to_v2_from_v1();
    }

    // update to HotPot v2.1
    if ($oldversion < 2005090700) {
        require_once $update_to_v2;
        $ok = $ok && hotpot_update_to_v2_1();
    }
    if ($oldversion > 2005031419 && $oldversion < 2005090702) {
        require_once $update_to_v2;
        $ok = $ok && hotpot_update_to_v2_1_2();
    }
    if ($oldversion < 2005090706) {
        require_once $update_to_v2;
        $ok = $ok && hotpot_update_to_v2_1_6();
    }
    if ($oldversion < 2005090708) {
        require_once $update_to_v2;
        $ok = $ok && hotpot_update_to_v2_1_8();
    }
    if ($oldversion < 2006042103) {
        require_once $update_to_v2;
        $ok = $ok && hotpot_update_to_v2_1_16();
    }
    if ($oldversion < 2006042602) {
        require_once $update_to_v2;
        $ok = $ok && hotpot_update_to_v2_1_17();
    }
    if ($oldversion < 2006042803) {
        require_once $update_to_v2;
        $ok = $ok && hotpot_update_to_v2_1_18();
    }
    if ($oldversion < 2006071600) {
        require_once $update_to_v2;
        $ok = $ok && hotpot_update_to_v2_1_21();
    }

    return $ok;
}
?>
