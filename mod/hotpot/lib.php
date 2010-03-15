<?PHP  // $Id$

//////////////////////////////////
/// CONFIGURATION settings

if (!isset($CFG->hotpot_showtimes)) {
    set_config("hotpot_showtimes", 0);
}
if (!isset($CFG->hotpot_excelencodings)) {
    set_config("hotpot_excelencodings", "");
}

//////////////////////////////////
/// CONSTANTS and GLOBAL VARIABLES

$CFG->hotpotroot = "$CFG->dirroot/mod/hotpot";
$CFG->hotpottemplate = "$CFG->hotpotroot/template";
if (!empty($_SERVER['HTTP_USER_AGENT'])) {
    $CFG->hotpotismobile = preg_match('/Alcatel|ATTWS|DoCoMo|Doris|Hutc3G|J-PHONE|Java|KDDI|KGT|LGE|MOT|Nokia|portalmmm|ReqwirelessWeb|SAGEM|SHARP|SIE-|SonyEricsson|Teleport|UP\.Browser|UPG1|Wapagsim/', $_SERVER['HTTP_USER_AGENT']);
} else {
    $CFG->hotpotismobile = false;
}

define("HOTPOT_JS", "$CFG->wwwroot/mod/hotpot/hotpot-full.js");

define("HOTPOT_NO",  "0");
define("HOTPOT_YES", "1");

define ("HOTPOT_TEXTSOURCE_QUIZ", "0");
define ("HOTPOT_TEXTSOURCE_FILENAME", "1");
define ("HOTPOT_TEXTSOURCE_FILEPATH", "2");
define ("HOTPOT_TEXTSOURCE_SPECIFIC", "3");

define("HOTPOT_LOCATION_COURSEFILES", "0");
define("HOTPOT_LOCATION_SITEFILES",   "1");

$HOTPOT_LOCATION = array (
    HOTPOT_LOCATION_COURSEFILES => get_string("coursefiles"),
    HOTPOT_LOCATION_SITEFILES   => get_string("sitefiles"),
);

define("HOTPOT_OUTPUTFORMAT_BEST",     "1");
define("HOTPOT_OUTPUTFORMAT_V3",      "10");
define("HOTPOT_OUTPUTFORMAT_V4",      "11");
define("HOTPOT_OUTPUTFORMAT_V5",      "12");
define("HOTPOT_OUTPUTFORMAT_V5_PLUS", "13");
define("HOTPOT_OUTPUTFORMAT_V6",      "14");
define("HOTPOT_OUTPUTFORMAT_V6_PLUS", "15");
define("HOTPOT_OUTPUTFORMAT_FLASH",   "20");
define("HOTPOT_OUTPUTFORMAT_MOBILE",  "30");

$HOTPOT_OUTPUTFORMAT = array (
    HOTPOT_OUTPUTFORMAT_BEST    => get_string("outputformat_best", "hotpot"),
    HOTPOT_OUTPUTFORMAT_V6_PLUS => get_string("outputformat_v6_plus", "hotpot"),
    HOTPOT_OUTPUTFORMAT_V6      => get_string("outputformat_v6", "hotpot"),
    HOTPOT_OUTPUTFORMAT_V5_PLUS => get_string("outputformat_v5_plus", "hotpot"),
    HOTPOT_OUTPUTFORMAT_V5      => get_string("outputformat_v5", "hotpot"),
    HOTPOT_OUTPUTFORMAT_V4      => get_string("outputformat_v4", "hotpot"),
    HOTPOT_OUTPUTFORMAT_V3      => get_string("outputformat_v3", "hotpot"),
    HOTPOT_OUTPUTFORMAT_FLASH   => get_string("outputformat_flash", "hotpot"),
    HOTPOT_OUTPUTFORMAT_MOBILE  => get_string("outputformat_mobile", "hotpot"),
);
$HOTPOT_OUTPUTFORMAT_DIR = array (
    HOTPOT_OUTPUTFORMAT_V6_PLUS => 'v6',
    HOTPOT_OUTPUTFORMAT_V6      => 'v6',
    HOTPOT_OUTPUTFORMAT_V5_PLUS => 'v5',
    HOTPOT_OUTPUTFORMAT_V5      => 'v5',
    HOTPOT_OUTPUTFORMAT_V4      => 'v4',
    HOTPOT_OUTPUTFORMAT_V3      => 'v3',
    HOTPOT_OUTPUTFORMAT_FLASH   => 'flash',
    HOTPOT_OUTPUTFORMAT_MOBILE  => 'mobile',
);
foreach ($HOTPOT_OUTPUTFORMAT_DIR as $format=>$dir) {
    if (is_file("$CFG->hotpottemplate/$dir.php") && is_dir("$CFG->hotpottemplate/$dir")) {
        // do nothing ($format is available)
    } else {
        // $format is not available, so remove it
        unset($HOTPOT_OUTPUTFORMAT[$format]);
        unset($HOTPOT_OUTPUTFORMAT_DIR[$format]);
    }
}
define("HOTPOT_NAVIGATION_BAR",     "1");
define("HOTPOT_NAVIGATION_FRAME",   "2");
define("HOTPOT_NAVIGATION_IFRAME",  "3");
define("HOTPOT_NAVIGATION_BUTTONS", "4");
define("HOTPOT_NAVIGATION_GIVEUP",  "5");
define("HOTPOT_NAVIGATION_NONE",    "6");

$HOTPOT_NAVIGATION = array (
    HOTPOT_NAVIGATION_BAR     => get_string("navigation_bar", "hotpot"),
    HOTPOT_NAVIGATION_FRAME   => get_string("navigation_frame", "hotpot"),
    HOTPOT_NAVIGATION_IFRAME  => get_string("navigation_iframe", "hotpot"),
    HOTPOT_NAVIGATION_BUTTONS => get_string("navigation_buttons", "hotpot"),
    HOTPOT_NAVIGATION_GIVEUP  => get_string("navigation_give_up", "hotpot"),
    HOTPOT_NAVIGATION_NONE    => get_string("navigation_none", "hotpot"),
);

define("HOTPOT_JCB",    "1");
define("HOTPOT_JCLOZE", "2");
define("HOTPOT_JCROSS", "3");
define("HOTPOT_JMATCH", "4");
define("HOTPOT_JMIX",   "5");
define("HOTPOT_JQUIZ",  "6");
define("HOTPOT_TEXTOYS_RHUBARB",   "7");
define("HOTPOT_TEXTOYS_SEQUITUR",  "8");

$HOTPOT_QUIZTYPE = array(
    HOTPOT_JCB    => 'JCB',
    HOTPOT_JCLOZE => 'JCloze',
    HOTPOT_JCROSS => 'JCross',
    HOTPOT_JMATCH => 'JMatch',
    HOTPOT_JMIX   => 'JMix',
    HOTPOT_JQUIZ  => 'JQuiz',
    HOTPOT_TEXTOYS_RHUBARB  => 'Rhubarb',
    HOTPOT_TEXTOYS_SEQUITUR => 'Sequitur'
);

define("HOTPOT_JQUIZ_MULTICHOICE", "1");
define("HOTPOT_JQUIZ_SHORTANSWER", "2");
define("HOTPOT_JQUIZ_HYBRID",      "3");
define("HOTPOT_JQUIZ_MULTISELECT", "4");

define("HOTPOT_GRADEMETHOD_HIGHEST", "1");
define("HOTPOT_GRADEMETHOD_AVERAGE", "2");
define("HOTPOT_GRADEMETHOD_FIRST",   "3");
define("HOTPOT_GRADEMETHOD_LAST",    "4");

$HOTPOT_GRADEMETHOD = array (
    HOTPOT_GRADEMETHOD_HIGHEST => get_string("gradehighest", "quiz"),
    HOTPOT_GRADEMETHOD_AVERAGE => get_string("gradeaverage", "quiz"),
    HOTPOT_GRADEMETHOD_FIRST   => get_string("attemptfirst", "quiz"),
    HOTPOT_GRADEMETHOD_LAST    => get_string("attemptlast",  "quiz"),
);

define("HOTPOT_STATUS_INPROGRESS", "1");
define("HOTPOT_STATUS_TIMEDOUT",   "2");
define("HOTPOT_STATUS_ABANDONED",  "3");
define("HOTPOT_STATUS_COMPLETED",  "4");

$HOTPOT_STATUS = array (
    HOTPOT_STATUS_INPROGRESS => get_string("inprogress", "hotpot"),
    HOTPOT_STATUS_TIMEDOUT   => get_string("timedout",   "hotpot"),
    HOTPOT_STATUS_ABANDONED  => get_string("abandoned",  "hotpot"),
    HOTPOT_STATUS_COMPLETED  => get_string("completed",  "hotpot"),
);

define("HOTPOT_FEEDBACK_NONE", "0");
define("HOTPOT_FEEDBACK_WEBPAGE", "1");
define("HOTPOT_FEEDBACK_FORMMAIL", "2");
define("HOTPOT_FEEDBACK_MOODLEFORUM", "3");
define("HOTPOT_FEEDBACK_MOODLEMESSAGING", "4");

$HOTPOT_FEEDBACK = array (
    HOTPOT_FEEDBACK_NONE => get_string("feedbacknone", "hotpot"),
    HOTPOT_FEEDBACK_WEBPAGE => get_string("feedbackwebpage",  "hotpot"),
    HOTPOT_FEEDBACK_FORMMAIL => get_string("feedbackformmail", "hotpot"),
    HOTPOT_FEEDBACK_MOODLEFORUM => get_string("feedbackmoodleforum", "hotpot"),
    HOTPOT_FEEDBACK_MOODLEMESSAGING => get_string("feedbackmoodlemessaging", "hotpot"),
);
if (empty($CFG->messaging)) { // Moodle 1.4 (and less)
    unset($HOTPOT_FEEDBACK[HOTPOT_FEEDBACK_MOODLEMESSAGING]);
}

define("HOTPOT_DISPLAYNEXT_QUIZ",   "0");
define("HOTPOT_DISPLAYNEXT_COURSE", "1");
define("HOTPOT_DISPLAYNEXT_INDEX",  "2");

/**
 * If start and end date for the quiz are more than this many seconds apart
 * they will be represented by two separate events in the calendar
 */
define("HOTPOT_MAX_EVENT_LENGTH", "432000");   // 5 days maximum

//////////////////////////////////
/// CORE FUNCTIONS


// possible return values:
//    false:
//        display moderr.html (if exists) OR "Could not update" and return to couse view
//    string:
//        display as error message and return to course view
//  true (or non-zero number):
//        continue to $hotpot->redirect (if set) OR hotpot/view.php (to displsay quiz)

// $hotpot is an object containing the values of the form in mod.html
// i.e. all the fields in the 'hotpot' table, plus the following:
//  $hotpot->course       : an id in the 'course' table
//  $hotpot->coursemodule : an id in the 'course_modules' table
//  $hotpot->section      : an id in the 'course_sections' table
//  $hotpot->module       : an id in the 'modules' table
//  $hotpot->modulename   : always 'hotpot'
//  $hotpot->instance     : an id in the 'hotpot' table
//  $hotpot->mode         : 'add' or 'update'
//  $hotpot->sesskey      : unique string required for Moodle's session management

function hotpot_add_instance(&$hotpot) {
    if (hotpot_set_form_values($hotpot)) {
        if ($result = insert_record('hotpot', $hotpot)) {
            $hotpot->id = $result;
            hotpot_update_events($hotpot);
            hotpot_grade_item_update(stripslashes_recursive($hotpot));
        }
    } else {
        $result=  false;
    }
    return $result;
}

