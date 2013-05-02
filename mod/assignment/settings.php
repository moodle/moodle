<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/assignment/lib.php');

    if (isset($CFG->maxbytes)) {
        $maxbytes = 0;
        if (isset($CFG->assignment_maxbytes)) {
            $maxbytes = $CFG->assignment_maxbytes;
        }
        $settings->add(new admin_setting_configselect('assignment_maxbytes', get_string('maximumsize', 'assignment'),
                           get_string('configmaxbytes', 'assignment'), 1048576, get_max_upload_sizes($CFG->maxbytes, 0, 0, $maxbytes)));
    }

    $options = array(ASSIGNMENT_COUNT_WORDS   => trim(get_string('numwords', '', '?')),
                     ASSIGNMENT_COUNT_LETTERS => trim(get_string('numletters', '', '?')));
    $settings->add(new admin_setting_configselect('assignment_itemstocount', get_string('itemstocount', 'assignment'),
                       get_string('configitemstocount', 'assignment'), ASSIGNMENT_COUNT_WORDS, $options));

    $settings->add(new admin_setting_configcheckbox('assignment_showrecentsubmissions', get_string('showrecentsubmissions', 'assignment'),
                       get_string('configshowrecentsubmissions', 'assignment'), 1));
}
