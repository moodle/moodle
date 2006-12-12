<?php
    // defaultsettings.php
    // deafault settings are done here, saves doing all this twice in
    // both the rendering routine and the config screen

    if (!isset($forcereset)) {
        $forcereset = false;
    }

    if (!isset($CFG->filter_multilang_force_old) or $forcereset) {
        set_config('filter_multilang_force_old', 0);
    }

?>
