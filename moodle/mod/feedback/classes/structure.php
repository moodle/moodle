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
 * Contains class mod_feedback_structure
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Stores and manipulates the structure of the feedback or template (items, pages, etc.)
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_feedback_structure {
    /** @var stdClass record from 'feedback' table.
     * Reliably has fields: id, course, timeopen, timeclose, anonymous, completionsubmit.
     * For full object or to access any other field use $this->get_feedback()
     */
    protected $feedback;
    /** @var cm_info */
    protected $cm;
    /** @var int course where the feedback is filled. For feedbacks that are NOT on the front page this is 0 */
    protected $courseid = 0;
    /** @var int */
    protected $templateid;
    /** @var array */
    protected $allitems;
    /** @var array */
    protected $allcourses;

    /**
     * Constructor
     *
     * @param stdClass $feedback feedback object, in case of the template
     *     this is the current feedback the template is accessed from
     * @param stdClass|cm_info $cm course module object corresponding to the $feedback
     *     (at least one of $feedback or $cm is required)
     * @param int $courseid current course (for site feedbacks only)
     * @param int $templateid template id if this class represents the template structure
     */
    public function __construct($feedback, $cm, $courseid = 0, $templateid = null) {
        if ((empty($feedback->id) || empty($feedback->course)) && (empty($cm->instance) || empty($cm->course))) {
            throw new coding_exception('Either $feedback or $cm must be passed to constructor');
        }
        $this->feedback = $feedback ?: (object)['id' => $cm->instance, 'course' => $cm->course];
        $this->cm = ($cm && $cm instanceof cm_info) ? $cm :
            get_fast_modinfo($this->feedback->course)->instances['feedback'][$this->feedback->id];
        $this->templateid = $templateid;
        $this->courseid = ($this->feedback->course == SITEID) ? $courseid : 0;

        if (!$feedback) {
            // If feedback object was not specified, populate object with fields required for the most of methods.
            // These fields were added to course module cache in feedback_get_coursemodule_info().
            // Full instance record can be retrieved by calling mod_feedback_structure::get_feedback().
            $customdata = ($this->cm->customdata ?: []) + ['timeopen' => 0, 'timeclose' => 0, 'anonymous' => 0];
            $this->feedback->timeopen = $customdata['timeopen'];
            $this->feedback->timeclose = $customdata['timeclose'];
            $this->feedback->anonymous = $customdata['anonymous'];
            $this->feedback->completionsubmit = empty($this->cm->customdata['customcompletionrules']['completionsubmit']) ? 0 : 1;
        }
    }

    /**
     * Current feedback
     * @return stdClass
     */
    public function get_feedback() {
        global $DB;
        if (!isset($this->feedback->publish_stats) || !isset($this->feedback->name)) {
            // Make sure the full object is retrieved.
            $this->feedback = $DB->get_record('feedback', ['id' => $this->feedback->id], '*', MUST_EXIST);
        }
        return $this->feedback;
    }

    /**
     * Current course module
     * @return stdClass
     */
    public function get_cm() {
        return $this->cm;
    }

    /**
     * Id of the current course (for site feedbacks only)
     * @return stdClass
     */
    public function get_courseid() {
        return $this->courseid;
    }

    /**
     * Template id
     * @return int
     */
    public function get_templateid() {
        return $this->templateid;
    }

    /**
     * Is this feedback open (check timeopen and timeclose)
     * @return bool
     */
    public function is_open() {
        $checktime = time();
        return (!$this->feedback->timeopen || $this->feedback->timeopen <= $checktime) &&
            (!$this->feedback->timeclose || $this->feedback->timeclose >= $checktime);
    }

    /**
     * Get all items in this feedback or this template
     * @param bool $hasvalueonly only count items with a value.
     * @return array of objects from feedback_item with an additional attribute 'itemnr'
     */
    public function get_items($hasvalueonly = false) {
        global $DB;
        if ($this->allitems === null) {
            if ($this->templateid) {
                $this->allitems = $DB->get_records('feedback_item', ['template' => $this->templateid], 'position');
            } else {
                $this->allitems = $DB->get_records('feedback_item', ['feedback' => $this->feedback->id], 'position');
            }
            $idx = 1;
            foreach ($this->allitems as $id => $item) {
                $this->allitems[$id]->itemnr = $item->hasvalue ? ($idx++) : null;
            }
        }
        if ($hasvalueonly && $this->allitems) {
            return array_filter($this->allitems, function($item) {
                return $item->hasvalue;
            });
        }
        return $this->allitems;
    }

    /**
     * Is the items list empty?
     * @return bool
     */
    public function is_empty() {
        $items = $this->get_items();
        $displayeditems = array_filter($items, function($item) {
            return $item->typ !== 'pagebreak';
        });
        return !$displayeditems;
    }

    /**
     * Is this feedback anonymous?
     * @return bool
     */
    public function is_anonymous() {
        return $this->feedback->anonymous == FEEDBACK_ANONYMOUS_YES;
    }

    /**
     * Returns the formatted text of the page after submit or null if it is not set
     *
     * @return string|null
     */
    public function page_after_submit() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $pageaftersubmit = $this->get_feedback()->page_after_submit;
        if (empty($pageaftersubmit)) {
            return null;
        }
        $pageaftersubmitformat = $this->get_feedback()->page_after_submitformat;

        $context = context_module::instance($this->get_cm()->id);
        $output = file_rewrite_pluginfile_urls($pageaftersubmit,
                'pluginfile.php', $context->id, 'mod_feedback', 'page_after_submit', 0);

        return format_text($output, $pageaftersubmitformat, array('overflowdiv' => true));
    }

    /**
     * Checks if current user is able to view feedback on this course.
     *
     * @return bool
     */
    public function can_view_analysis() {
        $context = context_module::instance($this->cm->id);
        if (has_capability('mod/feedback:viewreports', $context)) {
            return true;
        }

        if (intval($this->get_feedback()->publish_stats) != 1 ||
                !has_capability('mod/feedback:viewanalysepage', $context)) {
            return false;
        }

        if (!isloggedin() || isguestuser()) {
            // There is no tracking for the guests, assume that they can view analysis if condition above is satisfied.
            return $this->feedback->course == SITEID;
        }

        return $this->is_already_submitted(true);
    }

    /**
     * check for multiple_submit = false.
     * if the feedback is global so the courseid must be given
     *
     * @param bool $anycourseid if true checks if this feedback was submitted in any course, otherwise checks $this->courseid .
     *     Applicable to frontpage feedbacks only
     * @return bool true if the feedback already is submitted otherwise false
     */
    public function is_already_submitted($anycourseid = false) {
        global $USER, $DB;

        if (!isloggedin() || isguestuser()) {
            return false;
        }

        $params = array('userid' => $USER->id, 'feedback' => $this->feedback->id);
        if (!$anycourseid && $this->courseid) {
            $params['courseid'] = $this->courseid;
        }
        return $DB->record_exists('feedback_completed', $params);
    }

    /**
     * Check whether the feedback is mapped to the given courseid.
     */
    public function check_course_is_mapped() {
        global $DB;
        if ($this->feedback->course != SITEID) {
            return true;
        }
        if ($DB->get_records('feedback_sitecourse_map', array('feedbackid' => $this->feedback->id))) {
            $params = array('feedbackid' => $this->feedback->id, 'courseid' => $this->courseid);
            if (!$DB->get_record('feedback_sitecourse_map', $params)) {
                return false;
            }
        }
        // No mapping means any course is mapped.
        return true;
    }

    /**
     * If there are any new responses to the anonymous feedback, re-shuffle all
     * responses and assign response number to each of them.
     */
    public function shuffle_anonym_responses() {
        global $DB;
        $params = array('feedback' => $this->feedback->id,
            'random_response' => 0,
            'anonymous_response' => FEEDBACK_ANONYMOUS_YES);

        if ($DB->count_records('feedback_completed', $params, 'random_response')) {
            // Get all of the anonymous records, go through them and assign a response id.
            unset($params['random_response']);
            $feedbackcompleteds = $DB->get_records('feedback_completed', $params, 'id');
            shuffle($feedbackcompleteds);
            $num = 1;
            foreach ($feedbackcompleteds as $compl) {
                $compl->random_response = $num++;
                $DB->update_record('feedback_completed', $compl);
            }
        }
    }

    /**
     * Counts records from {feedback_completed} table for a given feedback
     *
     * If $groupid or $this->courseid is set, the records are filtered by the group/course
     *
     * @param int $groupid
     * @return mixed array of found completeds otherwise false
     */
    public function count_completed_responses($groupid = 0) {
        global $DB;
        if (intval($groupid) > 0) {
            $query = "SELECT COUNT(DISTINCT fbc.id)
                        FROM {feedback_completed} fbc, {groups_members} gm
                        WHERE fbc.feedback = :feedback
                            AND gm.groupid = :groupid
                            AND fbc.userid = gm.userid";
        } else if ($this->courseid) {
            $query = "SELECT COUNT(fbc.id)
                        FROM {feedback_completed} fbc
                        WHERE fbc.feedback = :feedback
                            AND fbc.courseid = :courseid";
        } else {
            $query = "SELECT COUNT(fbc.id) FROM {feedback_completed} fbc WHERE fbc.feedback = :feedback";
        }
        $params = ['feedback' => $this->feedback->id, 'groupid' => $groupid, 'courseid' => $this->courseid];
        return $DB->get_field_sql($query, $params);
    }

    /**
     * For the frontpage feedback returns the list of courses with at least one completed feedback
     *
     * @return array id=>name pairs of courses
     */
    public function get_completed_courses() {
        global $DB;

        if ($this->get_feedback()->course != SITEID) {
            return [];
        }

        if ($this->allcourses !== null) {
            return $this->allcourses;
        }

        $courseselect = "SELECT fbc.courseid
            FROM {feedback_completed} fbc
            WHERE fbc.feedback = :feedbackid";

        $ctxselect = context_helper::get_preload_record_columns_sql('ctx');

        $sql = 'SELECT c.id, c.shortname, c.fullname, c.idnumber, c.visible, '. $ctxselect. '
                FROM {course} c
                JOIN {context} ctx ON c.id = ctx.instanceid AND ctx.contextlevel = :contextcourse
                WHERE c.id IN ('. $courseselect.') ORDER BY c.sortorder';
        $list = $DB->get_records_sql($sql, ['contextcourse' => CONTEXT_COURSE, 'feedbackid' => $this->get_feedback()->id]);

        $this->allcourses = array();
        foreach ($list as $course) {
            context_helper::preload_from_record($course);
            if (!$course->visible && !has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))) {
                // Do not return courses that current user can not see.
                continue;
            }
            $label = get_course_display_name_for_list($course);
            $this->allcourses[$course->id] = $label;
        }
        return $this->allcourses;
    }
}