<?php // $Id$
    if (empty($CFG->hotpot_initialdisable)) {
        if (!$DB->count_records('hotpot')) {
            $DB->set_field('modules', 'visible', 0, array('name'=>'hotpot'));  // Disable it by default
            set_config('hotpot_initialdisable', 1);
        }
    }

?>
