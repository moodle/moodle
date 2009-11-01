<?php

//  Collect ratings, store them, then return to where we came from


    require_once('../../config.php');
    require_once('lib.php');

    $glossaryid = required_param('glossaryid', PARAM_INT); // The forum the rated posts are from

    $PAGE->set_url(new moodle_url($CFG->wwwroot.'/mod/glossary/rate.php', array('glossaryid'=>$glossaryid)));

    if (!$glossary = $DB->get_record('glossary', array('id'=>$glossaryid))) {
        print_error('invalidid', 'glossary');
    }

    if (!$course = $DB->get_record('course', array('id'=>$glossary->course))) {
        print_error('invalidcourseid');
    }

    if (!$cm = get_coursemodule_from_instance('glossary', $glossary->id)) {
        print_error('invalidcoursemodule');
    }

    require_login($course, false, $cm);

    if (isguestuser()) {
        print_error('guestnorate');
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $glossary->cmidnumber = $cm->idnumber;

    if (!$glossary->assessed) {
        print_error('nopermissiontorate');
    }

    if ($glossary->assessed == 2) {
        require_capability('mod/glossary:rate', $context);
    }

    if (!empty($_SERVER['HTTP_REFERER'])) {
        $returnurl = $_SERVER['HTTP_REFERER'];
    } else {
        $returnurl = $CFG->wwwroot.'/mod/glossary/view.php?id='.$cm->id;
    }

    if ($data = data_submitted()) {    // form submitted

    /// Calculate scale values
        $scale_values = make_grades_menu($glossary->scale);

        foreach ((array)$data as $entryid => $rating) {
            if (!is_numeric($entryid)) {
                continue;
            }

            if (!$entry = $DB->get_record('glossary_entries', array('id'=>$entryid))) {
                continue;
            }

            if ($entry->glossaryid != $glossary->id) {
                print_error('invalidentry');
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

            if ($oldrating = $DB->get_record("glossary_ratings", array("userid"=>$USER->id, "entryid"=>$entry->id))) {
                //Check if we must delete the rate
                if ($rating == -999) {
                    $DB->delete_records('glossary_ratings', array('userid'=>$oldrating->userid, 'entryid'=>$oldrating->entryid));
                    glossary_update_grades($glossary, $entry->userid);

                } else if ($rating != $oldrating->rating) {
                    $oldrating->rating = $rating;
                    $oldrating->time = time();
                    $DB->update_record("glossary_ratings", $oldrating);
                    glossary_update_grades($glossary, $entry->userid);
                }

            } else if ($rating >= 0) {
                $newrating = new object();
                $newrating->userid  = $USER->id;
                $newrating->time    = time();
                $newrating->entryid = $entry->id;
                $newrating->rating  = $rating;

                $DB->insert_record("glossary_ratings", $newrating);
                glossary_update_grades($glossary, $entry->userid);
            }
        }

        redirect($returnurl, get_string("ratingssaved", "glossary"));

    } else {
        print_error('invalidaccess');
    }


