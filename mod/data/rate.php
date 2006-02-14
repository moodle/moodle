<?php
    require_once("../../config.php");
    require_once("lib.php");
    print_object(data_submitted());

    if (isguest()) {
        error("Guests are not allowed to rate posts.", $_SERVER["HTTP_REFERER"]);
    }

    $id = required_param('id',PARAM_INT);  // The course these ratings are part of

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

    require_login($course->id);

    $CFG->debug = 0;    /// Temporarily

    $returntoview = false;

    if ($data = data_submitted()) {

        $lastrecordid = 0;

        foreach ((array)$data as $recordid => $rating) {
            if ($recordid == "id") {
                continue;
            }

            $recordid = (int)$recordid;
            $lastrecordid = $recordid;
            echo "recordid ".$recordid;
            if ($oldrating = get_record("data_ratings", "userid", $USER->id, "recordid", $recordid)) {
                if ($rating != $oldrating->rating) {
                    $oldrating->rating = $rating;
                    if (! update_record("data_ratings", $oldrating)) {
                        error("Could not update an old rating ($recordid = $rating)");
                    }
                }
            } else if ($rating) {
                unset($newrating);
                $newrating->userid = $USER->id;
                $newrating->recordid = $recordid;
                $newrating->rating = $rating;
                
                if (! insert_record("data_ratings", $newrating)) {
                    error("Could not insert a new rating ($recordid = $rating)");
                }
            }
        }

        redirect($_SERVER["HTTP_REFERER"], get_string("ratingssaved", "forum"));
    } else {
        error("This page was not accessed correctly");
    }


?>
