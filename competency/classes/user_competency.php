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
 * Class for user_competency persistence.
 *
 * @package    core_competency
 * @copyright  2015 Serge Gauthier
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use context_course;
use context_user;
use comment;
use lang_string;

/**
 * Class for loading/storing user_competency from the DB.
 *
 * @copyright  2015 Serge Gauthier
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency extends persistent {

    /** Table name for user_competency persistency */
    const TABLE = 'competency_usercomp';

    /** Idle status */
    const STATUS_IDLE = 0;

    /** Waiting for review status */
    const STATUS_WAITING_FOR_REVIEW = 1;

    /** In review status */
    const STATUS_IN_REVIEW = 2;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'userid' => array(
                'type' => PARAM_INT,
            ),
            'competencyid' => array(
                'type' => PARAM_INT,
            ),
            'status' => array(
                'choices' => array(
                    self::STATUS_IDLE,
                    self::STATUS_WAITING_FOR_REVIEW,
                    self::STATUS_IN_REVIEW,
                ),
                'type' => PARAM_INT,
                'default' => self::STATUS_IDLE,
            ),
            'reviewerid' => array(
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'proficiency' => array(
                'type' => PARAM_BOOL,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'grade' => array(
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
        );
    }

    /**
     * Whether the current user can comment on this user competency.
     *
     * @return bool
     */
    public function can_comment() {
        return static::can_comment_user($this->get('userid'));
    }

    /**
     * Whether the current user can read this user competency.
     *
     * @return bool
     */
    public function can_read() {
        return static::can_read_user($this->get('userid'));
    }

    /**
     * Whether the current user can read comments on this user competency.
     *
     * @return bool
     */
    public function can_read_comments() {
        return static::can_read_comments_user($this->get('userid'));
    }

    /**
     * Can the current user send the user competency for review?
     *
     * @return bool
     */
    public function can_request_review() {
        return static::can_request_review_user($this->get('userid'));
    }

    /**
     * Can the current user review the user competency?
     *
     * @return bool
     */
    public function can_review() {
        return static::can_review_user($this->get('userid'));
    }

    /**
     * Human readable status name.
     *
     * @param int $status The status code.
     * @return lang_string
     */
    public static function get_status_name($status) {

        switch ($status) {
            case self::STATUS_IDLE:
                $strname = 'idle';
                break;
            case self::STATUS_WAITING_FOR_REVIEW:
                $strname = 'waitingforreview';
                break;
            case self::STATUS_IN_REVIEW:
                $strname = 'inreview';
                break;
            default:
                throw new \moodle_exception('errorusercomptencystatus', 'core_competency', '', $status);
                break;
        }

        return new lang_string('usercompetencystatus_' . $strname, 'core_competency');
    }

    /**
     * Get list of competency status.
     *
     * @return array
     */
    public static function get_status_list() {

        static $list = null;

        if ($list === null) {
            $list = array(
                self::STATUS_IDLE => self::get_status_name(self::STATUS_IDLE),
                self::STATUS_WAITING_FOR_REVIEW => self::get_status_name(self::STATUS_WAITING_FOR_REVIEW),
                self::STATUS_IN_REVIEW => self::get_status_name(self::STATUS_IN_REVIEW));
        }

        return $list;
    }

    /**
     * Get the comment object.
     *
     * @return comment
     */
    public function get_comment_object() {
        global $CFG;
        require_once($CFG->dirroot . '/comment/lib.php');

        if (!$this->get('id')) {
            throw new coding_exception('The user competency record must exist.');
        }

        $comment = new comment((object) array(
            'context' => $this->get_context(),
            'component' => 'competency',    // This cannot be named 'core_competency'.
            'itemid' => $this->get('id'),
            'area' => 'user_competency',
            'showcount' => true,
        ));
        $comment->set_fullwidth(true);
        return $comment;
    }

    /**
     * Return the competency Object.
     *
     * @return competency Competency Object
     */
    public function get_competency() {
        return new competency($this->get('competencyid'));
    }

    /**
     * Get the context.
     *
     * @return \context The context.
     */
    public function get_context() {
        return context_user::instance($this->get('userid'));
    }

    /**
     * Find the plans for the user and this competency.
     *
     * Note that this:
     * - does not perform any capability check.
     * - may return completed plans.
     * - may return an empty array.
     *
     * @return plans[]
     */
    public function get_plans() {
        return plan::get_by_user_and_competency($this->get('userid'), $this->get('competencyid'));
    }

    /**
     * Validate the user ID.
     *
     * @param int $value The value.
     * @return true|lang_string
     */
    protected function validate_userid($value) {
        global $DB;

        if (!$DB->record_exists('user', array('id' => $value))) {
            return new lang_string('invaliduserid', 'error');
        }

        return true;
    }

    /**
     * Validate the competency ID.
     *
     * @param int $value The value.
     * @return true|lang_string
     */
    protected function validate_competencyid($value) {
        if (!competency::record_exists($value)) {
            return new lang_string('errornocompetency', 'core_competency', $value);
        }

        return true;
    }

    /**
     * Validate the proficiency.
     *
     * @param int $value The value.
     * @return true|lang_string
     */
    protected function validate_proficiency($value) {
        $grade = $this->get('grade');

        if ($grade !== null && $value === null) {
            // We must set a proficiency when we set a grade.
            return new lang_string('invaliddata', 'error');

        } else if ($grade === null && $value !== null) {
            // We must not set a proficiency when we don't set a grade.
            return new lang_string('invaliddata', 'error');
        }

        return true;
    }

    /**
     * Validate the reviewer ID.
     *
     * @param int $value The value.
     * @return true|lang_string
     */
    protected function validate_reviewerid($value) {
        global $DB;

        if ($value !== null && !$DB->record_exists('user', array('id' => $value))) {
            return new lang_string('invaliduserid', 'error');
        }

        return true;
    }

    /**
     * Validate the grade.
     *
     * @param int $value The value.
     * @return true|lang_string
     */
    protected function validate_grade($value) {
        if ($value !== null) {
            if ($value <= 0) {
                return new lang_string('invalidgrade', 'core_competency');
            }

            // TODO MDL-52243 Use a core method to validate the grade_scale item.
            // Check if grade exist in the scale item values.
            $competency = $this->get_competency();
            if (!array_key_exists($value - 1 , $competency->get_scale()->scale_items)) {
                return new lang_string('invalidgrade', 'core_competency');
            }
        }

        return true;
    }

    /**
     * Can the current user comment on a user's competency?
     *
     * @param int $userid The user ID the competency belongs to.
     * @return bool
     */
    public static function can_comment_user($userid) {
        global $USER;

        $capabilities = array('moodle/competency:usercompetencycomment');
        if ($USER->id == $userid) {
            $capabilities[] = 'moodle/competency:usercompetencycommentown';
        }

        if (has_any_capability($capabilities, context_user::instance($userid))) {
            return true;
        }

        return false;
    }

    /**
     * Can the current user grade a user's user competency?
     *
     * @param int $userid The user ID the competency belongs to.
     * @return bool
     */
    public static function can_grade_user($userid) {
        $ratecap = 'moodle/competency:competencygrade';
        return has_capability($ratecap, context_user::instance($userid));
    }

    /**
     * Can the current user grade a user's user competency in a course?
     *
     * @param int $userid The user ID the competency belongs to.
     * @param int $courseid The course ID.
     * @return bool
     */
    public static function can_grade_user_in_course($userid, $courseid) {
        $ratecap = 'moodle/competency:competencygrade';
        return has_capability($ratecap, context_course::instance($courseid))
            || static::can_grade_user($userid);
    }

    /**
     * Can the current user read the comments on a user's competency?
     *
     * @param int $userid The user ID the competency belongs to.
     * @return bool
     */
    public static function can_read_comments_user($userid) {
        // Everyone who can read the user competency can read the comments.
        return static::can_read_user($userid);
    }

    /**
     * Can the current user read the user competencies of a user in a course?
     *
     * @param int $userid The user ID the competency belongs to.
     * @param int $courseid The course ID.
     * @return bool
     */
    public static function can_read_user_in_course($userid, $courseid) {
        $capability = 'moodle/competency:usercompetencyview';
        return has_capability($capability, context_course::instance($courseid))
            || static::can_read_user($userid);
    }

    /**
     * Can the current user read a user's competency?
     *
     * @param int $userid The user ID the competency belongs to.
     * @return bool
     */
    public static function can_read_user($userid) {
        $capability = 'moodle/competency:usercompetencyview';
        return has_capability($capability, context_user::instance($userid))
            || plan::can_read_user($userid);
    }

    /**
     * Can the current user send a user's competency for review?
     *
     * Note that the status 'review' is not meant to be used for student to self-assess
     * themselves, then to ask the teacher to review their assessment. It is more intended
     * for a student to provide evidence of prior learning and request their review.
     *
     * @param int $userid The user ID the competency belongs to.
     * @return bool
     */
    public static function can_request_review_user($userid) {
        global $USER;

        $capabilities = array('moodle/competency:usercompetencyrequestreview');
        if ($USER->id == $userid) {
            $capabilities[] = 'moodle/competency:usercompetencyrequestreviewown';
        }

        if (has_any_capability($capabilities, context_user::instance($userid))) {
            return true;
        }

        return false;
    }

    /**
     * Can the current user review the user competency?
     *
     * @param int $userid The user ID the competency belongs to.
     * @return bool
     */
    public static function can_review_user($userid) {
        $capability = 'moodle/competency:usercompetencyreview';
        return has_capability($capability, context_user::instance($userid));
    }

    /**
     * Create a new user_competency object.
     *
     * Note, this is intended to be used to create a blank relation, for instance when
     * the record was not found in the database. This does not save the model.
     *
     * @param  int $userid The user ID.
     * @param  int $competencyid The competency ID.
     * @return \core_competency\user_competency
     */
    public static function create_relation($userid, $competencyid) {
        $relation = new user_competency(0, (object) array('userid' => $userid, 'competencyid' => $competencyid));
        return $relation;
    }

    /**
     * Fetch a competency by user competency ID.
     *
     * This is a convenience method to attempt to efficiently fetch a competency when
     * the only information we have is the user_competency ID, in evidence for instance.
     *
     * @param  int $id The user competency ID.
     * @return competency
     */
    public static function get_competency_by_usercompetencyid($id) {
        global $DB;
        $sql = "SELECT c.*
                  FROM {" . self::TABLE . "} uc
                  JOIN {" . competency::TABLE . "} c
                    ON c.id = uc.competencyid
                 WHERE uc.id = ?";
        $record = $DB->get_record_sql($sql, array($id), MUST_EXIST);
        return new competency(0, $record);
    }

    /**
     * Get multiple user_competency for a user.
     *
     * @param  int $userid
     * @param  array  $competenciesorids Limit search to those competencies, or competency IDs.
     * @return \core_competency\user_competency[]
     */
    public static function get_multiple($userid, array $competenciesorids = null) {
        global $DB;

        $params = array();
        $params['userid'] = $userid;
        $sql = '1 = 1';

        if (!empty($competenciesorids)) {
            $test = reset($competenciesorids);
            if (is_number($test)) {
                $ids = $competenciesorids;
            } else {
                $ids = array();
                foreach ($competenciesorids as $comp) {
                    $ids[] = $comp->get('id');
                }
            }

            list($insql, $inparams) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
            $params += $inparams;
            $sql = "competencyid $insql";
        }

        // Order by ID to prevent random ordering.
        return self::get_records_select("userid = :userid AND $sql", $params, 'id ASC');
    }

    /**
     * Checks if a competency has user competency records.
     *
     * @param  int $competencyid The competency ID
     * @return boolean
     */
    public static function has_records_for_competency($competencyid) {
        return self::record_exists_select('competencyid = ?', array($competencyid));
    }

    /**
     * Checks if any of the competencies of a framework has a user competency record.
     *
     * @param  int $frameworkid The competency framework ID.
     * @return boolean
     */
    public static function has_records_for_framework($frameworkid) {
        global $DB;

        $sql = "SELECT 'x'
                  FROM {" . self::TABLE . "} uc
                  JOIN {" . competency::TABLE . "} c
                    ON uc.competencyid = c.id
                 WHERE c.competencyframeworkid = ?";
        $params = array($frameworkid);

        return $DB->record_exists_sql($sql, $params);
    }

    /**
     * Check if user competency has records for competencies.
     *
     * @param array $competencyids The competencies ids.
     * @return boolean Return true if the delete was successfull.
     */
    public static function has_records_for_competencies($competencyids) {
        global $DB;
        list($insql, $params) = $DB->get_in_or_equal($competencyids, SQL_PARAMS_NAMED);
        return self::record_exists_select("competencyid $insql", $params);
    }

}
