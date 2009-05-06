<?php  // $Id$
    require_once('../../config.php');
    require_once('lib.php');

    $dataid = required_param('dataid', PARAM_INT); // The forum the rated posts are from

    if (!$data = get_record('data', 'id', $dataid)) {
        error("Incorrect data id");
    }

    if (!$course = get_record('course', 'id', $data->course)) {
        error("Course ID was incorrect");
    }

    if (!$cm = get_coursemodule_from_instance('data', $data->id)) {
        error("Course Module ID was incorrect");
    }

    require_login($course, false, $cm);

    if (isguestuser()) {
        error("Guests are not allowed to rate entries.");
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/data:rate', $context);

    if (!$data->assessed) {
        error("Rating of items not allowed!");
    }

    if (!$frmdata = data_submitted() or !confirm_sesskey()) {
        error("This page was not accessed correctly");
    }

/// Calculate scale values
    $scale_values = make_grades_menu($data->scale);

    $count = 0;

    foreach ((array)$frmdata as $recordid => $rating) {
        if (!is_numeric($recordid)) {
            continue;
        }

        if (!$record = get_record('data_records', 'id', $recordid)) {
            error("Record ID is incorrect");
        }

        if ($data->id != $record->dataid) {
            error("Incorrect record.");
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

        if ($oldrating = get_record('data_ratings', 'userid', $USER->id, 'recordid', $record->id)) {
            if ($rating == -999) {
                delete_records('data_ratings', 'userid', $oldrating->userid, 'recordid', $oldrating->recordid);
                data_update_grades($data, $record->userid);

            } else if ($rating != $oldrating->rating) {
                $oldrating->rating = $rating;
                if (! update_record('data_ratings', $oldrating)) {
                    error("Could not update an old rating ($record->id = $rating)");
                }
                data_update_grades($data, $record->userid);

            }

        } else if ($rating) {
            $newrating = new object();
            $newrating->userid   = $USER->id;
            $newrating->recordid = $record->id;
            $newrating->rating   = $rating;
            if (! insert_record('data_ratings', $newrating)) {
                error("Could not insert a new rating ($record->id = $rating)");
            }
            data_update_grades($data, $record->userid);
        }
    }

    if ($count == 0) {
        error("Incorrect submitted ratings data");
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

?>
