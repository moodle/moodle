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
 * @package   lesson
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

/** Include required libraries */
require_once($CFG->libdir.'/eventslib.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');

/** LESSON_MAX_EVENT_LENGTH = 432000 ; 5 days maximum */
define("LESSON_MAX_EVENT_LENGTH", "432000");

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
    global $SESSION, $DB;

    $cmid = $data->coursemodule;

    lesson_process_pre_save($data);

    unset($data->mediafile);
    $lessonid = $DB->insert_record("lesson", $data);
    $data->id = $lessonid;

    $context = get_context_instance(CONTEXT_MODULE, $cmid);
    $lesson = $DB->get_record('lesson', array('id'=>$lessonid), '*', MUST_EXIST);

    if ($filename = $mform->get_new_filename('mediafile')) {
        if ($file = $mform->save_stored_file('mediafile', $context->id, 'lesson_media_file', $lesson->id, '/', $filename)) {
            $DB->set_field('lesson', 'mediafile', $file->get_filename(), array('id'=>$lesson->id));
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
    if (!$result = $DB->update_record("lesson", $data)) {
        return false; // Awe man!
    }

    $context = get_context_instance(CONTEXT_MODULE, $cmid);
    if ($filename = $mform->get_new_filename('mediafile')) {
        if ($file = $mform->save_stored_file('mediafile', $context->id, 'lesson_media_file', $data->id, '/', $filename, true)) {
            $DB->set_field('lesson', 'mediafile', $file->get_filename(), array('id'=>$data->id));
        }
    }

    lesson_process_post_save($data);

    // update grade item definition
    lesson_grade_item_update($data);

    // update grades - TODO: do it only when grading style changes
    lesson_update_grades($data, 0, false);

    return $result;
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
    global $DB;
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
    global $DB;

    global $CFG;
    require_once("$CFG->libdir/gradelib.php");
    $grades = grade_get_grades($course->id, 'mod', 'lesson', $lesson->id, $user->id);

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
        echo $OUTPUT->table($table);
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
        $grade = new object();
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
    if ($rs = $DB->get_recordset_sql($sql)) {
        $pbar = new progress_bar('lessonupgradegrades', 500, true);
        $i=0;
        foreach ($rs as $lesson) {
            $i++;
            upgrade_set_timeout(60*5); // set up timeout, may also abort execution
            lesson_update_grades($lesson, 0, false);
            $pbar->update($i, $count, "Updating Lesson grades ($i/$count).");
        }
        $rs->close();
    }
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
            $grades[$key]['rawgrade'] = ($grade['rawgrade'] * $lesson->grade / 100);
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
 * in the instance, independient of his role (student, teacher, admin...)
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
    global $DB;

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
 * Actual implementation of the rest coures functionality, delete all the
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

        default: return null;
    }
}

/**
 * This function extends the global navigaiton for the site.
 * It is important to note that you should not rely on PAGE objects within this
 * body of code as there is no guarantee that during an AJAX request they are
 * available
 *
 * @param navigation_node $navigation The lesson node within the global navigation
 * @param stdClass $course The course object returned from the DB
 * @param stdClass $module The module object returned from the DB
 * @param stdClass $cm The course module isntance returned from the DB
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
 * @param navigation_node $settings
 * @param stdClass $module
 */