function hotpot_update_instance(&$hotpot) {
    if (hotpot_set_form_values($hotpot)) {
        $hotpot->id = $hotpot->instance;
        if ($result = update_record('hotpot', $hotpot)) {
            hotpot_update_events($hotpot);
            //hotpot_grade_item_update(stripslashes_recursive($hotpot));
            hotpot_update_grades(stripslashes_recursive($hotpot));
        }
    } else {
        $result=  false;
    }
    return $result;
}
function hotpot_update_events($hotpot) {

    // remove any previous calendar events for this hotpot
    delete_records('event', 'modulename', 'hotpot', 'instance', $hotpot->id);

    $event = new stdClass();
    $event->description = addslashes($hotpot->summary);
    $event->courseid    = $hotpot->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'hotpot';
    $event->instance    = $hotpot->id;
    $event->timestart   = $hotpot->timeopen;
    if ($cm = get_coursemodule_from_id('hotpot', $hotpot->id)) {
        $event->visible = hotpot_is_visible($cm);
    } else {
        $event->visible = 1;
    }

    if ($hotpot->timeclose && $hotpot->timeopen) {
        // we have both a start and an end date
        $event->eventtype   = 'open';
        $event->timeduration = ($hotpot->timeclose - $hotpot->timeopen);

        if ($event->timeduration > HOTPOT_MAX_EVENT_LENGTH) {  /// Long durations create two events

            $event->name          = addslashes($hotpot->name).' ('.get_string('hotpotopens', 'hotpot').')';
            $event->timeduration  = 0;
            add_event($event);

            $event->timestart    = $hotpot->timeclose;
            $event->eventtype    = 'close';
            $event->name         = addslashes($hotpot->name).' ('.get_string('hotpotcloses', 'hotpot').')';
            unset($event->id);
            add_event($event);
        } else { // single event with duration
            $event->name        = $hotpot->name;
            add_event($event);
        }
    } elseif ($hotpot->timeopen) { // only an open date
        $event->name          = addslashes($hotpot->name).' ('.get_string('hotpotopens', 'hotpot').')';
        $event->eventtype   = 'open';
        $event->timeduration = 0;
        add_event($event);
    } elseif ($hotpot->timeclose) { // only a closing date
        $event->name         = addslashes($hotpot->name).' ('.get_string('hotpotcloses', 'hotpot').')';
        $event->timestart    = $hotpot->timeclose;
        $event->eventtype    = 'close';
        $event->timeduration = 0;
        add_event($event);
    }
}

function hotpot_set_form_values(&$hotpot) {
    $ok = true;
    $hotpot->errors = array(); // these will be reported by moderr.html

    if (empty($hotpot->reference)) {
        $ok = false;
        $hotpot->errors['reference']= get_string('error_nofilename', 'hotpot');
    }

    if (empty($hotpot->studentfeedbackurl) || $hotpot->studentfeedbackurl=='http://') {
        $hotpot->studentfeedbackurl = '';
        switch ($hotpot->studentfeedback) {
            case HOTPOT_FEEDBACK_WEBPAGE:
                $ok = false;
                $hotpot->errors['studentfeedbackurl']= get_string('error_nofeedbackurlwebpage', 'hotpot');
            break;
            case HOTPOT_FEEDBACK_FORMMAIL:
                $ok = false;
                $hotpot->errors['studentfeedbackurl']= get_string('error_nofeedbackurlformmail', 'hotpot');
            break;
        }
    }

    $time = time();
    $hotpot->timecreated = $time;
    $hotpot->timemodified = $time;

    if (empty($hotpot->mode)) {
        // moodle 1.9 (from mod_form.lib)
        if ($hotpot->add) {
            $hotpot->mode = 'add';
        } else if ($hotpot->update) {
            $hotpot->mode = 'update';
        } else {
            $hotpot->mode = '';
        }
    }
    if ($hotpot->quizchain==HOTPOT_YES) {
        switch ($hotpot->mode) {
            case 'add':
                $ok = hotpot_add_chain($hotpot);
            break;
            case 'update':
                $ok = hotpot_update_chain($hotpot);
            break;
        }
    } else { // $hotpot->quizchain==HOTPOT_NO
        hotpot_set_name_summary_reference($hotpot);
    }

    if (isset($hotpot->displaynext)) {
        switch ($hotpot->displaynext) {
            // N.B. redirection only works for Moodle 1.5+
            case HOTPOT_DISPLAYNEXT_COURSE:
                $hotpot->redirect = true;
                $hotpot->redirecturl = "view.php?id=$hotpot->course";
                break;
            case HOTPOT_DISPLAYNEXT_INDEX:
                $hotpot->redirect = true;
                $hotpot->redirecturl = "../mod/hotpot/index.php?id=$hotpot->course";
                break;
            default:
                // use Moodle default action (i.e. go on to display the hotpot quiz)
        }
    } else {
        $hotpot->displaynext = HOTPOT_DISPLAYNEXT_QUIZ;
    }

    // if ($ok && $hotpot->setdefaults) {
    if ($ok) {
        set_user_preference('hotpot_timeopen', $hotpot->timeopen);
        set_user_preference('hotpot_timeclose', $hotpot->timeclose);
        set_user_preference('hotpot_navigation', $hotpot->navigation);
        set_user_preference('hotpot_outputformat', $hotpot->outputformat);
        set_user_preference('hotpot_studentfeedback', $hotpot->studentfeedback);
        set_user_preference('hotpot_studentfeedbackurl', $hotpot->studentfeedbackurl);
        set_user_preference('hotpot_forceplugins', $hotpot->forceplugins);
        set_user_preference('hotpot_shownextquiz', $hotpot->shownextquiz);
        set_user_preference('hotpot_review', $hotpot->review);
        set_user_preference('hotpot_grade', $hotpot->grade);
        set_user_preference('hotpot_grademethod', $hotpot->grademethod);
        set_user_preference('hotpot_attempts', $hotpot->attempts);
        set_user_preference('hotpot_subnet', $hotpot->subnet);
        set_user_preference('hotpot_displaynext', $hotpot->displaynext);
        if ($hotpot->mode=='add') {
            set_user_preference('hotpot_quizchain', $hotpot->quizchain);
            set_user_preference('hotpot_namesource', $hotpot->namesource);
            set_user_preference('hotpot_summarysource', $hotpot->summarysource);
        }
    }
    return $ok;
}
function hotpot_get_chain(&$cm) {
    // get details of course_modules in this section
    $course_module_ids = get_field('course_sections', 'sequence', 'id', $cm->section);
    if (empty($course_module_ids)) {
        $hotpot_modules = array();
    } else {
        $hotpot_modules = get_records_select('course_modules', "id IN ($course_module_ids) AND module=$cm->module");
        if (empty($hotpot_modules)) {
            $hotpot_modules = array();
        }
    }

    // get ids of hotpot modules in this section
    $ids = array();
    foreach ($hotpot_modules as $hotpot_module) {
        $ids[] = $hotpot_module->instance;
    }

    // get details of hotpots in this section
    if (empty($ids)) {
        $hotpots = array();
    } else {
        $hotpots = get_records_list('hotpot', 'id', implode(',', $ids));
    }

    $found = false;
    $chain = array();

    // loop through course_modules in this section
    $ids = explode(',', $course_module_ids);
    foreach ($ids as $id) {

        // check this course_module is a hotpot activity
        if (isset($hotpot_modules[$id])) {

            // store details of this course module and hotpot activity
            $hotpot_id = $hotpot_modules[$id]->instance;
            $chain[$id] = &$hotpot_modules[$id];
            $chain[$id]->hotpot = &$hotpots[$hotpot_id];

            // set $found, if this is the course module we're looking for
            if (isset($cm->coursemodule)) {
                if ($id==$cm->coursemodule) {
                    $found = true;
                }
            } else {
                if ($id==$cm->id) {
                    $found = true;
                }
            }

            // is this the end of a chain
            if (empty($hotpots[$hotpot_id]->shownextquiz)) {
                if ($found) {
                    break; // out of loop
                } else {
                    // restart chain (target cm has not been found yet)
                    $chain = array();
                }
            }
        }
    } // end foreach $ids

    return $found ? $chain : false;
}
function hotpot_is_visible(&$cm) {
    global $CFG, $COURSE;

    // check grouping
    $modulecontext = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (empty($CFG->enablegroupings) || empty($cm->groupmembersonly) || has_capability('moodle/site:accessallgroups', $modulecontext)) {
        // groupings not applicable
    } else if (!isguestuser() && groups_has_membership($cm)) {
        // user is in one of the groups in the allowed grouping
    } else {
        // user is not in the required grouping and does not have sufficiently privileges to view this hotpot activity
        return false;
    }

    // check if user can view hidden activities
    if (isset($COURSE->context)) {
        $coursecontext = &$COURSE->context;
    } else {
        $coursecontext = get_context_instance(CONTEXT_COURSE, $cm->course);
    }
    if (has_capability('moodle/course:viewhiddenactivities', $coursecontext)) {
        return true; // user can view hidden activities
    }

    if (!isset($cm->sectionvisible)) {
        if (! $section = get_record('course_sections', 'id', $cm->section)) {
            error('Course module record contains invalid section');
        }
        $cm->sectionvisible = $section->visible;
    }
    if (empty($cm->sectionvisible)) {
        $visible = HOTPOT_NO;
    } else {
        $visible = HOTPOT_YES;
        if (empty($cm->visible)) {
            if ($chain = hotpot_get_chain($cm)) {
                $startofchain = array_shift($chain);
                $visible = $startofchain->visible;
            }
        }
    }
    return $visible;
}
function hotpot_add_chain(&$hotpot) {
/// add a chain of hotpot actiivities

    global $CFG, $course;

    $ok = true;
    $hotpot->names = array();
    $hotpot->summaries = array();
    $hotpot->references = array();

    $xml_quiz = new hotpot_xml_quiz($hotpot, false, false, false, false, false);

    if (isset($xml_quiz->error)) {
        $hotpot->errors['reference'] = $xml_quiz->error;
        $ok = false;

    } else if (is_dir($xml_quiz->filepath)) {

        // get list of hotpot files in this folder
        if ($dh = @opendir($xml_quiz->filepath)) {
            while (false !== ($file = @readdir($dh))) {
                if (preg_match('/\.(jbc|jcl|jcw|jmt|jmx|jqz|htm|html)$/', $file)) {
                    $hotpot->references[] = "$xml_quiz->reference/$file";
                }
            }
            closedir($dh);

            // get titles
            foreach ($hotpot->references as $i=>$reference) {
                $filepath = $xml_quiz->fileroot.'/'.$reference;
                hotpot_get_titles_and_next_ex($hotpot, $filepath);
                $hotpot->names[$i] = $hotpot->exercisetitle;
                $hotpot->summaries[$i] = $hotpot->exercisesubtitle;
            }

        } else {
            $ok = false;
            $hotpot->errors['reference'] = get_string('error_couldnotopenfolder', 'hotpot', $hotpot->reference);
        }

    } else if (is_file($xml_quiz->filepath)) {

        $filerootlength = strlen($xml_quiz->fileroot) + 1;

        while ($xml_quiz->filepath) {
            hotpot_get_titles_and_next_ex($hotpot, $xml_quiz->filepath, true);
            $hotpot->names[] = $hotpot->exercisetitle;
            $hotpot->summaries[] = $hotpot->exercisesubtitle;
            $hotpot->references[] = substr($xml_quiz->filepath, $filerootlength);

            if ($hotpot->nextexercise) {
                $filepath = $xml_quiz->fileroot.'/'.$xml_quiz->filesubdir.$hotpot->nextexercise;

                // check file is not already in chain
                $reference = substr($filepath, $filerootlength);
                if (in_array($reference, $hotpot->references)) {
                    $filepath = '';
                }
            } else {
                $filepath = '';
            }
            if ($filepath && file_exists($filepath) && is_file($filepath) && is_readable($filepath)) {
                $xml_quiz->filepath = $filepath;
            } else {
                $xml_quiz->filepath = false; // finish while loop
            }
        } // end while

    } else {
        $ok = false;
        $hotpot->errors['reference'] = get_string('error_notfileorfolder', 'hotpot', $hotpot->reference);
    }

    if (empty($hotpot->references) && empty($hotpot->errors['reference'])) {
        $ok = false;
        $hotpot->errors['reference'] = get_string('error_noquizzesfound', 'hotpot', $hotpot->reference);
    }

    if ($ok) {
        $hotpot->visible = HOTPOT_YES;

        if (trim($hotpot->name)=='') {
            $hotpot->name = get_string("modulename", $hotpot->modulename);
        }
        $hotpot->specificname = $hotpot->name;
        $hotpot->specificsummary = $hotpot->summary;

        // add all except last activity in chain

        $i_max = count($hotpot->references)-1;
        for ($i=0; $i<$i_max; $i++) {

            hotpot_set_name_summary_reference($hotpot, $i);
            $hotpot->reference = addslashes($hotpot->reference);

            if (!$hotpot->instance = insert_record("hotpot", $hotpot)) {
                error("Could not add a new instance of $hotpot->modulename", "view.php?id=$hotpot->course");
            }

            // store (hotpot table) id of start of chain
            if ($i==0) {
                $hotpot->startofchain = $hotpot->instance;
            }

            if (isset($course->groupmode)) {
                $hotpot->groupmode = $course->groupmode;
            }

            if (! $hotpot->coursemodule = add_course_module($hotpot)) {
                error("Could not add a new course module");
            }
            if (! $sectionid = add_mod_to_section($hotpot) ) {
                error("Could not add the new course module to that section");
            }

            if (! set_field("course_modules", "section", $sectionid, "id", $hotpot->coursemodule)) {
                error("Could not update the course module with the correct section");
            }

            add_to_log($hotpot->course, "course", "add mod",
                "../mod/$hotpot->modulename/view.php?id=$hotpot->coursemodule",
                "$hotpot->modulename $hotpot->instance"
            );
            add_to_log($hotpot->course, $hotpot->modulename, "add",
                "view.php?id=$hotpot->coursemodule",
                "$hotpot->instance", $hotpot->coursemodule
            );

            // hide tail of chain
            if ($hotpot->shownextquiz==HOTPOT_YES) {
                $hotpot->visible = HOTPOT_NO;
            }
        } // end for ($hotpot->references)

        // settings for final activity in chain
        hotpot_set_name_summary_reference($hotpot, $i);
        $hotpot->reference = addslashes($hotpot->references[$i]);
        $hotpot->shownextquiz = HOTPOT_NO;

        if (isset($hotpot->startofchain)) {
            // redirection only works for Moodle 1.5+
            $hotpot->redirect = true;
            $hotpot->redirecturl = "$CFG->wwwroot/mod/hotpot/view.php?hp=$hotpot->startofchain";
        }
    } // end if $ok

    return $ok;
}
function hotpot_set_name_summary_reference(&$hotpot, $chain_index=NULL) {

    $xml_quiz = NULL;

    $textfields = array('name', 'summary');
    foreach ($textfields as $textfield) {

        $textsource = $textfield.'source';

        // are we adding a chain?
        if (isset($chain_index)) {

            switch ($hotpot->$textsource) {
                case HOTPOT_TEXTSOURCE_QUIZ:
                    if ($textfield=='name') {
                        $hotpot->exercisetitle = $hotpot->names[$chain_index];
                    } else if ($textfield=='summary') {
                        $hotpot->exercisesubtitle = $hotpot->summaries[$chain_index];
                    }
                    break;
                case HOTPOT_TEXTSOURCE_SPECIFIC:
                    $specifictext = 'specific'.$textfield;
                    if (empty($hotpot->$specifictext) && trim($hotpot->$specifictext)=='') {
                        $hotpot->$textfield = '';
                    } else {
                        $hotpot->$textfield = $hotpot->$specifictext.' ('.($chain_index+1).')';
                    }
                    break;
            }
            $hotpot->reference = $hotpot->references[$chain_index];
        }

        if ($hotpot->$textsource==HOTPOT_TEXTSOURCE_QUIZ) {
            if (empty($xml_quiz) && !isset($chain_index)) {
                $xml_quiz = new hotpot_xml_quiz($hotpot, false, false, false, false, false);
                hotpot_get_titles_and_next_ex($hotpot, $xml_quiz->filepath);
            }
            if ($textfield=='name') {
                $hotpot->$textfield = addslashes($hotpot->exercisetitle);
            } else if ($textfield=='summary') {
                $hotpot->$textfield = addslashes($hotpot->exercisesubtitle);
            }
        }
        switch ($hotpot->$textsource) {
            case HOTPOT_TEXTSOURCE_FILENAME:
                $hotpot->$textfield = basename($hotpot->reference);
                break;
            case HOTPOT_TEXTSOURCE_FILEPATH:
                $hotpot->$textfield = '';
                // continue to next lines
            default:
                if (empty($hotpot->$textfield)) {
                    $hotpot->$textfield = str_replace('/', ' ', $hotpot->reference);
                }
        } // end switch
    } // end foreach
}
function hotpot_get_titles_and_next_ex(&$hotpot, $filepath, $get_next=false) {

    $hotpot->exercisetitle = '';
    $hotpot->exercisesubtitle = '';
    $hotpot->nextexercise = '';

    // read the quiz file source
    if ($source = file_get_contents($filepath)) {

        $next = '';
        $title = '';
        $subtitle = '';

        if (preg_match('|\.html?$|', $filepath)) {
            // html file
            if (preg_match('|<h2[^>]*class="ExerciseTitle"[^>]*>(.*?)</h2>|is', $source, $matches)) {
                $title = trim(strip_tags($matches[1]));
            }
            if (empty($title)) {
                if (preg_match('|<title[^>]*>(.*?)</title>|is', $source, $matches)) {
                    $title = trim(strip_tags($matches[1]));
                }
            }
            if (preg_match('|<h3[^>]*class="ExerciseSubtitle"[^>]*>(.*?)</h3>|is', $source, $matches)) {
                $subtitle = trim(strip_tags($matches[1]));
            }
            if ($get_next) {
                if (preg_match('|<div[^>]*class="NavButtonBar"[^>]*>(.*?)</div>|is', $source, $matches)) {
                    $navbuttonbar = $matches[1];
                    if (preg_match_all('|<button[^>]*onclick="'."location='([^']*)'".'[^"]*"[^>]*>|is', $navbuttonbar, $matches)) {
                        $lastbutton = count($matches[0])-1;
                        $next = $matches[1][$lastbutton];
                    }
                }
            }

        } else {
            // xml file (...maybe)
            $xml_tree = new hotpot_xml_tree($source);
            $xml_tree->filetype = '';

            $keys = array_keys($xml_tree->xml);
            foreach ($keys as $key) {
                if (preg_match('/^(hotpot|textoys)-(\w+)-file$/i', $key, $matches)) {
                    $xml_tree->filetype = 'xml';
                    $xml_tree->xml_root = "['$key']['#']";
                    $xml_tree->quiztype = strtolower($matches[2]);
                    break;
                }
            }
            if ($xml_tree->filetype=='xml') {

                $title = strip_tags($xml_tree->xml_value('data,title'));
                $subtitle = $xml_tree->xml_value('hotpot-config-file,'.$xml_tree->quiztype.',exercise-subtitle');

                if ($get_next) {
                    $include = $xml_tree->xml_value('hotpot-config-file,global,include-next-ex');
                    if (!empty($include)) {
                        $next = $xml_tree->xml_value("hotpot-config-file,$xml_tree->quiztype,next-ex-url");
                        if (is_array($next)) {
                            $next = $next[0]; // in case "next-ex-url" was repeated in the xml file
                        }
                    }
                }
            }
        }

        $hotpot->nextexercise = $next;
        $hotpot->exercisetitle = (empty($title) || is_array($title)) ? basename($filepath) : $title;
        $hotpot->exercisesubtitle = (empty($subtitle) || is_array($subtitle)) ? $hotpot->exercisetitle : $subtitle;
    }
}
function hotpot_get_all_instances_in_course($modulename, $course) {
/// called from index.php

    global $CFG;
    $instances = array();

    if (isset($CFG->release) && substr($CFG->release, 0, 3)>=1.2) {
        $groupmode = 'cm.groupmode,';
    } else {
        $groupmode = '';
    }

    $query = "
        SELECT
            cm.id AS coursemodule,
            cm.course AS course,
            cm.module AS module,
            cm.instance AS instance,
            -- cm.section AS section,
            cm.visible AS visible,
            $groupmode
            -- cs.section AS sectionnumber,
            cs.section AS section,
            cs.sequence AS sequence,
            cs.visible AS sectionvisible,
            thismodule.*
        FROM
            {$CFG->prefix}course_modules cm,
            {$CFG->prefix}course_sections cs,
            {$CFG->prefix}modules m,
            {$CFG->prefix}$modulename thismodule
        WHERE
            m.name = '$modulename' AND
            m.id = cm.module AND
            cm.course = '$course->id' AND
            cm.section = cs.id AND
            cm.instance = thismodule.id
    ";
    if ($rawmods = get_records_sql($query)) {

        // cache $isteacher setting

        $isteacher = has_capability('mod/hotpot:viewreport', get_context_instance(CONTEXT_COURSE, $course->id));

        $explodesection = array();
        $order = array();

        foreach ($rawmods as $rawmod) {

            if (empty($explodesection[$rawmod->section])) {
                $explodesection[$rawmod->section] = true;

                $coursemodules = explode(',', $rawmod->sequence);
                foreach ($coursemodules as $i=>$coursemodule) {
                    $order[$coursemodule] = sprintf('%d.%04d', $rawmod->section, $i);
                }
            }

            if ($isteacher) {
                $visible = true;
            } else if ($modulename=='hotpot') {
                $visible = hotpot_is_visible($rawmod);
            } else {
                $visible = $rawmod->visible;
            }

            if ($visible) {
                $instances[$order[$rawmod->coursemodule]] = $rawmod;
            }

        } // end foreach $modinfo

        ksort($instances);
        $instances = array_values($instances);
    }

    return $instances;
}

