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
 * Standard library of functions and constants for lesson
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

/* Do not include any libraries here! */

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @global object
 * @param object $lesson Lesson post data from the form
 * @return int
 **/
function lesson_add_instance($data, $mform) {
    global $DB;

    $cmid = $data->coursemodule;

    lesson_process_pre_save($data);

    unset($data->mediafile);
    $lessonid = $DB->insert_record("lesson", $data);
    $data->id = $lessonid;

    $context = get_context_instance(CONTEXT_MODULE, $cmid);
    $lesson = $DB->get_record('lesson', array('id'=>$lessonid), '*', MUST_EXIST);

    if ($filename = $mform->get_new_filename('mediafilepicker')) {
        if ($file = $mform->save_stored_file('mediafilepicker', $context->id, 'mod_lesson', 'mediafile', 0, '/', $filename)) {
            $DB->set_field('lesson', 'mediafile', '/'.$file->get_filename(), array('id'=>$lesson->id));
        }
    }

    lesson_process_post_save($data);

    lesson_grade_item_update($data);

    return $lesson->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $lesson Lesson post data from the form
 * @return boolean
 **/
function lesson_update_instance($data, $mform) {
    global $DB;

    $data->id = $data->instance;
    $cmid = $data->coursemodule;

    lesson_process_pre_save($data);

    unset($data->mediafile);
    $DB->update_record("lesson", $data);

    $context = get_context_instance(CONTEXT_MODULE, $cmid);
    if ($filename = $mform->get_new_filename('mediafilepicker')) {
        if ($file = $mform->save_stored_file('mediafilepicker', $context->id, 'mod_lesson', 'mediafile', 0, '/', $filename, true)) {
            $DB->set_field('lesson', 'mediafile', '/'.$file->get_filename(), array('id'=>$data->id));
        } else {
            $DB->set_field('lesson', 'mediafile', '', array('id'=>$data->id));
        }
    } else {
        $DB->set_field('lesson', 'mediafile', '', array('id'=>$data->id));
    }

    lesson_process_post_save($data);

    // update grade item definition
    lesson_grade_item_update($data);

    // update grades - TODO: do it only when grading style changes
    lesson_update_grades($data, 0, false);

    return true;
}


/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id
 * @return bool
 */
function lesson_delete_instance($id) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/mod/lesson/locallib.php');

    $lesson = $DB->get_record("lesson", array("id"=>$id), '*', MUST_EXIST);
    $lesson = new lesson($lesson);
    return $lesson->delete();
}

/**
 * Given a course object, this function will clean up anything that
 * would be leftover after all the instances were deleted
 *
 * @global object
 * @param object $course an object representing the course that is being deleted
 * @param boolean $feedback to specify if the process must output a summary of its work
 * @return boolean
 */
