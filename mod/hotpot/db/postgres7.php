<?PHP

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function hotpot_upgrade($oldversion) {
    global $CFG;
    $ok = true;

    // set path to update functions
    $update_to_v2 = "$CFG->dirroot/mod/hotpot/db/update_to_v2.php";

    // if the version number indicates this could be an early HotPot v2.1 (Moodle 1.6),
    // check this is not actually HotPot v2.0 (Moodle 1.5) with an overly advanced version number
    if ($oldversion>2005031400 && $oldversion<=2006082899) {
        require_once $update_to_v2;
        if (hotpot_db_field_exists('hotpot_attempts', 'details')) {
            $oldversion = 2005031400;
        }
    }

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
    if ($oldversion < 2006083101) {
        require_once $update_to_v2;
        $ok = $ok && hotpot_update_to_v2_2();
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return $ok;
}
?>