function hotpot_update_chain(&$hotpot) {
/// update a chain of hotpot actiivities

    $ok = true;
    if ($hotpot_modules = hotpot_get_chain($hotpot)) {

        // skip updating of these fields
        $skipfields = array('id', 'course', 'name', 'reference', 'summary', 'shownextquiz');
        $fields = array();

        foreach ($hotpot_modules as $hotpot_module) {

            if ($hotpot->instance==$hotpot_module->id) {
                // don't need to update this hotpot

            } else {
                // shortcut to hotpot record
                $thishotpot = &$hotpot_module->hotpot;

                // get a list of fields to update (first time only)
                if (empty($fields)) {
                    $fields = array_keys(get_object_vars($thishotpot));
                }

                // assume update is NOT required
                $require_update = false;

                // update field values (except $skipfields)
                foreach($fields as $field) {
                    if (in_array($field, $skipfields) || $thishotpot->$field==$hotpot->$field) {
                        // update not required for this field
                    } else {
                        $require_update = true;
                        $thishotpot->$field = $hotpot->$field;
                    }
                }

                // update $thishotpot, if required
                if ($require_update && !update_record("hotpot", $thishotpot)) {
                    error("Could not update the $hotpot->modulename", "view.php?id=$hotpot->course");
                }
            }
        } // end foreach $ids
    }
    return $ok;
}
function hotpot_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

    if (! $hotpot = get_record("hotpot", "id", $id)) {
        return false;
    }

    if (! delete_records("hotpot", "id", "$id")) {
        return false;
    }

    delete_records("hotpot_questions", "hotpot", "$id");
    if ($attempts = get_records_select("hotpot_attempts", "hotpot='$id'")) {
        $ids = implode(',', array_keys($attempts));
        delete_records_select("hotpot_attempts",  "id IN ($ids)");
        delete_records_select("hotpot_details",   "attempt IN ($ids)");
        delete_records_select("hotpot_responses", "attempt IN ($ids)");
    }

     // remove calendar events for this hotpot
    delete_records('event', 'modulename', 'hotpot', 'instance', $id);

     // remove grade item for this hotpot
    hotpot_grade_item_delete($hotpot);

    return true;
}
function hotpot_delete_and_notify($table, $select, $strtable) {
    $count = max(0, count_records_select($table, $select));
    if ($count) {
        delete_records_select($table, $select);
        $count -= max(0, count_records_select($table, $select));
        if ($count) {
            notify(get_string('deleted')." $count x $strtable");
        }
    }
}

function hotpot_user_complete($course, $user, $mod, $hotpot) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.

    $report = hotpot_user_outline($course, $user, $mod, $hotpot);
    if (empty($report)) {
        print get_string("noactivity", "hotpot");
    } else {
        $date = userdate($report->time, get_string('strftimerecentfull'));
        print $report->info.' '.get_string('mostrecently').': '.$date;
    }
    return true;
}