function lesson_extend_settings_navigation($settings, $module) {
    global $PAGE, $CFG, $USER, $OUTPUT;

    $lessonnavkey = $settings->add(get_string('lessonadministration', 'lesson'));
    $lessonnav = $settings->get($lessonnavkey);
    $lessonnav->forceopen = true;

    if (empty($PAGE->cm->context)) {
        $PAGE->cm->context = get_context_instance(CONTEXT_MODULE, $PAGE->cm->instance);
    }

    $canedit = has_capability('mod/lesson:edit', $PAGE->cm->context);

    $url = new moodle_url('/mod/lesson/view.php', array('id'=>$PAGE->cm->id));
    $key = $lessonnav->add(get_string('preview', 'lesson'), $url);

    if ($canedit) {
        $url = new moodle_url('/mod/lesson/edit.php', array('id'=>$PAGE->cm->id));
        $key = $lessonnav->add(get_string('edit', 'lesson'), $url);
    }

    if (has_capability('mod/lesson:manage', $PAGE->cm->context)) {
        $key = $lessonnav->add(get_string('reports', 'lesson'));
        $url = new moodle_url('/mod/lesson/report.php', array('id'=>$PAGE->cm->id, 'action'=>'reportoverview'));
        $lessonnav->get($key)->add(get_string('overview', 'lesson'), $url);
        $url = new moodle_url('/mod/lesson/report.php', array('id'=>$PAGE->cm->id, 'action'=>'reportdetail'));
        $lessonnav->get($key)->add(get_string('detailedstats', 'lesson'), $url);
    }

    if ($canedit) {
        $url = new moodle_url('/mod/lesson/essay.php', array('id'=>$PAGE->cm->id));
        $lessonnav->add(get_string('manualgrading', 'lesson'), $url);
    }

    if ($lesson->highscores) {
        $url = new moodle_url('/mod/lesson/highscores.php', array('id'=>$PAGE->cm->id));
        $lessonnav->add(get_string('highscores', 'lesson'), $url);
    }

    if (has_capability('moodle/course:manageactivities', $PAGE->cm->context)) {
        $url = new moodle_url('/course/mod.php', array('update' => $PAGE->cm->id, 'return' => true, 'sesskey' => sesskey()));
        $lessonnav->add(get_string('updatethis', '', get_string('modulename', 'lesson')), $url);
    }

    if (count($lessonnav->children)<1) {
        $settings->remove_child($lessonnavkey);
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
 * @param object $cminfo
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - justsend the file
 */
function lesson_pluginfile($course, $cminfo, $context, $filearea, $args, $forcedownload) {
    global $CFG, $DB;

    if (!$cminfo->uservisible) {
        return false;
    }

    $fileareas = lesson_get_file_areas();
    if (!array_key_exists($filearea, $fileareas)) {
        return false;
    }

    if (!$cm = get_coursemodule_from_instance('lesson', $cminfo->instance, $course->id)) {
        return false;
    }

    if (!$lesson = $DB->get_record('lesson', array('id'=>$cminfo->instance))) {
        return false;
    }

    require_course_login($course, true, $cm);

    if ($filearea === 'lesson_page_content') {
        $pageid = (int)array_shift($args);
        if (!$page = $DB->get_record('lesson_pages', array('id'=>$pageid))) {
            return false;
        }
        $fullpath = $context->id.$filearea.$pageid.'/'.implode('/', $args);
        $forcedownload = true;
    } else {
        $fullpath = $context->id.$filearea.implode('/', $args);
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
    return array('lesson_page_contents'=>'lesson_page_contents', 'lesson_media_file'=>'lesson_media_file');
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
    $fs = get_file_storage();
    $filepath = is_null($filepath) ? '/' : $filepath;
    $filename = is_null($filename) ? '.' : $filename;
    $urlbase = $CFG->wwwroot.'/pluginfile.php';
    if (!$storedfile = $fs->get_file($context->id, $filearea, $itemid, $filepath, $filename)) {
        return null;
    }
    return new file_info_stored($browser, $context, $storedfile, $urlbase, $filearea, $itemid, true, true, false);
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

    // get the media file from file pool
    $browser = get_file_browser();
    $file_info  = $browser->get_file_info($context, 'lesson_media_file', $lesson->id, '/', $lesson->mediafile);
    $url = $file_info->get_url();
    $title = $lesson->mediafile;

    $clicktoopen = $OUTPUT->link(new moodle_url($url), get_string('download'));

    $mimetype = resourcelib_guess_url_mimetype($url);

    // find the correct type and print it out
    if (in_array($mimetype, array('image/gif','image/jpeg','image/png'))) {  // It's an image
        $code = resourcelib_embed_image($url, $title);

    } else if ($mimetype == 'audio/mp3') {
        // MP3 audio file
        $code = resourcelib_embed_mp3($url, $title, $clicktoopen);

    } else if ($mimetype == 'video/x-flv') {
        // Flash video file
        $code = resourcelib_embed_flashvideo($url, $title, $clicktoopen);

    } else if ($mimetype == 'application/x-shockwave-flash') {
        // Flash file
        $code = resourcelib_embed_flash($url, $title, $clicktoopen);

    } else if (substr($mimetype, 0, 10) == 'video/x-ms') {
        // Windows Media Player file
        $code = resourcelib_embed_mediaplayer($url, $title, $clicktoopen);

    } else if ($mimetype == 'video/quicktime') {
        // Quicktime file
        $code = resourcelib_embed_quicktime($url, $title, $clicktoopen);

    } else if ($mimetype == 'video/mpeg') {
        // Mpeg file
        $code = resourcelib_embed_mpeg($url, $title, $clicktoopen);

    } else if ($mimetype == 'audio/x-pn-realaudio-plugin') {
        // RealMedia file
        $code = resourcelib_embed_real($url, $title, $clicktoopen);

    } else {
        // anything else - just try object tag enlarged as much as possible
        $code = resourcelib_embed_general($url, $title, $clicktoopen, $mimetype);
        $PAGE->requires->yui2_lib('dom');
        $PAGE->requires->js('/mod/url/functions.js');
        $PAGE->requires->js_function_call('imscp_setup_object', null, true);
    }

    return $code;
}

/**
 * Abstract class to provide a core functions to the all lesson classes
 *
 * This class should be abstracted by ALL classes with the lesson module to ensure
 * that all classes within this module can be interacted with in the same way.
 *
 * This class provides the user with a basic properties array that can be fetched
 * or set via magic methods, or alternativily by defining methods get_blah() or
 * set_blah() within the extending object.
 *
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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

    /**
     * If overriden should create a new instance, save it in the DB and return it
     */
    public static function create() {}
    /**
     * If overriden should load an instance from the DB and return it
     */
    public static function load() {}
    /**
     * Fetches all of the properties of the object
     * @return stdClass
     */
    public function properties() {
        return $this->properties;
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
 * @property int $dependency ID of another lesson this lesson is dependant on
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
 * @property int $displayleft Display a left meun
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
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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

        $DB->delete_records("lesson", array("id"=>$this->properties->id));;
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

        grade_update('mod/lesson', $this->properties->course, 'mod', 'lesson', $this->properties->id, 0, NULL, array('deleted'=>1));
        return true;
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
        return $DB->get_records('lesson_attempts', $params, 'timeseen DESC');
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
     * Gets the next page to display after the one that is provided.
     * @param int $nextpageid
     * @return bool
     */
    public function get_next_page($nextpageid) {
        global $USER;
        $allpages = $this->load_all_pages();
        if ($this->properties->nextpagedefault) {
            // in Flash Card mode...first get number of retakes
            shuffle($allpages);
            $found = false;
            if ($this->properties->nextpagedefault == LESSON_UNSEENPAGE) {
                foreach ($allpages as $nextpage) {
                    if (!$DB->count_records("lesson_attempts", array("pageid"=>$nextpage->id, "userid"=>$USER->id, "retry"=>$nretakes))) {
                        $found = true;
                        break;
                    }
                }
            } elseif ($this->properties->nextpagedefault == LESSON_UNANSWEREDPAGE) {
                foreach ($allpages as $nextpage) {
                    if (!$DB->count_records("lesson_attempts", array('pageid'=>$nextpage->id, 'userid'=>$USER->id, 'correct'=>1, 'retry'=>$nretakes))) {
                        $found = true;
                        break;
                    }
                }
            }
            if ($found) {
                if ($this->properties->maxpages) {
                    // check number of pages viewed (in the lesson)
                    $nretakes = $DB->count_records("lesson_grades", array("lessonid"=>$this->properties->id, "userid"=>$USER->id));
                    if ($DB->count_records("lesson_attempts", array("lessonid"=>$this->properties->id, "userid"=>$USER->id, "retry"=>$nretakes)) >= $this->properties->maxpages) {
                        return false;
                    }
                }
                return $nextpage;
            }
        }
        // In a normal lesson mode
        foreach ($allpages as $nextpage) {
            if ((int)$nextpage->id===(int)$nextpageid) {
                return $nextpage;
            }
        }
        return false;
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
        $USER->startlesson[$this->properties->id] = true;
        $startlesson = new stdClass;
        $startlesson->lessonid = $this->properties->id;
        $startlesson->userid = $USER->id;
        $startlesson->starttime = time();
        $startlesson->lessontime = time();
        $DB->insert_record('lesson_timer', $startlesson);
        if ($this->properties->timed) {
            $this->add_message(get_string('maxtimewarning', 'lesson', $this->properties->maxtime), 'center');
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
    public function update_timer($restart=false, $continue=false) {
        global $USER, $DB;
        // clock code
        // get time information for this user
        if (!$timer = $DB->get_records('lesson_timer', array ("lessonid" => $this->properties->id, "userid" => $USER->id), 'starttime DESC', '*', 0, 1)) {
            print_error('cannotfindtimer', 'lesson');
        } else {
            $timer = current($timer); // this will get the latest start time record
        }

        if ($restart) {
            if ($continue) {
                // continue a previous test, need to update the clock  (think this option is disabled atm)
                $timer->starttime = time() - ($timer->lessontime - $timer->starttime);
            } else {
                // starting over, so reset the clock
                $timer->starttime = time();
            }
        }

        $timer->lessontime = time();
        $DB->update_record('lesson_timer', $timer);
        return $timer;
    }

    /**
     * Updates the timer to the current time then stops it by unsetting the user var
     * @return bool Returns true
     */
    public function stop_timer() {
        global $USER, $DB;
        unset($USER->startlesson[$this->properties->id]);
        return $this->update_timer(false, false);
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
     * @return html_link|false
     */
    public function link_for_activitylink() {
        global $DB;
        $module = $DB->get_record('course_modules', array('id' => $this->properties->activitylink));
        if ($module) {
            $modname = $DB->get_field('modules', 'name', array('id' => $module->module));
            if ($modname) {
                $instancename = $DB->get_field($modname, 'name', array('id' => $module->instance));
                if ($instancename) {
                    $link = html_link::make(new moodle_url('/mod/'.$modname.'/view.php', array('id'=>$this->properties->activitylink)), get_string('returnto', 'lesson', get_string('activitylinkname', 'lesson', $instancename)));
                    $link->set_classes(array('centerpadded','lessonbutton','standardbutton'));
                    return $link;
                }
            }
        }
        return false;
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
     * Determins if a jumpto value is correct or not.
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
        $timeleft = $starttime + $this->maxtime * 60 - time();
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
     * and the page selected will be inbetween a cluster page and end of cluter or end of lesson
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
            if ($cluster->type !== lesson_page::TYPE_QUESTION) {
                unset($clusterpages[$key]);
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
                while ($clusterendid != 0) { // this condition should not be satisfied... should be a cluster page
                    if ($lessonpages[$clusterendid]->qtype == LESSON_PAGE_CLUSTER) {
                        break;
                    }
                    $clusterendid = $lessonpages[$clusterendid]->prevpageid;
                }
                $exitjump = $DB->get_field("lesson_answers", "jumpto", array("pageid" => $clusterendid, "lessonid" => $this->properties->id));
                if ($exitjump == LESSON_NEXTPAGE) {
                    $exitjump = $lessonpages[$pageid]->nextpageid;
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
     * corrosponding to that of a type specified in $ends
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
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
     * the database and thoughout code. This maps back to the defines used in 1.x
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
        $editor = file_postupdate_standard_editor($editor, 'contents', array('noclean'=>true, 'maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$maxbytes), $context, 'lesson_page_contents', $editor->id);
        $DB->update_record("lesson_pages", $editor);

        if ($newpage->prevpageid > 0) {
            $DB->set_field("lesson_pages", "nextpageid", $newpage->id, array("id" => $newpage->prevpageid));
        }
        if ($newpage->nextpageid > 0) {
            $DB->set_field("lesson_pages", "prevpageid", $newpage->id, array("id" => $newpage->nextpageid));
        }

        $page = lesson_page::load($newpage, $lesson);
        $page->create_answers($properties);

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
        // first delete all the associated records...
        $DB->delete_records("lesson_attempts", array("pageid" => $this->properties->id));
        // ...now delete the answers...
        $DB->delete_records("lesson_answers", array("pageid" => $this->properties->id));
        // ..and the page itself
        $DB->delete_records("lesson_pages", array("id" => $this->properties->id));

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
                print_error('cannotfindanswer', 'lesson');
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
     * @param stdClass $context
     * @return stdClass Returns the result of the attempt
     */
    final public function record_attempt($context) {
        global $DB, $USER, $OUTPUT;

        /**
         * This should be overriden by each page type to actually check the response
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
                if (isset($USER->modattempts[$this->lesson->id])) {
                    $attempt->retry = $nretakes - 1; // they are going through on review, $nretakes will be too high
                }

                $DB->insert_record("lesson_attempts", $attempt);
                // "number of attempts remaining" message if $this->lesson->maxattempts > 1
                // displaying of message(s) is at the end of page for more ergonomic display
                if (!$result->correctanswer && ($result->newpageid == 0)) {
                    // wrong answer and student is stuck on this page - check how many attempts
                    // the student has had at this page/question
                    $nattempts = $DB->count_records("lesson_attempts", array("pageid"=>$this->properties->id, "userid"=>$USER->id),"retry", $nretakes);
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
                $nextpage = $this->lesson->get_next_page($this->properties->nextpageid);
                if ($nextpage === false) {
                    $result->newpageid = LESSON_EOL;
                } else {
                    $result->newpageid = $nextpage->id;
                }
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
                        $result->feedback = get_string("firstwrong", "lesson");
                    } else {
                        $result->feedback = get_string("secondpluswrong", "lesson");
                    }
                } else {
                    $class = 'response';
                    if ($result->correctanswer) {
                        $class .= ' correct'; //CSS over-ride this if they exist (!important)
                    } else if (!$result->isessayquestion) {
                        $class .= ' incorrect'; //CSS over-ride this if they exist (!important)
                    }
                    $options = new stdClass;
                    $options->noclean = true;
                    $options->para = true;
                    $result->feedback = $OUTPUT->box(format_text($this->properties->contents, FORMAT_MOODLE, $options), 'generalbox boxaligncenter');
                    $result->feedback .= '<em>'.get_string("youranswer", "lesson").'</em> : '.format_text($result->studentanswer, FORMAT_MOODLE, $options);
                    $result->feedback .= $OUTPUT->box(format_text($result->response, FORMAT_MOODLE, $options), $class);
                }
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
            if ($jumpto == 0) {
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
     * Construstor method
     * @param object $properties
     * @param lesson $lesson
     */
    public function __construct($properties, lesson $lesson) {
        parent::__construct($properties);
        $this->lesson = $lesson;
    }

    /**
     * Returns the score for the attempt
     * This may be overriden by page types that require manual grading
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
     * Updates a lesson page and its answers within the database
     *
     * @param object $properties
     * @return bool
     */
    public function update($properties,$context, $maxbytes) {
        global $DB;
        $answers  = $this->get_answers();
        $properties->id = $this->properties->id;
        $properties->lessonid = $this->lesson->id;
        if (empty($properties->qoption)) {
            $properties->qoption = '0';
        }
        $properties = file_postupdate_standard_editor($properties, 'contents', array('noclean'=>true, 'maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$maxbytes), $context, 'lesson_page_contents', $properties->id);
        $DB->update_record("lesson_pages", $properties);

        for ($i = 0; $i < $this->lesson->maxanswers; $i++) {
            if (!array_key_exists($i, $this->answers)) {
                $this->answers[$i] = new stdClass;
                $this->answers[$i]->lessonid = $this->lesson->id;
                $this->answers[$i]->pageid = $this->id;
                $this->answers[$i]->timecreated = $this->timecreated;
            }
            if (!empty($properties->answer[$i])) {
                $this->answers[$i]->answer = format_text($properties->answer[$i], FORMAT_PLAIN);
                if (isset($properties->response[$i])) {
                    $this->answers[$i]->response = format_text($properties->response[$i], FORMAT_PLAIN);
                }
                if (isset($properties->jumpto[$i])) {
                    $this->answers[$i]->jumpto = $properties->jumpto[$i];
                }
                if ($this->lesson->custom && isset($properties->score[$i])) {
                    $this->answers[$i]->score = $properties->score[$i];
                }
                if (!isset($this->answers[$i]->id)) {
                    $this->answers[$i]->id =  $DB->insert_record("lesson_answers", $this->answers[$i]);
                } else {
                    $DB->update_record("lesson_answers", $this->answers[$i]->properties());
                }

            } else {
                break;
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
        global $DB;
        // now add the answers
        $newanswer = new stdClass;
        $newanswer->lessonid = $this->lesson->id;
        $newanswer->pageid = $this->properties->id;
        $newanswer->timecreated = $this->properties->timecreated;

        $answers = array();

        for ($i = 0; $i < $this->lesson->maxanswers; $i++) {
            $answer = clone($newanswer);
            if (!empty($properties->answer[$i])) {
                $answer->answer = format_text($properties->answer[$i], FORMAT_PLAIN);
                if (isset($properties->response[$i])) {
                    $answer->response = format_text($properties->response[$i], FORMAT_PLAIN);
                }
                if (isset($properties->jumpto[$i])) {
                    $answer->jumpto = $properties->jumpto[$i];
                }
                if ($this->lesson->custom && isset($properties->score[$i])) {
                    $answer->score = $properties->score[$i];
                }
                $answer->id = $DB->insert_record("lesson_answers", $answer);
                $answers[$answer->id] = new lesson_page_answer($answer);
            } else {
                break;
            }
        }

        $this->answers = $answers;
        return $answers;
    }

    /**
     * This method MUST be overriden by all question page types, or page types that
     * wish to score a page.
     *
     * The structure of result should always be the same so it is a good idea when
     * overriding this method on a page type to call
     * <code>
     * $result = parent::check_answer();
     * </code>
     * before modifiying it as required.
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
            foreach ($this->answers as $answer) {
                $properties->{'answer['.$count.']'} = $answer->answer;
                $properties->{'response['.$count.']'} = $answer->response;
                $properties->{'jumpto['.$count.']'} = $answer->jumpto;
                $properties->{'score['.$count.']'} = $answer->score;
                $count++;
            }
        }
        return $properties;
    }

    /**
     * Returns an array of options to display whn choosing the jumpto for a page/answer
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
            $context = get_context_instance(CONTEXT_MODULE, $PAGE->cm->id);
            return file_rewrite_pluginfile_urls($this->properties->contents, 'pluginfile.php', $context->id, 'lesson_page_contents', $this->properties->id);
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
            $table->data[] = html_table_row::make($cells);
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
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
     * This function loads ALL pages that belong to the lesson.
     *
     * @param lesson $lesson
     * @return array An array of lesson_page_type_*
     */
    public function load_all_pages(lesson $lesson) {
        global $DB;
        if (!($pages =$DB->get_records('lesson_pages', array('lessonid'=>$lesson->id)))) {
            print_error('cannotfindpages', 'lesson');
        }
        foreach ($pages as $key=>$page) {
            $pagetype = get_class($this->types[$page->qtype]);
            $pages[$key] = new $pagetype($page, $lesson);
        }

        $orderedpages = array();
        $lastpageid = 0;

        while (true) {
            foreach ($pages as $page) {
                if ((int)$page->prevpageid === (int)$lastpageid) {
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

        return $orderedpages;
    }

    /**
     * Fetchs an mform that can be used to create/edit an page
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

        foreach ($this->types as $type) {
            if (($link = $type->add_page_link($previd)) instanceof html_link) {
                $links[] = $OUTPUT->link($link);
            }
        }

        return $links;
    }
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
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
     * Each page type can and should override this to add any custom elements to
     * the basic form that they want
     */
    public function custom_definition() {}

    /**
     * Sets the data for the form... but modifies if first for the editor then
     * calls the parent method
     *
     * @param stdClass $data An object containing properties to set
     * @param int $pageid
     */
    public final function set_data($data, $context=null, $pageid=null) {
        $data = file_prepare_standard_editor($data, 'contents', $this->editoroptions, $context, 'lesson_page_contents', $pageid);
        parent::set_data($data);
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

        $mform->addElement('header', 'qtypeheading', get_string('addaquestionpage', 'lesson', get_string($this->qtypestring, 'lesson')));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'pageid');
        $mform->setType('pageid', PARAM_INT);

        if ($this->standard === true) {
            $mform->addElement('hidden', 'qtype');
            $mform->setType('qtype', PARAM_TEXT);

            $mform->addElement('text', 'title', get_string("pagetitle", "lesson"), array('size'=>70));
            $mform->setType('title', PARAM_TEXT);
            $this->editoroptions = array('noclean'=>true, 'maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$this->_customdata['maxbytes']);
            $mform->addElement('editor', 'contents_editor', get_string("pagecontents", "lesson"), null, $this->editoroptions);
            $mform->setType('contents_editor', PARAM_CLEANHTML);
        }

        $this->custom_definition();

        if ($this->_customdata['edit'] === true) {
            $mform->addElement('hidden', 'edit', 1);
            $this->add_action_buttons(get_string('cancel'), get_string("savepage", "lesson"));
        } else {
            $this->add_action_buttons(get_string('cancel'), get_string("addaquestionpage", "lesson"));
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
        $this->_form->setHelpButton($name, array("jumpto", $title, "lesson"));
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
        if ($value !== null) {
            $this->_form->setDefault($name, $value);
        }
    }

    /**
     * Convenience function: Adds a textarea element
     *
     * @param string $name
     * @param int $count The count of the element to add
     * @param string|null $label
     */
    protected final function add_textarea($name, $count, $label) {
        $this->_form->addElement('textarea', $name.'['.$count.']', $label, array('rows'=>5, 'cols'=>70, 'width'=>630, 'height'=>300));
    }
    /**
     * Convenience function: Adds an answer textarea
     *
     * @param int $count The count of the element to add
     */
    protected final function add_answer($count) {
        $this->add_textarea('answer', $count, get_string('answer', 'lesson'));
    }
    /**
     * Convenience function: Adds an response textarea
     *
     * @param int $count The count of the element to add
     */
    protected final function add_response($count) {
        $this->add_textarea('response', $count, get_string('response', 'lesson'));
    }

    /**
     * A function that gets called upon init of this object by the calling script.
     *
     * This can be used to process an immediate action if required. Currently it
     * is only used in special cases by non-standard page types.
     *
     * @return bool
     */
    public function construction_override() {
        return true;
    }
}