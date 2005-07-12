<?php 
    if (empty($CFG->journal_initialdisable)) {
        if (!count_records('journal')) {
            set_field('modules', 'visible', 0, 'name', 'journal');  // Disable it by default
            set_config('journal_initialdisable', 1);
        }
    }

?>