function hotpot_user_outline($course, $user, $mod, $hotpot) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $report->time = the time they did it
/// $report->info = a short text description

    $report = NULL;
    if ($records = get_records_select("hotpot_attempts", "hotpot='$hotpot->id' AND userid='$user->id'", "timestart ASC", "*")) {
        $report = new stdClass();
        $scores = array();
        foreach ($records as $record){
            if (empty($report->time)) {
                $report->time = $record->timestart;
            }
            $scores[] = hotpot_format_score($record);
        }
        if (empty($scores)) {
            $report->time = 0;
            $report->info = get_string('noactivity', 'hotpot');
        } else {
            $report->info = get_string('score', 'quiz').': '.implode(', ', $scores);
        }
    }
    return $report;
}

function hotpot_format_score($record, $undefined='&nbsp;') {
    if (isset($record->score)) {
        $score = $record->score;
    } else {
        $score = $undefined;
    }
    return $score;
}

function hotpot_format_status($record, $undefined='&nbsp;') {
    global $HOTPOT_STATUS;

    if (isset($record->status) || isset($HOTPOT_STATUS[$record->status])) {
        $status = $HOTPOT_STATUS[$record->status];
    } else {
        $status = $undefined;
    }
    return $status;
}

function hotpot_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity
/// that has occurred in hotpot activities and print it out.
/// Return true if there was output, or false is there was none.

    global $CFG;
    $result = false;

    $records = get_records_sql("
        SELECT
            h.id AS id,
            h.name AS name,
            COUNT(*) AS count_attempts
        FROM
            {$CFG->prefix}hotpot h,
            {$CFG->prefix}hotpot_attempts a
        WHERE
            h.course = $course->id
            AND h.id = a.hotpot
            AND a.id = a.clickreportid
            AND a.starttime > $timestart
        GROUP BY
            h.id, h.name
    ");
    // note that PostGreSQL requires h.name in the GROUP BY clause

    if($records) {
        $names = array();
        foreach ($records as $id => $record){
            if ($cm = get_coursemodule_from_instance('hotpot', $record->id, $course->id)) {
                $context = get_context_instance(CONTEXT_MODULE, $cm->id);

                if (has_capability('mod/hotpot:viewreport', $context)) {
                    $href = "$CFG->wwwroot/mod/hotpot/view.php?hp=$id";
                    $name = '&nbsp;<a href="'.$href.'">'.$record->name.'</a>';
                    if ($record->count_attempts > 1) {
                        $name .= " ($record->count_attempts)";
                    }
                    $names[] = $name;
                }
            }
        }
        if (count($names) > 0) {
            print_headline(get_string('modulenameplural', 'hotpot').':');

            if ($CFG->version >= 2005050500) { // Moodle 1.5+
                echo '<div class="head"><div class="name">'.implode('<br />', $names).'</div></div>';
            } else { // Moodle 1.4.x (or less)
                echo '<font size="1">'.implode('<br />', $names).'</font>';
            }
            $result = true;
        }
    }
    return $result;  //  True if anything was printed, otherwise false
}

function hotpot_get_recent_mod_activity(&$activities, &$index, $sincetime, $courseid, $cmid="", $userid="", $groupid="") {
// Returns all quizzes since a given time.

    global $CFG;

    // If $cmid or $userid are specified, then this restricts the results
    $cm_select = empty($cmid) ? "" : " AND cm.id = '$cmid'";
    $user_select = empty($userid) ? "" : " AND u.id = '$userid'";

    $records = get_records_sql("
        SELECT
            a.*,
            h.name, h.course,
            cm.instance, cm.section,
            u.firstname, u.lastname, u.picture
        FROM
            {$CFG->prefix}hotpot_attempts a,
            {$CFG->prefix}hotpot h,
            {$CFG->prefix}course_modules cm,
            {$CFG->prefix}user u
        WHERE
            a.timefinish > '$sincetime'
            AND a.id = a.clickreportid
            AND a.userid = u.id $user_select
            AND a.hotpot = h.id $cm_select
            AND cm.instance = h.id
            AND cm.course = '$courseid'
            AND h.course = cm.course
        ORDER BY
            a.timefinish ASC
    ");

    if (!empty($records)) {
        foreach ($records as $record) {
            if (empty($groupid) || groups_is_member($groupid, $record->userid)) {

                unset($activity);

                $activity->type = "hotpot";
                $activity->defaultindex = $index;
                $activity->instance = $record->hotpot;

                $activity->name = $record->name;
                $activity->section = $record->section;

                $activity->content->attemptid = $record->id;
                $activity->content->attempt = $record->attempt;
                $activity->content->score = $record->score;
                $activity->content->timestart = $record->timestart;
                $activity->content->timefinish = $record->timefinish;

                $activity->user->userid = $record->userid;
                $activity->user->fullname = fullname($record);
                $activity->user->picture = $record->picture;

                $activity->timestamp = $record->timefinish;

                $activities[] = $activity;

                $index++;
            }
        } // end foreach
    }
}

function hotpot_print_recent_mod_activity($activity, $course, $detail=false) {
/// Basically, this function prints the results of "hotpot_get_recent_activity"

    global $CFG, $THEME, $USER;

    if (isset($THEME->cellcontent2)) {
        $bgcolor =  ' bgcolor="'.$THEME->cellcontent2.'"';
    } else {
        $bgcolor = '';
    }

    print '<table border="0" cellpadding="3" cellspacing="0">';

    print '<tr><td'.$bgcolor.' class="forumpostpicture" width="35" valign="top">';
    print_user_picture($activity->user->userid, $course, $activity->user->picture);
    print '</td><td width="100%"><font size="2">';

    if ($detail) {
        // activity icon
        $src = "$CFG->modpixpath/$activity->type/icon.gif";
        print '<img src="'.$src.'" class="icon" alt="'.$activity->type.'" /> ';

        // link to activity
        $href = "$CFG->wwwroot/mod/hotpot/view.php?hp=$activity->instance";
        print '<a href="'.$href.'">'.$activity->name.'</a> - ';
    }
    if (has_capability('mod/hotpot:viewreport',get_context_instance(CONTEXT_COURSE, $course))) {
        // score (with link to attempt details)
        $href = "$CFG->wwwroot/mod/hotpot/review.php?hp=$activity->instance&attempt=".$activity->content->attemptid;
        print '<a href="'.$href.'">('.hotpot_format_score($activity->content).')</a> ';

        // attempt number
        print get_string('attempt', 'quiz').' - '.$activity->content->attempt.'<br />';
    }

    // link to user
    $href = "$CFG->wwwroot/user/view.php?id=$activity->user->userid&course=$course";
    print '<a href="'.$href.'">'.$activity->user->fullname.'</a> ';

    // time and date
    print ' - ' . userdate($activity->timestamp);

    // duration
    $duration = format_time($activity->content->timestart - $activity->content->timefinish);
    print " &nbsp; ($duration)";

    print "</font></td></tr>";
    print "</table>";
}

function hotpot_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    return true;
}

