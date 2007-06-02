<?php   // $Id$

//  Collect ratings, store them, then return to where we came from


    require_once("../../config.php");
    require_once("lib.php");


    $id = required_param('id', PARAM_INT);  // The course these ratings are part of

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

    require_login($course);

    if (isguestuser()) {
        error("Guests are not allowed to rate entries.");
    }

    $returnurl = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null;

    $glossary = false;
    if ($data = data_submitted("$CFG->wwwroot/mod/glossary/view.php")) {    // form submitted
        foreach ((array)$data as $entryid => $rating) {
            if (!is_numeric($entryid)) {
                continue;
            }
            if (!$entry = get_record('glossary_entries', 'id', $entryid)) {
                continue;
            }
            if (!$glossary) {
                if (!$glossary = get_record('glossary', 'id', $entry->glossaryid)) {
                    error('Incorrect glossary id');
                }
                if (!$cm = get_coursemodule_from_instance('glossary', $glossary->id)) {
                    error("Course Module ID was incorrect");
                }
                $context = get_context_instance(CONTEXT_MODULE, $cm->id);

                require_login($course, false, $cm);

                if (!$glossary->assessed) {
                    error('Rating of items not allowed!');
                }
                if ($glossary->assessed == 2 and !has_capability('mod/glossary:rate', $context)) {
                    error('You can not rate items!');
                }

                // add extra info into glossary object
                $glossary->courseid   = $course->id;
                $glossary->cmidnumber = $cm->idnumber;

                $grade_item = glossary_grade_item_get($glossary);

                if (empty($returnurl)) {
                    $returnurl = $CFG->wwwroot.'/mod/glossary/view.php?id='.$cm->id;
                }
            }

            if ($entry->glossaryid != $glossary->id) {
                error('This is not valid entry!!');
            }

            if ($glossary->assesstimestart and $glossary->assesstimefinish) {
                if ($entry->timecreated < $glossary->assesstimestart or $entry->timecreated > $glossary->assesstimefinish) {
                    // we can not grade this, ignore it - this should not happen anyway unless teachr changes setting
                    continue;
                }
            }

            if ($entry->userid == $USER->id) {
                //can not rate own entry
                continue;
            }

            if ($oldrating = get_record("glossary_ratings", "userid", $USER->id, "entryid", $entry->id)) {
                //Check if we must delete the rate
                if ($rating == -999) {
                    delete_records('glossary_ratings','userid',$oldrating->userid, 'entryid',$oldrating->entryid);
                    glossary_update_grades($grade_item, $entry->userid);

                } else if ($rating != $oldrating->rating) {
                    $oldrating->rating = $rating;
                    $oldrating->time = time();
                    if (! update_record("glossary_ratings", $oldrating)) {
                        error("Could not update an old rating ($entry = $rating)");
                    }
                    glossary_update_grades($grade_item, $entry->userid);
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
                glossary_update_grades($grade_item, $entry->userid);
            }
        }

        if (!$glossary) {
            // something wrong happended - no rating changed/added
            error('Incorrect ratings submitted');
        }

        redirect($returnurl, get_string("ratingssaved", "glossary"));

    } else {
        error("This page was not accessed correctly");
    }

?>