function lesson_delete_course($course, $feedback=true) {
    return true;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $lesson
 * @return object
 */
function lesson_user_outline($course, $user, $mod, $lesson) {
    global $CFG;

    require_once("$CFG->libdir/gradelib.php");
    $grades = grade_get_grades($course->id, 'mod', 'lesson', $lesson->id, $user->id);

    $return = new stdClass();
    if (empty($grades->items[0]->grades)) {
        $return->info = get_string("no")." ".get_string("attempts", "lesson");
    } else {
        $grade = reset($grades->items[0]->grades);
        $return->info = get_string("grade") . ': ' . $grade->str_long_grade;
        $return->time = $grade->dategraded;
        $return->info = get_string("no")." ".get_string("attempts", "lesson");
    }
    return $return;
}

/**
 * Print a detailed representation of what a  user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $lesson
 * @return bool
 */
function lesson_user_complete($course, $user, $mod, $lesson) {
    global $DB, $OUTPUT, $CFG;

    require_once("$CFG->libdir/gradelib.php");

    $grades = grade_get_grades($course->id, 'mod', 'lesson', $lesson->id, $user->id);
    if (!empty($grades->items[0]->grades)) {
        $grade = reset($grades->items[0]->grades);
        echo $OUTPUT->container(get_string('grade').': '.$grade->str_long_grade);
        if ($grade->str_feedback) {
            echo $OUTPUT->container(get_string('feedback').': '.$grade->str_feedback);
        }
    }

    $params = array ("lessonid" => $lesson->id, "userid" => $user->id);
    if ($attempts = $DB->get_records_select("lesson_attempts", "lessonid = :lessonid AND userid = :userid", $params,
                "retry, timeseen")) {
        echo $OUTPUT->box_start();
        $table = new html_table();
        $table->head = array (get_string("attempt", "lesson"),  get_string("numberofpagesviewed", "lesson"),
            get_string("numberofcorrectanswers", "lesson"), get_string("time"));
        $table->width = "100%";
        $table->align = array ("center", "center", "center", "center");
        $table->size = array ("*", "*", "*", "*");
        $table->cellpadding = 2;
        $table->cellspacing = 0;

        $retry = 0;
        $npages = 0;
        $ncorrect = 0;

        foreach ($attempts as $attempt) {
            if ($attempt->retry == $retry) {
                $npages++;
                if ($attempt->correct) {
                    $ncorrect++;
                }
                $timeseen = $attempt->timeseen;
            } else {
                $table->data[] = array($retry + 1, $npages, $ncorrect, userdate($timeseen));
                $retry++;
                $npages = 1;
                if ($attempt->correct) {
                    $ncorrect = 1;
                } else {
                    $ncorrect = 0;
                }
            }
        }
        if ($npages) {
                $table->data[] = array($retry + 1, $npages, $ncorrect, userdate($timeseen));
        }
        echo html_writer::table($table);
        echo $OUTPUT->box_end();
    }

    return true;
}

/**
 * Prints lesson summaries on MyMoodle Page
 *
 * Prints lesson name, due date and attempt information on
 * lessons that have a deadline that has not already passed
 * and it is available for taking.
 *
 * @global object
 * @global stdClass
 * @global object
 * @uses CONTEXT_MODULE
 * @param array $courses An array of course objects to get lesson instances from
 * @param array $htmlarray Store overview output array( course ID => 'lesson' => HTML output )
 * @return void
 */
function lesson_print_overview($courses, &$htmlarray) {
    global $USER, $CFG, $DB, $OUTPUT;

    if (!$lessons = get_all_instances_in_courses('lesson', $courses)) {
        return;
    }

/// Get Necessary Strings
    $strlesson       = get_string('modulename', 'lesson');
    $strnotattempted = get_string('nolessonattempts', 'lesson');
    $strattempted    = get_string('lessonattempted', 'lesson');

    $now = time();
    foreach ($lessons as $lesson) {
        if ($lesson->deadline != 0                                         // The lesson has a deadline
            and $lesson->deadline >= $now                                  // And it is before the deadline has been met
            and ($lesson->available == 0 or $lesson->available <= $now)) { // And the lesson is available

            // Lesson name
            if (!$lesson->visible) {
                $class = ' class="dimmed"';
            } else {
                $class = '';
            }
            $str = $OUTPUT->box("$strlesson: <a$class href=\"$CFG->wwwroot/mod/lesson/view.php?id=$lesson->coursemodule\">".
                             format_string($lesson->name).'</a>', 'name');

            // Deadline
            $str .= $OUTPUT->box(get_string('lessoncloseson', 'lesson', userdate($lesson->deadline)), 'info');

            // Attempt information
            if (has_capability('mod/lesson:manage', get_context_instance(CONTEXT_MODULE, $lesson->coursemodule))) {
                // Number of user attempts
                $attempts = $DB->count_records('lesson_attempts', array('lessonid'=>$lesson->id));
                $str     .= $OUTPUT->box(get_string('xattempts', 'lesson', $attempts), 'info');
            } else {
                // Determine if the user has attempted the lesson or not
                if ($DB->count_records('lesson_attempts', array('lessonid'=>$lesson->id, 'userid'=>$USER->id))) {
                    $str .= $OUTPUT->box($strattempted, 'info');
                } else {
                    $str .= $OUTPUT->box($strnotattempted, 'info');
                }
            }
            $str = $OUTPUT->box($str, 'lesson overview');

            if (empty($htmlarray[$lesson->course]['lesson'])) {
                $htmlarray[$lesson->course]['lesson'] = $str;
            } else {
                $htmlarray[$lesson->course]['lesson'] .= $str;
            }
        }
    }
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 * @global stdClass
 * @return bool true
 */
function lesson_cron () {
    global $CFG;

    return true;
}

/**
 * Return grade for given user or all users.
 *
 * @global stdClass
 * @global object
 * @param int $lessonid id of lesson
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function lesson_get_user_grades($lesson, $userid=0) {
    global $CFG, $DB;

    $params = array("lessonid" => $lesson->id,"lessonid2" => $lesson->id);

    if (isset($userid)) {
        $params["userid"] = $userid;
        $params["userid2"] = $userid;
        $user = "AND u.id = :userid";
        $fuser = "AND uu.id = :userid2";
    }
    else {
        $user="";
        $fuser="";
    }

    if ($lesson->retake) {
        if ($lesson->usemaxgrade) {
            $sql = "SELECT u.id, u.id AS userid, MAX(g.grade) AS rawgrade
                      FROM {user} u, {lesson_grades} g
                     WHERE u.id = g.userid AND g.lessonid = :lessonid
                           $user
                  GROUP BY u.id";
        } else {
            $sql = "SELECT u.id, u.id AS userid, AVG(g.grade) AS rawgrade
                      FROM {user} u, {lesson_grades} g
                     WHERE u.id = g.userid AND g.lessonid = :lessonid
                           $user
                  GROUP BY u.id";
        }
        unset($params['lessonid2']);
        unset($params['userid2']);
    } else {
        // use only first attempts (with lowest id in lesson_grades table)
        $firstonly = "SELECT uu.id AS userid, MIN(gg.id) AS firstcompleted
                        FROM {user} uu, {lesson_grades} gg
                       WHERE uu.id = gg.userid AND gg.lessonid = :lessonid2
                             $fuser
                       GROUP BY uu.id";

        $sql = "SELECT u.id, u.id AS userid, g.grade AS rawgrade
                  FROM {user} u, {lesson_grades} g, ($firstonly) f
                 WHERE u.id = g.userid AND g.lessonid = :lessonid
                       AND g.id = f.firstcompleted AND g.userid=f.userid
                       $user";
    }

    return $DB->get_records_sql($sql, $params);
}

/**
 * Update grades in central gradebook
 *
 * @global stdclass
 * @global object
 * @param object $lesson
 * @param int $userid specific user only, 0 means all
 * @param bool $nullifnone
 */
function lesson_update_grades($lesson, $userid=0, $nullifnone=true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    if ($lesson->grade == 0) {
        lesson_grade_item_update($lesson);

    } else if ($grades = lesson_get_user_grades($lesson, $userid)) {
        lesson_grade_item_update($lesson, $grades);

    } else if ($userid and $nullifnone) {
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = NULL;
        lesson_grade_item_update($lesson, $grade);

    } else {
        lesson_grade_item_update($lesson);
    }
}

/**
 * Update all grades in gradebook.
 *
 * @global object
 */
function lesson_upgrade_grades() {
    global $DB;

    $sql = "SELECT COUNT('x')
              FROM {lesson} l, {course_modules} cm, {modules} m
             WHERE m.name='lesson' AND m.id=cm.module AND cm.instance=l.id";
    $count = $DB->count_records_sql($sql);

    $sql = "SELECT l.*, cm.idnumber AS cmidnumber, l.course AS courseid
              FROM {lesson} l, {course_modules} cm, {modules} m
             WHERE m.name='lesson' AND m.id=cm.module AND cm.instance=l.id";
    $rs = $DB->get_recordset_sql($sql);
    if ($rs->valid()) {
        $pbar = new progress_bar('lessonupgradegrades', 500, true);
        $i=0;
        foreach ($rs as $lesson) {
            $i++;
            upgrade_set_timeout(60*5); // set up timeout, may also abort execution
            lesson_update_grades($lesson, 0, false);
            $pbar->update($i, $count, "Updating Lesson grades ($i/$count).");
        }
    }
    $rs->close();
}

/**
 * Create grade item for given lesson
 *
 * @global stdClass
 * @uses GRADE_TYPE_VALUE
 * @uses GRADE_TYPE_NONE
 * @param object $lesson object with extra cmidnumber
 * @param array|object $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function lesson_grade_item_update($lesson, $grades=NULL) {
    global $CFG;
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }

    if (array_key_exists('cmidnumber', $lesson)) { //it may not be always present
        $params = array('itemname'=>$lesson->name, 'idnumber'=>$lesson->cmidnumber);
    } else {
        $params = array('itemname'=>$lesson->name);
    }

    if ($lesson->grade > 0) {
        $params['gradetype']  = GRADE_TYPE_VALUE;
        $params['grademax']   = $lesson->grade;
        $params['grademin']   = 0;
    } else if ($lesson->grade < 0) {
        $params['gradetype']  = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$lesson->grade;
    } else {
        $params['gradetype']  = GRADE_TYPE_NONE;
    }

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    } else if (!empty($grades)) {
        // Need to calculate raw grade (Note: $grades has many forms)
        if (is_object($grades)) {
            $grades = array($grades->userid => $grades);
        } else if (array_key_exists('userid', $grades)) {
            $grades = array($grades['userid'] => $grades);
        }
        foreach ($grades as $key => $grade) {
            if (!is_array($grade)) {
                $grades[$key] = $grade = (array) $grade;
            }
            //check raw grade isnt null otherwise we erroneously insert a grade of 0
            if ($grade['rawgrade'] !== null) {
                $grades[$key]['rawgrade'] = ($grade['rawgrade'] * $lesson->grade / 100);
            } else {
                //setting rawgrade to null just in case user is deleting a grade
                $grades[$key]['rawgrade'] = null;
            }
        }
    }

    return grade_update('mod/lesson', $lesson->course, 'mod', 'lesson', $lesson->id, 0, $grades, $params);
}

/**
 * Delete grade item for given lesson
 *
 * @global stdClass
 * @param object $lesson object
 * @return object lesson
 */
function lesson_grade_item_delete($lesson) {
    global $CFG;

}


/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of lesson. Must include every user involved
 * in the instance, independent of his role (student, teacher, admin...)
 *
 * @global stdClass
 * @global object
 * @param int $lessonid
 * @return array
 */
function lesson_get_participants($lessonid) {
    global $CFG, $DB;

    //Get students
    $params = array ("lessonid" => $lessonid);
    $students = $DB->get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {user} u,
                                      {lesson_attempts} a
                                 WHERE a.lessonid = :lessonid and
                                       u.id = a.userid", $params);

    //Return students array (it contains an array of unique users)
    return ($students);
}

/**
 * @return array
 */
function lesson_get_view_actions() {
    return array('view','view all');
}

/**
 * @return array
 */
function lesson_get_post_actions() {
    return array('end','start');
}

/**
 * Runs any processes that must run before
 * a lesson insert/update
 *
 * @global object
 * @param object $lesson Lesson form data
 * @return void
 **/
function lesson_process_pre_save(&$lesson) {
    global $DB;

    $lesson->timemodified = time();

    if (empty($lesson->timed)) {
        $lesson->timed = 0;
    }
    if (empty($lesson->timespent) or !is_numeric($lesson->timespent) or $lesson->timespent < 0) {
        $lesson->timespent = 0;
    }
    if (!isset($lesson->completed)) {
        $lesson->completed = 0;
    }
    if (empty($lesson->gradebetterthan) or !is_numeric($lesson->gradebetterthan) or $lesson->gradebetterthan < 0) {
        $lesson->gradebetterthan = 0;
    } else if ($lesson->gradebetterthan > 100) {
        $lesson->gradebetterthan = 100;
    }

    if (empty($lesson->width)) {
        $lesson->width = 640;
    }
    if (empty($lesson->height)) {
        $lesson->height = 480;
    }
    if (empty($lesson->bgcolor)) {
        $lesson->bgcolor = '#FFFFFF';
    }

    // Conditions for dependency
    $conditions = new stdClass;
    $conditions->timespent = $lesson->timespent;
    $conditions->completed = $lesson->completed;
    $conditions->gradebetterthan = $lesson->gradebetterthan;
    $lesson->conditions = serialize($conditions);
    unset($lesson->timespent);
    unset($lesson->completed);
    unset($lesson->gradebetterthan);

    if (empty($lesson->password)) {
        unset($lesson->password);
    }
}

/**
 * Runs any processes that must be run
 * after a lesson insert/update
 *
 * @global object
 * @param object $lesson Lesson form data
 * @return void
 **/
function lesson_process_post_save(&$lesson) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/calendar/lib.php');
    require_once($CFG->dirroot . '/mod/lesson/locallib.php');

    if ($events = $DB->get_records('event', array('modulename'=>'lesson', 'instance'=>$lesson->id))) {
        foreach($events as $event) {
            $event = calendar_event::load($event->id);
            $event->delete();
        }
    }

    $event = new stdClass;
    $event->description = $lesson->name;
    $event->courseid    = $lesson->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'lesson';
    $event->instance    = $lesson->id;
    $event->eventtype   = 'open';
    $event->timestart   = $lesson->available;

    $event->visible     = instance_is_visible('lesson', $lesson);

    $event->timeduration = ($lesson->deadline - $lesson->available);

    if ($lesson->deadline and $lesson->available and $event->timeduration <= LESSON_MAX_EVENT_LENGTH) {
        // Single event for the whole lesson.
        $event->name = $lesson->name;
        calendar_event::create(clone($event));
    } else {
        // Separate start and end events.
        $event->timeduration  = 0;
        if ($lesson->available) {
            $event->name = $lesson->name.' ('.get_string('lessonopens', 'lesson').')';
            calendar_event::create(clone($event));
        } else if ($lesson->deadline) {
            $event->name      = $lesson->name.' ('.get_string('lessoncloses', 'lesson').')';
            $event->timestart = $lesson->deadline;
            $event->eventtype = 'close';
            calendar_event::create(clone($event));
        }
    }
}


/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the lesson.
 *
 * @param $mform form passed by reference
 */
function lesson_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'lessonheader', get_string('modulenameplural', 'lesson'));
    $mform->addElement('advcheckbox', 'reset_lesson', get_string('deleteallattempts','lesson'));
}