function hotpot_grades($hotpotid) {
/// Must return an array of grades for a given instance of this module,
/// indexed by user.  It also returns a maximum allowed grade.

    $hotpot = get_record('hotpot', 'id', $hotpotid);
    $return->grades = hotpot_get_grades($hotpot);
    $return->maxgrade = $hotpot->grade;

    return $return;
}
function hotpot_get_grades($hotpot, $user_ids='') {
    global $CFG;

    $grades = array();

    $weighting = $hotpot->grade / 100;
    $precision = hotpot_get_precision($hotpot);

    // set the SQL string to determine the $grade
    $grade = "";
    switch ($hotpot->grademethod) {
        case HOTPOT_GRADEMETHOD_HIGHEST:
            $grade = "ROUND(MAX(score) * $weighting, $precision) AS grade";
            break;
        case HOTPOT_GRADEMETHOD_AVERAGE:
            // the 'AVG' function skips abandoned quizzes, so use SUM(score)/COUNT(id)
            $grade = "ROUND(SUM(score)/COUNT(id) * $weighting, $precision) AS grade";
            break;
        case HOTPOT_GRADEMETHOD_FIRST:
            $grade = "ROUND(score * $weighting, $precision)";
            $grade = sql_concat('timestart', "'_'", $grade);
            $grade = "MIN($grade) AS grade";
            break;
        case HOTPOT_GRADEMETHOD_LAST:
            $grade = "ROUND(score * $weighting, $precision)";
            $grade = sql_concat('timestart', "'_'", $grade);
            $grade = "MAX($grade) AS grade";
            break;
    }

    if ($grade) {
        $userid_condition = empty($user_ids) ? '' : "AND userid IN ($user_ids) ";
        $grades = get_records_sql_menu("
            SELECT userid, $grade
            FROM {$CFG->prefix}hotpot_attempts
            WHERE timefinish>0 AND hotpot='$hotpot->id' $userid_condition
            GROUP BY userid
        ");
        if ($grades) {
            if ($hotpot->grademethod==HOTPOT_GRADEMETHOD_FIRST || $hotpot->grademethod==HOTPOT_GRADEMETHOD_LAST) {
                // remove left hand characters in $grade (up to and including the underscore)
                foreach ($grades as $userid=>$grade) {
                    $grades[$userid] = substr($grades[$userid], strpos($grades[$userid], '_')+1);
                }
            }
        }
    }

    return $grades;
}
function hotpot_get_precision(&$hotpot) {
    return ($hotpot->grademethod==HOTPOT_GRADEMETHOD_AVERAGE || $hotpot->grade<100) ? 1 : 0;
}

/**
 * Return grade for given user or all users.
 *
 * @param object $hotpot
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function hotpot_get_user_grades($hotpot, $userid=0) {
    $grades = array();
    if ($hotpotgrades = hotpot_get_grades($hotpot, $userid)) {
        foreach ($hotpotgrades as $hotpotuserid => $hotpotgrade) {
            $grades[$hotpotuserid] = new stdClass();
            $grades[$hotpotuserid]->id        = $hotpotuserid;
            $grades[$hotpotuserid]->userid    = $hotpotuserid;
            $grades[$hotpotuserid]->rawgrade  = $hotpotgrade;
        }
    }
    if (count($grades)) {
        return $grades;
    } else {
        return false;
    }
}

/**
 * Update grades in central gradebook
 * this function is called from db/upgrade.php
 *     it is initially called with no arguments, which forces it to get a list of all hotpots
 *     it then iterates through the hotpots, calling itself to create a grade record for each hotpot
 *
 * @param object $hotpot null means all hotpots
 * @param int $userid specific user only, 0 means all users
 */
function hotpot_update_grades($hotpot=null, $userid=0, $nullifnone=true) {
    global $CFG;
    if (! function_exists('grade_update')) {
        require_once($CFG->libdir.'/gradelib.php');
    }
    if (is_null($hotpot)) {
        // update (=create) grades for all hotpots
        $sql = "
            SELECT h.*, cm.idnumber as cmidnumber
            FROM {$CFG->prefix}hotpot h, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
            WHERE m.name='hotpot' AND m.id=cm.module AND cm.instance=h.id"
        ;
        if ($rs = get_recordset_sql($sql)) {
            while ($hotpot = rs_fetch_next_record($rs)) {
                hotpot_update_grades($hotpot, 0, false);
            }
            rs_close($rs);
        }
    } else {
        // update (=create) grade for a single hotpot
        if ($grades = hotpot_get_user_grades($hotpot, $userid)) {
            hotpot_grade_item_update($hotpot, $grades);

        } else if ($userid && $nullifnone) {
            // no grades for this user, but we must force the creation of a "null" grade record
            $grade = new object();
            $grade->userid   = $userid;
            $grade->rawgrade = null;
            hotpot_grade_item_update($hotpot, $grade);

        } else {
            // no grades and no userid
            hotpot_grade_item_update($hotpot);
        }
    }
}

/**
 * Update/create grade item for given hotpot
 *
 * @param object $hotpot object with extra cmidnumber
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return object grade_item
 */
function hotpot_grade_item_update($hotpot, $grades=null) {
    global $CFG;
    if (! function_exists('grade_update')) {
        require_once($CFG->libdir.'/gradelib.php');
    }
    $params = array('itemname' => $hotpot->name);
    if (array_key_exists('cmidnumber', $hotpot)) {
        //cmidnumber may not be always present
        $params['idnumber'] = $hotpot->cmidnumber;
    }
    if ($hotpot->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $hotpot->grade;
        $params['grademin']  = 0;

    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
        // Note: when adding a new activity, a gradeitem will *not*
        // be created in the grade book if gradetype==GRADE_TYPE_NONE
        // A gradeitem will be created later if gradetype changes to GRADE_TYPE_VALUE
        // However, the gradeitem will *not* be deleted if the activity's
        // gradetype changes back from GRADE_TYPE_VALUE to GRADE_TYPE_NONE
        // Therefore, we give the user the ability to force the removal of empty gradeitems
        if (! empty($hotpot->removegradeitem)) {
            $params['deleted'] = true;
        }
    }
    return grade_update('mod/hotpot', $hotpot->course, 'mod', 'hotpot', $hotpot->id, 0, $grades, $params);
}

/**
 * Delete grade item for given hotpot
 *
 * @param object $hotpot object
 * @return object grade_item
 */
function hotpot_grade_item_delete($hotpot) {
    global $CFG;
    if (! function_exists('grade_update')) {
        require_once($CFG->libdir.'/gradelib.php');
    }
    return grade_update('mod/hotpot', $hotpot->course, 'mod', 'hotpot', $hotpot->id, 0, null, array('deleted'=>1));
}

function hotpot_get_participants($hotpotid) {
//Must return an array of user ids who are participants
//for a given instance of hotpot. Must include every user involved
//in the instance, independient of his role (student, teacher, admin...)
//See other modules as example.
    global $CFG;

    return get_records_sql("
        SELECT DISTINCT
            u.id, u.id
        FROM
            {$CFG->prefix}user u,
            {$CFG->prefix}hotpot_attempts a
        WHERE
            u.id = a.userid
            AND a.hotpot = '$hotpotid'
    ");
}

function hotpot_scale_used ($hotpotid, $scaleid) {
//This function returns if a scale is being used by one hotpot
//it it has support for grading and scales. Commented code should be
//modified if necessary. See forum, glossary or journal modules
//as reference.

    $report = false;

    //$rec = get_record("hotpot","id","$hotpotid","scale","-$scaleid");
    //
    //if (!empty($rec)  && !empty($scaleid)) {
    //  $report = true;
    //}

    return $report;
}

/**
 * Checks if scale is being used by any instance of hotpot
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any hotpot
 */
function hotpot_scale_used_anywhere($scaleid) {
 return false;
}

//////////////////////////////////////////////////////////
/// Any other hotpot functions go here.
/// Each of them must have a name that starts with hotpot


function hotpot_add_attempt($hotpotid) {
    global $db, $CFG, $USER;

    // get start time of this attempt
    $time = time();

    // set all previous "in progress" attempts at this quiz to "abandoned"
    if ($attempts = get_records_select('hotpot_attempts', "hotpot='$hotpotid' AND userid='$USER->id' AND status='".HOTPOT_STATUS_INPROGRESS."'")) {
        foreach ($attempts as $attempt) {
            if ($attempt->timefinish==0) {
                $attempt->timefinish = $time;
            }
            if ($attempt->clickreportid==0) {
                $attempt->clickreportid = $attempt->id;
            }
            $attempt->status = HOTPOT_STATUS_ABANDONED;
            update_record('hotpot_attempts', $attempt);
        }
    }

    // create and add new attempt record
    $attempt = new stdClass();
    $attempt->hotpot = $hotpotid;
    $attempt->userid = $USER->id;
    $attempt->attempt = hotpot_get_next_attempt($hotpotid);
    $attempt->timestart = $time;

    return insert_record("hotpot_attempts", $attempt);
}
function hotpot_get_next_attempt($hotpotid) {
    global $USER;

    // get max attempt so far
    $i = count_records_select('hotpot_attempts', "hotpot='$hotpotid' AND userid='$USER->id'", 'MAX(attempt)');

    return empty($i) ? 1 : ($i+1);
}
function hotpot_get_question_name($question) {
    $name = '';
    if (isset($question->text)) {
        $name = hotpot_strings($question->text);
    }
    if (empty($name)) {
        $name = $question->name;
    }
    return $name;
}
function hotpot_strings($ids) {

    // array of ids of empty strings
    static $HOTPOT_EMPTYSTRINGS;

    if (!isset($HOTPOT_EMPTYSTRINGS)) { // first time only
        // get ids of empty strings
        $emptystrings = get_records_select('hotpot_strings', 'LENGTH(TRIM(string))=0');
        $HOTPOT_EMPTYSTRINGS = empty($emptystrings) ? array() : array_keys($emptystrings);
    }

    $strings = array();
    if (!empty($ids)) {
        $ids = explode(',', $ids);
        foreach ($ids as $id) {
            if (!in_array($id, $HOTPOT_EMPTYSTRINGS)) {
                $strings[] = hotpot_string($id);
            }
        }
    }
    return implode(',', $strings);
}
function hotpot_string($id) {
    return get_field('hotpot_strings', 'string', 'id', $id);
}

//////////////////////////////////////////////////////////////////////////////////////
/// the class definitions to handle XML trees

// get the standard XML parser supplied with Moodle
require_once("$CFG->libdir/xmlize.php");

// get the default class for hotpot quiz templates
require_once("$CFG->hotpottemplate/default.php");

class hotpot_xml_tree {
    function hotpot_xml_tree($str, $xml_root='') {
        if (empty($str)) {
            $this->xml =  array();
        } else {
            if (empty($CFG->unicodedb)) {
                $str = utf8_encode($str);
            }
            $this->xml =  xmlize($str, 0);
        }
        $this->xml_root = $xml_root;
    }
    function xml_value($tags, $more_tags="[0]['#']") {

        $tags = empty($tags) ? '' : "['".str_replace(",", "'][0]['#']['", $tags)."']";
        eval('$value = &$this->xml'.$this->xml_root.$tags.$more_tags.';');

        if (is_string($value)) {
            if (empty($CFG->unicodedb)) {
                $value = utf8_decode($value);
            }

            // decode angle brackets
            $value = strtr($value, array('&#x003C;'=>'<', '&#x003E;'=>'>', '&#x0026;'=>'&'));

            // remove white space between <table>, <ul|OL|DL> and <OBJECT|EMBED> parts
            // (so it doesn't get converted to <br />)
            $htmltags = '('
            .   'TABLE|\\/?CAPTION|\\/?COL|\\/?COLGROUP|\\/?TBODY|\\/?TFOOT|\\/?THEAD|\\/?TD|\\/?TH|\\/?TR'
            .   '|OL|UL|\\/?LI'
            .   '|DL|\\/?DT|\\/?DD'
            .   '|EMBED|OBJECT|APPLET|\\/?PARAM'
            //. '|SELECT|\\/?OPTION'
            //. '|FIELDSET|\\/?LEGEND'
            //. '|FRAMESET|\\/?FRAME'
            .   ')'
            ;

            $space = '(?:\s|(?:<br[^>]*>))+';
            $search = '/(<'.$htmltags.'[^>]*'.'>)'.$space.'(?='.'<)/is';
            $value = preg_replace($search, '\\1', $value);

            // replace remaining newlines with <br />
            $value = str_replace("\n", '<br />', $value);

            // encode unicode characters as HTML entities
            // (in particular, accented charaters that have not been encoded by HP)

            // unicode characters can be detected by checking the hex value of a character
            //  00 - 7F : ascii char (roman alphabet + punctuation)
            //  80 - BF : byte 2, 3 or 4 of a unicode char
            //  C0 - DF : 1st byte of 2-byte char
            //  E0 - EF : 1st byte of 3-byte char
            //  F0 - FF : 1st byte of 4-byte char
            // if the string doesn't match the above, it might be
            //  80 - FF : single-byte, non-ascii char
            $search = '/'.'[\xc0-\xdf][\x80-\xbf]'.'|'.'[\xe0-\xef][\x80-\xbf]{2}'.'|'.'[\xf0-\xff][\x80-\xbf]{3}'.'|'.'[\x80-\xff]'.'/';
            $value = preg_replace_callback($search, array(&$this, 'xml_value_callback'), $value);
        }
        return $value;
    }
    function xml_value_callback(&$matches) {
        return hotpot_utf8_to_html_entity($matches[0]);
    }
    function xml_values($tags) {
        $i = 0;
        $values = array();
        while ($value = $this->xml_value($tags, "[$i]['#']")) {
            $values[$i++] = $value;
        }
        return $values;
    }
    function obj_value(&$obj, $name) {
        return is_object($obj) ? @$obj->$name : (is_array($obj) ? @$obj[$name] : NULL);
    }
    function encode_cdata(&$str, $tag) {

        static $ILLEGAL_STRINGS = array(
            "\r\n"  => '&lt;br /&gt;',
            "\r"    => '&lt;br /&gt;',
            "\n"    => '&lt;br /&gt;',
            '['     => '&#91;',
            ']'     => '&#93;'
        );

        // extract the $tag from the $str(ing), if possible
        $pattern = '|(^.*<'.$tag.'[^>]*)(>.*<)(/'.$tag.'>.*$)|is';
        if (preg_match($pattern, $str, $matches)) {

            // encode problematic CDATA chars and strings
            $matches[2] = strtr($matches[2], $ILLEGAL_STRINGS);

            // if there are any ampersands in "open text"
            // surround them by CDATA start and end markers
            // (and convert HTML entities to plain text)
            $search = '/(?<=>)'.'[^<]*&[^<]*'.'(?=<)/';
            $matches[2] = preg_replace_callback($search, array(&$this, 'encode_cdata_callback'), $matches[2]);

            $str = $matches[1].$matches[2].$matches[3];
        }
    }
    function encode_cdata_callback(&$matches) {
        static $HTML_ENTITIES = array(
            '&apos;' => "'",
            '&quot;' => '"',
            '&lt;'   => '<',
            '&gt;'   => '>',
            '&amp;'  => '&',
        );
        return '<![CDATA['.strtr($matches[0], $HTML_ENTITIES).']]>';
    }
}

class hotpot_xml_quiz extends hotpot_xml_tree {

    // constructor function
    function hotpot_xml_quiz(&$obj, $read_file=true, $parse_xml=true, $convert_urls=true, $report_errors=true, $create_html=true) {
        // obj can be the $_GET array or a form object/array

        global $CFG, $HOTPOT_OUTPUTFORMAT, $HOTPOT_OUTPUTFORMAT_DIR;

        // check xmlize functions are available
        if (! function_exists("xmlize")) {
            error('xmlize functions are not available');
        }

        $this->read_file = $read_file;
        $this->parse_xml = $parse_xml;
        $this->convert_urls = $convert_urls;
        $this->report_errors = $report_errors;
        $this->create_html = $create_html;

        // extract fields from $obj
        //  course     : the course id
        //  reference   : the filename within the files folder
        //  location     : "site" files folder or "course" files folder
        //  navigation   : type of navigation required in quiz
        //  forceplugins : force Moodle compatible media players
        $this->course = $this->obj_value($obj, 'course');
        $this->reference = $this->obj_value($obj, 'reference');
        $this->location = $this->obj_value($obj, 'location');
        $this->navigation = $this->obj_value($obj, 'navigation');
        $this->forceplugins = $this->obj_value($obj, 'forceplugins');

        // can't continue if there is no course or reference
        if (empty($this->course) || empty($this->reference)) {
            $this->error = get_string('error_nocourseorfilename', 'hotpot');
            if ($this->report_errors) {
                error($this->error);
            }
            return;
        }

        $this->course_homeurl = "$CFG->wwwroot/course/view.php?id=$this->course";

        // set filedir, filename and filepath
        switch ($this->location) {
            case HOTPOT_LOCATION_SITEFILES:
                $site = get_site();
                $this->filedir = $site->id;
                break;

            case HOTPOT_LOCATION_COURSEFILES:
            default:
                $this->filedir = $this->course;
                break;
        }
        $this->filesubdir = dirname($this->reference);
        if ($this->filesubdir=='.') {
            $this->filesubdir = '';
        }
        if ($this->filesubdir) {
            $this->filesubdir .= '/';
        }
        $this->filename = basename($this->reference);
        $this->fileroot = "$CFG->dataroot/$this->filedir";
        $this->filepath = "$this->fileroot/$this->reference";

        // read the file, if required
        if ($this->read_file) {

            if (!file_exists($this->filepath) || !is_readable($this->filepath)) {
                $this->error = get_string('error_couldnotopensourcefile', 'hotpot', $this->filepath);
                if ($this->report_errors) {
                    error($this->error, $this->course_homeurl);
                }
                return;
            }

            // read in the XML source
            $this->source = file_get_contents($this->filepath);

            // convert relative URLs to absolute URLs
            if ($this->convert_urls) {
                $this->hotpot_convert_relative_urls($this->source);
            }

            $this->html = '';
            $this->quiztype = '';
            $this->outputformat = 0;

            // is this an html file?
            if (preg_match('|\.html?$|', $this->filename)) {

                $this->filetype = 'html';
                $this->html = &$this->source;

                // relative URLs in stylesheets
                $search = '/'.'(<style[^>]*>)'.'(.*?)'.'(<\/style>)'.'/is';
                $this->source = preg_replace_callback($search, array(&$this, 'callback_stylesheets_urls'), $this->source);

                // relative URLs in "PreloadImages(...);"
                $search = '/'.'(?<='.'PreloadImages'.'\('.')'."([^)]+?)".'(?='.'\);'.')'.'/is';
                $this->source = preg_replace_callback($search, array(&$this, 'callback_preloadimages_urls'), $this->source);

                // relative URLs in <button class="NavButton" ... onclick="location='...'">
                $search = '/'.'(?<='.'onclick="'."location='".')'."([^']*)".'(?='."'; return false;".'")'.'/is';
                $this->source = preg_replace_callback($search, array(&$this, 'callback_navbutton_url'), $this->source);

                // relative URLs in <a ... onclick="window.open('...')...">...</a>
                $search = '/'.'(?<='.'onclick="'."window.open\\('".')'."([^']*)".'(?='."'\\);return false;".'")'.'/is';
                $this->source = preg_replace_callback($search, array(&$this, 'callback_url'), $this->source);

            } else {

                // relative URLs in <a ... onclick="window.open('...')...">...</a>
                $search = '/'.'(?<='.'onclick=&quot;'."window.open\\(&apos;".')'."(.*?)".'(?='."&apos;\\);return false;".'&quot;)'.'/is';
                $this->source = preg_replace_callback($search, array(&$this, 'callback_url'), $this->source);

                if ($this->parse_xml) {

                    $this->filetype = 'xml';

                    // encode "gap fill" text in JCloze exercise
                    $this->encode_cdata($this->source, 'gap-fill');

                    // convert source to xml tree
                    $this->hotpot_xml_tree($this->source);

                    $keys = array_keys($this->xml);
                    foreach ($keys as $key) {
                        if (preg_match('/^(hotpot|textoys)-(\w+)-file$/i', $key, $matches)) {
                            $this->quiztype = strtolower($matches[2]);
                            $this->xml_root = "['$key']['#']";
                            break;
                        }
                    }
                }

                if ($this->create_html) {

                    // set the real output format from the requested output format
                    $this->real_outputformat = $this->obj_value($obj, 'outputformat');
                    $this->draganddrop = '';
                    if (
                        empty($this->real_outputformat) ||
                        $this->real_outputformat==HOTPOT_OUTPUTFORMAT_BEST ||
                        empty($HOTPOT_OUTPUTFORMAT_DIR[$this->real_outputformat])
                    ) {
                        if ($CFG->hotpotismobile && isset($HOTPOT_OUTPUTFORMAT_DIR[HOTPOT_OUTPUTFORMAT_MOBILE])) {
                                $this->real_outputformat = HOTPOT_OUTPUTFORMAT_MOBILE;
                        } else { // PC
                            if ($this->quiztype=='jmatch' || $this->quiztype=='jmix') {
                                $this->real_outputformat = HOTPOT_OUTPUTFORMAT_V6_PLUS;
                            } else {
                                $this->real_outputformat = HOTPOT_OUTPUTFORMAT_V6;
                            }
                        }
                    }

                    if ($this->real_outputformat==HOTPOT_OUTPUTFORMAT_V6_PLUS) {
                        if ($this->quiztype=='jmatch' || $this->quiztype=='jmix') {
                            $this->draganddrop = 'd'; // prefix for templates (can also be "f" ?)
                        }
                        $this->real_outputformat = HOTPOT_OUTPUTFORMAT_V6;
                    }

                    // set path(s) to template
                    $this->template_dir = $HOTPOT_OUTPUTFORMAT_DIR[$this->real_outputformat];
                    $this->template_dirpath = $CFG->hotpottemplate.'/'.$this->template_dir;
                    $this->template_filepath = $CFG->hotpottemplate.'/'.$this->template_dir.'.php';

                    // check template class exists
                    if (!file_exists($this->template_filepath) || !is_readable($this->template_filepath)) {
                        $this->error = get_string('error_couldnotopentemplate', 'hotpot', $this->template_dir);
                        if ($this->report_errors) {
                            error($this->error, $this->course_homeurl);
                        }
                        return;
                    }

                    // get default and output-specfic template classes
                    include($this->template_filepath);

                    // create html (using the template for the specified output format)
                    $this->template = new hotpot_xml_quiz_template($this);
                    $this->html = &$this->template->html;

                } // end $this->create_html
            } // end if html/xml file
        } // end if $this->read_file
    } // end constructor function

    function callback_stylesheets_urls(&$matches) {
        return $matches[1].hotpot_convert_stylesheets_urls($this->get_baseurl(), $this->reference , $matches[2], false).$matches[3];
    }
    function callback_preloadimages_urls(&$matches) {
        return hotpot_convert_preloadimages_urls($this->get_baseurl(), $this->reference, $matches[1], false);
    }
    function callback_navbutton_url(&$matches) {
        return hotpot_convert_navbutton_url($this->get_baseurl(), $this->reference, $matches[1], $this->course, false);
    }
    function callback_url(&$matches) {
        return hotpot_convert_url($this->get_baseurl(), $this->reference, $matches[1], false);
    }
    function callback_relative_url(&$matches) {
        return hotpot_convert_relative_url($this->get_baseurl(), $this->reference, $matches[1], $matches[6], $matches[7], false);
    }

    function hotpot_convert_relative_urls(&$str) {
        $tagopen = '(?:(<)|(&lt;)|(&amp;#x003C;))'; // left angle bracket
        $tagclose = '(?(2)>|(?(3)&gt;|(?(4)&amp;#x003E;)))'; //  right angle bracket (to match left angle bracket)

        $space = '\s+'; // at least one space
        $anychar = '(?:[^>]*?)'; // any character

        $quoteopen = '("|&quot;|&amp;quot;)'; // open quote
        $quoteclose = '\\5'; //  close quote (to match open quote)

        $tags = array('script'=>'src', 'link'=>'href', 'a'=>'href','img'=>'src','param'=>'value', 'object'=>'data', 'embed'=>'src');
        foreach ($tags as $tag=>$attribute) {
            if ($tag=='param') {
                $url = '\S+?\.\S+?'; // must include a filename and have no spaces
            } else {
                $url = '.*?';
            }
            $search = "/($tagopen$tag$space$anychar$attribute=$quoteopen)($url)($quoteclose$anychar$tagclose)/is";
            $str = preg_replace_callback($search, array(&$this, 'callback_relative_url'), $str);
        }
    }

    function get_baseurl() {
        // set the url base (first time only)
        if (!isset($this->baseurl)) {
            global $CFG;
            require_once($CFG->libdir.'/filelib.php');
            $this->baseurl = get_file_url($this->filedir).'/';
        }
        return $this->baseurl;
    }


    // insert forms and messages

    function remove_nav_buttons() {
        $search = '/<!-- Begin(Top|Bottom)NavButtons -->(.*?)<!-- End(Top|Bottom)NavButtons -->/s';
        $this->html = preg_replace($search, '', $this->html);
    }
    function insert_script($src=HOTPOT_JS) {
        $script = '<script src="'.$src.'" type="text/javascript"></script>'."\n";
        $this->html = preg_replace('|</head>|i', $script.'</head>', $this->html, 1);
    }
    function insert_submission_form($attemptid, $startblock, $endblock, $keep_contents=false, $targetframe='') {
        $form_id = 'store';
        $form_fields = ''
        .   '<fieldset style="display:none">'
        .   '<input type="hidden" name="attemptid" value="'.$attemptid.'" />'
        .   '<input type="hidden" name="starttime" value="" />'
        .   '<input type="hidden" name="endtime" value="" />'
        .   '<input type="hidden" name="mark" value="" />'
        .   '<input type="hidden" name="detail" value="" />'
        .   '<input type="hidden" name="status" value="" />'
        .   '</fieldset>'
        ;
        $this->insert_form($startblock, $endblock, $form_id, $form_fields, $keep_contents, false, $targetframe);
    }
    function insert_giveup_form($attemptid, $startblock, $endblock, $keep_contents=false) {
        $form_id = ''; // no <form> tag will be generated
        $form_fields = ''
        .   '<button onclick="Finish('.HOTPOT_STATUS_ABANDONED.')" class="FuncButton" '
        .   'onfocus="FuncBtnOver(this)" onblur="FuncBtnOut(this)" '
        .   'onmouseover="FuncBtnOver(this)" onmouseout="FuncBtnOut(this)" '
        .   'onmousedown="FuncBtnDown(this)" onmouseup="FuncBtnOut(this)">'
        .   get_string('giveup', 'hotpot').'</button>'
        ;
        $this->insert_form($startblock, $endblock, $form_id, $form_fields, $keep_contents, true);
    }
    function insert_form($startblock, $endblock, $form_id, $form_fields, $keep_contents, $center=false, $targetframe='') {
        global $CFG;
        $search = '/('.preg_quote($startblock, '/').')(.*?)('.preg_quote($endblock, '/').')/s';
        $replace = $form_fields;
        if ($keep_contents) {
            $replace .= '\\2';
        }
        if ($targetframe) {
            $frametarget = ' onsubmit="'."this.target='$targetframe';".'"';
        } else if (! empty($CFG->framename)) {
            $frametarget = ' onsubmit="'."this.target='$CFG->framename';".'"';
        } else if (! empty($CFG->frametarget)) {
            $frametarget = $CFG->frametarget;
        } else {
            $frametarget = '';
        }
        if ($form_id) {
            $replace = '<form action="'.$CFG->wwwroot.'/mod/hotpot/attempt.php" method="post" id="'.$form_id.'"'.$frametarget.'>'.$replace.'</form>';
        }
        if ($center) {
            $replace = '<div style="margin-left:auto; margin-right:auto; text-align: center;">'.$replace.'</div>';
        }
        $replace = '\\1'.$replace.'\\3';
        $this->html = preg_replace($search, $replace, $this->html, 1);
    }
    function insert_message($start_str, $message, $color='red', $align='center') {
        $message = '<p align="'.$align.'" style="text-align:'.$align.'"><b><font color="'.$color.'">'.$message."</font></b></p>\n";
        $this->html = preg_replace('|'.preg_quote($start_str).'|', $start_str.$message, $this->html, 1);
    }

    function adjust_media_urls() {

        if ($this->forceplugins) {

            // make sure the Moodle media plugin is available
            global $CFG;
            //include_once "$CFG->dirroot/filter/mediaplugin/filter.php";
            include_once "$CFG->dirroot/mod/hotpot/mediaplayers/moodle/filter.php";

            $space = '\s(?:.+\s)?';
            $quote = '["'."']?"; // single, double, or no quote

            // patterns to media files types and paths
            $filetypes = "avi|mpeg|mpg|mp3|mov|wmv|flv";
            if ($CFG->filter_mediaplugin_enable_swf) {
                $filetypes .= '|swf';
            }
            $filepath = '[^"'."']*".'\\.(?:'.$filetypes.')[^"'."']*";

            $tagopen = '(?:(<)|(\\\\u003C))'; // left angle-bracket (uses two parenthese)
            $tagchars = '(?(1)[^>]|(?(2).(?!\\\\u003E)))*?';  // string of chars inside the tag
            $tagclose = '(?(1)>|(?(2)\\\\u003E))'; // right angle-bracket (to match the left one)
            $tagreopen = '(?(1)<|(?(2)\\\\u003C))'; // another left angle-bracket (to match the first one)

            // pattern to match <param> tags which contain the file path
            $param_names = 'movie|src|url|flashvars';
            //  wmp        : url
            //  quicktime  : src
            //  realplayer : src
            //  flash      : movie, flashvars
            $param_url = '/'.$tagopen.'param'.'\s'.$tagchars.'name="(?:'.$param_names.')"'.$tagchars.'value="('.$filepath.')"'.$tagchars.$tagclose.'/is';

            // pattern to match <a> tags which link to multimedia files
            $link_url = '/'.$tagopen.'a'.'\s'.$tagchars.'href="('.$filepath.')"'.$tagchars.$tagclose.'.*?'.$tagreopen.'\/a'.$tagclose.'/is';

            // extract <object> tags
            $object_tag = '/'.$tagopen.'object'.'\s'.$tagchars.$tagclose.'(.*?)'.'(?:'.$tagreopen.'\/object'.$tagclose.')+/is';
            preg_match_all($object_tag, $this->html, $objects);

            $i_max = count($objects[0]);
            for ($i=0; $i<$i_max; $i++) {

                // extract URL from <param> or <a>
                $url = '';
                if (preg_match($param_url, $objects[3][$i], $matches) || preg_match($link_url, $objects[3][$i], $matches)) {
                    $url = $matches[3];
                }
                if ($url) {
                    // strip inner tags (e.g. <embed>)
                    $txt = preg_replace("/$tagopen.*?$tagclose/", '', $objects[3][$i]);

                    // if url is in the query string, remove the leading characters
                    $url = preg_replace('/^([^=]+=[^&]*&)*[^=]+=(http:[^&]*)$/', '$2', $url, 1);
                    $link = '<a href="'.$url.'">'.$txt.'</a>';

                    $new_object = hotpot_mediaplayer_moodle($this, $link);
                    $new_object = str_replace($link, '', $new_object);
                    $new_object = str_replace('&amp;', '&', $new_object);

                    $this->html = str_replace($objects[0][$i], $new_object, $this->html);
                }
            }
        }
    }

} // end class

function hotpot_stripslashes($str) {
    // strip slashes from  double quotes, single quotes and  back slashes
    // the slashes were added by preg_replace() when using the "e" modifier
    static $escapedchars = array('\\\\', '\\"', "\\'");
    static $unescapedchars = array('\\', '"', "'");
    return str_replace($escapedchars, $unescapedchars, $str);
}
function hotpot_convert_stylesheets_urls($baseurl, $reference, $css, $stripslashes=true) {
    if ($stripslashes) {
        $css = hotpot_stripslashes($css);
    }
    $search = '/(?<=url\()'.'(?:.+?)'.'(?=\))/is';
    if (preg_match_all($search, $css, $matches, PREG_OFFSET_CAPTURE)) {
        $i_max = count($matches[0]) - 1;
        for ($i=$i_max; $i>=0; $i--) {
            $match = $matches[0][$i][0];
            $start = $matches[0][$i][1];
            $replace = hotpot_convert_url($baseurl, $reference, $match, false);
            $css = substr_replace($css, $replace, $start, strlen($match));
        }
    }
    return $css;
}
function hotpot_convert_preloadimages_urls($baseurl, $reference, $urls, $stripslashes=true) {
    if ($stripslashes) {
        $urls = hotpot_stripslashes($urls);
    }
    $search = '|(?<=["'."'])(?:[^,'".'"]*?)(?=["'."'])|is";
    if (preg_match_all($search, $urls, $matches, PREG_OFFSET_CAPTURE)) {
        $i_max = count($matches[0]) - 1;
        for ($i=$i_max; $i>=0; $i--) {
            $match = $matches[0][$i][0];
            $start = $matches[0][$i][1];
            $replace = hotpot_convert_url($baseurl, $reference, $match, false);
            $urls = substr_replace($urls, $replace, $start, strlen($match));
        }
    }
    return $urls;
}
function hotpot_convert_navbutton_url($baseurl, $reference, $url, $course, $stripslashes=true) {
    global $CFG;

    if ($stripslashes) {
        $url = hotpot_stripslashes($url);
    }
    $url = hotpot_convert_url($baseurl, $reference, $url, false);

    // is this a $url for another hotpot in this course ?
    if (preg_match("/^".preg_quote($baseurl, '/')."(.*)$/", $url, $matches)) {
        if ($records = get_records_select('hotpot', "course='$course' AND reference='".$matches[1]."'")) {
            $ids = array_keys($records);
            $url = "$CFG->wwwroot/mod/hotpot/view.php?hp=".$ids[0];
        }
    }

    return $url;
}

function hotpot_convert_relative_url($baseurl, $reference, $opentag, $url, $closetag, $stripslashes=true) {
    if ($stripslashes) {
        $opentag = hotpot_stripslashes($opentag);
        $url = hotpot_stripslashes($url);
        $closetag = hotpot_stripslashes($closetag);
    }

    // catch <PARAM name="FlashVars" value="TheSound=soundfile.mp3">
    //  ampersands can appear as "&", "&amp;" or "&amp;#x0026;amp;"
    if (preg_match('/^'.'\w+=[^&]+'.'('.'&((amp;#x0026;)?amp;)?'.'\w+=[^&]+)*'.'$/', $url)) {
        $query = $url;
        $url = '';
        $fragment = '';

    // parse the $url into $matches
    //  [1] path
    //  [2] query string, if any
    //  [3] anchor fragment, if any
    } else if (preg_match('/^'.'([^?]*)'.'((?:\\?[^#]*)?)'.'((?:#.*)?)'.'$/', $url, $matches)) {
        $url = $matches[1];
        $query = $matches[2];
        $fragment = $matches[3];

    // these appears to be no query or fragment in this url
    } else {
        $query = '';
        $fragment = '';
    }

    if ($url) {
        $url = hotpot_convert_url($baseurl, $reference, $url, false);
    }

    if ($query) {
        $search = '/'.'(file|src|thesound|mp3)='."([^&]+)".'/is';
        if (preg_match_all($search, $query, $matches, PREG_OFFSET_CAPTURE)) {
            $i_max = count($matches[0]) - 1;
            for ($i=$i_max; $i>=0; $i--) {
                $match = $matches[2][$i][0];
                $start = $matches[2][$i][1];
                $replace = hotpot_convert_url($baseurl, $reference, $match, false);
                $query = substr_replace($query, $replace, $start, strlen($match));
            }
        }
    }

    $url = $opentag.$url.$query.$fragment.$closetag;

    return $url;
}

function hotpot_convert_url($baseurl, $reference, $url, $stripslashes=true) {
    // maintain a cache of converted urls
    static $HOTPOT_RELATIVE_URLS = array();

    if ($stripslashes) {
        $url = hotpot_stripslashes($url);
    }

    // is this an absolute url? (or javascript pseudo url)
    if (preg_match('%^(http://|https://|/|javascript:)%i', $url)) {
        // do nothing

    // has this relative url already been converted?
    } else if (isset($HOTPOT_RELATIVE_URLS[$url])) {
        $url = $HOTPOT_RELATIVE_URLS[$url];

    } else {
        $relativeurl = $url;

        // get the subdirectory, $dir, of the quiz $reference
        $dir = dirname($reference);

        // allow for leading "./" and "../"
        while (preg_match('|^(\.{1,2})/(.*)$|', $url, $matches)) {
            if ($matches[1]=='..') {
                $dir = dirname($dir);
            }
            $url = $matches[2];
        }

        // add subdirectory, $dir, to $baseurl, if necessary
        if ($dir && $dir<>'.') {
            $baseurl .= "$dir/";
        }

        // prefix $url with $baseurl
        $url = "$baseurl$url";

        // add url to cache
        $HOTPOT_RELATIVE_URLS[$relativeurl] = $url;
    }
    return $url;
}

// ===================================================
// function for adding attempt questions and responses
// ===================================================

function hotpot_add_attempt_details(&$attempt) {

    // encode ampersands so that HTML entities are preserved in the XML parser
    // N.B. ampersands inside <![CDATA[ ]]> blocks do NOT need to be encoded

    $old = &$attempt->details; // shortcut to "old" details
    $new = '';
    $str_start = 0;
    while (($cdata_start = strpos($old, '<![CDATA[', $str_start)) && ($cdata_end = strpos($old, ']]>', $cdata_start))) {
        $cdata_end += 3;
        $new .= str_replace('&', '&amp;', substr($old, $str_start, $cdata_start-$str_start)).substr($old, $cdata_start, $cdata_end-$cdata_start);
        $str_start = $cdata_end;
    }
    $new .= str_replace('&', '&amp;', substr($old, $str_start));
    unset($old);

    // parse the attempt details as xml
    $details = new hotpot_xml_tree($new, "['hpjsresult']['#']");

    $num = -1;
    $q_num = -1;
    $question = NULL;
    $reponse = NULL;

    $i = 0;
    $tags = 'fields,field';

    while (($field="[$i]['#']") && $details->xml_value($tags, $field)) {

        $name = $details->xml_value($tags, $field."['fieldname'][0]['#']");
        $data = $details->xml_value($tags, $field."['fielddata'][0]['#']");

        // parse the field name into $matches
        //  [1] quiz type
        //  [2] attempt detail name
        if (preg_match('/^(\w+?)_(\w+)$/', $name, $matches)) {
            $quiztype = strtolower($matches[1]);
            $name = strtolower($matches[2]);

            // parse the attempt detail $name into $matches
            //  [1] question number
            //  [2] question detail name
            if (preg_match('/^q(\d+)_(\w+)$/', $name, $matches)) {
                $num = $matches[1];
                $name = strtolower($matches[2]);
                $data = addslashes($data);

                // adjust JCross question numbers
                if (preg_match('/^(across|down)(.*)$/', $name, $matches)) {
                    $num .= '_'.$matches[1]; // e.g. 01_across, 02_down
                    $name = $matches[2];
                    if (substr($name, 0, 1)=='_') {
                        $name = substr($name, 1); // remove leading '_'
                    }
                }

                // is this a new question (or the first one)?
                if ($q_num<>$num) {

                    // add previous question and response, if any
                    hotpot_add_response($attempt, $question, $response);

                    // initialize question object
                    $question = NULL;
                    $question->name = '';
                    $question->text = '';
                    $question->hotpot = $attempt->hotpot;

                    // initialize response object
                    $response = NULL;
                    $response->attempt = $attempt->id;

                    // update question number
                    $q_num = $num;
                }

                // adjust field name and value, and set question type
                // (may not be necessary one day)
                hotpot_adjust_response_field($quiztype, $question, $num, $name, $data);

                // add $data to the question/response details
                switch ($name) {
                    case 'name':
                    case 'type':
                        $question->$name = $data;
                        break;
                    case 'text':
                        $question->$name = hotpot_string_id($data);
                        break;

                    case 'correct':
                    case 'ignored':
                    case 'wrong':
                        $response->$name = hotpot_string_ids($data);
                        break;

                    case 'score':
                    case 'weighting':
                    case 'hints':
                    case 'clues':
                    case 'checks':
                        $response->$name = intval($data);
                        break;
                }

            } else { // attempt details

                // adjust field name and value
                hotpot_adjust_response_field($quiztype, $question, $num='', $name, $data);

                // add $data to the attempt details
                if ($name=='penalties') {
                    $attempt->$name = intval($data);
                }
            }
        }

        $i++;
    } // end while

    // add the final question and response, if any
    hotpot_add_response($attempt, $question, $response);
}
function hotpot_add_response(&$attempt, &$question, &$response) {
    global $db, $next_url;

    $loopcount = 1;

    $looping = isset($question) && isset($question->name) && isset($response);
    while ($looping) {

        if ($loopcount==1) {
            $questionname = $question->name;
        }

        $question->md5key = md5($question->name);
        if (!$question->id = get_field('hotpot_questions', 'id', 'hotpot', $attempt->hotpot, 'md5key', $question->md5key, 'name', $question->name)) {
            // add question record
            if (!$question->id = insert_record('hotpot_questions', $question)) {
                error("Could not add question record (attempt_id=$attempt->id): ".$db->ErrorMsg(), $next_url);
            }
        }

        if (record_exists('hotpot_responses', 'attempt', $attempt->id, 'question', $question->id)) {
            // there is already a response to this question for this attempt
            // probably because this quiz has two questions with the same text
            //  e.g. Which one of these answers is correct?

            // To workaround this, we create new question names
            //  e.g. Which one of these answers is correct? (2)
            // until we get a question name for which there is no response yet on this attempt

            $loopcount++;
            $question->name = "$questionname ($loopcount)";

            // This method fails to correctly identify questions in
            // quizzes which allow questions to be shuffled or omitted.
            // As yet, there is no workaround for such cases.

        } else {
            $response->question = $question->id;

            // add response record
            if(!$response->id = insert_record('hotpot_responses', $response)) {
                error("Could not add response record (attempt_id=$attempt->id, question_id=$question->id): ".$db->ErrorMsg(), $next_url);
            }

            // we can stop looping now
            $looping = false;
        }
    } // end while
}
function hotpot_adjust_response_field($quiztype, &$question, &$num, &$name, &$data) {
    switch ($quiztype) {
        case 'jbc':
            $question->type = HOTPOT_JCB;
            switch ($name) {
                case 'right':
                    $name = 'correct';
                break;
            }
            break;
        case 'jcloze':
            $question->type = HOTPOT_JCLOZE;
            if (is_numeric($num)) {
                $question->name = $num;
            }
            switch ($name) {
                case 'penalties':
                    if (is_numeric($num)) {
                        $name = 'checks';
                        if (is_numeric($data)) {
                            $data++;
                        }
                    }
                    break;
                case 'clue_shown':
                    $name = 'clues';
                    $data = ($data=='YES' ? 1 : 0);
                    break;
                case 'clue_text':
                    $name = 'text';
                    break;
            }
            break;
        case 'jcross':
            $question->type = HOTPOT_JCROSS;
            $question->name = $num;
            switch ($name) {
                case '': // HotPot v2.0.x
                    $name = 'correct';
                    break;
                case 'clue':
                    $name = 'text';
                    break;
            }
            break;
        case 'jmatch':
            $question->type = HOTPOT_JMATCH;
            switch ($name) {
                case 'attempts':
                    $name = 'penalties';
                    if (is_numeric($data) && $data>0) {
                        $data--;
                    }
                break;
                case 'lhs':
                    $name = 'name';
                break;
                case 'rhs':
                    $name = 'correct';
                break;
            }
            break;
        case 'jmix':
            $question->type = HOTPOT_JMIX;
            $question->name = $num;
            switch ($name) {
                // keep these in for "restore" of courses
                // which were backed up with HotPot v2.0.x
                case 'wrongguesses':
                    $name = 'checks';
                    if (is_numeric($data)) {
                        $data++;
                    }
                break;
                case 'right':
                    $name = 'correct';
                break;
            }
            break;
            break;
        case 'jquiz':
            switch ($name) {
                case 'type':
                    $data = HOTPOT_JQUIZ;
                    switch ($data) {
                        case 'multiple-choice':
                            $data .= '.'.HOTPOT_JQUIZ_MULTICHOICE;
                        break;
                        case 'short-answer':
                            $data .= '.'.HOTPOT_JQUIZ_SHORTANSWER;
                        break;
                        case 'hybrid':
                            $data .= '.'.HOTPOT_JQUIZ_HYBRID;
                        break;
                        case 'multi-select':
                            $data .= '.'.HOTPOT_JQUIZ_MULTISELECT;
                        case 'n/a':
                        default:
                            // do nothing more
                        break;
                    }
                break;
                case 'question':
                    $name = 'name';
                break;
            }
            break;

        case 'rhubarb':
            $question->type = HOTPOT_TEXTOYS_RHUBARB;
            if (empty($question->name)) {
                $question->name = $num;
            }
            break;

        case 'sequitur':
            $question->type = HOTPOT_TEXTOYS_SEQUITUR;
            break;
    }
}
function hotpot_string_ids($field_value) {
    $ids = array();
    $strings = explode(',', $field_value);
    foreach($strings as $str) {
        if ($id = hotpot_string_id($str)) {
            $ids[] = $id;
        }
    }
    return implode(',', $ids);
}
function hotpot_string_id($str) {
    $id = '';
    if (isset($str) && $str<>'') {

        // get the id from the table if it is already there
        $md5key = md5($str);
        if (!$id = get_field('hotpot_strings', 'id', 'md5key', $md5key, 'string', $str)) {

            // create a string record
            $record = new stdClass();
            $record->string = $str;
            $record->md5key = $md5key;

            // try and add the new string record
            if (!$id = insert_record('hotpot_strings', $record)) {
                global $db;
                error("Could not add string record for '".htmlspecialchars($str)."': ".$db->ErrorMsg());
            }
        }
    }
    return $id;
}

function hotpot_get_view_actions() {
    return array('view','view all','report');
}

function hotpot_get_post_actions() {
    return array('attempt','review','submit');
}

if (!function_exists('file_get_contents')) {
    // add this function for php version<4.3
    function file_get_contents($filepath) {
        $contents = file($filepath);
        if (is_array($contents)) {
             $contents = implode('', $contents);
        }
        return $contents;
    }
}
if (!function_exists('html_entity_decode')) {
    // add this function for php version<4.3
    function html_entity_decode($str) {
        $t = get_html_translation_table(HTML_ENTITIES);
        $t = array_flip($t);
        return strtr($str, $t);
    }

}

// required for Moodle 1.x
if (!isset($CFG->pixpath)) {
    $CFG->pixpath = "$CFG->wwwroot/pix";
}

if (!function_exists('fullname')) {
    // add this function for Moodle 1.x
    function fullname($user) {
        return "$user->firstname $user->lastname";
    }
}
if (!function_exists('get_user_preferences')) {
    // add this function for Moodle 1.x
    function get_user_preferences($name=NULL, $default=NULL, $userid=NULL) {
        return $default;
    }
}
if (!function_exists('set_user_preference')) {
    // add this function for Moodle 1.x
    function set_user_preference($name, $value, $otheruser=NULL) {
        return false;
    }
}
if (!function_exists('get_coursemodule_from_id')) {
    // add this function for Moodle < 1.5.4
    function get_coursemodule_from_id($modulename, $cmid, $courseid=0) {
        global $CFG;
        return get_record_sql("
            SELECT
                cm.*, m.name, md.name as modname
            FROM
                {$CFG->prefix}course_modules cm,
                {$CFG->prefix}modules md,
                {$CFG->prefix}$modulename m
            WHERE
                ".($courseid ? "cm.course = '$courseid' AND " : '')."
                cm.id = '$cmid' AND
                cm.instance = m.id AND
                md.name = '$modulename' AND
                md.id = cm.module
        ");
    }
}
if (!function_exists('get_coursemodule_from_instance')) {
    // add this function for Moodle < 1.5.4
    function get_coursemodule_from_instance($modulename, $instance, $courseid=0) {
        global $CFG;
        return get_record_sql("
            SELECT
                cm.*, m.name, md.name as modname
            FROM
                {$CFG->prefix}course_modules cm,
                {$CFG->prefix}modules md,
                {$CFG->prefix}$modulename m
            WHERE
                ".($courseid ? "cm.course = '$courseid' AND" : '')."
                cm.instance = m.id AND
                md.name = '$modulename' AND
                md.id = cm.module AND
                m.id = '$instance'
        ");
    }
}
function hotpot_utf8_to_html_entity($char) {
    // http://www.zend.com/codex.php?id=835&single=1

    // array used to figure what number to decrement from character order value
    // according to number of characters used to map unicode to ascii by utf-8
    static $HOTPOT_UTF8_DECREMENT = array(
        1=>0, 2=>192, 3=>224, 4=>240
    );

    // the number of bits to shift each character by
    static $HOTPOT_UTF8_SHIFT = array(
        1=>array(0=>0),
        2=>array(0=>6,  1=>0),
        3=>array(0=>12, 1=>6,  2=>0),
        4=>array(0=>18, 1=>12, 2=>6, 3=>0)
    );

    $dec = 0;
    $len = strlen($char);
    for ($pos=0; $pos<$len; $pos++) {
        $ord = ord ($char{$pos});
        $ord -= ($pos ? 128 : $HOTPOT_UTF8_DECREMENT[$len]);
        $dec += ($ord << $HOTPOT_UTF8_SHIFT[$len][$pos]);
    }
    return '&#x'.sprintf('%04X', $dec).';';
}

function hotpot_print_show_links($course, $location, $reference, $actions='', $spacer=' &nbsp; ', $new_window=false, $return=false) {
    global $CFG;
    if (is_string($actions)) {
        if (empty($actions)) {
            $actions = 'showxmlsource,showxmltree,showhtmlsource';
        }
        $actions = explode(',', $actions);
    }
    $strenterafilename = get_string('enterafilename', 'hotpot');
    $html = <<<END_OF_SCRIPT
<script type="text/javascript">
//<![CDATA[
    function setLink(lnk) {
        var form = null;
        if (document.forms['mform1']) {
            var form = document.forms['mform1'];
        } else if (document.forms['form']) {
            var form = document.forms['form'];
        }
        return setLinkAttribute(lnk, 'reference', form) && setLinkAttribute(lnk, 'location', form);
    }
    function setLinkAttribute(lnk, name, form) {
        // set link attribute value using
        // f(orm) name and e(lement) name

        var r = true; // result

        var obj = (form) ? form.elements[name] : null;
        if (obj) {
            r = false;
            var v = getObjValue(obj);
            if (v=='') {
                alert('$strenterafilename');
            } else {
                var s = lnk.href;
                var i = s.indexOf('?');
                if (i>=0) {
                    i = s.indexOf(name+'=', i+1);
                    if (i>=0) {
                        i += name.length+1;
                        var ii = s.indexOf('&', i);
                        if (ii<0) {
                            ii = s.length;
                        }
                        lnk.href = s.substring(0, i) + v + s.substring(ii);
                        r = true;
                    }
                }
            }
        }
        return r;
    }
    function getObjValue(obj) {
        var v = ''; // the value
        var t = (obj && obj.type) ? obj.type : "";
        if (t=="text" || t=="textarea" || t=="hidden") {
            v = obj.value;
        } else if (t=="select-one" || t=="select-multiple") {
            var l = obj.options.length;
            for (var i=0; i<l; i++) {
                if (obj.options[i].selected) {
                    v += (v=="" ? "" : ",") + obj.options[i].value;
                }
            }
        }
        return v;
    }
    function getDir(s) {
        if (s.charAt(0)!='/') {
            s = '/' + s;
        }
        var i = s.lastIndexOf('/');
        return s.substring(0, i);
    }
//]]>
</script>
END_OF_SCRIPT;

    foreach ($actions as $action) {
        $html .= $spacer
        .   '<a href="'
        .           $CFG->wwwroot.'/mod/hotpot/show.php'
        .           '?course='.$course.'&amp;location='.$location.'&amp;reference='.urlencode($reference).'&amp;action='.$action
        .       '"'
        .       ' onclick="return setLink(this);"'
        .       ($new_window ? ' target="_blank"' : '')
        .   '>'.get_string($action, 'hotpot').'</a>'
        ;
    }
    $html = '<span class="helplink">'.$html.'</span>';
    if ($return) {
        return $html;
    } else {
        print $html;
    }
}

/**
 * Returns all other caps used in module
 */
function hotpot_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all attempts from hotpot quizzes in the specified course.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function hotpot_reset_userdata($data) {
    global $CFG;
    require_once($CFG->libdir.'/filelib.php');

    $status = array();

    if (!empty($data->reset_hotpot_deleteallattempts)) {

        $hotpotids = "SELECT h.id FROM {$CFG->prefix}hotpot h WHERE h.course={$data->courseid}";
        $attemptids = "SELECT a.id FROM {$CFG->prefix}hotpot_attempts a WHERE a.hotpot in ($hotpotids)";

        delete_records_select('hotpot_responses', "attempt in ($attemptids)");
        delete_records_select('hotpot_details', "attempt in ($attemptids)");
        delete_records_select('hotpot_attempts', "hotpot IN ($hotpotids)");

        $status[] = array('component' => get_string('modulenameplural', 'hotpot'),
                          'item' => get_string('deleteallattempts', 'hotpot'),
                          'error' => false);
    }

    return $status;
}

/**
 * Called by course/reset.php
 * @param $mform form passed by reference
 */
function hotpot_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'hotpotheader', get_string('modulenameplural', 'hotpot'));
    $mform->addElement('checkbox', 'reset_hotpot_deleteallattempts', get_string('deleteallattempts', 'hotpot'));
}

/**
 * Course reset form defaults.
 */
function hotpot_reset_course_form_defaults($course) {
    return array('reset_hotpot_deleteallattempts' => 1);
}

/**
 * Tells if files in moddata are trusted and can be served without XSS protection.
 * @return bool true if file can be submitted by teacher only (trusted), false otherwise
 */
function hotpot_is_moddata_trusted() {
    return true;
}

?>