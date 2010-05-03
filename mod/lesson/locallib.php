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
 * @package   lesson
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

/** Make sure this isn't being directly accessed */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); /// It must be included from a Moodle page.
}

/** Include the files that are required by this module */
require_once($CFG->dirroot . '/mod/lesson/lib.php');

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
 * @param int $lesson Id of the lesson that is to be checked.
 * @return boolean True or false.
 **/
function lesson_display_teacher_warning($lesson) {
    global $DB;

    // get all of the lesson answers
    $params = array ("lessonid" => $lesson->id);
    if (!$lessonanswers = $DB->get_records_select("lesson_answers", "lessonid = :lessonid", $params)) {
        // no answers, then not useing cluster or unseen
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

    $pagesinbranch = $this->get_sub_pages_of($pageid, array(LESSON_PAGE_BRANCHTABLE, LESSON_PAGE_ENDOFBRANCH));

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

    // this loads all the viewed branch tables into $seen untill it finds the branch table with the flag
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
    $pagesinbranch = $this->get_sub_pages_of($pageid, array(LESSON_PAGE_BRANCHTABLE, LESSON_PAGE_ENDOFBRANCH));

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
 * @param int $userid Id of the user (optinal, default current user).
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
                    $earned += $page->earned_score($answers, $attempt);
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
function lesson_add_pretend_blocks($page, $cm, $lesson, $timer = null) {
    $bc = lesson_menu_block_contents($cm->id, $lesson);
    if (!empty($bc)) {
        $regions = $page->blocks->get_regions();
        $firstregion = reset($regions);
        $page->blocks->add_pretend_block($bc, $firstregion);
    }

    $bc = lesson_mediafile_block_contents($cm->id, $lesson);
    if (!empty($bc)) {
        $page->blocks->add_pretend_block($bc, $page->blocks->get_default_region());
    }

    if (!empty($timer)) {
        $bc = lesson_clock_block_contents($cm->id, $lesson, $timer, $page);
        if (!empty($bc)) {
            $page->blocks->add_pretend_block($bc, $page->blocks->get_default_region());
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
    if (empty($lesson->mediafile) && empty($lesson->mediafileid)) {
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
    $bc->attributes['class'] = 'mediafile';
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
    $context = get_context_instance(CONTEXT_MODULE, $cmid);
    if(!$lesson->timed || has_capability('mod/lesson:manage', $context)) {
        return null;
    }

    $content = '<div class="jshidewhenenabled">';
    $content .=  $lesson->time_remaining($timer->starttime);
    $content .= '</div>';

    $clocksettings = array('starttime'=>$timer->starttime, 'servertime'=>time(),'testlength'=>($lesson->maxtime * 60));
    $page->requires->data_for_js('clocksettings', $clocksettings);
    $page->requires->js('/mod/lesson/timer.js');
    $page->requires->js_function_call('show_clock');

    $bc = new block_contents();
    $bc->title = get_string('timeremaining', 'lesson');
    $bc->attributes['class'] = 'clock sideblock';
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
    $bc->attributes['class'] = 'menu sideblock';
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
            $url = new moodle_url('/mod/lesson/lesson.php', array('id'=>$cm->id, 'redirect'=>'navigation', 'pageid'=>$lessonpageid));
            $PAGE->set_button($OUTPUT->single_button($url, get_string('editpagecontent', 'lesson')));
        }
    }
}
