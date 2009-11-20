<?php   // $Id$

//  Collect ratings, store them, then return to where we came from


    require_once('../../config.php');
    require_once('lib.php');

    $glossaryid = required_param('glossaryid', PARAM_INT); // The forum the rated posts are from

    if (!$glossary = get_record('glossary', 'id', $glossaryid)) {
        error("Incorrect glossary id");
    }

    if (!$course = get_record('course', 'id', $glossary->course)) {
        error("Course ID was incorrect");
    }

    if (!$cm = get_coursemodule_from_instance('glossary', $glossary->id)) {
        error("Course Module ID was incorrect");
    }

    require_login($course, false, $cm);

    if (isguestuser()) {
        error("Guests are not allowed to rate entries.");
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $glossary->cmidnumber = $cm->idnumber;

    if (!$glossary->assessed) {
        error("Rating of items not allowed!");
    }

    if ($glossary->assessed == 2) {
        require_capability('mod/glossary:rate', $context);
    }

    if (!empty($_SERVER['HTTP_REFERER'])) {
        $returnurl = $_SERVER['HTTP_REFERER'];
    } else {
        $returnurl = $CFG->wwwroot.'/mod/glossary/view.php?id='.$cm->id;
    }

    if ($data = data_submitted() and confirm_sesskey()) {    // form submitted

    /// Calculate scale values
        $scale_values = make_grades_menu($glossary->scale);

        foreach ((array)$data as $entryid => $rating) {
            if (!is_numeric($entryid)) {
                continue;
            }
            if (!$entry = get_record('glossary_entries', 'id', $entryid)) {
                continue;
            }

            if ($entry->glossaryid != $glossary->id) {
                error("This is not valid entry!");
            }

            if ($glossary->assesstimestart and $glossary->assesstimefinish) {
                if ($entry->timecreated < $glossary->assesstimestart or $entry->timecreated > $glossary->assesstimefinish) {
                    // we can not rate this, ignore it - this should not happen anyway unless teacher changes setting
                    continue;
                }
            }

            if ($entry->userid == $USER->id) {
                //can not rate own entry
                continue;
            }

        /// Check rate is valid for that glossary scale values
            if (!array_key_exists($rating, $scale_values) && $rating != -999) {
                print_error('invalidrate', 'glossary', '', $rating);
            }

            if ($oldrating = get_record("glossary_ratings", "userid", $USER->id, "entryid", $entry->id)) {
                //Check if we must delete the rate
                if ($rating == -999) {
                    delete_records('glossary_ratings','userid',$oldrating->userid, 'entryid',$oldrating->entryid);
                    glossary_update_grades($glossary, $entry->userid);

                } else if ($rating != $oldrating->rating) {
                    $oldrating->rating = $rating;
                    $oldrating->time = time();
                    if (! update_record("glossary_ratings", $oldrating)) {
                        error("Could not update an old rating ($entry = $rating)");
                    }
                    glossary_update_grades($glossary, $entry->userid);
                }

            } else if ($rating >= 0) {
                $newrating = new object();
                $newrating->userid  = $USER->id;
                $newrating->time    = time();
                $newrating->entryid = $entry->id;
                $newrating->rating  = $rating;

                if (! insert_record("glossary_ratings", $newrating)) {
                    error("Could not insert a new rating ($entry->id = $rating)");
                }
                glossary_update_grades($glossary, $entry->userid);
            }
        }

        redirect($returnurl, get_string("ratingssaved", "glossary"));

    } else {
        error("This page was not accessed correctly");
    }

?>
