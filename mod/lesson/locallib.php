<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Local library file for Lesson.  These are non-standard functions that are used
 * only by Lesson.
 *
 * @package mod_lesson
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

/** Make sure this isn't being directly accessed */
defined('MOODLE_INTERNAL') || die();

/** Include the files that are required by this module */
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/lesson/lib.php');
require_once($CFG->libdir . '/filelib.php');

/** This page */
define('LESSON_THISPAGE', 0);
/** Next page -> any page not seen before */
define("LESSON_UNSEENPAGE", 1);
/** Next page -> any page not answered correctly */
define("LESSON_UNANSWEREDPAGE", 2);
/** Jump to Next Page */
define("LESSON_NEXTPAGE", -1);
/** End of Lesson */
define("LESSON_EOL", -9);
/** Jump to an unseen page within a branch and end of branch or end of lesson */
define("LESSON_UNSEENBRANCHPAGE", -50);
/** Jump to Previous Page */
define("LESSON_PREVIOUSPAGE", -40);
/** Jump to a random page within a branch and end of branch or end of lesson */
define("LESSON_RANDOMPAGE", -60);
/** Jump to a random Branch */
define("LESSON_RANDOMBRANCH", -70);
/** Cluster Jump */
define("LESSON_CLUSTERJUMP", -80);
/** Undefined */
define("LESSON_UNDEFINED", -99);

/** LESSON_MAX_EVENT_LENGTH = 432000 ; 5 days maximum */
define("LESSON_MAX_EVENT_LENGTH", "432000");

/** Answer format is HTML */
define("LESSON_ANSWER_HTML", "HTML");

//////////////////////////////////////////////////////////////////////////////////////
/// Any other lesson functions go here.  Each of them must have a name that
/// starts with lesson_

/**
 * Checks to see if a LESSON_CLUSTERJUMP or
 * a LESSON_UNSEENBRANCHPAGE is used in a lesson.
 *
 * This function is only executed when a teacher is
 * checking the navigation for a lesson.
 *
 * @param stdClass $lesson Id of the lesson that is to be checked.
 * @return boolean True or false.
 **/
function lesson_display_teacher_warning($lesson) {
    global $DB;

    // get all of the lesson answers
    $params = array ("lessonid" => $lesson->id);
    if (!$lessonanswers = $DB->get_records_select("lesson_answers", "lessonid = :lessonid", $params)) {
        // no answers, then not using cluster or unseen
        return false;
    }
    // just check for the first one that fulfills the requirements
    foreach ($lessonanswers as $lessonanswer) {
        if ($lessonanswer->jumpto == LESSON_CLUSTERJUMP || $lessonanswer->jumpto == LESSON_UNSEENBRANCHPAGE) {
            return true;
        }
    }

    // if no answers use either of the two jumps
    return false;
}

/**
 * Interprets the LESSON_UNSEENBRANCHPAGE jump.
 *
 * will return the pageid of a random unseen page that is within a branch
 *
 * @param lesson $lesson
 * @param int $userid Id of the user.
 * @param int $pageid Id of the page from which we are jumping.
 * @return int Id of the next page.
 **/
function lesson_unseen_question_jump($lesson, $user, $pageid) {
    global $DB;

    // get the number of retakes
    if (!$retakes = $DB->count_records("lesson_grades", array("lessonid"=>$lesson->id, "userid"=>$user))) {
        $retakes = 0;
    }

    // get all the lesson_attempts aka what the user has seen
    if ($viewedpages = $DB->get_records("lesson_attempts", array("lessonid"=>$lesson->id, "userid"=>$user, "retry"=>$retakes), "timeseen DESC")) {
        foreach($viewedpages as $viewed) {
            $seenpages[] = $viewed->pageid;
        }
    } else {
        $seenpages = array();
    }

    // get the lesson pages
    $lessonpages = $lesson->load_all_pages();

    if ($pageid == LESSON_UNSEENBRANCHPAGE) {  // this only happens when a student leaves in the middle of an unseen question within a branch series
        $pageid = $seenpages[0];  // just change the pageid to the last page viewed inside the branch table
    }

    // go up the pages till branch table
    while ($pageid != 0) { // this condition should never be satisfied... only happens if there are no branch tables above this page
        if ($lessonpages[$pageid]->qtype == LESSON_PAGE_BRANCHTABLE) {
            break;
        }
        $pageid = $lessonpages[$pageid]->prevpageid;
    }

    $pagesinbranch = $lesson->get_sub_pages_of($pageid, array(LESSON_PAGE_BRANCHTABLE, LESSON_PAGE_ENDOFBRANCH));

    // this foreach loop stores all the pages that are within the branch table but are not in the $seenpages array
    $unseen = array();
    foreach($pagesinbranch as $page) {
        if (!in_array($page->id, $seenpages)) {
            $unseen[] = $page->id;
        }
    }

    if(count($unseen) == 0) {
        if(isset($pagesinbranch)) {
            $temp = end($pagesinbranch);
            $nextpage = $temp->nextpageid; // they have seen all the pages in the branch, so go to EOB/next branch table/EOL
        } else {
            // there are no pages inside the branch, so return the next page
            $nextpage = $lessonpages[$pageid]->nextpageid;
        }
        if ($nextpage == 0) {
            return LESSON_EOL;
        } else {
            return $nextpage;
        }
    } else {
        return $unseen[rand(0, count($unseen)-1)];  // returns a random page id for the next page
    }
}

/**
 * Handles the unseen branch table jump.
 *
 * @param lesson $lesson
 * @param int $userid User id.
 * @return int Will return the page id of a branch table or end of lesson
 **/
function lesson_unseen_branch_jump($lesson, $userid) {
    global $DB;

    if (!$retakes = $DB->count_records("lesson_grades", array("lessonid"=>$lesson->id, "userid"=>$userid))) {
        $retakes = 0;
    }

    $params = array ("lessonid" => $lesson->id, "userid" => $userid, "retry" => $retakes);
    if (!$seenbranches = $DB->get_records_select("lesson_branch", "lessonid = :lessonid AND userid = :userid AND retry = :retry", $params,
                "timeseen DESC")) {
        print_error('cannotfindrecords', 'lesson');
    }

    // get the lesson pages
    $lessonpages = $lesson->load_all_pages();

    // this loads all the viewed branch tables into $seen until it finds the branch table with the flag
    // which is the branch table that starts the unseenbranch function
    $seen = array();
    foreach ($seenbranches as $seenbranch) {
        if (!$seenbranch->flag) {
            $seen[$seenbranch->pageid] = $seenbranch->pageid;
        } else {
            $start = $seenbranch->pageid;
            break;
        }
    }
    // this function searches through the lesson pages to find all the branch tables
    // that follow the flagged branch table
    $pageid = $lessonpages[$start]->nextpageid; // move down from the flagged branch table
    $branchtables = array();
    while ($pageid != 0) {  // grab all of the branch table till eol
        if ($lessonpages[$pageid]->qtype == LESSON_PAGE_BRANCHTABLE) {
            $branchtables[] = $lessonpages[$pageid]->id;
        }
        $pageid = $lessonpages[$pageid]->nextpageid;
    }
    $unseen = array();
    foreach ($branchtables as $branchtable) {
        // load all of the unseen branch tables into unseen
        if (!array_key_exists($branchtable, $seen)) {
            $unseen[] = $branchtable;
        }
    }
    if (count($unseen) > 0) {
        return $unseen[rand(0, count($unseen)-1)];  // returns a random page id for the next page
    } else {
        return LESSON_EOL;  // has viewed all of the branch tables
    }
}

/**
 * Handles the random jump between a branch table and end of branch or end of lesson (LESSON_RANDOMPAGE).
 *
 * @param lesson $lesson
 * @param int $pageid The id of the page that we are jumping from (?)
 * @return int The pageid of a random page that is within a branch table
 **/
function lesson_random_question_jump($lesson, $pageid) {
    global $DB;

    // get the lesson pages
    $params = array ("lessonid" => $lesson->id);
    if (!$lessonpages = $DB->get_records_select("lesson_pages", "lessonid = :lessonid", $params)) {
        print_error('cannotfindpages', 'lesson');
    }

    // go up the pages till branch table
    while ($pageid != 0) { // this condition should never be satisfied... only happens if there are no branch tables above this page

        if ($lessonpages[$pageid]->qtype == LESSON_PAGE_BRANCHTABLE) {
            break;
        }
        $pageid = $lessonpages[$pageid]->prevpageid;
    }

    // get the pages within the branch
    $pagesinbranch = $lesson->get_sub_pages_of($pageid, array(LESSON_PAGE_BRANCHTABLE, LESSON_PAGE_ENDOFBRANCH));

    if(count($pagesinbranch) == 0) {
        // there are no pages inside the branch, so return the next page
        return $lessonpages[$pageid]->nextpageid;
    } else {
        return $pagesinbranch[rand(0, count($pagesinbranch)-1)]->id;  // returns a random page id for the next page
    }
}

/**
 * Calculates a user's grade for a lesson.
 *
 * @param object $lesson The lesson that the user is taking.
 * @param int $retries The attempt number.
 * @param int $userid Id of the user (optional, default current user).
 * @return object { nquestions => number of questions answered
                    attempts => number of question attempts
                    total => max points possible
                    earned => points earned by student
                    grade => calculated percentage grade
                    nmanual => number of manually graded questions
                    manualpoints => point value for manually graded questions }
 */
