<?php   // LAMS is disabled by default
    if (empty($CFG->lams_initialdisable)) {
        if (!count_records('lams')) {
            set_field('modules', 'visible', 0, 'name', 'lams');  // Disable it by default
            set_config('lams_initialdisable', 1);
        }
    }
?>
