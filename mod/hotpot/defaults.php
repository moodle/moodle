<?php // $Id$
    if (empty($CFG->hotpot_initialdisable)) {
        if (!count_records('hotpot')) {
            set_field('modules', 'visible', 0, 'name', 'hotpot');  // Disable it by default
            set_config('hotpot_initialdisable', 1);
        }
    }

?>