/**
 * Course reset form defaults.
 * @param object $course
 * @return array
 */
function lesson_reset_course_form_defaults($course) {
    return array('reset_lesson'=>1);
}

/**
 * Removes all grades from gradebook
 *
 * @global stdClass
 * @global object
 * @param int $courseid
 * @param string optional type
 */
function lesson_reset_gradebook($courseid, $type='') {
    global $CFG, $DB;

    $sql = "SELECT l.*, cm.idnumber as cmidnumber, l.course as courseid
              FROM {lesson} l, {course_modules} cm, {modules} m
             WHERE m.name='lesson' AND m.id=cm.module AND cm.instance=l.id AND l.course=:course";
    $params = array ("course" => $courseid);
    if ($lessons = $DB->get_records_sql($sql,$params)) {
        foreach ($lessons as $lesson) {
            lesson_grade_item_update($lesson, 'reset');
        }
    }
}

/**
 * Actual implementation of the reset course functionality, delete all the
 * lesson attempts for course $data->courseid.
 *
 * @global stdClass
 * @global object
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function lesson_reset_userdata($data) {
    global $CFG, $DB;

    $componentstr = get_string('modulenameplural', 'lesson');
    $status = array();

    if (!empty($data->reset_lesson)) {
        $lessonssql = "SELECT l.id
                         FROM {lesson} l
                        WHERE l.course=:course";

        $params = array ("course" => $data->courseid);
        $DB->delete_records_select('lesson_timer', "lessonid IN ($lessonssql)", $params);
        $DB->delete_records_select('lesson_high_scores', "lessonid IN ($lessonssql)", $params);
        $DB->delete_records_select('lesson_grades', "lessonid IN ($lessonssql)", $params);
        $DB->delete_records_select('lesson_attempts', "lessonid IN ($lessonssql)", $params);

        // remove all grades from gradebook
        if (empty($data->reset_gradebook_grades)) {
            lesson_reset_gradebook($data->courseid);
        }

        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallattempts', 'lesson'), 'error'=>false);
    }

    /// updating dates - shift may be negative too
    if ($data->timeshift) {
        shift_course_mod_dates('lesson', array('available', 'deadline'), $data->timeshift, $data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('datechanged'), 'error'=>false);
    }

    return $status;
}

/**
 * Returns all other caps used in module
 * @return array
 */
