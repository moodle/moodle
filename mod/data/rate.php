<?php

require_once('../../config.php');
require_once('lib.php');

$dataid = required_param('dataid', PARAM_INT); // The forum the rated posts are from

$PAGE->set_url('/mod/data/rate.php', array('dataid'=>$dataid));

if (!$data = $DB->get_record('data', array('id'=>$dataid))) {
    print_error('invalidid', 'data');
}

if (!$course = $DB->get_record('course', array('id'=>$data->course))) {
    print_error('invalidcourseid');
}

if (!$cm = get_coursemodule_from_instance('data', $data->id)) {
    print_error('invalidcoursemodule');
} else {
    $data->cmidnumber = $cm->id; //MDL-12961
}

require_login($course, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/data:rate', $context);

if (!$data->assessed) {
    print_error('cannotrate', 'data');
}

if (!$frmdata = data_submitted() or !confirm_sesskey()) {
    print_error('invalidaccess', 'data');
}

/// Calculate scale values
$scale_values = make_grades_menu($data->scale);

$count = 0;

foreach ((array)$frmdata as $recordid => $rating) {
    if (!is_numeric($recordid)) {
        continue;
    }

    if (!$record = $DB->get_record('data_records', array('id'=>$recordid))) {
        print_error('invalidid', 'data');
    }

    if ($data->id != $record->dataid) {
        print_error('invalidrecord', 'data');
    }

    if ($record->userid == $USER->id) {
        continue;
    }

/// Check rate is valid for that database scale values
    if (!array_key_exists($rating, $scale_values) && $rating != -999) {
        print_error('invalidrate', 'data', '', $rating);
    }

    // input validation ok

    $count++;

    if ($oldrating = $DB->get_record('data_ratings', array('userid'=>$USER->id, 'recordid'=>$record->id))) {
        if ($rating == -999) {
            $DB->delete_records('data_ratings', array('userid'=>$oldrating->userid, 'recordid'=>$oldrating->recordid));
            data_update_grades($data, $record->userid);

        } else if ($rating != $oldrating->rating) {
            $oldrating->rating = $rating;
            $DB->update_record('data_ratings', $oldrating);
            data_update_grades($data, $record->userid);

        }

    } else if ($rating) {
        $newrating = new object();
        $newrating->userid   = $USER->id;
        $newrating->recordid = $record->id;
        $newrating->rating   = $rating;
        $DB->insert_record('data_ratings', $newrating);
        data_update_grades($data, $record->userid);
    }
}

if ($count == 0) {
    print_error('invalidratedata', 'data');
}

if (!empty($_SERVER['HTTP_REFERER'])) {
    redirect($_SERVER['HTTP_REFERER'], get_string('ratingssaved', 'data'));
} else {
    // try to guess where to return
    if ($count == 1) {
        redirect('view.php?mode=single&amp;rid='.$record->id, get_string('ratingssaved', 'data'));
    } else {
        redirect('view.php?d='.$data->id, get_string('ratingssaved', 'data'));
    }
}

