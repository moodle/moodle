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
 * Contains classes, functions and constants used during the tracking
 * of activity completion for users.
 *
 * Completion top-level options (admin setting enablecompletion)
 *
 * @package core_completion
 * @category completion
 * @copyright 1999 onwards Martin Dougiamas   {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Include the required completion libraries
 */
require_once $CFG->dirroot.'/completion/completion_aggregation.php';
require_once $CFG->dirroot.'/completion/criteria/completion_criteria.php';
require_once $CFG->dirroot.'/completion/completion_completion.php';
require_once $CFG->dirroot.'/completion/completion_criteria_completion.php';


/**
 * The completion system is enabled in this site/course
 */
define('COMPLETION_ENABLED', 1);
/**
 * The completion system is not enabled in this site/course
 */
define('COMPLETION_DISABLED', 0);

/**
 * Completion tracking is disabled for this activity
 * This is a completion tracking option per-activity  (course_modules/completion)
 */
define('COMPLETION_TRACKING_NONE', 0);

/**
 * Manual completion tracking (user ticks box) is enabled for this activity
 * This is a completion tracking option per-activity  (course_modules/completion)
 */
define('COMPLETION_TRACKING_MANUAL', 1);
/**
 * Automatic completion tracking (system ticks box) is enabled for this activity
 * This is a completion tracking option per-activity  (course_modules/completion)
 */
define('COMPLETION_TRACKING_AUTOMATIC', 2);

/**
 * The user has not completed this activity.
 * This is a completion state value (course_modules_completion/completionstate)
 */
define('COMPLETION_INCOMPLETE', 0);
/**
 * The user has completed this activity. It is not specified whether they have
 * passed or failed it.
 * This is a completion state value (course_modules_completion/completionstate)
 */
define('COMPLETION_COMPLETE', 1);
/**
 * The user has completed this activity with a grade above the pass mark.
 * This is a completion state value (course_modules_completion/completionstate)
 */
define('COMPLETION_COMPLETE_PASS', 2);
/**
 * The user has completed this activity but their grade is less than the pass mark
 * This is a completion state value (course_modules_completion/completionstate)
 */
define('COMPLETION_COMPLETE_FAIL', 3);

/**
 * The effect of this change to completion status is unknown.
 * A completion effect changes (used only in update_state)
 */
define('COMPLETION_UNKNOWN', -1);
/**
 * The user's grade has changed, so their new state might be
 * COMPLETION_COMPLETE_PASS or COMPLETION_COMPLETE_FAIL.
 * A completion effect changes (used only in update_state)
 */
define('COMPLETION_GRADECHANGE', -2);

/**
 * User must view this activity.
 * Whether view is required to create an activity (course_modules/completionview)
 */
define('COMPLETION_VIEW_REQUIRED', 1);
/**
 * User does not need to view this activity
 * Whether view is required to create an activity (course_modules/completionview)
 */
define('COMPLETION_VIEW_NOT_REQUIRED', 0);

/**
 * User has viewed this activity.
 * Completion viewed state (course_modules_completion/viewed)
 */
define('COMPLETION_VIEWED', 1);
/**
 * User has not viewed this activity.
 * Completion viewed state (course_modules_completion/viewed)
 */
define('COMPLETION_NOT_VIEWED', 0);

/**
 * Completion details should be ORed together and you should return false if
 * none apply.
 */
define('COMPLETION_OR', false);
/**
 * Completion details should be ANDed together and you should return true if
 * none apply
 */
define('COMPLETION_AND', true);

/**
 * Course completion criteria aggregation method.
 */
define('COMPLETION_AGGREGATION_ALL', 1);
/**
 * Course completion criteria aggregation method.
 */
define('COMPLETION_AGGREGATION_ANY', 2);


/**
 * Utility function for checking if the logged in user can view
 * another's completion data for a particular course
 *
 * @access  public
 * @param   int         $userid     Completion data's owner
 * @param   mixed       $course     Course object or Course ID (optional)
 * @return  boolean
 */
function completion_can_view_data($userid, $course = null) {
    global $USER;

    if (!isloggedin()) {
        return false;
    }

    if (!is_object($course)) {
        $cid = $course;
        $course = new stdClass();
        $course->id = $cid;
    }

    // Check if this is the site course
    if ($course->id == SITEID) {
        $course = null;
    }

    // Check if completion is enabled
    if ($course) {
        $cinfo = new completion_info($course);
        if (!$cinfo->is_enabled()) {
            return false;
        }
    } else {
        if (!completion_info::is_enabled_for_site()) {
            return false;
        }
    }

    // Is own user's data?
    if ($USER->id == $userid) {
        return true;
    }

    // Check capabilities
    $personalcontext = context_user::instance($userid);

    if (has_capability('moodle/user:viewuseractivitiesreport', $personalcontext)) {
        return true;
    } elseif (has_capability('report/completion:view', $personalcontext)) {
        return true;
    }

    if ($course->id) {
        $coursecontext = context_course::instance($course->id);
    } else {
        $coursecontext = context_system::instance();
    }

    if (has_capability('report/completion:view', $coursecontext)) {
        return true;
    }

    return false;
}


/**
 * Class represents completion information for a course.
 *
 * Does not contain any data, so you can safely construct it multiple times
 * without causing any problems.
 *
 * @package core
 * @category completion
 * @copyright 2008 Sam Marshall
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_info {

    /* @var stdClass Course object passed during construction */
    private $course;

    /* @var int Course id */
    public $course_id;

    /* @var array Completion criteria {@link completion_info::get_criteria()}  */
    private $criteria;

    /**
     * Return array of aggregation methods
     * @return array
     */
    public static function get_aggregation_methods() {
        return array(
            COMPLETION_AGGREGATION_ALL => get_string('all'),
            COMPLETION_AGGREGATION_ANY => get_string('any', 'completion'),
        );
    }

    /**
     * Constructs with course details.
     *
     * When instantiating a new completion info object you must provide a course
     * object with at least id, and enablecompletion properties. Property
     * cacherev is needed if you check completion of the current user since
     * it is used for cache validation.
     *
     * @param stdClass $course Moodle course object.
     */
    public function __construct($course) {
        $this->course = $course;
        $this->course_id = $course->id;
    }

    /**
     * Determines whether completion is enabled across entire site.
     *
     * @return bool COMPLETION_ENABLED (true) if completion is enabled for the site,
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
     * @param stdClass|cm_info $cm Course-module object. If not specified, returns the course
     *   completion enable state.
     * @return mixed COMPLETION_ENABLED or COMPLETION_DISABLED (==0) in the case of
     *   site and course; COMPLETION_TRACKING_MANUAL, _AUTOMATIC or _NONE (==0)
     *   for a course-module.
     */
    public function is_enabled($cm = null) {
        global $CFG, $DB;

        // First check global completion
        if (!isset($CFG->enablecompletion) || $CFG->enablecompletion == COMPLETION_DISABLED) {
            return COMPLETION_DISABLED;
        }

        // Load data if we do not have enough
        if (!isset($this->course->enablecompletion)) {
            $this->course = get_course($this->course_id);
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
     * Displays the 'Your progress' help icon, if completion tracking is enabled.
     * Just prints the result of display_help_icon().
     *
     * @deprecated since Moodle 2.0 - Use display_help_icon instead.
     */
    public function print_help_icon() {
        print $this->display_help_icon();
    }

    /**
     * Returns the 'Your progress' help icon, if completion tracking is enabled.
     *
     * @return string HTML code for help icon, or blank if not needed
     */
    public function display_help_icon() {
        global $PAGE, $OUTPUT, $USER;
        $result = '';
        if ($this->is_enabled() && !$PAGE->user_is_editing() && $this->is_tracked_user($USER->id) && isloggedin() &&
                !isguestuser()) {
            $result .= html_writer::tag('div', get_string('yourprogress','completion') .
                    $OUTPUT->help_icon('completionicons', 'completion'), array('id' => 'completionprogressid',
                    'class' => 'completionprogress'));
        }
        return $result;
    }

    /**
     * Get a course completion for a user
     *
     * @param int $user_id User id
     * @param int $criteriatype Specific criteria type to return
     * @return bool|completion_criteria_completion returns false on fail
     */
    public function get_completion($user_id, $criteriatype) {
        $completions = $this->get_completions($user_id, $criteriatype);

        if (empty($completions)) {
            return false;
        } elseif (count($completions) > 1) {
            print_error('multipleselfcompletioncriteria', 'completion');
        }

        return $completions[0];
    }

    /**
     * Get all course criteria's completion objects for a user
     *
     * @param int $user_id User id
     * @param int $criteriatype Specific criteria type to return (optional)
     * @return array
     */
    public function get_completions($user_id, $criteriatype = null) {
        $criteria = $this->get_criteria($criteriatype);

        $completions = array();

        foreach ($criteria as $criterion) {
            $params = array(
                'course'        => $this->course_id,
                'userid'        => $user_id,
                'criteriaid'    => $criterion->id
            );

            $completion = new completion_criteria_completion($params);
            $completion->attach_criteria($criterion);

            $completions[] = $completion;
        }

        return $completions;
    }

    /**
     * Get completion object for a user and a criteria
     *
     * @param int $user_id User id
     * @param completion_criteria $criteria Criteria object
     * @return completion_criteria_completion
     */
    public function get_user_completion($user_id, $criteria) {
        $params = array(
            'course'        => $this->course_id,
            'userid'        => $user_id,
            'criteriaid'    => $criteria->id,
        );

        $completion = new completion_criteria_completion($params);
        return $completion;
    }

    /**
     * Check if course has completion criteria set
     *
     * @return bool Returns true if there are criteria
     */
    public function has_criteria() {
        $criteria = $this->get_criteria();

        return (bool) count($criteria);
    }

    /**
     * Get course completion criteria
     *
     * @param int $criteriatype Specific criteria type to return (optional)
     */
    public function get_criteria($criteriatype = null) {

        // Fill cache if empty
        if (!is_array($this->criteria)) {
            global $DB;

            $params = array(
                'course'    => $this->course->id
            );

            // Load criteria from database
            $records = (array)$DB->get_records('course_completion_criteria', $params);

            // Order records so activities are in the same order as they appear on the course view page.
            if ($records) {
                $activitiesorder = array_keys(get_fast_modinfo($this->course)->get_cms());
                usort($records, function ($a, $b) use ($activitiesorder) {
                    $aidx = ($a->criteriatype == COMPLETION_CRITERIA_TYPE_ACTIVITY) ?
                        array_search($a->moduleinstance, $activitiesorder) : false;
                    $bidx = ($b->criteriatype == COMPLETION_CRITERIA_TYPE_ACTIVITY) ?
                        array_search($b->moduleinstance, $activitiesorder) : false;
                    if ($aidx === false || $bidx === false || $aidx == $bidx) {
                        return 0;
                    }
                    return ($aidx < $bidx) ? -1 : 1;
                });
            }

            // Build array of criteria objects
            $this->criteria = array();
            foreach ($records as $record) {
                $this->criteria[$record->id] = completion_criteria::factory((array)$record);
            }
        }

        // If after all criteria
        if ($criteriatype === null) {
            return $this->criteria;
        }

        // If we are only after a specific criteria type
        $criteria = array();
        foreach ($this->criteria as $criterion) {

            if ($criterion->criteriatype != $criteriatype) {
                continue;
            }

            $criteria[$criterion->id] = $criterion;
        }

        return $criteria;
    }

    /**
     * Get aggregation method
     *
     * @param int $criteriatype If none supplied, get overall aggregation method (optional)
     * @return int One of COMPLETION_AGGREGATION_ALL or COMPLETION_AGGREGATION_ANY
     */
    public function get_aggregation_method($criteriatype = null) {
        $params = array(
            'course'        => $this->course_id,
            'criteriatype'  => $criteriatype
        );

        $aggregation = new completion_aggregation($params);

        if (!$aggregation->id) {
            $aggregation->method = COMPLETION_AGGREGATION_ALL;
        }

        return $aggregation->method;
    }

    /**
     * @deprecated since Moodle 2.8 MDL-46290.
     */
    public function get_incomplete_criteria() {
        throw new coding_exception('completion_info->get_incomplete_criteria() is removed.');
    }

    /**
     * Clear old course completion criteria
     */
    public function clear_criteria() {
        global $DB;

        // Remove completion criteria records for the course itself, and any records that refer to the course.
        $select = 'course = :course OR (criteriatype = :type AND courseinstance = :courseinstance)';
        $params = [
            'course' => $this->course_id,
            'type' => COMPLETION_CRITERIA_TYPE_COURSE,
            'courseinstance' => $this->course_id,
        ];

        $DB->delete_records_select('course_completion_criteria', $select, $params);
        $DB->delete_records('course_completion_aggr_methd', array('course' => $this->course_id));

        $this->delete_course_completion_data();
    }

    /**
     * Has the supplied user completed this course
     *
     * @param int $user_id User's id
     * @return boolean
     */
    public function is_course_complete($user_id) {
        $params = array(
            'userid'    => $user_id,
            'course'  => $this->course_id
        );

        $ccompletion = new completion_completion($params);
        return $ccompletion->is_complete();
    }

    /**
     * Check whether the supplied user can override the activity completion statuses within the current course.
     *
     * @param stdClass $user The user object.
     * @return bool True if the user can override, false otherwise.
     */
    public function user_can_override_completion($user) {
        return has_capability('moodle/course:overridecompletion', context_course::instance($this->course_id), $user);
    }

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
     * @param stdClass|cm_info $cm Course-module
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
     * @param bool $override Whether manually overriding the existing completion state.
     * @return void
     * @throws moodle_exception if trying to override without permission.
     */
    public function update_state($cm, $possibleresult=COMPLETION_UNKNOWN, $userid=0, $override = false) {
        global $USER;

        // Do nothing if completion is not enabled for that activity
        if (!$this->is_enabled($cm)) {
            return;
        }

        // If we're processing an override and the current user isn't allowed to do so, then throw an exception.
        if ($override) {
            if (!$this->user_can_override_completion($USER)) {
                throw new required_capability_exception(context_course::instance($this->course_id),
                                                        'moodle/course:overridecompletion', 'nopermission', '');
            }
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

        // For auto tracking, if the status is overridden to 'COMPLETION_COMPLETE', then disallow further changes,
        // unless processing another override.
        // Basically, we want those activities which have been overridden to COMPLETE to hold state, and those which have been
        // overridden to INCOMPLETE to still be processed by normal completion triggers.
        if ($cm->completion == COMPLETION_TRACKING_AUTOMATIC && !is_null($current->overrideby)
            && $current->completionstate == COMPLETION_COMPLETE && !$override) {
            return;
        }

        // For manual tracking, or if overriding the completion state, we set the state directly.
        if ($cm->completion == COMPLETION_TRACKING_MANUAL || $override) {
            switch($possibleresult) {
                case COMPLETION_COMPLETE:
                case COMPLETION_INCOMPLETE:
                    $newstate = $possibleresult;
                    break;
                default:
                    $this->internal_systemerror("Unexpected manual completion state for {$cm->id}: $possibleresult");
            }

        } else {
            $newstate = $this->internal_get_state($cm, $userid, $current);
        }

        // If changed, update
        if ($newstate != $current->completionstate) {
            $current->completionstate = $newstate;
            $current->timemodified    = time();
            $current->overrideby      = $override ? $USER->id : null;
            $this->internal_set_data($cm, $current);
        }
    }

    /**
     * Calculates the completion state for an activity and user.
     *
     * Internal function. Not private, so we can unit-test it.
     *
     * @param stdClass|cm_info $cm Activity
     * @param int $userid ID of user
     * @param stdClass $current Previous completion information from database
     * @return mixed
     */
    public function internal_get_state($cm, $userid, $current) {
        global $USER, $DB;

        // Get user ID
        if (!$userid) {
            $userid = $USER->id;
        }

        // Check viewed
        if ($cm->completionview == COMPLETION_VIEW_REQUIRED &&
            $current->viewed == COMPLETION_NOT_VIEWED) {

            return COMPLETION_INCOMPLETE;
        }

        if ($cm instanceof stdClass) {
            // Modname hopefully is provided in $cm but just in case it isn't, let's grab it.
            if (!isset($cm->modname)) {
                $cm->modname = $DB->get_field('modules', 'name', array('id' => $cm->module));
            }
            // Some functions call this method and pass $cm as an object with ID only. Make sure course is set as well.
            if (!isset($cm->course)) {
                $cm->course = $this->course_id;
            }
        }
        // Make sure we're using a cm_info object.
        $cminfo = cm_info::create($cm, $userid);

        $newstate = COMPLETION_COMPLETE;

        // Check grade
        if (!is_null($cminfo->completiongradeitemnumber)) {
            $newstate = $this->get_grade_completion($cminfo, $userid);
            if ($newstate == COMPLETION_INCOMPLETE) {
                return COMPLETION_INCOMPLETE;
            }
        }

        if (plugin_supports('mod', $cminfo->modname, FEATURE_COMPLETION_HAS_RULES)) {
            $function = $cminfo->modname . '_get_completion_state';
            if (!function_exists($function)) {
                $this->internal_systemerror("Module {$cminfo->modname} claims to support
                    FEATURE_COMPLETION_HAS_RULES but does not have required
                    {$cm->modname}_get_completion_state function");
            }
            if (!$function($this->course, $cminfo, $userid, COMPLETION_AND)) {
                return COMPLETION_INCOMPLETE;
            }
        }

        return $newstate;

    }

    /**
     * Fetches the completion state for an activity completion's require grade completion requirement.
     *
     * @param cm_info $cm The course module information.
     * @param int $userid The user ID.
     * @return int The completion state.
     */
    public function get_grade_completion(cm_info $cm, int $userid): int {
        global $CFG;

        require_once($CFG->libdir . '/gradelib.php');
        $item = grade_item::fetch([
            'courseid' => $cm->course,
            'itemtype' => 'mod',
            'itemmodule' => $cm->modname,
            'iteminstance' => $cm->instance,
            'itemnumber' => $cm->completiongradeitemnumber
        ]);
        if ($item) {
            // Fetch 'grades' (will be one or none).
            $grades = grade_grade::fetch_users_grades($item, [$userid], false);
            if (empty($grades)) {
                // No grade for user.
                return COMPLETION_INCOMPLETE;
            }
            if (count($grades) > 1) {
                $this->internal_systemerror("Unexpected result: multiple grades for
                        item '{$item->id}', user '{$userid}'");
            }
            return self::internal_get_grade_state($item, reset($grades));
        } else {
            $this->internal_systemerror("Cannot find grade item for '{$cm->modname}'
                    cm '{$cm->id}' matching number '{$cm->completiongradeitemnumber}'");
        }

        return COMPLETION_INCOMPLETE;
    }

    /**
     * Marks a module as viewed.
     *
     * Should be called whenever a module is 'viewed' (it is up to the module how to
     * determine that). Has no effect if viewing is not set as a completion condition.
     *
     * Note that this function must be called before you print the page header because
     * it is possible that the navigation block may depend on it. If you call it after
     * printing the header, it shows a developer debug warning.
     *
     * @param stdClass|cm_info $cm Activity
     * @param int $userid User ID or 0 (default) for current user
     * @return void
     */
    public function set_module_viewed($cm, $userid=0) {
        global $PAGE;
        if ($PAGE->headerprinted) {
            debugging('set_module_viewed must be called before header is printed',
                    DEBUG_DEVELOPER);
        }

        // Don't do anything if view condition is not turned on
        if ($cm->completionview == COMPLETION_VIEW_NOT_REQUIRED || !$this->is_enabled($cm)) {
            return;
        }

        // Get current completion state
        $data = $this->get_data($cm, false, $userid);

        // If we already viewed it, don't do anything unless the completion status is overridden.
        // If the completion status is overridden, then we need to allow this 'view' to trigger automatic completion again.
        if ($data->viewed == COMPLETION_VIEWED && empty($data->overrideby)) {
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
     * @param cm_info $cm Activity
     * @return int The number of users who have completion data stored for this
     *   activity, 0 if none
     */
    public function count_user_data($cm) {
        global $DB;

        return $DB->get_field_sql("
    SELECT
        COUNT(1)
    FROM
        {course_modules_completion}
    WHERE
        coursemoduleid=? AND completionstate<>0", array($cm->id));
    }

    /**
     * Determines how much course completion data exists for a course. This is used when
     * deciding whether completion information should be 'locked' in the completion
     * settings form and activity completion settings.
     *
     * @param int $user_id Optionally only get course completion data for a single user
     * @return int The number of users who have completion data stored for this
     *     course, 0 if none
     */
    public function count_course_user_data($user_id = null) {
        global $DB;

        $sql = '
    SELECT
        COUNT(1)
    FROM
        {course_completion_crit_compl}
    WHERE
        course = ?
        ';

        $params = array($this->course_id);

        // Limit data to a single user if an ID is supplied
        if ($user_id) {
            $sql .= ' AND userid = ?';
            $params[] = $user_id;
        }

        return $DB->get_field_sql($sql, $params);
    }

    /**
     * Check if this course's completion criteria should be locked
     *
     * @return boolean
     */
    public function is_course_locked() {
        return (bool) $this->count_course_user_data();
    }

    /**
     * Deletes all course completion completion data.
     *
     * Intended to be used when unlocking completion criteria settings.
     */
    public function delete_course_completion_data() {
        global $DB;

        $DB->delete_records('course_completions', array('course' => $this->course_id));
        $DB->delete_records('course_completion_crit_compl', array('course' => $this->course_id));

        // Difficult to find affected users, just purge all completion cache.
        cache::make('core', 'completion')->purge();
        cache::make('core', 'coursecompletion')->purge();
    }

    /**
     * Deletes all activity and course completion data for an entire course
     * (the below delete_all_state function does this for a single activity).
     *
     * Used by course reset page.
     */
    public function delete_all_completion_data() {
        global $DB;

        // Delete from database.
        $DB->delete_records_select('course_modules_completion',
                'coursemoduleid IN (SELECT id FROM {course_modules} WHERE course=?)',
                array($this->course_id));

        // Wipe course completion data too.
        $this->delete_course_completion_data();
    }

    /**
     * Deletes completion state related to an activity for all users.
     *
     * Intended for use only when the activity itself is deleted.
     *
     * @param stdClass|cm_info $cm Activity
     */
    public function delete_all_state($cm) {
        global $DB;

        // Delete from database
        $DB->delete_records('course_modules_completion', array('coursemoduleid'=>$cm->id));

        // Check if there is an associated course completion criteria
        $criteria = $this->get_criteria(COMPLETION_CRITERIA_TYPE_ACTIVITY);
        $acriteria = false;
        foreach ($criteria as $criterion) {
            if ($criterion->moduleinstance == $cm->id) {
                $acriteria = $criterion;
                break;
            }
        }

        if ($acriteria) {
            // Delete all criteria completions relating to this activity
            $DB->delete_records('course_completion_crit_compl', array('course' => $this->course_id, 'criteriaid' => $acriteria->id));
            $DB->delete_records('course_completions', array('course' => $this->course_id));
        }

        // Difficult to find affected users, just purge all completion cache.
        cache::make('core', 'completion')->purge();
        cache::make('core', 'coursecompletion')->purge();
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
     * @param stcClass|cm_info $cm Activity
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

        // Delete all existing state.
        $this->delete_all_state($cm);

        // Merge this with list of planned users (according to roles)
        $trackedusers = $this->get_tracked_users();
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
     * completion cache if available, or by SQL query)
     *
     * @param stcClass|cm_info $cm Activity; only required field is ->id
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
    public function get_data($cm, $wholecourse = false, $userid = 0, $modinfo = null) {
        global $USER, $CFG, $DB;
        $completioncache = cache::make('core', 'completion');

        // Get user ID
        if (!$userid) {
            $userid = $USER->id;
        }

        // See if requested data is present in cache (use cache for current user only).
        $usecache = $userid == $USER->id;
        $cacheddata = array();
        if ($usecache) {
            $key = $userid . '_' . $this->course->id;
            if (!isset($this->course->cacherev)) {
                $this->course = get_course($this->course_id);
            }
            if ($cacheddata = $completioncache->get($key)) {
                if ($cacheddata['cacherev'] != $this->course->cacherev) {
                    // Course structure has been changed since the last caching, forget the cache.
                    $cacheddata = array();
                } else if (isset($cacheddata[$cm->id])) {
                    return (object)$cacheddata[$cm->id];
                }
            }
        }

        // Not there, get via SQL
        if ($usecache && $wholecourse) {
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
            foreach ($alldatabycmc as $data) {
                $alldata[$data->coursemoduleid] = (array)$data;
            }

            // Get the module info and build up condition info for each one
            if (empty($modinfo)) {
                $modinfo = get_fast_modinfo($this->course, $userid);
            }
            foreach ($modinfo->cms as $othercm) {
                if (isset($alldata[$othercm->id])) {
                    $data = $alldata[$othercm->id];
                } else {
                    // Row not present counts as 'not complete'
                    $data = array();
                    $data['id'] = 0;
                    $data['coursemoduleid'] = $othercm->id;
                    $data['userid'] = $userid;
                    $data['completionstate'] = 0;
                    $data['viewed'] = 0;
                    $data['overrideby'] = null;
                    $data['timemodified'] = 0;
                }
                $cacheddata[$othercm->id] = $data;
            }

            if (!isset($cacheddata[$cm->id])) {
                $this->internal_systemerror("Unexpected error: course-module {$cm->id} could not be found on course {$this->course->id}");
            }

        } else {
            // Get single record
            $data = $DB->get_record('course_modules_completion', array('coursemoduleid'=>$cm->id, 'userid'=>$userid));
            if ($data) {
                $data = (array)$data;
            } else {
                // Row not present counts as 'not complete'
                $data = array();
                $data['id'] = 0;
                $data['coursemoduleid'] = $cm->id;
                $data['userid'] = $userid;
                $data['completionstate'] = 0;
                $data['viewed'] = 0;
                $data['overrideby'] = null;
                $data['timemodified'] = 0;
            }

            // Put in cache
            $cacheddata[$cm->id] = $data;
        }

        if ($usecache) {
            $cacheddata['cacherev'] = $this->course->cacherev;
            $completioncache->set($key, $cacheddata);
        }
        return (object)$cacheddata[$cm->id];
    }

    /**
     * Updates completion data for a particular coursemodule and user (user is
     * determined from $data).
     *
     * (Internal function. Not private, so we can unit-test it.)
     *
     * @param stdClass|cm_info $cm Activity
     * @param stdClass $data Data about completion for that user
     */
    public function internal_set_data($cm, $data) {
        global $USER, $DB;

        $transaction = $DB->start_delegated_transaction();
        if (!$data->id) {
            // Check there isn't really a row
            $data->id = $DB->get_field('course_modules_completion', 'id',
                    array('coursemoduleid'=>$data->coursemoduleid, 'userid'=>$data->userid));
        }
        if (!$data->id) {
            // Didn't exist before, needs creating
            $data->id = $DB->insert_record('course_modules_completion', $data);
        } else {
            // Has real (nonzero) id meaning that a database row exists, update
            $DB->update_record('course_modules_completion', $data);
        }
        $transaction->allow_commit();

        $cmcontext = context_module::instance($data->coursemoduleid, MUST_EXIST);
        $coursecontext = $cmcontext->get_parent_context();

        $completioncache = cache::make('core', 'completion');
        if ($data->userid == $USER->id) {
            // Update module completion in user's cache.
            if (!($cachedata = $completioncache->get($data->userid . '_' . $cm->course))
                    || $cachedata['cacherev'] != $this->course->cacherev) {
                $cachedata = array('cacherev' => $this->course->cacherev);
            }
            $cachedata[$cm->id] = $data;
            $completioncache->set($data->userid . '_' . $cm->course, $cachedata);

            // reset modinfo for user (no need to call rebuild_course_cache())
            get_fast_modinfo($cm->course, 0, true);
        } else {
            // Remove another user's completion cache for this course.
            $completioncache->delete($data->userid . '_' . $cm->course);
        }

        // Trigger an event for course module completion changed.
        $event = \core\event\course_module_completion_updated::create(array(
            'objectid' => $data->id,
            'context' => $cmcontext,
            'relateduserid' => $data->userid,
            'other' => array(
                'relateduserid' => $data->userid,
                'overrideby' => $data->overrideby,
                'completionstate' => $data->completionstate
            )
        ));
        $event->add_record_snapshot('course_modules_completion', $data);
        $event->trigger();
    }

     /**
     * Return whether or not the course has activities with completion enabled.
     *
     * @return boolean true when there is at least one activity with completion enabled.
     */
    public function has_activities() {
        $modinfo = get_fast_modinfo($this->course);
        foreach ($modinfo->get_cms() as $cm) {
            if ($cm->completion != COMPLETION_TRACKING_NONE) {
                return true;
            }
        }
        return false;
    }

    /**
     * Obtains a list of activities for which completion is enabled on the
     * course. The list is ordered by the section order of those activities.
     *
     * @return cm_info[] Array from $cmid => $cm of all activities with completion enabled,
     *   empty array if none
     */
    public function get_activities() {
        $modinfo = get_fast_modinfo($this->course);
        $result = array();
        foreach ($modinfo->get_cms() as $cm) {
            if ($cm->completion != COMPLETION_TRACKING_NONE && !$cm->deletioninprogress) {
                $result[$cm->id] = $cm;
            }
        }
        return $result;
    }

    /**
     * Checks to see if the userid supplied has a tracked role in
     * this course
     *
     * @param int $userid User id
     * @return bool
     */
    public function is_tracked_user($userid) {
        return is_enrolled(context_course::instance($this->course->id), $userid, 'moodle/course:isincompletionreports', true);
    }

    /**
     * Returns the number of users whose progress is tracked in this course.
     *
     * Optionally supply a search's where clause, or a group id.
     *
     * @param string $where Where clause sql (use 'u.whatever' for user table fields)
     * @param array $whereparams Where clause params
     * @param int $groupid Group id
     * @return int Number of tracked users
     */
    public function get_num_tracked_users($where = '', $whereparams = array(), $groupid = 0) {
        global $DB;

        list($enrolledsql, $enrolledparams) = get_enrolled_sql(
                context_course::instance($this->course->id), 'moodle/course:isincompletionreports', $groupid, true);
        $sql  = 'SELECT COUNT(eu.id) FROM (' . $enrolledsql . ') eu JOIN {user} u ON u.id = eu.id';
        if ($where) {
            $sql .= " WHERE $where";
        }

        $params = array_merge($enrolledparams, $whereparams);
        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Return array of users whose progress is tracked in this course.
     *
     * Optionally supply a search's where clause, group id, sorting, paging.
     *
     * @param string $where Where clause sql, referring to 'u.' fields (optional)
     * @param array $whereparams Where clause params (optional)
     * @param int $groupid Group ID to restrict to (optional)
     * @param string $sort Order by clause (optional)
     * @param int $limitfrom Result start (optional)
     * @param int $limitnum Result max size (optional)
     * @param context $extracontext If set, includes extra user information fields
     *   as appropriate to display for current user in this context
     * @return array Array of user objects with standard user fields
     */
    public function get_tracked_users($where = '', $whereparams = array(), $groupid = 0,
             $sort = '', $limitfrom = '', $limitnum = '', context $extracontext = null) {

        global $DB;

        list($enrolledsql, $params) = get_enrolled_sql(
                context_course::instance($this->course->id),
                'moodle/course:isincompletionreports', $groupid, true);

        $allusernames = get_all_user_name_fields(true, 'u');
        $sql = 'SELECT u.id, u.idnumber, ' . $allusernames;
        if ($extracontext) {
            $sql .= get_extra_user_fields_sql($extracontext, 'u', '', array('idnumber'));
        }
        $sql .= ' FROM (' . $enrolledsql . ') eu JOIN {user} u ON u.id = eu.id';

        if ($where) {
            $sql .= " AND $where";
            $params = array_merge($params, $whereparams);
        }

        if ($sort) {
            $sql .= " ORDER BY $sort";
        }

        return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
    }

    /**
     * Obtains progress information across a course for all users on that course, or
     * for all users in a specific group. Intended for use when displaying progress.
     *
     * This includes only users who, in course context, have one of the roles for
     * which progress is tracked (the gradebookroles admin option) and are enrolled in course.
     *
     * Users are included (in the first array) even if they do not have
     * completion progress for any course-module.
     *
     * @param bool $sortfirstname If true, sort by first name, otherwise sort by
     *   last name
     * @param string $where Where clause sql (optional)
     * @param array $where_params Where clause params (optional)
     * @param int $groupid Group ID or 0 (default)/false for all groups
     * @param int $pagesize Number of users to actually return (optional)
     * @param int $start User to start at if paging (optional)
     * @param context $extracontext If set, includes extra user information fields
     *   as appropriate to display for current user in this context
     * @return stdClass with ->total and ->start (same as $start) and ->users;
     *   an array of user objects (like mdl_user id, firstname, lastname)
     *   containing an additional ->progress array of coursemoduleid => completionstate
     */
    public function get_progress_all($where = '', $where_params = array(), $groupid = 0,
            $sort = '', $pagesize = '', $start = '', context $extracontext = null) {
        global $CFG, $DB;

        // Get list of applicable users
        $users = $this->get_tracked_users($where, $where_params, $groupid, $sort,
                $start, $pagesize, $extracontext);

        // Get progress information for these users in groups of 1, 000 (if needed)
        // to avoid making the SQL IN too long
        $results = array();
        $userids = array();
        foreach ($users as $user) {
            $userids[] = $user->id;
            $results[$user->id] = $user;
            $results[$user->id]->progress = array();
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
                    cm.course=? AND cmc.userid $insql", $params);
            foreach ($rs as $progress) {
                $progress = (object)$progress;
                $results[$progress->userid]->progress[$progress->coursemoduleid] = $progress;
            }
            $rs->close();
        }

        return $results;
    }

    /**
     * Called by grade code to inform the completion system when a grade has
     * been changed. If the changed grade is used to determine completion for
     * the course-module, then the completion status will be updated.
     *
     * @param stdClass|cm_info $cm Course-module for item that owns grade
     * @param grade_item $item Grade item
     * @param stdClass $grade
     * @param bool $deleted
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
            $possibleresult = self::internal_get_grade_state($item, $grade);
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
     * @param grade_item $item an instance of grade_item
     * @param grade_grade $grade an instance of grade_grade
     * @return int Completion state e.g. COMPLETION_INCOMPLETE
     */
    public static function internal_get_grade_state($item, $grade) {
        // If no grade is supplied or the grade doesn't have an actual value, then
        // this is not complete.
        if (!$grade || (is_null($grade->finalgrade) && is_null($grade->rawgrade))) {
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
            // Not displaying pass/fail, so just if there is a grade
            if (!is_null($grade->finalgrade) || !is_null($grade->rawgrade)) {
                // Grade exists, so maybe complete now
                return COMPLETION_COMPLETE;
            } else {
                // Grade does not exist, so maybe incomplete now
                return COMPLETION_INCOMPLETE;
            }
        }
    }

    /**
     * Aggregate activity completion state
     *
     * @param   int     $type   Aggregation type (COMPLETION_* constant)
     * @param   bool    $old    Old state
     * @param   bool    $new    New state
     * @return  bool
     */
    public static function aggregate_completion_states($type, $old, $new) {
        if ($type == COMPLETION_AND) {
            return $old && $new;
        } else {
            return $old || $new;
        }
    }

    /**
     * This is to be used only for system errors (things that shouldn't happen)
     * and not user-level errors.
     *
     * @global type $CFG
     * @param string $error Error string (will not be displayed to user unless debugging is enabled)
     * @throws moodle_exception Exception with the error string as debug info
     */
    public function internal_systemerror($error) {
        global $CFG;
        throw new moodle_exception('err_system','completion',
            $CFG->wwwroot.'/course/view.php?id='.$this->course->id,null,$error);
    }
}

/**
 * Aggregate criteria status's as per configured aggregation method.
 *
 * @param int $method COMPLETION_AGGREGATION_* constant.
 * @param bool $data Criteria completion status.
 * @param bool|null $state Aggregation state.
 */
function completion_cron_aggregate($method, $data, &$state) {
    if ($method == COMPLETION_AGGREGATION_ALL) {
        if ($data && $state !== false) {
            $state = true;
        } else {
            $state = false;
        }
    } else if ($method == COMPLETION_AGGREGATION_ANY) {
        if ($data) {
            $state = true;
        } else if (!$data && $state === null) {
            $state = false;
        }
    }
}
