<?php

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->dirroot.'/course/report/log/lib.php');

    if (has_capability('coursereport/log:view', $context)) {
        echo $OUTPUT->heading(get_string('chooselogs') .':');

        print_log_selector_form($course);
    }

    if (has_capability('coursereport/log:viewlive', $context)) {
        echo $OUTPUT->heading(get_string('chooselivelogs') .':');
        echo '<p>';
        $link = new moodle_url('/course/report/log/live.php?id='. $course->id);
        echo $OUTPUT->action_link($link, get_string('livelogs'), new popup_action('click', $link, 'livelog', array('height' => 500, 'width' => 800)));
        echo '</p>';
    }