function lesson_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function lesson_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        default: return null;
    }
}

/**
 * This function extends the global navigation for the site.
 * It is important to note that you should not rely on PAGE objects within this
 * body of code as there is no guarantee that during an AJAX request they are
 * available
 *
 * @param navigation_node $navigation The lesson node within the global navigation
 * @param stdClass $course The course object returned from the DB
 * @param stdClass $module The module object returned from the DB
 * @param stdClass $cm The course module instance returned from the DB
 */
function lesson_extend_navigation($navigation, $course, $module, $cm) {
    /**
     * This is currently just a stub so  that it can be easily expanded upon.
     * When expanding just remove this comment and the line below and then add
     * you content.
     */
    $navigation->nodetype = navigation_node::NODETYPE_LEAF;
}

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param settings_navigation $settings
 * @param navigation_node $lessonnode
 */
function lesson_extend_settings_navigation($settings, $lessonnode) {
    global $PAGE, $DB;

    $canedit = has_capability('mod/lesson:edit', $PAGE->cm->context);

    $url = new moodle_url('/mod/lesson/view.php', array('id'=>$PAGE->cm->id));
    $lessonnode->add(get_string('preview', 'lesson'), $url);

    if ($canedit) {
        $url = new moodle_url('/mod/lesson/edit.php', array('id'=>$PAGE->cm->id));
        $lessonnode->add(get_string('edit', 'lesson'), $url);
    }

    if (has_capability('mod/lesson:manage', $PAGE->cm->context)) {
        $reportsnode = $lessonnode->add(get_string('reports', 'lesson'));
        $url = new moodle_url('/mod/lesson/report.php', array('id'=>$PAGE->cm->id, 'action'=>'reportoverview'));
        $reportsnode->add(get_string('overview', 'lesson'), $url);
        $url = new moodle_url('/mod/lesson/report.php', array('id'=>$PAGE->cm->id, 'action'=>'reportdetail'));
        $reportsnode->add(get_string('detailedstats', 'lesson'), $url);
    }

    if ($canedit) {
        $url = new moodle_url('/mod/lesson/essay.php', array('id'=>$PAGE->cm->id));
        $lessonnode->add(get_string('manualgrading', 'lesson'), $url);
    }

    if ($PAGE->activityrecord->highscores) {
        $url = new moodle_url('/mod/lesson/highscores.php', array('id'=>$PAGE->cm->id));
        $lessonnode->add(get_string('highscores', 'lesson'), $url);
    }
}

