<?php

require('../config.php');
$PAGE->set_url(new moodle_url($CFG->wwwroot.'/calendar/view.php'));
redirect($CFG->wwwroot.'/calendar/view.php');