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
 * Contains a class used for tracking whether activities have been completed
 * by students ('completion')
 *
 * Completion top-level options (admin setting enablecompletion)
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas   {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** The completion system is enabled in this site/course */
define('COMPLETION_ENABLED', 1);
/** The completion system is not enabled in this site/course */
define('COMPLETION_DISABLED', 0);

// Completion tracking options per-activity (course_modules/completion)

/** Completion tracking is disabled for this activity */
define('COMPLETION_TRACKING_NONE', 0);
/** Manual completion tracking (user ticks box) is enabled for this activity */
define('COMPLETION_TRACKING_MANUAL', 1);
/** Automatic completion tracking (system ticks box) is enabled for this activity */
define('COMPLETION_TRACKING_AUTOMATIC', 2);

// Completion state values (course_modules_completion/completionstate)

/** The user has not completed this activity. */
define('COMPLETION_INCOMPLETE', 0);
/** The user has completed this activity. It is not specified whether they have
 * passed or failed it. */
define('COMPLETION_COMPLETE', 1);
/** The user has completed this activity with a grade above the pass mark. */
define('COMPLETION_COMPLETE_PASS', 2);
/** The user has completed this activity but their grade is less than the pass mark */
define('COMPLETION_COMPLETE_FAIL', 3);

// Completion effect changes (used only in update_state)

/** The effect of this change to completion status is unknown. */
define('COMPLETION_UNKNOWN', -1);
/** The user's grade has changed, so their new state might be
 * COMPLETION_COMPLETE_PASS or COMPLETION_COMPLETE_FAIL. */
// TODO Is this useful?
define('COMPLETION_GRADECHANGE', -2);

// Whether view is required to create an activity (course_modules/completionview)

/** User must view this activity */
define('COMPLETION_VIEW_REQUIRED', 1);
/** User does not need to view this activity */
define('COMPLETION_VIEW_NOT_REQUIRED', 0);

// Completion viewed state (course_modules_completion/viewed)

/** User has viewed this activity */
define('COMPLETION_VIEWED', 1);
/** User has not viewed this activity */
define('COMPLETION_NOT_VIEWED', 0);

// Completion cacheing

/** Cache expiry time in seconds (10 minutes) */
define('COMPLETION_CACHE_EXPIRY', 10*60);

// Combining completion condition. This is also the value you should return
// if you don't have any applicable conditions.
/** Completion details should be ORed together and you should return false if
 none apply */
define('COMPLETION_OR', false);
/** Completion details should be ANDed together and you should return true if
 none apply */
define('COMPLETION_AND', true);

/**
 * Class represents completion information for a course.
 *
 * Does not contain any data, so you can safely construct it multiple times
 * without causing any problems.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class completion_info {
    /** @var object Passed during construction */
    private $course;

    /**
     * Constructs with course details.
     *
     * @param object $course Moodle course object. Must have at least ->id, ->enablecompletion
     */
    public function __construct($course) {
        $this->course = $course;
    }

    /**
     * Determines whether completion is enabled across entire site.
     *
     * Static function.
     *
     * @global object
     * @return int COMPLETION_ENABLED (true) if completion is enabled for the site,
     *     COMPLETION_DISABLED (false) if it's complete
     */
    public static function is_enabled_for_site() {
        global $CFG;
        return !empty($CFG->enablecompletion);
    }

    /**
     * Checks whether completion is enabled in a particular course and possibly
     * activity.
     *
     * @global object
     * @uses COMPLETION_DISABLED
     * @uses COMPLETION_ENABLED
     * @param object $cm Course-module object. If not specified, returns the course
     *   completion enable state.
     * @return mixed COMPLETION_ENABLED or COMPLETION_DISABLED (==0) in the case of
     *   site and course; COMPLETION_TRACKING_MANUAL, _AUTOMATIC or _NONE (==0)
     *   for a course-module.
     */
    public function is_enabled($cm=null) {
        global $CFG;

        // First check global completion
        if ($CFG->enablecompletion == COMPLETION_DISABLED) {
            return COMPLETION_DISABLED;
        }

        // Check course completion
        if ($this->course->enablecompletion == COMPLETION_DISABLED) {
            return COMPLETION_DISABLED;
        }

        // If there was no $cm and we got this far, then it's enabled
        if (!$cm) {
            return COMPLETION_ENABLED;
        }

        // Return course-module completion value
        return $cm->completion;
    }

    /**
     * Print the Your progress help icon if the completion tracking is enabled.
     * @global object
     * @return void
     */
    public function print_help_icon() {
        global $PAGE, $OUTPUT;
        if ($this->is_enabled() && !$PAGE->user_is_editing() && isloggedin() && !isguestuser()) {
            echo '<span id = "completionprogressid" class="completionprogress">'.get_string('yourprogress','completion').' ';
            echo $OUTPUT->old_help_icon('completionicons',get_string('completionicons','completion'),'completion');
            echo '</span>';
        }
    }
    // OU specific end
    /**
     * Updates (if necessary) the completion state of activity $cm for the given
     * user.
     *
     * For manual completion, this function is called when completion is toggled
     * with $possibleresult set to the target state.
     *
     * For automatic completion, this function should be called every time a module
     * does something which might influence a user's completion state. For example,
     * if a forum provides options for marking itself 'completed' once a user makes
     * N posts, this function should be called every time a user makes a new post.
     * [After the post has been saved to the database]. When calling, you do not
     * need to pass in the new completion state. Instead this function carries out
     * completion calculation by checking grades and viewed state itself, and
     * calling the involved module via modulename_get_completion_state() to check
     * module-specific conditions.
     *
     * @global object
     * @global object
     * @uses COMPLETION_COMPLETE
     * @uses COMPLETION_INCOMPLETE
     * @uses COMPLETION_COMPLETE_PASS
     * @uses COMPLETION_COMPLETE_FAIL
     * @uses COMPLETION_TRACKING_MANUAL
     * @param object $cm Course-module
     * @param int $possibleresult Expected completion result. If the event that
     *   has just occurred (e.g. add post) can only result in making the activity
     *   complete when it wasn't before, use COMPLETION_COMPLETE. If the event that
     *   has just occurred (e.g. delete post) can only result in making the activity
     *   not complete when it was previously complete, use COMPLETION_INCOMPLETE.
     *   Otherwise use COMPLETION_UNKNOWN. Setting this value to something other than
     *   COMPLETION_UNKNOWN significantly improves performance because it will abandon
     *   processing early if the user's completion state already matches the expected
     *   result. For manual events, COMPLETION_COMPLETE or COMPLETION_INCOMPLETE
     *   must be used; these directly set the specified state.
     * @param int $userid User ID to be updated. Default 0 = current user
     * @return void
     */
    public function update_state($cm, $possibleresult=COMPLETION_UNKNOWN, $userid=0) {
        global $USER, $SESSION;

        // Do nothing if completion is not enabled for that activity
        if (!$this->is_enabled($cm)) {
            return;
        }

        // Get current value of completion state and do nothing if it's same as
        // the possible result of this change. If the change is to COMPLETE and the
        // current value is one of the COMPLETE_xx subtypes, ignore that as well
        $current = $this->get_data($cm, false, $userid);
        if ($possibleresult == $current->completionstate ||
            ($possibleresult == COMPLETION_COMPLETE &&
                ($current->completionstate == COMPLETION_COMPLETE_PASS ||
                $current->completionstate == COMPLETION_COMPLETE_FAIL))) {
            return;
        }

        if ($cm->completion == COMPLETION_TRACKING_MANUAL) {
            // For manual tracking we set the result directly
            switch($possibleresult) {
                case COMPLETION_COMPLETE:
                case COMPLETION_INCOMPLETE:
                    $newstate = $possibleresult;
                    break;
                default:
                    $this->internal_systemerror("Unexpected manual completion state for {$cm->id}: $possibleresult");
            }

        } else {
            // Automatic tracking; get new state
            $newstate = $this->internal_get_state($cm, $userid, $current);
        }

        // If changed, update
        if ($newstate != $current->completionstate) {
            $current->completionstate = $newstate;
            $current->timemodified    = time();
            $this->internal_set_data($cm, $current);
        }
    }

    /**
     * Calculates the completion state for an activity and user.
     *
     * Internal function. Not private, so we can unit-test it.
     *
     * @global object
     * @global object
     * @global object
     * @uses COMPLETION_VIEW_REQUIRED
     * @uses COMPLETION_NOT_VIEWED
     * @uses COMPLETION_INCOMPLETE
     * @uses FEATURE_COMPLETION_HAS_RULES
     * @uses COMPLETION_COMPLETE
     * @uses COMPLETION_AND
     * @param object $cm Activity
     * @param int $userid ID of user
     * @param object $current Previous completion information from database
     * @return mixed
     */
    function internal_get_state($cm, $userid, $current) {
        global $USER, $DB, $CFG;

        // Get user ID
        if (!$userid) {
            $userid = $USER->id;
        }

        // Check viewed
        if ($cm->completionview == COMPLETION_VIEW_REQUIRED &&
            $current->viewed == COMPLETION_NOT_VIEWED) {

            return COMPLETION_INCOMPLETE;
        }

        // Modname hopefully is provided in $cm but just in case it isn't, let's grab it
        if (!isset($cm->modname)) {
            $cm->modname = $DB->get_field('modules', 'name', array('id'=>$cm->module));
        }

        $newstate = COMPLETION_COMPLETE;

        // Check grade
        if (!is_null($cm->completiongradeitemnumber)) {
            require_once($CFG->libdir.'/gradelib.php');
            $item = grade_item::fetch(array('courseid'=>$cm->course, 'itemtype'=>'mod',
                'itemmodule'=>$cm->modname, 'iteminstance'=>$cm->instance,
                'itemnumber'=>$cm->completiongradeitemnumber));
            if ($item) {
                // Fetch 'grades' (will be one or none)
                $grades = grade_grade::fetch_users_grades($item, array($userid), false);
                if (empty($grades)) {
                    // No grade for user
                    return COMPLETION_INCOMPLETE;
                }
                if (count($grades) > 1) {
                    $this->internal_systemerror("Unexpected result: multiple grades for
                        item '{$item->id}', user '{$userid}'");
                }
                $newstate = $this->internal_get_grade_state($item, reset($grades));
                if ($newstate == COMPLETION_INCOMPLETE) {
                    return COMPLETION_INCOMPLETE;
                }

            } else {
                $this->internal_systemerror("Cannot find grade item for '{$cm->modname}'
                    cm '{$cm->id}' matching number '{$cm->completiongradeitemnumber}'");
            }
        }

        if (plugin_supports('mod', $cm->modname, FEATURE_COMPLETION_HAS_RULES)) {
            $function = $cm->modname.'_get_completion_state';
            if (!function_exists($function)) {
                $this->internal_systemerror("Module {$cm->modname} claims to support
                    FEATURE_COMPLETION_HAS_RULES but does not have required
                    {$cm->modname}_get_completion_state function");
            }
            if (!$function($this->course, $cm, $userid, COMPLETION_AND)) {
                return COMPLETION_INCOMPLETE;
            }
        }

        return $newstate;

    }


    /**
     * Marks a module as viewed.
     *
     * Should be called whenever a module is 'viewed' (it is up to the module how to
     * determine that). Has no effect if viewing is not set as a completion condition.
     *
     * @uses COMPLETION_VIEW_NOT_REQUIRED
     * @uses COMPLETION_VIEWED
     * @uses COMPLETION_COMPLETE
     * @param object $cm Activity
     * @param int $userid User ID or 0 (default) for current user
     * @return void
     */
    public function set_module_viewed($cm, $userid=0) {
        // Don't do anything if view condition is not turned on
        if ($cm->completionview == COMPLETION_VIEW_NOT_REQUIRED || !$this->is_enabled($cm)) {
            return;
        }
        // Get current completion state
        $data = $this->get_data($cm, $userid);
        // If we already viewed it, don't do anything
        if ($data->viewed == COMPLETION_VIEWED) {
            return;
        }
        // OK, change state, save it, and update completion
        $data->viewed = COMPLETION_VIEWED;
        $this->internal_set_data($cm, $data);
        $this->update_state($cm, COMPLETION_COMPLETE, $userid);
    }

    /**
     * Determines how much completion data exists for an activity. This is used when
     * deciding whether completion information should be 'locked' in the module
     * editing form.
     *
     * @global object
     * @global object
     * @param object $cm Activity
     * @return int The number of users who have completion data stored for this
     *   activity, 0 if none
     */
    public function count_user_data($cm) {
        global $CFG, $DB;

        return $DB->get_field_sql("
    SELECT
        COUNT(1)
    FROM
        {course_modules_completion}
    WHERE
        coursemoduleid=? AND completionstate<>0", array($cm->id));
    }

    /**
     * Deletes completion state related to an activity for all users.
     *
     * Intended for use only when the activity itself is deleted.
     *
     * @global object
     * @global object
     * @param object $cm Activity
     */
    public function delete_all_state($cm) {
        global $SESSION, $DB;

        // Delete from database
        $DB->delete_records('course_modules_completion', array('coursemoduleid'=>$cm->id));

        // Erase cache data for current user if applicable
        if (isset($SESSION->completioncache) &&
            array_key_exists($cm->course, $SESSION->completioncache) &&
            array_key_exists($cm->id, $SESSION->completioncache[$cm->course])) {

            unset($SESSION->completioncache[$cm->course][$cm->id]);
        }
    }

    /**
     * Recalculates completion state related to an activity for all users.
     *
     * Intended for use if completion conditions change. (This should be avoided
     * as it may cause some things to become incomplete when they were previously
     * complete, with the effect - for example - of hiding a later activity that
     * was previously available.)
     *
     * Resetting state of manual tickbox has same result as deleting state for
     * it.
     *
     * @global object
     * @uses COMPLETION_TRACKING_MANUAL
     * @uses COMPLETION_UNKNOWN
     * @param object $cm Activity
     */
    public function reset_all_state($cm) {
        global $DB;

        if ($cm->completion == COMPLETION_TRACKING_MANUAL) {
            $this->delete_all_state($cm);
            return;
        }
        // Get current list of users with completion state
        $rs = $DB->get_recordset('course_modules_completion', array('coursemoduleid'=>$cm->id), '', 'userid');
        $keepusers = array();
        foreach ($rs as $rec) {
            $keepusers[] = $rec->userid;
        }
        $rs->close();

        // Delete all existing state [also clears session cache for current user]
        $this->delete_all_state($cm);

        // Merge this with list of planned users (according to roles)
        $trackedusers = $this->internal_get_tracked_users(false);
        foreach ($trackedusers as $trackeduser) {
            $keepusers[] = $trackeduser->id;
        }
        $keepusers = array_unique($keepusers);

        // Recalculate state for each kept user
        foreach ($keepusers as $keepuser) {
            $this->update_state($cm, COMPLETION_UNKNOWN, $keepuser);
        }
    }

    /**
     * Obtains completion data for a particular activity and user (from the
     * session cache if available, or by SQL query)
     *
     * @global object
     * @global object
     * @global object
     * @global object
     * @uses COMPLETION_CACHE_EXPIRY
     * @param object $cm Activity; only required field is ->id
     * @param bool $wholecourse If true (default false) then, when necessary to
     *   fill the cache, retrieves information from the entire course not just for
     *   this one activity
     * @param int $userid User ID or 0 (default) for current user
     * @param array $modinfo Supply the value here - this is used for unit
     *   testing and so that it can be called recursively from within
     *   get_fast_modinfo. (Needs only list of all CMs with IDs.)
     *   Otherwise the method calls get_fast_modinfo itself.
     * @return object Completion data (record from course_modules_completion)
     */
    public function get_data($cm, $wholecourse=false, $userid=0, $modinfo=null) {
        global $USER, $CFG, $SESSION, $DB;

        // Get user ID
        if (!$userid) {
            $userid = $USER->id;
        }

        // Is this the current user?
        $currentuser = $userid==$USER->id;

        if ($currentuser) {
            // Make sure cache is present and is for current user (loginas
            // changes this)
            if (!isset($SESSION->completioncache) || $SESSION->completioncacheuserid!=$USER->id) {
                $SESSION->completioncache = array();
                $SESSION->completioncacheuserid = $USER->id;
            }
            // Expire any old data from cache
            foreach ($SESSION->completioncache as $courseid=>$activities) {
                if (empty($activities['updated']) || $activities['updated'] < time()-COMPLETION_CACHE_EXPIRY) {
                    unset($SESSION->completioncache[$courseid]);
                }
            }
            // See if requested data is present, if so use cache to get it
            if (isset($SESSION->completioncache) &&
                array_key_exists($this->course->id, $SESSION->completioncache) &&
                array_key_exists($cm->id, $SESSION->completioncache[$this->course->id])) {
                return $SESSION->completioncache[$this->course->id][$cm->id];
            }
        }

        // Not there, get via SQL
        if ($currentuser && $wholecourse) {
            // Get whole course data for cache
            $alldatabycmc = $DB->get_records_sql("
    SELECT
        cmc.*
    FROM
        {course_modules} cm
        INNER JOIN {course_modules_completion} cmc ON cmc.coursemoduleid=cm.id
    WHERE
        cm.course=? AND cmc.userid=?", array($this->course->id, $userid));

            // Reindex by cm id
            $alldata = array();
            if ($alldatabycmc) {
                foreach ($alldatabycmc as $data) {
                    $alldata[$data->coursemoduleid] = $data;
                }
            }

            // Get the module info and build up condition info for each one
            if (empty($modinfo)) {
                $modinfo = get_fast_modinfo($this->course, $userid);
            }
            foreach ($modinfo->cms as $othercm) {
                if (array_key_exists($othercm->id, $alldata)) {
                    $data = $alldata[$othercm->id];
                } else {
                    // Row not present counts as 'not complete'
                    $data = new StdClass;
                    $data->id              = 0;
                    $data->coursemoduleid  = $othercm->id;
                    $data->userid          = $userid;
                    $data->completionstate = 0;
                    $data->viewed          = 0;
                    $data->timemodified    = 0;
                }
                $SESSION->completioncache[$this->course->id][$othercm->id] = $data;
            }
            $SESSION->completioncache[$this->course->id]['updated'] = time();

            if (!isset($SESSION->completioncache[$this->course->id][$cm->id])) {
                $this->internal_systemerror("Unexpected error: course-module {$cm->id} could not be found on course {$this->course->id}");
            }
            return $SESSION->completioncache[$this->course->id][$cm->id];

        } else {
            // Get single record
            $data = $DB->get_record('course_modules_completion', array('coursemoduleid'=>$cm->id, 'userid'=>$userid));
            if ($data == false) {
                // Row not present counts as 'not complete'
                $data = new StdClass;
                $data->id              = 0;
                $data->coursemoduleid  = $cm->id;
                $data->userid          = $userid;
                $data->completionstate = 0;
                $data->viewed          = 0;
                $data->timemodified    = 0;
            }

            // Put in cache
            if ($currentuser) {
                $SESSION->completioncache[$this->course->id][$cm->id] = $data;
                // For single updates, only set date if it was empty before
                if (empty($SESSION->completioncache[$this->course->id]['updated'])) {
                    $SESSION->completioncache[$this->course->id]['updated'] = time();
                }
            }
        }

        return $data;
    }

    /**
     * Updates completion data for a particular coursemodule and user (user is
     * determined from $data).
     *
     * (Internal function. Not private, so we can unit-test it.)
     *
     * @global object
     * @global object
     * @global object
     * @param object $cm Activity
     * @param object $data Data about completion for that user
     */
    function internal_set_data($cm, $data) {
        global $USER, $SESSION, $DB;

        if ($data->id) {
            // Has real (nonzero) id meaning that a database row exists
            $DB->update_record('course_modules_completion', $data);
        } else {
            // Didn't exist before, needs creating
            $data->id = $DB->insert_record('course_modules_completion', $data);
        }

        if ($data->userid == $USER->id) {
            $SESSION->completioncache[$cm->course][$cm->id] = $data;
        }
    }

    /**
     * Obtains a list of activities for which completion is enabled on the
     * course. The list is ordered by the section order of those activities.
     *
     * @global object
     * @uses COMPLETION_TRACKING_NONE
     * @param array $modinfo For unit testing only, supply the value
     *   here. Otherwise the method calls get_fast_modinfo
     * @return array Array from $cmid => $cm of all activities with completion enabled,
     *   empty array if none
     */
    public function get_activities($modinfo=null) {
        global $DB;

        // Obtain those activities which have completion turned on
        $withcompletion = $DB->get_records_select('course_modules', 'course='.$this->course->id.
          ' AND completion<>'.COMPLETION_TRACKING_NONE);
        if (count($withcompletion) == 0) {
            return array();
        }

        // Use modinfo to get section order and also add in names
        if (empty($modinfo)) {
            $modinfo = get_fast_modinfo($this->course);
        }
        $result = array();
        foreach ($modinfo->sections as $sectioncms) {
            foreach ($sectioncms as $cmid) {
                if (array_key_exists($cmid, $withcompletion)) {
                    $result[$cmid] = $withcompletion[$cmid];
                    $result[$cmid]->modname = $modinfo->cms[$cmid]->modname;
                    $result[$cmid]->name    = $modinfo->cms[$cmid]->name;
                }
            }
        }

        return $result;
    }

    /**
     * Gets list of users in a course whose progress is tracked for display on the
     * progress report.
     *
     * @global object
     * @global object
     * @uses CONTEXT_COURSE
     * @param bool $sortfirstname True to sort with firstname
     * @param int $groupid Optionally restrict to groupid
     * @return array Array of user objects containing id, firstname, lastname (empty if none)
     */
    function internal_get_tracked_users($sortfirstname, $groupid=0) {
        global $CFG, $DB;

        if (!empty($CFG->progresstrackedroles)) {
            $roles = explode(', ', $CFG->progresstrackedroles);
        } else {
            // This causes it to default to everyone (if there is no student role)
            $roles = array();
        }
        $users = get_role_users($roles, get_context_instance(CONTEXT_COURSE, $this->course->id), true,
            'u.id, u.firstname, u.lastname, u.idnumber',
            $sortfirstname ? 'u.firstname ASC' : 'u.lastname ASC', true, $groupid);
        $users = $users ? $users : array(); // In case it returns false
        return $users;
    }

    /**
     * Obtains progress information across a course for all users on that course, or
     * for all users in a specific group. Intended for use when displaying progress.
     *
     * This includes only users who, in course context, have one of the roles for
     * which progress is tracked (the progresstrackedroles admin option).
     *
     * Users are included (in the first array) even if they do not have
     * completion progress for any course-module.
     *
     * @global object
     * @global object
     * @param bool $sortfirstname If true, sort by first name, otherwise sort by
     *   last name
     * @param int $groupid Group ID or 0 (default)/false for all groups
     * @param int $pagesize Number of users to actually return (0 = unlimited)
     * @param int $start User to start at if paging (0 = first set)
     * @return Object with ->total and ->start (same as $start) and ->users;
     *   an array of user objects (like mdl_user id, firstname, lastname)
     *   containing an additional ->progress array of coursemoduleid => completionstate
     */
    public function get_progress_all($sortfirstname=false, $groupid=0,
        $pagesize=0,$start=0) {
        global $CFG, $DB;
        $resultobject=new StdClass;

        // Get list of applicable users
        $users = $this->internal_get_tracked_users($sortfirstname, $groupid);
        $resultobject->start=$start;
        $resultobject->total=count($users);
        $users=array_slice($users,$start,$pagesize==0 ? count($users)-$start : $pagesize);

        // Get progress information for these users in groups of 1, 000 (if needed)
        // to avoid making the SQL IN too long
        $resultobject->users=array();
        $userids = array();
        foreach ($users as $user) {
            $userids[] = $user->id;
            $resultobject->users[$user->id]=$user;
            $resultobject->users[$user->id]->progress=array();
        }

        for($i=0; $i<count($userids); $i+=1000) {
            $blocksize = count($userids)-$i < 1000 ? count($userids)-$i : 1000;

            list($insql, $params) = $DB->get_in_or_equal(array_slice($userids, $i, $blocksize));
            array_splice($params, 0, 0, array($this->course->id));
            $rs = $DB->get_recordset_sql("
SELECT
    cmc.*
FROM
    {course_modules} cm
    INNER JOIN {course_modules_completion} cmc ON cm.id=cmc.coursemoduleid
WHERE
    cm.course=? AND cmc.userid $insql
    ", $params);
            foreach ($rs as $progress) {
                $resultobject->users[$progress->userid]->progress[$progress->coursemoduleid]=$progress;
            }
            $rs->close();
        }

        return $resultobject;
    }

    /**
     * @todo Document this function
     *
     * @uses COMPLETION_TRACKING_MANUAL
     * @uses COMPLETION_INCOMPLETE
     * @param object $cm
     * @param object $item
     * @param object $grade
     * @param bool $deleted
     * @return void
     */
    public function inform_grade_changed($cm, $item, $grade, $deleted) {
        // Bail out now if completion is not enabled for course-module, it is enabled
        // but is set to manual, grade is not used to compute completion, or this
        // is a different numbered grade
        if (!$this->is_enabled($cm) ||
            $cm->completion == COMPLETION_TRACKING_MANUAL ||
            is_null($cm->completiongradeitemnumber) ||
            $item->itemnumber != $cm->completiongradeitemnumber) {

            return;
        }

        // What is the expected result based on this grade?
        if ($deleted) {
            // Grade being deleted, so only change could be to make it incomplete
            $possibleresult = COMPLETION_INCOMPLETE;
        } else {
            $possibleresult = $this->internal_get_grade_state($item, $grade);
        }

        // OK, let's update state based on this
        $this->update_state($cm, $possibleresult, $grade->userid);
    }

    /**
     * Calculates the completion state that would result from a graded item
     * (where grade-based completion is turned on) based on the actual grade
     * and settings.
     *
     * Internal function. Not private, so we can unit-test it.
     *
     * @uses COMPLETION_INCOMPLETE
     * @uses COMPLETION_COMPLETE_PASS
     * @uses COMPLETION_COMPLETE_FAIL
     * @uses COMPLETION_COMPLETE
     * @param object $item grade_item
     * @param object $grade grade_grade
     * @return int Completion state e.g. COMPLETION_INCOMPLETE
     */
    function internal_get_grade_state($item, $grade) {
        if (!$grade) {
            return COMPLETION_INCOMPLETE;
        }
        // Conditions to show pass/fail:
        // a) Grade has pass mark (default is 0.00000 which is boolean true so be careful)
        // b) Grade is visible (neither hidden nor hidden-until)
        if ($item->gradepass && $item->gradepass > 0.000009 && !$item->hidden) {
            // Use final grade if set otherwise raw grade
            $score = !is_null($grade->finalgrade) ? $grade->finalgrade : $grade->rawgrade;

            // We are displaying and tracking pass/fail
            if ($score >= $item->gradepass) {
                return COMPLETION_COMPLETE_PASS;
            } else {
                return COMPLETION_COMPLETE_FAIL;
            }
        } else {
            // Not displaying pass/fail, but we know grade exists b/c we got here
            return COMPLETION_COMPLETE;
        }
    }

    /**
     * This is to be used only for system errors (things that shouldn't happen)
     * and not user-level errors.
     *
     * @global object
     * @param string $error Error string (will not be displayed to user unless
     *   debugging is enabled)
     * @return void Throws moodle_exception Exception with the error string as debug info
     */
    function internal_systemerror($error) {
        global $CFG;
        throw new moodle_exception('err_system','completion',
            $CFG->wwwroot.'/course/view.php?id='.$this->course->id,null,$error);
    }

    /**
     * For testing only. Wipes information cached in user session.
     *
     * @global object
     */
    static function wipe_session_cache() {
        global $SESSION;
        unset($SESSION->completioncache);
        unset($SESSION->completioncacheuserid);
    }
}
