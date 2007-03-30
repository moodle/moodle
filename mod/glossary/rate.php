<?php   // $Id$

//  Collect ratings, store them, then return to where we came from


    require_once("../../config.php");
    require_once("lib.php");


    $id = required_param('id', PARAM_INT);  // The course these ratings are part of

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

    require_login($course->id);

    if (isguest()) {
        error("Guests are not allowed to rate entries.", $_SERVER["HTTP_REFERER"]);
    }

    if ($data = data_submitted("$CFG->wwwroot/mod/glossary/view.php")) {    // form submitted
        print_object($data);
        foreach ((array)$data as $entry => $rating) {
            if ($entry == "id") {
                continue;
            }
            if ($oldrating = get_record("glossary_ratings", "userid", $USER->id, "entryid", $entry)) {
                //Check if we must delete the rate
                if ($rating == -999) {
                    delete_records('glossary_ratings','userid',$oldrating->userid, 'entryid',$oldrating->entryid);
                } else if ($rating != $oldrating->rating) {
                    $oldrating->rating = $rating;
                    $oldrating->time = time();
                    if (! update_record("glossary_ratings", $oldrating)) {
                        error("Could not update an old rating ($entry = $rating)");
                    }
                }
            } else if ($rating >= 0) {
                unset($newrating);
                $newrating->userid = $USER->id;
                $newrating->time = time();
                $newrating->entryid = $entry;
                $newrating->rating = $rating;

                if (! insert_record("glossary_ratings", $newrating)) {
                    error("Could not insert a new rating ($entry = $rating)");
                }
            }
        }
        redirect($_SERVER["HTTP_REFERER"], get_string("ratingssaved", "glossary"));

    } else {
        error("This page was not accessed correctly");
    }

?>