function lesson_grade($lesson, $ntries, $userid = 0) {
    global $USER, $DB;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    // Zero out everything
    $ncorrect     = 0;
    $nviewed      = 0;
    $score        = 0;
    $nmanual      = 0;
    $manualpoints = 0;
    $thegrade     = 0;
    $nquestions   = 0;
    $total        = 0;
    $earned       = 0;

    $params = array ("lessonid" => $lesson->id, "userid" => $userid, "retry" => $ntries);
    if ($useranswers = $DB->get_records_select("lesson_attempts",  "lessonid = :lessonid AND
            userid = :userid AND retry = :retry", $params, "timeseen")) {
        // group each try with its page
        $attemptset = array();
        foreach ($useranswers as $useranswer) {
            $attemptset[$useranswer->pageid][] = $useranswer;
        }

        // Drop all attempts that go beyond max attempts for the lesson
        foreach ($attemptset as $key => $set) {
            $attemptset[$key] = array_slice($set, 0, $lesson->maxattempts);
        }

        // get only the pages and their answers that the user answered
        list($usql, $parameters) = $DB->get_in_or_equal(array_keys($attemptset));
        array_unshift($parameters, $lesson->id);
        $pages = $DB->get_records_select("lesson_pages", "lessonid = ? AND id $usql", $parameters);
        $answers = $DB->get_records_select("lesson_answers", "lessonid = ? AND pageid $usql", $parameters);

        // Number of pages answered
        $nquestions = count($pages);

        foreach ($attemptset as $attempts) {
            $page = lesson_page::load($pages[end($attempts)->pageid], $lesson);
            if ($lesson->custom) {
                $attempt = end($attempts);
                // If essay question, handle it, otherwise add to score
                if ($page->requires_manual_grading()) {
                    $useranswerobj = unserialize($attempt->useranswer);
                    if (isset($useranswerobj->score)) {
                        $earned += $useranswerobj->score;
                    }
                    $nmanual++;
                    $manualpoints += $answers[$attempt->answerid]->score;
                } else if (!empty($attempt->answerid)) {
                    $earned += $page->earned_score($answers, $attempt);
                }
            } else {
                foreach ($attempts as $attempt) {
                    $earned += $attempt->correct;
                }
                $attempt = end($attempts); // doesn't matter which one
                // If essay question, increase numbers
                if ($page->requires_manual_grading()) {
                    $nmanual++;
                    $manualpoints++;
                }
            }
            // Number of times answered
            $nviewed += count($attempts);
        }

        if ($lesson->custom) {
            $bestscores = array();
            // Find the highest possible score per page to get our total
            foreach ($answers as $answer) {
                if(!isset($bestscores[$answer->pageid])) {
                    $bestscores[$answer->pageid] = $answer->score;
                } else if ($bestscores[$answer->pageid] < $answer->score) {
                    $bestscores[$answer->pageid] = $answer->score;
                }
            }
            $total = array_sum($bestscores);
        } else {
            // Check to make sure the student has answered the minimum questions
            if ($lesson->minquestions and $nquestions < $lesson->minquestions) {
                // Nope, increase number viewed by the amount of unanswered questions
                $total =  $nviewed + ($lesson->minquestions - $nquestions);
            } else {
                $total = $nviewed;
            }
        }
    }

    if ($total) { // not zero
        $thegrade = round(100 * $earned / $total, 5);
    }

    // Build the grade information object
    $gradeinfo               = new stdClass;
    $gradeinfo->nquestions   = $nquestions;
    $gradeinfo->attempts     = $nviewed;
    $gradeinfo->total        = $total;
    $gradeinfo->earned       = $earned;
    $gradeinfo->grade        = $thegrade;
    $gradeinfo->nmanual      = $nmanual;
    $gradeinfo->manualpoints = $manualpoints;

    return $gradeinfo;
}

/**
 * Determines if a user can view the left menu.  The determining factor
 * is whether a user has a grade greater than or equal to the lesson setting
 * of displayleftif
 *
 * @param object $lesson Lesson object of the current lesson
 * @return boolean 0 if the user cannot see, or $lesson->displayleft to keep displayleft unchanged
 **/
function lesson_displayleftif($lesson) {
    global $CFG, $USER, $DB;

    if (!empty($lesson->displayleftif)) {
        // get the current user's max grade for this lesson
        $params = array ("userid" => $USER->id, "lessonid" => $lesson->id);
        if ($maxgrade = $DB->get_record_sql('SELECT userid, MAX(grade) AS maxgrade FROM {lesson_grades} WHERE userid = :userid AND lessonid = :lessonid GROUP BY userid', $params)) {
            if ($maxgrade->maxgrade < $lesson->displayleftif) {
                return 0;  // turn off the displayleft
            }
        } else {
            return 0; // no grades
        }
    }

    // if we get to here, keep the original state of displayleft lesson setting
    return $lesson->displayleft;
}

/**
 *
 * @param $cm
 * @param $lesson
 * @param $page
 * @return unknown_type
 */
function lesson_add_fake_blocks($page, $cm, $lesson, $timer = null) {
    $bc = lesson_menu_block_contents($cm->id, $lesson);
    if (!empty($bc)) {
        $regions = $page->blocks->get_regions();
        $firstregion = reset($regions);
        $page->blocks->add_fake_block($bc, $firstregion);
    }

    $bc = lesson_mediafile_block_contents($cm->id, $lesson);
    if (!empty($bc)) {
        $page->blocks->add_fake_block($bc, $page->blocks->get_default_region());
    }

    if (!empty($timer)) {
        $bc = lesson_clock_block_contents($cm->id, $lesson, $timer, $page);
        if (!empty($bc)) {
            $page->blocks->add_fake_block($bc, $page->blocks->get_default_region());
        }
    }
}

/**
 * If there is a media file associated with this
 * lesson, return a block_contents that displays it.
 *
 * @param int $cmid Course Module ID for this lesson
 * @param object $lesson Full lesson record object
 * @return block_contents
 **/
function lesson_mediafile_block_contents($cmid, $lesson) {
    global $OUTPUT;
    if (empty($lesson->mediafile)) {
        return null;
    }

    $options = array();
    $options['menubar'] = 0;
    $options['location'] = 0;
    $options['left'] = 5;
    $options['top'] = 5;
    $options['scrollbars'] = 1;
    $options['resizable'] = 1;
    $options['width'] = $lesson->mediawidth;
    $options['height'] = $lesson->mediaheight;

    $link = new moodle_url('/mod/lesson/mediafile.php?id='.$cmid);
    $action = new popup_action('click', $link, 'lessonmediafile', $options);
    $content = $OUTPUT->action_link($link, get_string('mediafilepopup', 'lesson'), $action, array('title'=>get_string('mediafilepopup', 'lesson')));

    $bc = new block_contents();
    $bc->title = get_string('linkedmedia', 'lesson');
    $bc->attributes['class'] = 'mediafile block';
    $bc->content = $content;

    return $bc;
}

/**
 * If a timed lesson and not a teacher, then
 * return a block_contents containing the clock.
 *
 * @param int $cmid Course Module ID for this lesson
 * @param object $lesson Full lesson record object
 * @param object $timer Full timer record object
 * @return block_contents
 **/
function lesson_clock_block_contents($cmid, $lesson, $timer, $page) {
    // Display for timed lessons and for students only
    $context = context_module::instance($cmid);
    if ($lesson->timelimit == 0 || has_capability('mod/lesson:manage', $context)) {
        return null;
    }

    $content = '<div id="lesson-timer">';
    $content .=  $lesson->time_remaining($timer->starttime);
    $content .= '</div>';

    $clocksettings = array('starttime' => $timer->starttime, 'servertime' => time(), 'testlength' => $lesson->timelimit);
    $page->requires->data_for_js('clocksettings', $clocksettings, true);
    $page->requires->strings_for_js(array('timeisup'), 'lesson');
    $page->requires->js('/mod/lesson/timer.js');
    $page->requires->js_init_call('show_clock');

    $bc = new block_contents();
    $bc->title = get_string('timeremaining', 'lesson');
    $bc->attributes['class'] = 'clock block';
    $bc->content = $content;

    return $bc;
}

/**
 * If left menu is turned on, then this will
 * print the menu in a block
 *
 * @param int $cmid Course Module ID for this lesson
 * @param lesson $lesson Full lesson record object
 * @return void
 **/
function lesson_menu_block_contents($cmid, $lesson) {
    global $CFG, $DB;

    if (!$lesson->displayleft) {
        return null;
    }

    $pages = $lesson->load_all_pages();
    foreach ($pages as $page) {
        if ((int)$page->prevpageid === 0) {
            $pageid = $page->id;
            break;
        }
    }
    $currentpageid = optional_param('pageid', $pageid, PARAM_INT);

    if (!$pageid || !$pages) {
        return null;
    }

    $content = '<a href="#maincontent" class="skip">'.get_string('skip', 'lesson')."</a>\n<div class=\"menuwrapper\">\n<ul>\n";

    while ($pageid != 0) {
        $page = $pages[$pageid];

        // Only process branch tables with display turned on
        if ($page->displayinmenublock && $page->display) {
            if ($page->id == $currentpageid) {
                $content .= '<li class="selected">'.format_string($page->title,true)."</li>\n";
            } else {
                $content .= "<li class=\"notselected\"><a href=\"$CFG->wwwroot/mod/lesson/view.php?id=$cmid&amp;pageid=$page->id\">".format_string($page->title,true)."</a></li>\n";
            }

        }
        $pageid = $page->nextpageid;
    }
    $content .= "</ul>\n</div>\n";

    $bc = new block_contents();
    $bc->title = get_string('lessonmenu', 'lesson');
    $bc->attributes['class'] = 'menu block';
    $bc->content = $content;

    return $bc;
}

/**
 * Adds header buttons to the page for the lesson
 *
 * @param object $cm
 * @param object $context
 * @param bool $extraeditbuttons
 * @param int $lessonpageid
 */
function lesson_add_header_buttons($cm, $context, $extraeditbuttons=false, $lessonpageid=null) {
    global $CFG, $PAGE, $OUTPUT;
    if (has_capability('mod/lesson:edit', $context) && $extraeditbuttons) {
        if ($lessonpageid === null) {
            print_error('invalidpageid', 'lesson');
        }
        if (!empty($lessonpageid) && $lessonpageid != LESSON_EOL) {
            $url = new moodle_url('/mod/lesson/editpage.php', array(
                'id'       => $cm->id,
                'pageid'   => $lessonpageid,
                'edit'     => 1,
                'returnto' => $PAGE->url->out(false)
            ));
            $PAGE->set_button($OUTPUT->single_button($url, get_string('editpagecontent', 'lesson')));
        }
    }
}

/**
 * This is a function used to detect media types and generate html code.
 *
 * @global object $CFG
 * @global object $PAGE
 * @param object $lesson
 * @param object $context
 * @return string $code the html code of media
 */
function lesson_get_media_html($lesson, $context) {
    global $CFG, $PAGE, $OUTPUT;
    require_once("$CFG->libdir/resourcelib.php");

    // get the media file link
    if (strpos($lesson->mediafile, '://') !== false) {
        $url = new moodle_url($lesson->mediafile);
    } else {
        // the timemodified is used to prevent caching problems, instead of '/' we should better read from files table and use sortorder
        $url = moodle_url::make_pluginfile_url($context->id, 'mod_lesson', 'mediafile', $lesson->timemodified, '/', ltrim($lesson->mediafile, '/'));
    }
    $title = $lesson->mediafile;

    $clicktoopen = html_writer::link($url, get_string('download'));

    $mimetype = resourcelib_guess_url_mimetype($url);

    $extension = resourcelib_get_extension($url->out(false));

    $mediarenderer = $PAGE->get_renderer('core', 'media');
    $embedoptions = array(
        core_media::OPTION_TRUSTED => true,
        core_media::OPTION_BLOCK => true
    );

    // find the correct type and print it out
    if (in_array($mimetype, array('image/gif','image/jpeg','image/png'))) {  // It's an image
        $code = resourcelib_embed_image($url, $title);

    } else if ($mediarenderer->can_embed_url($url, $embedoptions)) {
        // Media (audio/video) file.
        $code = $mediarenderer->embed_url($url, $title, 0, 0, $embedoptions);

    } else {
        // anything else - just try object tag enlarged as much as possible
        $code = resourcelib_embed_general($url, $title, $clicktoopen, $mimetype);
    }

    return $code;
}

/**
 * Logic to happen when a/some group(s) has/have been deleted in a course.
 *
 * @param int $courseid The course ID.
 * @param int $groupid The group id if it is known
 * @return void
 */
function lesson_process_group_deleted_in_course($courseid, $groupid = null) {
    global $DB;

    $params = array('courseid' => $courseid);
    if ($groupid) {
        $params['groupid'] = $groupid;
        // We just update the group that was deleted.
        $sql = "SELECT o.id, o.lessonid
                  FROM {lesson_overrides} o
                  JOIN {lesson} lesson ON lesson.id = o.lessonid
                 WHERE lesson.course = :courseid
                   AND o.groupid = :groupid";
    } else {
        // No groupid, we update all orphaned group overrides for all lessons in course.
        $sql = "SELECT o.id, o.lessonid
                  FROM {lesson_overrides} o
                  JOIN {lesson} lesson ON lesson.id = o.lessonid
             LEFT JOIN {groups} grp ON grp.id = o.groupid
                 WHERE lesson.course = :courseid
                   AND o.groupid IS NOT NULL
                   AND grp.id IS NULL";
    }
    $records = $DB->get_records_sql_menu($sql, $params);
    if (!$records) {
        return; // Nothing to do.
    }
    $DB->delete_records_list('lesson_overrides', 'id', array_keys($records));
}

/**
 * Abstract class that page type's MUST inherit from.
 *
 * This is the abstract class that ALL add page type forms must extend.
 * You will notice that all but two of the methods this class contains are final.
 * Essentially the only thing that extending classes can do is extend custom_definition.
 * OR if it has a special requirement on creation it can extend construction_override
 *
 * @abstract
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class lesson_add_page_form_base extends moodleform {

    /**
     * This is the classic define that is used to identify this pagetype.
     * Will be one of LESSON_*
     * @var int
     */
    public $qtype;

    /**
     * The simple string that describes the page type e.g. truefalse, multichoice
     * @var string
     */
    public $qtypestring;

    /**
     * An array of options used in the htmleditor
     * @var array
     */
    protected $editoroptions = array();

    /**
     * True if this is a standard page of false if it does something special.
     * Questions are standard pages, branch tables are not
     * @var bool
     */
    protected $standard = true;

    /**
     * Answer format supported by question type.
     */
    protected $answerformat = '';

    /**
     * Response format supported by question type.
     */
    protected $responseformat = '';

    /**
     * Each page type can and should override this to add any custom elements to
     * the basic form that they want
     */
    public function custom_definition() {}

    /**
     * Returns answer format used by question type.
     */
    public function get_answer_format() {
        return $this->answerformat;
    }

    /**
     * Returns response format used by question type.
     */
    public function get_response_format() {
        return $this->responseformat;
    }

    /**
     * Used to determine if this is a standard page or a special page
     * @return bool
     */
    public final function is_standard() {
        return (bool)$this->standard;
    }

    /**
     * Add the required basic elements to the form.
     *
     * This method adds the basic elements to the form including title and contents
     * and then calls custom_definition();
     */
    public final function definition() {
        $mform = $this->_form;
        $editoroptions = $this->_customdata['editoroptions'];

        $mform->addElement('header', 'qtypeheading', get_string('createaquestionpage', 'lesson', get_string($this->qtypestring, 'lesson')));

        if (!empty($this->_customdata['returnto'])) {
            $mform->addElement('hidden', 'returnto', $this->_customdata['returnto']);
            $mform->setType('returnto', PARAM_URL);
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'pageid');
        $mform->setType('pageid', PARAM_INT);

        if ($this->standard === true) {
            $mform->addElement('hidden', 'qtype');
            $mform->setType('qtype', PARAM_INT);

            $mform->addElement('text', 'title', get_string('pagetitle', 'lesson'), array('size'=>70));
            $mform->setType('title', PARAM_TEXT);
            $mform->addRule('title', get_string('required'), 'required', null, 'client');

            $this->editoroptions = array('noclean'=>true, 'maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$this->_customdata['maxbytes']);
            $mform->addElement('editor', 'contents_editor', get_string('pagecontents', 'lesson'), null, $this->editoroptions);
            $mform->setType('contents_editor', PARAM_RAW);
            $mform->addRule('contents_editor', get_string('required'), 'required', null, 'client');
        }

        $this->custom_definition();

        if ($this->_customdata['edit'] === true) {
            $mform->addElement('hidden', 'edit', 1);
            $mform->setType('edit', PARAM_BOOL);
            $this->add_action_buttons(get_string('cancel'), get_string('savepage', 'lesson'));
        } else if ($this->qtype === 'questiontype') {
            $this->add_action_buttons(get_string('cancel'), get_string('addaquestionpage', 'lesson'));
        } else {
            $this->add_action_buttons(get_string('cancel'), get_string('savepage', 'lesson'));
        }
    }

    /**
     * Convenience function: Adds a jumpto select element
     *
     * @param string $name
     * @param string|null $label
     * @param int $selected The page to select by default
     */
    protected final function add_jumpto($name, $label=null, $selected=LESSON_NEXTPAGE) {
        $title = get_string("jump", "lesson");
        if ($label === null) {
            $label = $title;
        }
        if (is_int($name)) {
            $name = "jumpto[$name]";
        }
        $this->_form->addElement('select', $name, $label, $this->_customdata['jumpto']);
        $this->_form->setDefault($name, $selected);
        $this->_form->addHelpButton($name, 'jumps', 'lesson');
    }

    /**
     * Convenience function: Adds a score input element
     *
     * @param string $name
     * @param string|null $label
     * @param mixed $value The default value
     */
    protected final function add_score($name, $label=null, $value=null) {
        if ($label === null) {
            $label = get_string("score", "lesson");
        }

        if (is_int($name)) {
            $name = "score[$name]";
        }
        $this->_form->addElement('text', $name, $label, array('size'=>5));
        $this->_form->setType($name, PARAM_INT);
        if ($value !== null) {
            $this->_form->setDefault($name, $value);
        }
        $this->_form->addHelpButton($name, 'score', 'lesson');

        // Score is only used for custom scoring. Disable the element when not in use to stop some confusion.
        if (!$this->_customdata['lesson']->custom) {
            $this->_form->freeze($name);
        }
    }

    /**
     * Convenience function: Adds an answer editor
     *
     * @param int $count The count of the element to add
     * @param string $label, null means default
     * @param bool $required
     * @param string $format
     * @return void
     */
    protected final function add_answer($count, $label = null, $required = false, $format= '') {
        if ($label === null) {
            $label = get_string('answer', 'lesson');
        }

        if ($format == LESSON_ANSWER_HTML) {
            $this->_form->addElement('editor', 'answer_editor['.$count.']', $label,
                    array('rows' => '4', 'columns' => '80'),
                    array('noclean' => true, 'maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $this->_customdata['maxbytes']));
            $this->_form->setType('answer_editor['.$count.']', PARAM_RAW);
            $this->_form->setDefault('answer_editor['.$count.']', array('text' => '', 'format' => FORMAT_HTML));
        } else {
            $this->_form->addElement('text', 'answer_editor['.$count.']', $label,
                    array('size' => '50', 'maxlength' => '200'));
            $this->_form->setType('answer_editor['.$count.']', PARAM_TEXT);
        }

        if ($required) {
            $this->_form->addRule('answer_editor['.$count.']', get_string('required'), 'required', null, 'client');
        }
    }
    /**
     * Convenience function: Adds an response editor
     *
     * @param int $count The count of the element to add
     * @param string $label, null means default
     * @param bool $required
     * @return void
     */
    protected final function add_response($count, $label = null, $required = false) {
        if ($label === null) {
            $label = get_string('response', 'lesson');
        }
        $this->_form->addElement('editor', 'response_editor['.$count.']', $label,
                 array('rows' => '4', 'columns' => '80'),
                 array('noclean' => true, 'maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $this->_customdata['maxbytes']));
        $this->_form->setType('response_editor['.$count.']', PARAM_RAW);
        $this->_form->setDefault('response_editor['.$count.']', array('text' => '', 'format' => FORMAT_HTML));

        if ($required) {
            $this->_form->addRule('response_editor['.$count.']', get_string('required'), 'required', null, 'client');
        }
    }

    /**
     * A function that gets called upon init of this object by the calling script.
     *
     * This can be used to process an immediate action if required. Currently it
     * is only used in special cases by non-standard page types.
     *
     * @return bool
     */
    public function construction_override($pageid, lesson $lesson) {
        return true;
    }
}



/**
 * Class representation of a lesson
 *
 * This class is used the interact with, and manage a lesson once instantiated.
 * If you need to fetch a lesson object you can do so by calling
 *
 * <code>
 * lesson::load($lessonid);
 * // or
 * $lessonrecord = $DB->get_record('lesson', $lessonid);
 * $lesson = new lesson($lessonrecord);
 * </code>
 *
 * The class itself extends lesson_base as all classes within the lesson module should
 *
 * These properties are from the database
 * @property int $id The id of this lesson
 * @property int $course The ID of the course this lesson belongs to
 * @property string $name The name of this lesson
 * @property int $practice Flag to toggle this as a practice lesson
 * @property int $modattempts Toggle to allow the user to go back and review answers
 * @property int $usepassword Toggle the use of a password for entry
 * @property string $password The password to require users to enter
 * @property int $dependency ID of another lesson this lesson is dependent on
 * @property string $conditions Conditions of the lesson dependency
 * @property int $grade The maximum grade a user can achieve (%)
 * @property int $custom Toggle custom scoring on or off
 * @property int $ongoing Toggle display of an ongoing score
 * @property int $usemaxgrade How retakes are handled (max=1, mean=0)
 * @property int $maxanswers The max number of answers or branches
 * @property int $maxattempts The maximum number of attempts a user can record
 * @property int $review Toggle use or wrong answer review button
 * @property int $nextpagedefault Override the default next page
 * @property int $feedback Toggles display of default feedback
 * @property int $minquestions Sets a minimum value of pages seen when calculating grades
 * @property int $maxpages Maximum number of pages this lesson can contain
 * @property int $retake Flag to allow users to retake a lesson
 * @property int $activitylink Relate this lesson to another lesson
 * @property string $mediafile File to pop up to or webpage to display
 * @property int $mediaheight Sets the height of the media file popup
 * @property int $mediawidth Sets the width of the media file popup
 * @property int $mediaclose Toggle display of a media close button
 * @property int $slideshow Flag for whether branch pages should be shown as slideshows
 * @property int $width Width of slideshow
 * @property int $height Height of slideshow
 * @property string $bgcolor Background colour of slideshow
 * @property int $displayleft Display a left menu
 * @property int $displayleftif Sets the condition on which the left menu is displayed
 * @property int $progressbar Flag to toggle display of a lesson progress bar
 * @property int $highscores Flag to toggle collection of high scores
 * @property int $maxhighscores Number of high scores to limit to
 * @property int $available Timestamp of when this lesson becomes available
 * @property int $deadline Timestamp of when this lesson is no longer available
 * @property int $timemodified Timestamp when lesson was last modified
 *
 * These properties are calculated
 * @property int $firstpageid Id of the first page of this lesson (prevpageid=0)
 * @property int $lastpageid Id of the last page of this lesson (nextpageid=0)
 *
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lesson extends lesson_base {

    /**
     * The id of the first page (where prevpageid = 0) gets set and retrieved by
     * {@see get_firstpageid()} by directly calling <code>$lesson->firstpageid;</code>
     * @var int
     */
    protected $firstpageid = null;
    /**
     * The id of the last page (where nextpageid = 0) gets set and retrieved by
     * {@see get_lastpageid()} by directly calling <code>$lesson->lastpageid;</code>
     * @var int
     */
    protected $lastpageid = null;
    /**
     * An array used to cache the pages associated with this lesson after the first
     * time they have been loaded.
     * A note to developers: If you are going to be working with MORE than one or
     * two pages from a lesson you should probably call {@see $lesson->load_all_pages()}
     * in order to save excess database queries.
     * @var array An array of lesson_page objects
     */
    protected $pages = array();
    /**
     * Flag that gets set to true once all of the pages associated with the lesson
     * have been loaded.
     * @var bool
     */
    protected $loadedallpages = false;

    /**
     * Simply generates a lesson object given an array/object of properties
     * Overrides {@see lesson_base->create()}
     * @static
     * @param object|array $properties
     * @return lesson
     */
    public static function create($properties) {
        return new lesson($properties);
    }

    /**
     * Generates a lesson object from the database given its id
     * @static
     * @param int $lessonid
     * @return lesson
     */
    public static function load($lessonid) {
        global $DB;

        if (!$lesson = $DB->get_record('lesson', array('id' => $lessonid))) {
            print_error('invalidcoursemodule');
        }
        return new lesson($lesson);
    }

    /**
     * Deletes this lesson from the database
     */
    public function delete() {
        global $CFG, $DB;
        require_once($CFG->libdir.'/gradelib.php');
        require_once($CFG->dirroot.'/calendar/lib.php');

        $cm = get_coursemodule_from_instance('lesson', $this->properties->id, $this->properties->course);
        $context = context_module::instance($cm->id);

        $this->delete_all_overrides();

        $DB->delete_records("lesson", array("id"=>$this->properties->id));
        $DB->delete_records("lesson_pages", array("lessonid"=>$this->properties->id));
        $DB->delete_records("lesson_answers", array("lessonid"=>$this->properties->id));
        $DB->delete_records("lesson_attempts", array("lessonid"=>$this->properties->id));
        $DB->delete_records("lesson_grades", array("lessonid"=>$this->properties->id));
        $DB->delete_records("lesson_timer", array("lessonid"=>$this->properties->id));
        $DB->delete_records("lesson_branch", array("lessonid"=>$this->properties->id));
        $DB->delete_records("lesson_high_scores", array("lessonid"=>$this->properties->id));
        if ($events = $DB->get_records('event', array("modulename"=>'lesson', "instance"=>$this->properties->id))) {
            foreach($events as $event) {
                $event = calendar_event::load($event);
                $event->delete();
            }
        }

        // Delete files associated with this module.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id);

        grade_update('mod/lesson', $this->properties->course, 'mod', 'lesson', $this->properties->id, 0, null, array('deleted'=>1));
        return true;
    }

    /**
     * Deletes a lesson override from the database and clears any corresponding calendar events
     *
     * @param int $overrideid The id of the override being deleted
     * @return bool true on success
     */
    public function delete_override($overrideid) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/calendar/lib.php');

        $cm = get_coursemodule_from_instance('lesson', $this->properties->id, $this->properties->course);

        $override = $DB->get_record('lesson_overrides', array('id' => $overrideid), '*', MUST_EXIST);

        // Delete the events.
        $conds = array('modulename' => 'lesson',
                'instance' => $this->properties->id);
        if (isset($override->userid)) {
            $conds['userid'] = $override->userid;
        } else {
            $conds['groupid'] = $override->groupid;
        }
        $events = $DB->get_records('event', $conds);
        foreach ($events as $event) {
            $eventold = calendar_event::load($event);
            $eventold->delete();
        }

        $DB->delete_records('lesson_overrides', array('id' => $overrideid));

        // Set the common parameters for one of the events we will be triggering.
        $params = array(
            'objectid' => $override->id,
            'context' => context_module::instance($cm->id),
            'other' => array(
                'lessonid' => $override->lessonid
            )
        );
        // Determine which override deleted event to fire.
        if (!empty($override->userid)) {
            $params['relateduserid'] = $override->userid;
            $event = \mod_lesson\event\user_override_deleted::create($params);
        } else {
            $params['other']['groupid'] = $override->groupid;
            $event = \mod_lesson\event\group_override_deleted::create($params);
        }

        // Trigger the override deleted event.
        $event->add_record_snapshot('lesson_overrides', $override);
        $event->trigger();

        return true;
    }

    /**
     * Deletes all lesson overrides from the database and clears any corresponding calendar events
     */
    public function delete_all_overrides() {
        global $DB;

        $overrides = $DB->get_records('lesson_overrides', array('lessonid' => $this->properties->id), 'id');
        foreach ($overrides as $override) {
            $this->delete_override($override->id);
        }
    }

    /**
     * Updates the lesson properties with override information for a user.
     *
     * Algorithm:  For each lesson setting, if there is a matching user-specific override,
     *   then use that otherwise, if there are group-specific overrides, return the most
     *   lenient combination of them.  If neither applies, leave the quiz setting unchanged.
     *
     *   Special case: if there is more than one password that applies to the user, then
     *   lesson->extrapasswords will contain an array of strings giving the remaining
     *   passwords.
     *
     * @param int $userid The userid.
     */
    public function update_effective_access($userid) {
        global $DB;

        // Check for user override.
        $override = $DB->get_record('lesson_overrides', array('lessonid' => $this->properties->id, 'userid' => $userid));

        if (!$override) {
            $override = new stdClass();
            $override->available = null;
            $override->deadline = null;
            $override->timelimit = null;
            $override->review = null;
            $override->maxattempts = null;
            $override->retake = null;
            $override->password = null;
        }

        // Check for group overrides.
        $groupings = groups_get_user_groups($this->properties->course, $userid);

        if (!empty($groupings[0])) {
            // Select all overrides that apply to the User's groups.
            list($extra, $params) = $DB->get_in_or_equal(array_values($groupings[0]));
            $sql = "SELECT * FROM {lesson_overrides}
                    WHERE groupid $extra AND lessonid = ?";
            $params[] = $this->properties->id;
            $records = $DB->get_records_sql($sql, $params);

            // Combine the overrides.
            $availables = array();
            $deadlines = array();
            $timelimits = array();
            $reviews = array();
            $attempts = array();
            $retakes = array();
            $passwords = array();

            foreach ($records as $gpoverride) {
                if (isset($gpoverride->available)) {
                    $availables[] = $gpoverride->available;
                }
                if (isset($gpoverride->deadline)) {
                    $deadlines[] = $gpoverride->deadline;
                }
                if (isset($gpoverride->timelimit)) {
                    $timelimits[] = $gpoverride->timelimit;
                }
                if (isset($gpoverride->review)) {
                    $reviews[] = $gpoverride->review;
                }
                if (isset($gpoverride->maxattempts)) {
                    $attempts[] = $gpoverride->maxattempts;
                }
                if (isset($gpoverride->retake)) {
                    $retakes[] = $gpoverride->retake;
                }
                if (isset($gpoverride->password)) {
                    $passwords[] = $gpoverride->password;
                }
            }
            // If there is a user override for a setting, ignore the group override.
            if (is_null($override->available) && count($availables)) {
                $override->available = min($availables);
            }
            if (is_null($override->deadline) && count($deadlines)) {
                if (in_array(0, $deadlines)) {
                    $override->deadline = 0;
                } else {
                    $override->deadline = max($deadlines);
                }
            }
            if (is_null($override->timelimit) && count($timelimits)) {
                if (in_array(0, $timelimits)) {
                    $override->timelimit = 0;
                } else {
                    $override->timelimit = max($timelimits);
                }
            }
            if (is_null($override->review) && count($reviews)) {
                $override->review = max($reviews);
            }
            if (is_null($override->maxattempts) && count($attempts)) {
                $override->maxattempts = max($attempts);
            }
            if (is_null($override->retake) && count($retakes)) {
                $override->retake = max($retakes);
            }
            if (is_null($override->password) && count($passwords)) {
                $override->password = array_shift($passwords);
                if (count($passwords)) {
                    $override->extrapasswords = $passwords;
                }
            }

        }

        // Merge with lesson defaults.
        $keys = array('available', 'deadline', 'timelimit', 'maxattempts', 'review', 'retake');
        foreach ($keys as $key) {
            if (isset($override->{$key})) {
                $this->properties->{$key} = $override->{$key};
            }
        }

        // Special handling of lesson usepassword and password.
        if (isset($override->password)) {
            if ($override->password == '') {
                $this->properties->usepassword = 0;
            } else {
                $this->properties->usepassword = 1;
                $this->properties->password = $override->password;
                if (isset($override->extrapasswords)) {
                    $this->properties->extrapasswords = $override->extrapasswords;
                }
            }
        }
    }

    /**
     * Fetches messages from the session that may have been set in previous page
     * actions.
     *
     * <code>
     * // Do not call this method directly instead use
     * $lesson->messages;
     * </code>
     *
     * @return array
     */
    protected function get_messages() {
        global $SESSION;

        $messages = array();
        if (!empty($SESSION->lesson_messages) && is_array($SESSION->lesson_messages) && array_key_exists($this->properties->id, $SESSION->lesson_messages)) {
            $messages = $SESSION->lesson_messages[$this->properties->id];
            unset($SESSION->lesson_messages[$this->properties->id]);
        }

        return $messages;
    }

    /**
     * Get all of the attempts for the current user.
     *
     * @param int $retries
     * @param bool $correct Optional: only fetch correct attempts
     * @param int $pageid Optional: only fetch attempts at the given page
     * @param int $userid Optional: defaults to the current user if not set
     * @return array|false
     */
    public function get_attempts($retries, $correct=false, $pageid=null, $userid=null) {
        global $USER, $DB;
        $params = array("lessonid"=>$this->properties->id, "userid"=>$userid, "retry"=>$retries);
        if ($correct) {
            $params['correct'] = 1;
        }
        if ($pageid !== null) {
            $params['pageid'] = $pageid;
        }
        if ($userid === null) {
            $params['userid'] = $USER->id;
        }
        return $DB->get_records('lesson_attempts', $params, 'timeseen ASC');
    }

    /**
     * Returns the first page for the lesson or false if there isn't one.
     *
     * This method should be called via the magic method __get();
     * <code>
     * $firstpage = $lesson->firstpage;
     * </code>
     *
     * @return lesson_page|bool Returns the lesson_page specialised object or false
     */
    protected function get_firstpage() {
        $pages = $this->load_all_pages();
        if (count($pages) > 0) {
            foreach ($pages as $page) {
                if ((int)$page->prevpageid === 0) {
                    return $page;
                }
            }
        }
        return false;
    }

    /**
     * Returns the last page for the lesson or false if there isn't one.
     *
     * This method should be called via the magic method __get();
     * <code>
     * $lastpage = $lesson->lastpage;
     * </code>
     *
     * @return lesson_page|bool Returns the lesson_page specialised object or false
     */
    protected function get_lastpage() {
        $pages = $this->load_all_pages();
        if (count($pages) > 0) {
            foreach ($pages as $page) {
                if ((int)$page->nextpageid === 0) {
                    return $page;
                }
            }
        }
        return false;
    }

    /**
     * Returns the id of the first page of this lesson. (prevpageid = 0)
     * @return int
     */
    protected function get_firstpageid() {
        global $DB;
        if ($this->firstpageid == null) {
            if (!$this->loadedallpages) {
                $firstpageid = $DB->get_field('lesson_pages', 'id', array('lessonid'=>$this->properties->id, 'prevpageid'=>0));
                if (!$firstpageid) {
                    print_error('cannotfindfirstpage', 'lesson');
                }
                $this->firstpageid = $firstpageid;
            } else {
                $firstpage = $this->get_firstpage();
                $this->firstpageid = $firstpage->id;
            }
        }
        return $this->firstpageid;
    }

    /**
     * Returns the id of the last page of this lesson. (nextpageid = 0)
     * @return int
     */
    public function get_lastpageid() {
        global $DB;
        if ($this->lastpageid == null) {
            if (!$this->loadedallpages) {
                $lastpageid = $DB->get_field('lesson_pages', 'id', array('lessonid'=>$this->properties->id, 'nextpageid'=>0));
                if (!$lastpageid) {
                    print_error('cannotfindlastpage', 'lesson');
                }
                $this->lastpageid = $lastpageid;
            } else {
                $lastpageid = $this->get_lastpage();
                $this->lastpageid = $lastpageid->id;
            }
        }

        return $this->lastpageid;
    }

     /**
     * Gets the next page id to display after the one that is provided.
     * @param int $nextpageid
     * @return bool
     */
    public function get_next_page($nextpageid) {
        global $USER, $DB;
        $allpages = $this->load_all_pages();
        if ($this->properties->nextpagedefault) {
            // in Flash Card mode...first get number of retakes
            $nretakes = $DB->count_records("lesson_grades", array("lessonid" => $this->properties->id, "userid" => $USER->id));
            shuffle($allpages);
            $found = false;
            if ($this->properties->nextpagedefault == LESSON_UNSEENPAGE) {
                foreach ($allpages as $nextpage) {
                    if (!$DB->count_records("lesson_attempts", array("pageid" => $nextpage->id, "userid" => $USER->id, "retry" => $nretakes))) {
                        $found = true;
                        break;
                    }
                }
            } elseif ($this->properties->nextpagedefault == LESSON_UNANSWEREDPAGE) {
                foreach ($allpages as $nextpage) {
                    if (!$DB->count_records("lesson_attempts", array('pageid' => $nextpage->id, 'userid' => $USER->id, 'correct' => 1, 'retry' => $nretakes))) {
                        $found = true;
                        break;
                    }
                }
            }
            if ($found) {
                if ($this->properties->maxpages) {
                    // check number of pages viewed (in the lesson)
                    if ($DB->count_records("lesson_attempts", array("lessonid" => $this->properties->id, "userid" => $USER->id, "retry" => $nretakes)) >= $this->properties->maxpages) {
                        return LESSON_EOL;
                    }
                }
                return $nextpage->id;
            }
        }
        // In a normal lesson mode
        foreach ($allpages as $nextpage) {
            if ((int)$nextpage->id === (int)$nextpageid) {
                return $nextpage->id;
            }
        }
        return LESSON_EOL;
    }

    /**
     * Sets a message against the session for this lesson that will displayed next
     * time the lesson processes messages
     *
     * @param string $message
     * @param string $class
     * @param string $align
     * @return bool
     */
    public function add_message($message, $class="notifyproblem", $align='center') {
        global $SESSION;

        if (empty($SESSION->lesson_messages) || !is_array($SESSION->lesson_messages)) {
            $SESSION->lesson_messages = array();
            $SESSION->lesson_messages[$this->properties->id] = array();
        } else if (!array_key_exists($this->properties->id, $SESSION->lesson_messages)) {
            $SESSION->lesson_messages[$this->properties->id] = array();
        }

        $SESSION->lesson_messages[$this->properties->id][] = array($message, $class, $align);

        return true;
    }

    /**
     * Check if the lesson is accessible at the present time
     * @return bool True if the lesson is accessible, false otherwise
     */
    public function is_accessible() {
        $available = $this->properties->available;
        $deadline = $this->properties->deadline;
        return (($available == 0 || time() >= $available) && ($deadline == 0 || time() < $deadline));
    }

    /**
     * Starts the lesson time for the current user
     * @return bool Returns true
     */
    public function start_timer() {
        global $USER, $DB;

        $cm = get_coursemodule_from_instance('lesson', $this->properties()->id, $this->properties()->course,
            false, MUST_EXIST);

        // Trigger lesson started event.
        $event = \mod_lesson\event\lesson_started::create(array(
            'objectid' => $this->properties()->id,
            'context' => context_module::instance($cm->id),
            'courseid' => $this->properties()->course
        ));
        $event->trigger();

        $USER->startlesson[$this->properties->id] = true;
        $startlesson = new stdClass;
        $startlesson->lessonid = $this->properties->id;
        $startlesson->userid = $USER->id;
        $startlesson->starttime = time();
        $startlesson->lessontime = time();
        $DB->insert_record('lesson_timer', $startlesson);
        if ($this->properties->timelimit) {
            $this->add_message(get_string('timelimitwarning', 'lesson', format_time($this->properties->timelimit)), 'center');
        }
        return true;
    }

    /**
     * Updates the timer to the current time and returns the new timer object
     * @param bool $restart If set to true the timer is restarted
     * @param bool $continue If set to true AND $restart=true then the timer
     *                        will continue from a previous attempt
     * @return stdClass The new timer
     */
    public function update_timer($restart=false, $continue=false, $endreached =false) {
        global $USER, $DB;

        $cm = get_coursemodule_from_instance('lesson', $this->properties->id, $this->properties->course);

        // clock code
        // get time information for this user
        $params = array("lessonid" => $this->properties->id, "userid" => $USER->id);
        if (!$timer = $DB->get_records('lesson_timer', $params, 'starttime DESC', '*', 0, 1)) {
            $this->start_timer();
            $timer = $DB->get_records('lesson_timer', $params, 'starttime DESC', '*', 0, 1);
        }
        $timer = current($timer); // This will get the latest start time record.

        if ($restart) {
            if ($continue) {
                // continue a previous test, need to update the clock  (think this option is disabled atm)
                $timer->starttime = time() - ($timer->lessontime - $timer->starttime);

                // Trigger lesson resumed event.
                $event = \mod_lesson\event\lesson_resumed::create(array(
                    'objectid' => $this->properties->id,
                    'context' => context_module::instance($cm->id),
                    'courseid' => $this->properties->course
                ));
                $event->trigger();

            } else {
                // starting over, so reset the clock
                $timer->starttime = time();

                // Trigger lesson restarted event.
                $event = \mod_lesson\event\lesson_restarted::create(array(
                    'objectid' => $this->properties->id,
                    'context' => context_module::instance($cm->id),
                    'courseid' => $this->properties->course
                ));
                $event->trigger();

            }
        }

        $timer->lessontime = time();
        $timer->completed = $endreached;
        $DB->update_record('lesson_timer', $timer);

        // Update completion state.
        $cm = get_coursemodule_from_instance('lesson', $this->properties()->id, $this->properties()->course,
            false, MUST_EXIST);
        $course = get_course($cm->course);
        $completion = new completion_info($course);
        if ($completion->is_enabled($cm) && $this->properties()->completiontimespent > 0) {
            $completion->update_state($cm, COMPLETION_COMPLETE);
        }
        return $timer;
    }

    /**
     * Updates the timer to the current time then stops it by unsetting the user var
     * @return bool Returns true
     */
    public function stop_timer() {
        global $USER, $DB;
        unset($USER->startlesson[$this->properties->id]);

        $cm = get_coursemodule_from_instance('lesson', $this->properties()->id, $this->properties()->course,
            false, MUST_EXIST);

        // Trigger lesson ended event.
        $event = \mod_lesson\event\lesson_ended::create(array(
            'objectid' => $this->properties()->id,
            'context' => context_module::instance($cm->id),
            'courseid' => $this->properties()->course
        ));
        $event->trigger();

        return $this->update_timer(false, false, true);
    }

    /**
     * Checks to see if the lesson has pages
     */
    public function has_pages() {
        global $DB;
        $pagecount = $DB->count_records('lesson_pages', array('lessonid'=>$this->properties->id));
        return ($pagecount>0);
    }

    /**
     * Returns the link for the related activity
     * @return array|false
     */
    public function link_for_activitylink() {
        global $DB;
        $module = $DB->get_record('course_modules', array('id' => $this->properties->activitylink));
        if ($module) {
            $modname = $DB->get_field('modules', 'name', array('id' => $module->module));
            if ($modname) {
                $instancename = $DB->get_field($modname, 'name', array('id' => $module->instance));
                if ($instancename) {
                    return html_writer::link(new moodle_url('/mod/'.$modname.'/view.php', array('id'=>$this->properties->activitylink)),
                        get_string('activitylinkname', 'lesson', $instancename),
                        array('class'=>'centerpadded lessonbutton standardbutton'));
                }
            }
        }
        return '';
    }

    /**
     * Loads the requested page.
     *
     * This function will return the requested page id as either a specialised
     * lesson_page object OR as a generic lesson_page.
     * If the page has been loaded previously it will be returned from the pages
     * array, otherwise it will be loaded from the database first
     *
     * @param int $pageid
     * @return lesson_page A lesson_page object or an object that extends it
     */
    public function load_page($pageid) {
        if (!array_key_exists($pageid, $this->pages)) {
            $manager = lesson_page_type_manager::get($this);
            $this->pages[$pageid] = $manager->load_page($pageid, $this);
        }
        return $this->pages[$pageid];
    }

    /**
     * Loads ALL of the pages for this lesson
     *
     * @return array An array containing all pages from this lesson
     */
    public function load_all_pages() {
        if (!$this->loadedallpages) {
            $manager = lesson_page_type_manager::get($this);
            $this->pages = $manager->load_all_pages($this);
            $this->loadedallpages = true;
        }
        return $this->pages;
    }

    /**
     * Determines if a jumpto value is correct or not.
     *
     * returns true if jumpto page is (logically) after the pageid page or
     * if the jumpto value is a special value.  Returns false in all other cases.
     *
     * @param int $pageid Id of the page from which you are jumping from.
     * @param int $jumpto The jumpto number.
     * @return boolean True or false after a series of tests.
     **/
    public function jumpto_is_correct($pageid, $jumpto) {
        global $DB;

        // first test the special values
        if (!$jumpto) {
            // same page
            return false;
        } elseif ($jumpto == LESSON_NEXTPAGE) {
            return true;
        } elseif ($jumpto == LESSON_UNSEENBRANCHPAGE) {
            return true;
        } elseif ($jumpto == LESSON_RANDOMPAGE) {
            return true;
        } elseif ($jumpto == LESSON_CLUSTERJUMP) {
            return true;
        } elseif ($jumpto == LESSON_EOL) {
            return true;
        }

        $pages = $this->load_all_pages();
        $apageid = $pages[$pageid]->nextpageid;
        while ($apageid != 0) {
            if ($jumpto == $apageid) {
                return true;
            }
            $apageid = $pages[$apageid]->nextpageid;
        }
        return false;
    }

    /**
     * Returns the time a user has remaining on this lesson
     * @param int $starttime Starttime timestamp
     * @return string
     */
    public function time_remaining($starttime) {
        $timeleft = $starttime + $this->properties->timelimit - time();
        $hours = floor($timeleft/3600);
        $timeleft = $timeleft - ($hours * 3600);
        $minutes = floor($timeleft/60);
        $secs = $timeleft - ($minutes * 60);

        if ($minutes < 10) {
            $minutes = "0$minutes";
        }
        if ($secs < 10) {
            $secs = "0$secs";
        }
        $output   = array();
        $output[] = $hours;
        $output[] = $minutes;
        $output[] = $secs;
        $output = implode(':', $output);
        return $output;
    }

    /**
     * Interprets LESSON_CLUSTERJUMP jumpto value.
     *
     * This will select a page randomly
     * and the page selected will be inbetween a cluster page and end of clutter or end of lesson
     * and the page selected will be a page that has not been viewed already
     * and if any pages are within a branch table or end of branch then only 1 page within
     * the branch table or end of branch will be randomly selected (sub clustering).
     *
     * @param int $pageid Id of the current page from which we are jumping from.
     * @param int $userid Id of the user.
     * @return int The id of the next page.
     **/
    public function cluster_jump($pageid, $userid=null) {
        global $DB, $USER;

        if ($userid===null) {
            $userid = $USER->id;
        }
        // get the number of retakes
        if (!$retakes = $DB->count_records("lesson_grades", array("lessonid"=>$this->properties->id, "userid"=>$userid))) {
            $retakes = 0;
        }
        // get all the lesson_attempts aka what the user has seen
        $seenpages = array();
        if ($attempts = $this->get_attempts($retakes)) {
            foreach ($attempts as $attempt) {
                $seenpages[$attempt->pageid] = $attempt->pageid;
            }

        }

        // get the lesson pages
        $lessonpages = $this->load_all_pages();
        // find the start of the cluster
        while ($pageid != 0) { // this condition should not be satisfied... should be a cluster page
            if ($lessonpages[$pageid]->qtype == LESSON_PAGE_CLUSTER) {
                break;
            }
            $pageid = $lessonpages[$pageid]->prevpageid;
        }

        $clusterpages = array();
        $clusterpages = $this->get_sub_pages_of($pageid, array(LESSON_PAGE_ENDOFCLUSTER));
        $unseen = array();
        foreach ($clusterpages as $key=>$cluster) {
            // Remove the page if  it is in a branch table or is an endofbranch.
            if ($this->is_sub_page_of_type($cluster->id,
                    array(LESSON_PAGE_BRANCHTABLE), array(LESSON_PAGE_ENDOFBRANCH, LESSON_PAGE_CLUSTER))
                    || $cluster->qtype == LESSON_PAGE_ENDOFBRANCH) {
                unset($clusterpages[$key]);
            } else if ($cluster->qtype == LESSON_PAGE_BRANCHTABLE) {
                // If branchtable, check to see if any pages inside have been viewed.
                $branchpages = $this->get_sub_pages_of($cluster->id, array(LESSON_PAGE_BRANCHTABLE, LESSON_PAGE_ENDOFBRANCH));
                $flag = true;
                foreach ($branchpages as $branchpage) {
                    if (array_key_exists($branchpage->id, $seenpages)) {  // Check if any of the pages have been viewed.
                        $flag = false;
                    }
                }
                if ($flag && count($branchpages) > 0) {
                    // Add branch table.
                    $unseen[] = $cluster;
                }
            } elseif ($cluster->is_unseen($seenpages)) {
                $unseen[] = $cluster;
            }
        }

        if (count($unseen) > 0) {
            // it does not contain elements, then use exitjump, otherwise find out next page/branch
            $nextpage = $unseen[rand(0, count($unseen)-1)];
            if ($nextpage->qtype == LESSON_PAGE_BRANCHTABLE) {
                // if branch table, then pick a random page inside of it
                $branchpages = $this->get_sub_pages_of($nextpage->id, array(LESSON_PAGE_BRANCHTABLE, LESSON_PAGE_ENDOFBRANCH));
                return $branchpages[rand(0, count($branchpages)-1)]->id;
            } else { // otherwise, return the page's id
                return $nextpage->id;
            }
        } else {
            // seen all there is to see, leave the cluster
            if (end($clusterpages)->nextpageid == 0) {
                return LESSON_EOL;
            } else {
                $clusterendid = $pageid;
                while ($clusterendid != 0) { // This condition should not be satisfied... should be an end of cluster page.
                    if ($lessonpages[$clusterendid]->qtype == LESSON_PAGE_ENDOFCLUSTER) {
                        break;
                    }
                    $clusterendid = $lessonpages[$clusterendid]->nextpageid;
                }
                $exitjump = $DB->get_field("lesson_answers", "jumpto", array("pageid" => $clusterendid, "lessonid" => $this->properties->id));
                if ($exitjump == LESSON_NEXTPAGE) {
                    $exitjump = $lessonpages[$clusterendid]->nextpageid;
                }
                if ($exitjump == 0) {
                    return LESSON_EOL;
                } else if (in_array($exitjump, array(LESSON_EOL, LESSON_PREVIOUSPAGE))) {
                    return $exitjump;
                } else {
                    if (!array_key_exists($exitjump, $lessonpages)) {
                        $found = false;
                        foreach ($lessonpages as $page) {
                            if ($page->id === $clusterendid) {
                                $found = true;
                            } else if ($page->qtype == LESSON_PAGE_ENDOFCLUSTER) {
                                $exitjump = $DB->get_field("lesson_answers", "jumpto", array("pageid" => $page->id, "lessonid" => $this->properties->id));
                                if ($exitjump == LESSON_NEXTPAGE) {
                                    $exitjump = $lessonpages[$page->id]->nextpageid;
                                }
                                break;
                            }
                        }
                    }
                    if (!array_key_exists($exitjump, $lessonpages)) {
                        return LESSON_EOL;
                    }
                    return $exitjump;
                }
            }
        }
    }

    /**
     * Finds all pages that appear to be a subtype of the provided pageid until
     * an end point specified within $ends is encountered or no more pages exist
     *
     * @param int $pageid
     * @param array $ends An array of LESSON_PAGE_* types that signify an end of
     *               the subtype
     * @return array An array of specialised lesson_page objects
     */
    public function get_sub_pages_of($pageid, array $ends) {
        $lessonpages = $this->load_all_pages();
        $pageid = $lessonpages[$pageid]->nextpageid;  // move to the first page after the branch table
        $pages = array();

        while (true) {
            if ($pageid == 0 || in_array($lessonpages[$pageid]->qtype, $ends)) {
                break;
            }
            $pages[] = $lessonpages[$pageid];
            $pageid = $lessonpages[$pageid]->nextpageid;
        }

        return $pages;
    }

    /**
     * Checks to see if the specified page[id] is a subpage of a type specified in
     * the $types array, until either there are no more pages of we find a type
     * corresponding to that of a type specified in $ends
     *
     * @param int $pageid The id of the page to check
     * @param array $types An array of types that would signify this page was a subpage
     * @param array $ends An array of types that mean this is not a subpage
     * @return bool
     */
    public function is_sub_page_of_type($pageid, array $types, array $ends) {
        $pages = $this->load_all_pages();
        $pageid = $pages[$pageid]->prevpageid; // move up one

        array_unshift($ends, 0);
        // go up the pages till branch table
        while (true) {
            if ($pageid==0 || in_array($pages[$pageid]->qtype, $ends)) {
                return false;
            } else if (in_array($pages[$pageid]->qtype, $types)) {
                return true;
            }
            $pageid = $pages[$pageid]->prevpageid;
        }
    }

    /**
     * Move a page resorting all other pages.
     *
     * @param int $pageid
     * @param int $after
     * @return void
     */
    public function resort_pages($pageid, $after) {
        global $CFG;

        $cm = get_coursemodule_from_instance('lesson', $this->properties->id, $this->properties->course);
        $context = context_module::instance($cm->id);

        $pages = $this->load_all_pages();

        if (!array_key_exists($pageid, $pages) || ($after!=0 && !array_key_exists($after, $pages))) {
            print_error('cannotfindpages', 'lesson', "$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id");
        }

        $pagetomove = clone($pages[$pageid]);
        unset($pages[$pageid]);

        $pageids = array();
        if ($after === 0) {
            $pageids['p0'] = $pageid;
        }
        foreach ($pages as $page) {
            $pageids[] = $page->id;
            if ($page->id == $after) {
                $pageids[] = $pageid;
            }
        }

        $pageidsref = $pageids;
        reset($pageidsref);
        $prev = 0;
        $next = next($pageidsref);
        foreach ($pageids as $pid) {
            if ($pid === $pageid) {
                $page = $pagetomove;
            } else {
                $page = $pages[$pid];
            }
            if ($page->prevpageid != $prev || $page->nextpageid != $next) {
                $page->move($next, $prev);

                if ($pid === $pageid) {
                    // We will trigger an event.
                    $pageupdated = array('next' => $next, 'prev' => $prev);
                }
            }

            $prev = $page->id;
            $next = next($pageidsref);
            if (!$next) {
                $next = 0;
            }
        }

        // Trigger an event: page moved.
        if (!empty($pageupdated)) {
            $eventparams = array(
                'context' => $context,
                'objectid' => $pageid,
                'other' => array(
                    'pagetype' => $page->get_typestring(),
                    'prevpageid' => $pageupdated['prev'],
                    'nextpageid' => $pageupdated['next']
                )
            );
            $event = \mod_lesson\event\page_moved::create($eventparams);
            $event->trigger();
        }

    }
}


/**
 * Abstract class to provide a core functions to the all lesson classes
 *
 * This class should be abstracted by ALL classes with the lesson module to ensure
 * that all classes within this module can be interacted with in the same way.
 *
 * This class provides the user with a basic properties array that can be fetched
 * or set via magic methods, or alternatively by defining methods get_blah() or
 * set_blah() within the extending object.
 *
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class lesson_base {

    /**
     * An object containing properties
     * @var stdClass
     */
    protected $properties;

    /**
     * The constructor
     * @param stdClass $properties
     */
    public function __construct($properties) {
        $this->properties = (object)$properties;
    }

    /**
     * Magic property method
     *
     * Attempts to call a set_$key method if one exists otherwise falls back
     * to simply set the property
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        if (method_exists($this, 'set_'.$key)) {
            $this->{'set_'.$key}($value);
        }
        $this->properties->{$key} = $value;
    }

    /**
     * Magic get method
     *
     * Attempts to call a get_$key method to return the property and ralls over
     * to return the raw property
     *
     * @param str $key
     * @return mixed
     */
    public function __get($key) {
        if (method_exists($this, 'get_'.$key)) {
            return $this->{'get_'.$key}();
        }
        return $this->properties->{$key};
    }

    /**
     * Stupid PHP needs an isset magic method if you use the get magic method and
     * still want empty calls to work.... blah ~!
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key) {
        if (method_exists($this, 'get_'.$key)) {
            $val = $this->{'get_'.$key}();
            return !empty($val);
        }
        return !empty($this->properties->{$key});
    }

    //NOTE: E_STRICT does not allow to change function signature!

    /**
     * If implemented should create a new instance, save it in the DB and return it
     */
    //public static function create() {}
    /**
     * If implemented should load an instance from the DB and return it
     */
    //public static function load() {}
    /**
     * Fetches all of the properties of the object
     * @return stdClass
     */
    public function properties() {
        return $this->properties;
    }
}


/**
 * Abstract class representation of a page associated with a lesson.
 *
 * This class should MUST be extended by all specialised page types defined in
 * mod/lesson/pagetypes/.
 * There are a handful of abstract methods that need to be defined as well as
 * severl methods that can optionally be defined in order to make the page type
 * operate in the desired way
 *
 * Database properties
 * @property int $id The id of this lesson page
 * @property int $lessonid The id of the lesson this page belongs to
 * @property int $prevpageid The id of the page before this one
 * @property int $nextpageid The id of the next page in the page sequence
 * @property int $qtype Identifies the page type of this page
 * @property int $qoption Used to record page type specific options
 * @property int $layout Used to record page specific layout selections
 * @property int $display Used to record page specific display selections
 * @property int $timecreated Timestamp for when the page was created
 * @property int $timemodified Timestamp for when the page was last modified
 * @property string $title The title of this page
 * @property string $contents The rich content shown to describe the page
 * @property int $contentsformat The format of the contents field
 *
 * Calculated properties
 * @property-read array $answers An array of answers for this page
 * @property-read bool $displayinmenublock Toggles display in the left menu block
 * @property-read array $jumps An array containing all the jumps this page uses
 * @property-read lesson $lesson The lesson this page belongs to
 * @property-read int $type The type of the page [question | structure]
 * @property-read typeid The unique identifier for the page type
 * @property-read typestring The string that describes this page type
 *
 * @abstract
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class lesson_page extends lesson_base {

    /**
     * A reference to the lesson this page belongs to
     * @var lesson
     */
    protected $lesson = null;
    /**
     * Contains the answers to this lesson_page once loaded
     * @var null|array
     */
    protected $answers = null;
    /**
     * This sets the type of the page, can be one of the constants defined below
     * @var int
     */
    protected $type = 0;

    /**
     * Constants used to identify the type of the page
     */
    const TYPE_QUESTION = 0;
    const TYPE_STRUCTURE = 1;

    /**
     * This method should return the integer used to identify the page type within
     * the database and throughout code. This maps back to the defines used in 1.x
     * @abstract
     * @return int
     */
    abstract protected function get_typeid();
    /**
     * This method should return the string that describes the pagetype
     * @abstract
     * @return string
     */
    abstract protected function get_typestring();

    /**
     * This method gets called to display the page to the user taking the lesson
     * @abstract
     * @param object $renderer
     * @param object $attempt
     * @return string
     */
    abstract public function display($renderer, $attempt);

    /**
     * Creates a new lesson_page within the database and returns the correct pagetype
     * object to use to interact with the new lesson
     *
     * @final
     * @static
     * @param object $properties
     * @param lesson $lesson
     * @return lesson_page Specialised object that extends lesson_page
     */
    final public static function create($properties, lesson $lesson, $context, $maxbytes) {
        global $DB;
        $newpage = new stdClass;
        $newpage->title = $properties->title;
        $newpage->contents = $properties->contents_editor['text'];
        $newpage->contentsformat = $properties->contents_editor['format'];
        $newpage->lessonid = $lesson->id;
        $newpage->timecreated = time();
        $newpage->qtype = $properties->qtype;
        $newpage->qoption = (isset($properties->qoption))?1:0;
        $newpage->layout = (isset($properties->layout))?1:0;
        $newpage->display = (isset($properties->display))?1:0;
        $newpage->prevpageid = 0; // this is a first page
        $newpage->nextpageid = 0; // this is the only page

        if ($properties->pageid) {
            $prevpage = $DB->get_record("lesson_pages", array("id" => $properties->pageid), 'id, nextpageid');
            if (!$prevpage) {
                print_error('cannotfindpages', 'lesson');
            }
            $newpage->prevpageid = $prevpage->id;
            $newpage->nextpageid = $prevpage->nextpageid;
        } else {
            $nextpage = $DB->get_record('lesson_pages', array('lessonid'=>$lesson->id, 'prevpageid'=>0), 'id');
            if ($nextpage) {
                // This is the first page, there are existing pages put this at the start
                $newpage->nextpageid = $nextpage->id;
            }
        }

        $newpage->id = $DB->insert_record("lesson_pages", $newpage);

        $editor = new stdClass;
        $editor->id = $newpage->id;
        $editor->contents_editor = $properties->contents_editor;
        $editor = file_postupdate_standard_editor($editor, 'contents', array('noclean'=>true, 'maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$maxbytes), $context, 'mod_lesson', 'page_contents', $editor->id);
        $DB->update_record("lesson_pages", $editor);

        if ($newpage->prevpageid > 0) {
            $DB->set_field("lesson_pages", "nextpageid", $newpage->id, array("id" => $newpage->prevpageid));
        }
        if ($newpage->nextpageid > 0) {
            $DB->set_field("lesson_pages", "prevpageid", $newpage->id, array("id" => $newpage->nextpageid));
        }

        $page = lesson_page::load($newpage, $lesson);
        $page->create_answers($properties);

        // Trigger an event: page created.
        $eventparams = array(
            'context' => $context,
            'objectid' => $newpage->id,
            'other' => array(
                'pagetype' => $page->get_typestring()
                )
            );
        $event = \mod_lesson\event\page_created::create($eventparams);
        $snapshot = clone($newpage);
        $snapshot->timemodified = 0;
        $event->add_record_snapshot('lesson_pages', $snapshot);
        $event->trigger();

        $lesson->add_message(get_string('insertedpage', 'lesson').': '.format_string($newpage->title, true), 'notifysuccess');

        return $page;
    }

    /**
     * This method loads a page object from the database and returns it as a
     * specialised object that extends lesson_page
     *
     * @final
     * @static
     * @param int $id
     * @param lesson $lesson
     * @return lesson_page Specialised lesson_page object
     */
    final public static function load($id, lesson $lesson) {
        global $DB;

        if (is_object($id) && !empty($id->qtype)) {
            $page = $id;
        } else {
            $page = $DB->get_record("lesson_pages", array("id" => $id));
            if (!$page) {
                print_error('cannotfindpages', 'lesson');
            }
        }
        $manager = lesson_page_type_manager::get($lesson);

        $class = 'lesson_page_type_'.$manager->get_page_type_idstring($page->qtype);
        if (!class_exists($class)) {
            $class = 'lesson_page';
        }

        return new $class($page, $lesson);
    }

    /**
     * Deletes a lesson_page from the database as well as any associated records.
     * @final
     * @return bool
     */
    final public function delete() {
        global $DB;

        $cm = get_coursemodule_from_instance('lesson', $this->lesson->id, $this->lesson->course);
        $context = context_module::instance($cm->id);

        // Delete files associated with attempts.
        $fs = get_file_storage();
        if ($attempts = $DB->get_records('lesson_attempts', array("pageid" => $this->properties->id))) {
            foreach ($attempts as $attempt) {
                $fs->delete_area_files($context->id, 'mod_lesson', 'essay_responses', $attempt->id);
            }
        }

        // Then delete all the associated records...
        $DB->delete_records("lesson_attempts", array("pageid" => $this->properties->id));

        $DB->delete_records("lesson_branch", array("pageid" => $this->properties->id));
        // ...now delete the answers...
        $DB->delete_records("lesson_answers", array("pageid" => $this->properties->id));
        // ..and the page itself
        $DB->delete_records("lesson_pages", array("id" => $this->properties->id));

        // Trigger an event: page deleted.
        $eventparams = array(
            'context' => $context,
            'objectid' => $this->properties->id,
            'other' => array(
                'pagetype' => $this->get_typestring()
                )
            );
        $event = \mod_lesson\event\page_deleted::create($eventparams);
        $event->add_record_snapshot('lesson_pages', $this->properties);
        $event->trigger();

        // Delete files associated with this page.
        $fs->delete_area_files($context->id, 'mod_lesson', 'page_contents', $this->properties->id);
        $fs->delete_area_files($context->id, 'mod_lesson', 'page_answers', $this->properties->id);
        $fs->delete_area_files($context->id, 'mod_lesson', 'page_responses', $this->properties->id);

        // repair the hole in the linkage
        if (!$this->properties->prevpageid && !$this->properties->nextpageid) {
            //This is the only page, no repair needed
        } elseif (!$this->properties->prevpageid) {
            // this is the first page...
            $page = $this->lesson->load_page($this->properties->nextpageid);
            $page->move(null, 0);
        } elseif (!$this->properties->nextpageid) {
            // this is the last page...
            $page = $this->lesson->load_page($this->properties->prevpageid);
            $page->move(0);
        } else {
            // page is in the middle...
            $prevpage = $this->lesson->load_page($this->properties->prevpageid);
            $nextpage = $this->lesson->load_page($this->properties->nextpageid);

            $prevpage->move($nextpage->id);
            $nextpage->move(null, $prevpage->id);
        }
        return true;
    }

    /**
     * Moves a page by updating its nextpageid and prevpageid values within
     * the database
     *
     * @final
     * @param int $nextpageid
     * @param int $prevpageid
     */
    final public function move($nextpageid=null, $prevpageid=null) {
        global $DB;
        if ($nextpageid === null) {
            $nextpageid = $this->properties->nextpageid;
        }
        if ($prevpageid === null) {
            $prevpageid = $this->properties->prevpageid;
        }
        $obj = new stdClass;
        $obj->id = $this->properties->id;
        $obj->prevpageid = $prevpageid;
        $obj->nextpageid = $nextpageid;
        $DB->update_record('lesson_pages', $obj);
    }

    /**
     * Returns the answers that are associated with this page in the database
     *
     * @final
     * @return array
     */
    final public function get_answers() {
        global $DB;
        if ($this->answers === null) {
            $this->answers = array();
            $answers = $DB->get_records('lesson_answers', array('pageid'=>$this->properties->id, 'lessonid'=>$this->lesson->id), 'id');
            if (!$answers) {
                // It is possible that a lesson upgraded from Moodle 1.9 still
                // contains questions without any answers [MDL-25632].
                // debugging(get_string('cannotfindanswer', 'lesson'));
                return array();
            }
            foreach ($answers as $answer) {
                $this->answers[count($this->answers)] = new lesson_page_answer($answer);
            }
        }
        return $this->answers;
    }

    /**
     * Returns the lesson this page is associated with
     * @final
     * @return lesson
     */
    final protected function get_lesson() {
        return $this->lesson;
    }

    /**
     * Returns the type of page this is. Not to be confused with page type
     * @final
     * @return int
     */
    final protected function get_type() {
        return $this->type;
    }

    /**
     * Records an attempt at this page
     *
     * @final
     * @global moodle_database $DB
     * @param stdClass $context
     * @return stdClass Returns the result of the attempt
     */
    final public function record_attempt($context) {
        global $DB, $USER, $OUTPUT, $PAGE;

        /**
         * This should be overridden by each page type to actually check the response
         * against what ever custom criteria they have defined
         */
        $result = $this->check_answer();

        $result->attemptsremaining  = 0;
        $result->maxattemptsreached = false;

        if ($result->noanswer) {
            $result->newpageid = $this->properties->id; // display same page again
            $result->feedback  = get_string('noanswer', 'lesson');
        } else {
            if (!has_capability('mod/lesson:manage', $context)) {
                $nretakes = $DB->count_records("lesson_grades", array("lessonid"=>$this->lesson->id, "userid"=>$USER->id));
                // record student's attempt
                $attempt = new stdClass;
                $attempt->lessonid = $this->lesson->id;
                $attempt->pageid = $this->properties->id;
                $attempt->userid = $USER->id;
                $attempt->answerid = $result->answerid;
                $attempt->retry = $nretakes;
                $attempt->correct = $result->correctanswer;
                if($result->userresponse !== null) {
                    $attempt->useranswer = $result->userresponse;
                }

                $attempt->timeseen = time();
                // if allow modattempts, then update the old attempt record, otherwise, insert new answer record
                $userisreviewing = false;
                if (isset($USER->modattempts[$this->lesson->id])) {
                    $attempt->retry = $nretakes - 1; // they are going through on review, $nretakes will be too high
                    $userisreviewing = true;
                }

                // Only insert a record if we are not reviewing the lesson.
                if (!$userisreviewing) {
                    if ($this->lesson->retake || (!$this->lesson->retake && $nretakes == 0)) {
                        $attempt->id = $DB->insert_record("lesson_attempts", $attempt);
                        // Trigger an event: question answered.
                        $eventparams = array(
                            'context' => context_module::instance($PAGE->cm->id),
                            'objectid' => $this->properties->id,
                            'other' => array(
                                'pagetype' => $this->get_typestring()
                                )
                            );
                        $event = \mod_lesson\event\question_answered::create($eventparams);
                        $event->add_record_snapshot('lesson_attempts', $attempt);
                        $event->trigger();

                    }
                }
                // "number of attempts remaining" message if $this->lesson->maxattempts > 1
                // displaying of message(s) is at the end of page for more ergonomic display
                if (!$result->correctanswer && ($result->newpageid == 0)) {
                    // wrong answer and student is stuck on this page - check how many attempts
                    // the student has had at this page/question
                    $nattempts = $DB->count_records("lesson_attempts", array("pageid"=>$this->properties->id, "userid"=>$USER->id, "retry" => $attempt->retry));
                    // retreive the number of attempts left counter for displaying at bottom of feedback page
                    if ($nattempts >= $this->lesson->maxattempts) {
                        if ($this->lesson->maxattempts > 1) { // don't bother with message if only one attempt
                            $result->maxattemptsreached = true;
                        }
                        $result->newpageid = LESSON_NEXTPAGE;
                    } else if ($this->lesson->maxattempts > 1) { // don't bother with message if only one attempt
                        $result->attemptsremaining = $this->lesson->maxattempts - $nattempts;
                    }
                }
            }
            // TODO: merge this code with the jump code below.  Convert jumpto page into a proper page id
            if ($result->newpageid == 0) {
                $result->newpageid = $this->properties->id;
            } elseif ($result->newpageid == LESSON_NEXTPAGE) {
                $result->newpageid = $this->lesson->get_next_page($this->properties->nextpageid);
            }

            // Determine default feedback if necessary
            if (empty($result->response)) {
                if (!$this->lesson->feedback && !$result->noanswer && !($this->lesson->review & !$result->correctanswer && !$result->isessayquestion)) {
                    // These conditions have been met:
                    //  1. The lesson manager has not supplied feedback to the student
                    //  2. Not displaying default feedback
                    //  3. The user did provide an answer
                    //  4. We are not reviewing with an incorrect answer (and not reviewing an essay question)

                    $result->nodefaultresponse = true;  // This will cause a redirect below
                } else if ($result->isessayquestion) {
                    $result->response = get_string('defaultessayresponse', 'lesson');
                } else if ($result->correctanswer) {
                    $result->response = get_string('thatsthecorrectanswer', 'lesson');
                } else {
                    $result->response = get_string('thatsthewronganswer', 'lesson');
                }
            }

            if ($result->response) {
                if ($this->lesson->review && !$result->correctanswer && !$result->isessayquestion) {
                    $nretakes = $DB->count_records("lesson_grades", array("lessonid"=>$this->lesson->id, "userid"=>$USER->id));
                    $qattempts = $DB->count_records("lesson_attempts", array("userid"=>$USER->id, "retry"=>$nretakes, "pageid"=>$this->properties->id));
                    if ($qattempts == 1) {
                        $result->feedback = $OUTPUT->box(get_string("firstwrong", "lesson"), 'feedback');
                    } else {
                        $result->feedback = $OUTPUT->box(get_string("secondpluswrong", "lesson"), 'feedback');
                    }
                } else {
                    $result->feedback = '';
                }
                $class = 'response';
                if ($result->correctanswer) {
                    $class .= ' correct'; // CSS over-ride this if they exist (!important).
                } else if (!$result->isessayquestion) {
                    $class .= ' incorrect'; // CSS over-ride this if they exist (!important).
                }
                $options = new stdClass;
                $options->noclean = true;
                $options->para = true;
                $options->overflowdiv = true;
                $options->context = $context;

                $result->feedback .= $OUTPUT->box(format_text($this->get_contents(), $this->properties->contentsformat, $options),
                        'generalbox boxaligncenter');
                if (isset($result->studentanswerformat)) {
                    // This is the student's answer so it should be cleaned.
                    $studentanswer = format_text($result->studentanswer, $result->studentanswerformat,
                            array('context' => $context, 'para' => true));
                } else {
                    $studentanswer = format_string($result->studentanswer);
                }
                $result->feedback .= '<div class="correctanswer generalbox"><em>'
                        . get_string("youranswer", "lesson").'</em> : ' . $studentanswer;
                if (isset($result->responseformat)) {
                    $result->response = file_rewrite_pluginfile_urls($result->response, 'pluginfile.php', $context->id,
                            'mod_lesson', 'page_responses', $result->answerid);
                    $result->feedback .= $OUTPUT->box(format_text($result->response, $result->responseformat, $options)
                            , $class);
                } else {
                    $result->feedback .= $OUTPUT->box($result->response, $class);
                }
                $result->feedback .= '</div>';
            }
        }

        return $result;
    }

    /**
     * Returns the string for a jump name
     *
     * @final
     * @param int $jumpto Jump code or page ID
     * @return string
     **/
    final protected function get_jump_name($jumpto) {
        global $DB;
        static $jumpnames = array();

        if (!array_key_exists($jumpto, $jumpnames)) {
            if ($jumpto == LESSON_THISPAGE) {
                $jumptitle = get_string('thispage', 'lesson');
            } elseif ($jumpto == LESSON_NEXTPAGE) {
                $jumptitle = get_string('nextpage', 'lesson');
            } elseif ($jumpto == LESSON_EOL) {
                $jumptitle = get_string('endoflesson', 'lesson');
            } elseif ($jumpto == LESSON_UNSEENBRANCHPAGE) {
                $jumptitle = get_string('unseenpageinbranch', 'lesson');
            } elseif ($jumpto == LESSON_PREVIOUSPAGE) {
                $jumptitle = get_string('previouspage', 'lesson');
            } elseif ($jumpto == LESSON_RANDOMPAGE) {
                $jumptitle = get_string('randompageinbranch', 'lesson');
            } elseif ($jumpto == LESSON_RANDOMBRANCH) {
                $jumptitle = get_string('randombranch', 'lesson');
            } elseif ($jumpto == LESSON_CLUSTERJUMP) {
                $jumptitle = get_string('clusterjump', 'lesson');
            } else {
                if (!$jumptitle = $DB->get_field('lesson_pages', 'title', array('id' => $jumpto))) {
                    $jumptitle = '<strong>'.get_string('notdefined', 'lesson').'</strong>';
                }
            }
            $jumpnames[$jumpto] = format_string($jumptitle,true);
        }

        return $jumpnames[$jumpto];
    }

    /**
     * Constructor method
     * @param object $properties
     * @param lesson $lesson
     */
    public function __construct($properties, lesson $lesson) {
        parent::__construct($properties);
        $this->lesson = $lesson;
    }

    /**
     * Returns the score for the attempt
     * This may be overridden by page types that require manual grading
     * @param array $answers
     * @param object $attempt
     * @return int
     */
    public function earned_score($answers, $attempt) {
        return $answers[$attempt->answerid]->score;
    }

    /**
     * This is a callback method that can be override and gets called when ever a page
     * is viewed
     *
     * @param bool $canmanage True if the user has the manage cap
     * @return mixed
     */
    public function callback_on_view($canmanage) {
        return true;
    }

    /**
     * save editor answers files and update answer record
     *
     * @param object $context
     * @param int $maxbytes
     * @param object $answer
     * @param object $answereditor
     * @param object $responseeditor
     */
    public function save_answers_files($context, $maxbytes, &$answer, $answereditor = '', $responseeditor = '') {
        global $DB;
        if (isset($answereditor['itemid'])) {
            $answer->answer = file_save_draft_area_files($answereditor['itemid'],
                    $context->id, 'mod_lesson', 'page_answers', $answer->id,
                    array('noclean' => true, 'maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $maxbytes),
                    $answer->answer, null);
            $DB->set_field('lesson_answers', 'answer', $answer->answer, array('id' => $answer->id));
        }
        if (isset($responseeditor['itemid'])) {
            $answer->response = file_save_draft_area_files($responseeditor['itemid'],
                    $context->id, 'mod_lesson', 'page_responses', $answer->id,
                    array('noclean' => true, 'maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes' => $maxbytes),
                    $answer->response, null);
            $DB->set_field('lesson_answers', 'response', $answer->response, array('id' => $answer->id));
        }
    }

    /**
     * Rewrite urls in response and optionality answer of a question answer
     *
     * @param object $answer
     * @param bool $rewriteanswer must rewrite answer
     * @return object answer with rewritten urls
     */
    public static function rewrite_answers_urls($answer, $rewriteanswer = true) {
        global $PAGE;

        $context = context_module::instance($PAGE->cm->id);
        if ($rewriteanswer) {
            $answer->answer = file_rewrite_pluginfile_urls($answer->answer, 'pluginfile.php', $context->id,
                    'mod_lesson', 'page_answers', $answer->id);
        }
        $answer->response = file_rewrite_pluginfile_urls($answer->response, 'pluginfile.php', $context->id,
                'mod_lesson', 'page_responses', $answer->id);

        return $answer;
    }

    /**
     * Updates a lesson page and its answers within the database
     *
     * @param object $properties
     * @return bool
     */
    public function update($properties, $context = null, $maxbytes = null) {
        global $DB, $PAGE;
        $answers  = $this->get_answers();
        $properties->id = $this->properties->id;
        $properties->lessonid = $this->lesson->id;
        if (empty($properties->qoption)) {
            $properties->qoption = '0';
        }
        if (empty($context)) {
            $context = $PAGE->context;
        }
        if ($maxbytes === null) {
            $maxbytes = get_user_max_upload_file_size($context);
        }
        $properties->timemodified = time();
        $properties = file_postupdate_standard_editor($properties, 'contents', array('noclean'=>true, 'maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$maxbytes), $context, 'mod_lesson', 'page_contents', $properties->id);
        $DB->update_record("lesson_pages", $properties);

        // Trigger an event: page updated.
        \mod_lesson\event\page_updated::create_from_lesson_page($this, $context)->trigger();

        if ($this->type == self::TYPE_STRUCTURE && $this->get_typeid() != LESSON_PAGE_BRANCHTABLE) {
            // These page types have only one answer to save the jump and score.
            if (count($answers) > 1) {
                $answer = array_shift($answers);
                foreach ($answers as $a) {
                    $DB->delete_record('lesson_answers', array('id' => $a->id));
                }
            } else if (count($answers) == 1) {
                $answer = array_shift($answers);
            } else {
                $answer = new stdClass;
                $answer->lessonid = $properties->lessonid;
                $answer->pageid = $properties->id;
                $answer->timecreated = time();
            }

            $answer->timemodified = time();
            if (isset($properties->jumpto[0])) {
                $answer->jumpto = $properties->jumpto[0];
            }
            if (isset($properties->score[0])) {
                $answer->score = $properties->score[0];
            }
            if (!empty($answer->id)) {
                $DB->update_record("lesson_answers", $answer->properties());
            } else {
                $DB->insert_record("lesson_answers", $answer);
            }
        } else {
            for ($i = 0; $i < $this->lesson->maxanswers; $i++) {
                if (!array_key_exists($i, $this->answers)) {
                    $this->answers[$i] = new stdClass;
                    $this->answers[$i]->lessonid = $this->lesson->id;
                    $this->answers[$i]->pageid = $this->id;
                    $this->answers[$i]->timecreated = $this->timecreated;
                }

                if (!empty($properties->answer_editor[$i])) {
                    if (is_array($properties->answer_editor[$i])) {
                        // Multichoice and true/false pages have an HTML editor.
                        $this->answers[$i]->answer = $properties->answer_editor[$i]['text'];
                        $this->answers[$i]->answerformat = $properties->answer_editor[$i]['format'];
                    } else {
                        // Branch tables, shortanswer and mumerical pages have only a text field.
                        $this->answers[$i]->answer = $properties->answer_editor[$i];
                        $this->answers[$i]->answerformat = FORMAT_MOODLE;
                    }
                }

                if (!empty($properties->response_editor[$i]) && is_array($properties->response_editor[$i])) {
                    $this->answers[$i]->response = $properties->response_editor[$i]['text'];
                    $this->answers[$i]->responseformat = $properties->response_editor[$i]['format'];
                }

                if (isset($this->answers[$i]->answer) && $this->answers[$i]->answer != '') {
                    if (isset($properties->jumpto[$i])) {
                        $this->answers[$i]->jumpto = $properties->jumpto[$i];
                    }
                    if ($this->lesson->custom && isset($properties->score[$i])) {
                        $this->answers[$i]->score = $properties->score[$i];
                    }
                    if (!isset($this->answers[$i]->id)) {
                        $this->answers[$i]->id = $DB->insert_record("lesson_answers", $this->answers[$i]);
                    } else {
                        $DB->update_record("lesson_answers", $this->answers[$i]->properties());
                    }

                    // Save files in answers and responses.
                    if (isset($properties->response_editor[$i])) {
                        $this->save_answers_files($context, $maxbytes, $this->answers[$i],
                                $properties->answer_editor[$i], $properties->response_editor[$i]);
                    } else {
                        $this->save_answers_files($context, $maxbytes, $this->answers[$i],
                                $properties->answer_editor[$i]);
                    }

                } else if (isset($this->answers[$i]->id)) {
                    $DB->delete_records('lesson_answers', array('id' => $this->answers[$i]->id));
                    unset($this->answers[$i]);
                }
            }
        }
        return true;
    }

    /**
     * Can be set to true if the page requires a static link to create a new instance
     * instead of simply being included in the dropdown
     * @param int $previd
     * @return bool
     */
    public function add_page_link($previd) {
        return false;
    }

    /**
     * Returns true if a page has been viewed before
     *
     * @param array|int $param Either an array of pages that have been seen or the
     *                   number of retakes a user has had
     * @return bool
     */
    public function is_unseen($param) {
        global $USER, $DB;
        if (is_array($param)) {
            $seenpages = $param;
            return (!array_key_exists($this->properties->id, $seenpages));
        } else {
            $nretakes = $param;
            if (!$DB->count_records("lesson_attempts", array("pageid"=>$this->properties->id, "userid"=>$USER->id, "retry"=>$nretakes))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks to see if a page has been answered previously
     * @param int $nretakes
     * @return bool
     */
    public function is_unanswered($nretakes) {
        global $DB, $USER;
        if (!$DB->count_records("lesson_attempts", array('pageid'=>$this->properties->id, 'userid'=>$USER->id, 'correct'=>1, 'retry'=>$nretakes))) {
            return true;
        }
        return false;
    }

    /**
     * Creates answers within the database for this lesson_page. Usually only ever
     * called when creating a new page instance
     * @param object $properties
     * @return array
     */
    public function create_answers($properties) {
        global $DB, $PAGE;
        // now add the answers
        $newanswer = new stdClass;
        $newanswer->lessonid = $this->lesson->id;
        $newanswer->pageid = $this->properties->id;
        $newanswer->timecreated = $this->properties->timecreated;

        $cm = get_coursemodule_from_instance('lesson', $this->lesson->id, $this->lesson->course);
        $context = context_module::instance($cm->id);

        $answers = array();

        for ($i = 0; $i < $this->lesson->maxanswers; $i++) {
            $answer = clone($newanswer);

            if (!empty($properties->answer_editor[$i])) {
                if (is_array($properties->answer_editor[$i])) {
                    // Multichoice and true/false pages have an HTML editor.
                    $answer->answer = $properties->answer_editor[$i]['text'];
                    $answer->answerformat = $properties->answer_editor[$i]['format'];
                } else {
                    // Branch tables, shortanswer and mumerical pages have only a text field.
                    $answer->answer = $properties->answer_editor[$i];
                    $answer->answerformat = FORMAT_MOODLE;
                }
            }
            if (!empty($properties->response_editor[$i]) && is_array($properties->response_editor[$i])) {
                $answer->response = $properties->response_editor[$i]['text'];
                $answer->responseformat = $properties->response_editor[$i]['format'];
            }

            if (isset($answer->answer) && $answer->answer != '') {
                if (isset($properties->jumpto[$i])) {
                    $answer->jumpto = $properties->jumpto[$i];
                }
                if ($this->lesson->custom && isset($properties->score[$i])) {
                    $answer->score = $properties->score[$i];
                }
                $answer->id = $DB->insert_record("lesson_answers", $answer);
                if (isset($properties->response_editor[$i])) {
                    $this->save_answers_files($context, $PAGE->course->maxbytes, $answer,
                            $properties->answer_editor[$i], $properties->response_editor[$i]);
                } else {
                    $this->save_answers_files($context, $PAGE->course->maxbytes, $answer,
                            $properties->answer_editor[$i]);
                }
                $answers[$answer->id] = new lesson_page_answer($answer);
            } else {
                break;
            }
        }

        $this->answers = $answers;
        return $answers;
    }

    /**
     * This method MUST be overridden by all question page types, or page types that
     * wish to score a page.
     *
     * The structure of result should always be the same so it is a good idea when
     * overriding this method on a page type to call
     * <code>
     * $result = parent::check_answer();
     * </code>
     * before modifying it as required.
     *
     * @return stdClass
     */
    public function check_answer() {
        $result = new stdClass;
        $result->answerid        = 0;
        $result->noanswer        = false;
        $result->correctanswer   = false;
        $result->isessayquestion = false;   // use this to turn off review button on essay questions
        $result->response        = '';
        $result->newpageid       = 0;       // stay on the page
        $result->studentanswer   = '';      // use this to store student's answer(s) in order to display it on feedback page
        $result->userresponse    = null;
        $result->feedback        = '';
        $result->nodefaultresponse  = false; // Flag for redirecting when default feedback is turned off
        return $result;
    }

    /**
     * True if the page uses a custom option
     *
     * Should be override and set to true if the page uses a custom option.
     *
     * @return bool
     */
    public function has_option() {
        return false;
    }

    /**
     * Returns the maximum number of answers for this page given the maximum number
     * of answers permitted by the lesson.
     *
     * @param int $default
     * @return int
     */
    public function max_answers($default) {
        return $default;
    }

    /**
     * Returns the properties of this lesson page as an object
     * @return stdClass;
     */
    public function properties() {
        $properties = clone($this->properties);
        if ($this->answers === null) {
            $this->get_answers();
        }
        if (count($this->answers)>0) {
            $count = 0;
            $qtype = $properties->qtype;
            foreach ($this->answers as $answer) {
                $properties->{'answer_editor['.$count.']'} = array('text' => $answer->answer, 'format' => $answer->answerformat);
                if ($qtype != LESSON_PAGE_MATCHING) {
                    $properties->{'response_editor['.$count.']'} = array('text' => $answer->response, 'format' => $answer->responseformat);
                } else {
                    $properties->{'response_editor['.$count.']'} = $answer->response;
                }
                $properties->{'jumpto['.$count.']'} = $answer->jumpto;
                $properties->{'score['.$count.']'} = $answer->score;
                $count++;
            }
        }
        return $properties;
    }

    /**
     * Returns an array of options to display when choosing the jumpto for a page/answer
     * @static
     * @param int $pageid
     * @param lesson $lesson
     * @return array
     */
    public static function get_jumptooptions($pageid, lesson $lesson) {
        global $DB;
        $jump = array();
        $jump[0] = get_string("thispage", "lesson");
        $jump[LESSON_NEXTPAGE] = get_string("nextpage", "lesson");
        $jump[LESSON_PREVIOUSPAGE] = get_string("previouspage", "lesson");
        $jump[LESSON_EOL] = get_string("endoflesson", "lesson");

        if ($pageid == 0) {
            return $jump;
        }

        $pages = $lesson->load_all_pages();
        if ($pages[$pageid]->qtype == LESSON_PAGE_BRANCHTABLE || $lesson->is_sub_page_of_type($pageid, array(LESSON_PAGE_BRANCHTABLE), array(LESSON_PAGE_ENDOFBRANCH, LESSON_PAGE_CLUSTER))) {
            $jump[LESSON_UNSEENBRANCHPAGE] = get_string("unseenpageinbranch", "lesson");
            $jump[LESSON_RANDOMPAGE] = get_string("randompageinbranch", "lesson");
        }
        if($pages[$pageid]->qtype == LESSON_PAGE_CLUSTER || $lesson->is_sub_page_of_type($pageid, array(LESSON_PAGE_CLUSTER), array(LESSON_PAGE_ENDOFCLUSTER))) {
            $jump[LESSON_CLUSTERJUMP] = get_string("clusterjump", "lesson");
        }
        if (!optional_param('firstpage', 0, PARAM_INT)) {
            $apageid = $DB->get_field("lesson_pages", "id", array("lessonid" => $lesson->id, "prevpageid" => 0));
            while (true) {
                if ($apageid) {
                    $title = $DB->get_field("lesson_pages", "title", array("id" => $apageid));
                    $jump[$apageid] = strip_tags(format_string($title,true));
                    $apageid = $DB->get_field("lesson_pages", "nextpageid", array("id" => $apageid));
                } else {
                    // last page reached
                    break;
                }
            }
        }
        return $jump;
    }
    /**
     * Returns the contents field for the page properly formatted and with plugin
     * file url's converted
     * @return string
     */
    public function get_contents() {
        global $PAGE;
        if (!empty($this->properties->contents)) {
            if (!isset($this->properties->contentsformat)) {
                $this->properties->contentsformat = FORMAT_HTML;
            }
            $context = context_module::instance($PAGE->cm->id);
            $contents = file_rewrite_pluginfile_urls($this->properties->contents, 'pluginfile.php', $context->id, 'mod_lesson',
                                                     'page_contents', $this->properties->id);  // Must do this BEFORE format_text()!
            return format_text($contents, $this->properties->contentsformat,
                               array('context' => $context, 'noclean' => true,
                                     'overflowdiv' => true));  // Page edit is marked with XSS, we want all content here.
        } else {
            return '';
        }
    }

    /**
     * Set to true if this page should display in the menu block
     * @return bool
     */
    protected function get_displayinmenublock() {
        return false;
    }

    /**
     * Get the string that describes the options of this page type
     * @return string
     */
    public function option_description_string() {
        return '';
    }

    /**
     * Updates a table with the answers for this page
     * @param html_table $table
     * @return html_table
     */
    public function display_answers(html_table $table) {
        $answers = $this->get_answers();
        $i = 1;
        foreach ($answers as $answer) {
            $cells = array();
            $cells[] = "<span class=\"label\">".get_string("jump", "lesson")." $i<span>: ";
            $cells[] = $this->get_jump_name($answer->jumpto);
            $table->data[] = new html_table_row($cells);
            if ($i === 1){
                $table->data[count($table->data)-1]->cells[0]->style = 'width:20%;';
            }
            $i++;
        }
        return $table;
    }

    /**
     * Determines if this page should be grayed out on the management/report screens
     * @return int 0 or 1
     */
    protected function get_grayout() {
        return 0;
    }

    /**
     * Adds stats for this page to the &pagestats object. This should be defined
     * for all page types that grade
     * @param array $pagestats
     * @param int $tries
     * @return bool
     */
    public function stats(array &$pagestats, $tries) {
        return true;
    }

    /**
     * Formats the answers of this page for a report
     *
     * @param object $answerpage
     * @param object $answerdata
     * @param object $useranswer
     * @param array $pagestats
     * @param int $i Count of first level answers
     * @param int $n Count of second level answers
     * @return object The answer page for this
     */
    public function report_answers($answerpage, $answerdata, $useranswer, $pagestats, &$i, &$n) {
        $answers = $this->get_answers();
        $formattextdefoptions = new stdClass;
        $formattextdefoptions->para = false;  //I'll use it widely in this page
        foreach ($answers as $answer) {
            $data = get_string('jumpsto', 'lesson', $this->get_jump_name($answer->jumpto));
            $answerdata->answers[] = array($data, "");
            $answerpage->answerdata = $answerdata;
        }
        return $answerpage;
    }

    /**
     * Gets an array of the jumps used by the answers of this page
     *
     * @return array
     */
    public function get_jumps() {
        global $DB;
        $jumps = array();
        $params = array ("lessonid" => $this->lesson->id, "pageid" => $this->properties->id);
        if ($answers = $this->get_answers()) {
            foreach ($answers as $answer) {
                $jumps[] = $this->get_jump_name($answer->jumpto);
            }
        } else {
            $jumps[] = $this->get_jump_name($this->properties->nextpageid);
        }
        return $jumps;
    }
    /**
     * Informs whether this page type require manual grading or not
     * @return bool
     */
    public function requires_manual_grading() {
        return false;
    }

    /**
     * A callback method that allows a page to override the next page a user will
     * see during when this page is being completed.
     * @return false|int
     */
    public function override_next_page() {
        return false;
    }

    /**
     * This method is used to determine if this page is a valid page
     *
     * @param array $validpages
     * @param array $pageviews
     * @return int The next page id to check
     */
    public function valid_page_and_view(&$validpages, &$pageviews) {
        $validpages[$this->properties->id] = 1;
        return $this->properties->nextpageid;
    }
}



/**
 * Class used to represent an answer to a page
 *
 * @property int $id The ID of this answer in the database
 * @property int $lessonid The ID of the lesson this answer belongs to
 * @property int $pageid The ID of the page this answer belongs to
 * @property int $jumpto Identifies where the user goes upon completing a page with this answer
 * @property int $grade The grade this answer is worth
 * @property int $score The score this answer will give
 * @property int $flags Used to store options for the answer
 * @property int $timecreated A timestamp of when the answer was created
 * @property int $timemodified A timestamp of when the answer was modified
 * @property string $answer The answer itself
 * @property string $response The response the user sees if selecting this answer
 *
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lesson_page_answer extends lesson_base {

    /**
     * Loads an page answer from the DB
     *
     * @param int $id
     * @return lesson_page_answer
     */
    public static function load($id) {
        global $DB;
        $answer = $DB->get_record("lesson_answers", array("id" => $id));
        return new lesson_page_answer($answer);
    }

    /**
     * Given an object of properties and a page created answer(s) and saves them
     * in the database.
     *
     * @param stdClass $properties
     * @param lesson_page $page
     * @return array
     */
    public static function create($properties, lesson_page $page) {
        return $page->create_answers($properties);
    }

}

/**
 * A management class for page types
 *
 * This class is responsible for managing the different pages. A manager object can
 * be retrieved by calling the following line of code:
 * <code>
 * $manager  = lesson_page_type_manager::get($lesson);
 * </code>
 * The first time the page type manager is retrieved the it includes all of the
 * different page types located in mod/lesson/pagetypes.
 *
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lesson_page_type_manager {

    /**
     * An array of different page type classes
     * @var array
     */
    protected $types = array();

    /**
     * Retrieves the lesson page type manager object
     *
     * If the object hasn't yet been created it is created here.
     *
     * @staticvar lesson_page_type_manager $pagetypemanager
     * @param lesson $lesson
     * @return lesson_page_type_manager
     */
    public static function get(lesson $lesson) {
        static $pagetypemanager;
        if (!($pagetypemanager instanceof lesson_page_type_manager)) {
            $pagetypemanager = new lesson_page_type_manager();
            $pagetypemanager->load_lesson_types($lesson);
        }
        return $pagetypemanager;
    }

    /**
     * Finds and loads all lesson page types in mod/lesson/pagetypes
     *
     * @param lesson $lesson
     */
    public function load_lesson_types(lesson $lesson) {
        global $CFG;
        $basedir = $CFG->dirroot.'/mod/lesson/pagetypes/';
        $dir = dir($basedir);
        while (false !== ($entry = $dir->read())) {
            if (strpos($entry, '.')===0 || !preg_match('#^[a-zA-Z]+\.php#i', $entry)) {
                continue;
            }
            require_once($basedir.$entry);
            $class = 'lesson_page_type_'.strtok($entry,'.');
            if (class_exists($class)) {
                $pagetype = new $class(new stdClass, $lesson);
                $this->types[$pagetype->typeid] = $pagetype;
            }
        }

    }

    /**
     * Returns an array of strings to describe the loaded page types
     *
     * @param int $type Can be used to return JUST the string for the requested type
     * @return array
     */
    public function get_page_type_strings($type=null, $special=true) {
        $types = array();
        foreach ($this->types as $pagetype) {
            if (($type===null || $pagetype->type===$type) && ($special===true || $pagetype->is_standard())) {
                $types[$pagetype->typeid] = $pagetype->typestring;
            }
        }
        return $types;
    }

    /**
     * Returns the basic string used to identify a page type provided with an id
     *
     * This string can be used to instantiate or identify the page type class.
     * If the page type id is unknown then 'unknown' is returned
     *
     * @param int $id
     * @return string
     */
    public function get_page_type_idstring($id) {
        foreach ($this->types as $pagetype) {
            if ((int)$pagetype->typeid === (int)$id) {
                return $pagetype->idstring;
            }
        }
        return 'unknown';
    }

    /**
     * Loads a page for the provided lesson given it's id
     *
     * This function loads a page from the lesson when given both the lesson it belongs
     * to as well as the page's id.
     * If the page doesn't exist an error is thrown
     *
     * @param int $pageid The id of the page to load
     * @param lesson $lesson The lesson the page belongs to
     * @return lesson_page A class that extends lesson_page
     */
    public function load_page($pageid, lesson $lesson) {
        global $DB;
        if (!($page =$DB->get_record('lesson_pages', array('id'=>$pageid, 'lessonid'=>$lesson->id)))) {
            print_error('cannotfindpages', 'lesson');
        }
        $pagetype = get_class($this->types[$page->qtype]);
        $page = new $pagetype($page, $lesson);
        return $page;
    }

    /**
     * This function detects errors in the ordering between 2 pages and updates the page records.
     *
     * @param stdClass $page1 Either the first of 2 pages or null if the $page2 param is the first in the list.
     * @param stdClass $page1 Either the second of 2 pages or null if the $page1 param is the last in the list.
     */
    protected function check_page_order($page1, $page2) {
        global $DB;
        if (empty($page1)) {
            if ($page2->prevpageid != 0) {
                debugging("***prevpageid of page " . $page2->id . " set to 0***");
                $page2->prevpageid = 0;
                $DB->set_field("lesson_pages", "prevpageid", 0, array("id" => $page2->id));
            }
        } else if (empty($page2)) {
            if ($page1->nextpageid != 0) {
                debugging("***nextpageid of page " . $page1->id . " set to 0***");
                $page1->nextpageid = 0;
                $DB->set_field("lesson_pages", "nextpageid", 0, array("id" => $page1->id));
            }
        } else {
            if ($page1->nextpageid != $page2->id) {
                debugging("***nextpageid of page " . $page1->id . " set to " . $page2->id . "***");
                $page1->nextpageid = $page2->id;
                $DB->set_field("lesson_pages", "nextpageid", $page2->id, array("id" => $page1->id));
            }
            if ($page2->prevpageid != $page1->id) {
                debugging("***prevpageid of page " . $page2->id . " set to " . $page1->id . "***");
                $page2->prevpageid = $page1->id;
                $DB->set_field("lesson_pages", "prevpageid", $page1->id, array("id" => $page2->id));
            }
        }
    }

    /**
     * This function loads ALL pages that belong to the lesson.
     *
     * @param lesson $lesson
     * @return array An array of lesson_page_type_*
     */
    public function load_all_pages(lesson $lesson) {
        global $DB;
        if (!($pages =$DB->get_records('lesson_pages', array('lessonid'=>$lesson->id)))) {
            return array(); // Records returned empty.
        }
        foreach ($pages as $key=>$page) {
            $pagetype = get_class($this->types[$page->qtype]);
            $pages[$key] = new $pagetype($page, $lesson);
        }

        $orderedpages = array();
        $lastpageid = 0;
        $morepages = true;
        while ($morepages) {
            $morepages = false;
            foreach ($pages as $page) {
                if ((int)$page->prevpageid === (int)$lastpageid) {
                    // Check for errors in page ordering and fix them on the fly.
                    $prevpage = null;
                    if ($lastpageid !== 0) {
                        $prevpage = $orderedpages[$lastpageid];
                    }
                    $this->check_page_order($prevpage, $page);
                    $morepages = true;
                    $orderedpages[$page->id] = $page;
                    unset($pages[$page->id]);
                    $lastpageid = $page->id;
                    if ((int)$page->nextpageid===0) {
                        break 2;
                    } else {
                        break 1;
                    }
                }
            }
        }

        // Add remaining pages and fix the nextpageid links for each page.
        foreach ($pages as $page) {
            // Check for errors in page ordering and fix them on the fly.
            $prevpage = null;
            if ($lastpageid !== 0) {
                $prevpage = $orderedpages[$lastpageid];
            }
            $this->check_page_order($prevpage, $page);
            $orderedpages[$page->id] = $page;
            unset($pages[$page->id]);
            $lastpageid = $page->id;
        }

        if ($lastpageid !== 0) {
            $this->check_page_order($orderedpages[$lastpageid], null);
        }

        return $orderedpages;
    }

    /**
     * Fetches an mform that can be used to create/edit an page
     *
     * @param int $type The id for the page type
     * @param array $arguments Any arguments to pass to the mform
     * @return lesson_add_page_form_base
     */
    public function get_page_form($type, $arguments) {
        $class = 'lesson_add_page_form_'.$this->get_page_type_idstring($type);
        if (!class_exists($class) || get_parent_class($class)!=='lesson_add_page_form_base') {
            debugging('Lesson page type unknown class requested '.$class, DEBUG_DEVELOPER);
            $class = 'lesson_add_page_form_selection';
        } else if ($class === 'lesson_add_page_form_unknown') {
            $class = 'lesson_add_page_form_selection';
        }
        return new $class(null, $arguments);
    }

    /**
     * Returns an array of links to use as add page links
     * @param int $previd The id of the previous page
     * @return array
     */
    public function get_add_page_type_links($previd) {
        global $OUTPUT;

        $links = array();

        foreach ($this->types as $key=>$type) {
            if ($link = $type->add_page_link($previd)) {
                $links[$key] = $link;
            }
        }

        return $links;
    }
}