/**
 * Get list of available import or export formats
 *
 * Copied and modified from lib/questionlib.php
 *
 * @param string $type 'import' if import list, otherwise export list assumed
 * @return array sorted list of import/export formats available
 */
function lesson_get_import_export_formats($type) {
    global $CFG;
    $fileformats = get_plugin_list("qformat");

    $fileformatname=array();
    foreach ($fileformats as $fileformat=>$fdir) {
        $format_file = "$fdir/format.php";
        if (file_exists($format_file) ) {
            require_once($format_file);
        } else {
            continue;
        }
        $classname = "qformat_$fileformat";
        $format_class = new $classname();
        if ($type=='import') {
            $provided = $format_class->provide_import();
        } else {
            $provided = $format_class->provide_export();
        }
        if ($provided) {
            //TODO: do NOT rely on [[]] any more!!
            $formatname = get_string($fileformat, 'quiz');
            if ($formatname == "[[$fileformat]]") {
                $formatname = get_string($fileformat, 'qformat_'.$fileformat);
                if ($formatname == "[[$fileformat]]") {
                    $formatname = $fileformat;  // Just use the raw folder name
                }
            }
            $fileformatnames[$fileformat] = $formatname;
        }
    }
    natcasesort($fileformatnames);

    return $fileformatnames;
}

