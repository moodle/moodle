<?php  // $Id$
    require_once('../../config.php');
    require_once('lib.php');

    if (!($data = data_submitted($CFG->wwwroot.'/mod/data/view.php')) or !confirm_sesskey()) {
        error("This page was not accessed correctly");
    }

    $count = 0;

    foreach ((array)$data as $recordid => $rating) {
        if (!is_numeric($recordid)) {
            continue;
        }

        if (!$record = get_record('data_records', 'id', $recordid)) {
            error("Record ID is incorrect");
        }
        if (!$data = get_record('data', 'id', $record->dataid)) {
            error("Data ID is incorrect");
        }
        if (!$course = get_record('course', 'id', $data->course)) {
            error("Course is misconfigured");
        }
        if (!$cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
            error("Course Module ID was incorrect");
        }

        require_login($course->id, false, $cm);

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        if (isguest()) {
            error("Guests are not allowed to rate posts");
        }

        if (!$data->assessed or !has_capability('mod/data:rate', $context)) {
            error("Not allowed to rate.");
        }

        if ($record->userid == $USER->id) {
            error("You can not rate your own data");
        }

        if (!$scale = make_grades_menu($data->scale)) {
            error("Icorrect scale");
        }

        if (!array_key_exists($rating, $scale)) {
            error("Icorrect rating value");
        }

        // input validation ok

        $count++;

        if ($oldrating = get_record('data_ratings', 'userid', $USER->id, 'recordid', $record->id)) {
            if ($rating != $oldrating->rating) {
                $oldrating->rating = $rating;
                if (! update_record('data_ratings', $oldrating)) {
                    error("Could not update an old rating ($record->id = $rating)");
                }
            }
        } else if ($rating) {
            $newrating = new object();
            $newrating->userid   = $USER->id;
            $newrating->recordid = $record->id;
            $newrating->rating   = $rating;
            if (! insert_record('data_ratings', $newrating)) {
                error("Could not insert a new rating ($record->id = $rating)");
            }
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