/**
 * Serves the lesson attachments. Implements needed access control ;-)
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - justsend the file
 */
function lesson_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    $fileareas = lesson_get_file_areas();
    if (!array_key_exists($filearea, $fileareas)) {
        return false;
    }

    if (!$lesson = $DB->get_record('lesson', array('id'=>$cm->instance))) {
        return false;
    }

    require_course_login($course, true, $cm);

    if ($filearea === 'page_contents') {
        $pageid = (int)array_shift($args);
        if (!$page = $DB->get_record('lesson_pages', array('id'=>$pageid))) {
            return false;
        }
        $fullpath = "/$context->id/mod_lesson/$filearea/$pageid/".implode('/', $args);

    } else if ($filearea === 'mediafile') {
        array_shift($args); // ignore itemid - caching only
        $fullpath = "/$context->id/mod_lesson/$filearea/0/".implode('/', $args);

    } else {
        return false;
    }

    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // finally send the file
    send_stored_file($file, 0, 0, $forcedownload); // download MUST be forced - security!
}

/**
 * Returns an array of file areas
 * @return array
 */
function lesson_get_file_areas() {
    $areas = array();
    $areas['page_contents'] = 'Page contents'; //TODO: localize!!!!
    $areas['mediafile'] = 'Media file'; //TODO: localize!!!!

    return $areas;
}

/**
 * Returns a file_info_stored object for the file being requested here
 *
 * @global <type> $CFG
 * @param file_browse $browser
 * @param array $areas
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info_stored
 */
function lesson_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;
    if (has_capability('moodle/course:managefiles', $context)) {
        // no peaking here for students!!
        return null;
    }

    $fs = get_file_storage();
    $filepath = is_null($filepath) ? '/' : $filepath;
    $filename = is_null($filename) ? '.' : $filename;
    $urlbase = $CFG->wwwroot.'/pluginfile.php';
    if (!$storedfile = $fs->get_file($context->id, 'mod_lesson', $filearea, $itemid, $filepath, $filename)) {
        return null;
    }
    return new file_info_stored($browser, $context, $storedfile, $urlbase, $filearea, $itemid, true, true, false);
}
