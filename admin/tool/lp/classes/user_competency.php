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
 * @package    tool_lp
 * @copyright  2015 Serge Gauthier
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use lang_string;

/**
 * Class for loading/storing user_competency from the DB.
 *
 * @copyright  2015 Serge Gauthier
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency extends persistent {

    /** Table name for user_competency persistency */
    const TABLE = 'tool_lp_user_competency';

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
                throw new \moodle_exception('errorcomptencystatus', 'tool_lp', '', $status);
                break;
        }

        return new lang_string('usercompetencystatus_' . $strname, 'tool_lp');
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
            return new lang_string('errornocompetency', 'tool_lp', $value);
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
        if ($value !== null && $value <= 0) {
            return new lang_string('invalidgrade', 'tool_lp');
        }

        return true;
    }

    /**
     * Create a new user_competency object.
     *
     * Note, this is intended to be used to create a blank relation, for instance when
     * the record was not found in the database. This does not save the model.
     *
     * @param  int $userid The user ID.
     * @param  int $competencyid The competency ID.
     * @return \tool_lp\user_competency
     */
    public static function create_relation($userid, $competencyid) {
        $relation = new user_competency(0, (object) array('userid' => $userid, 'competencyid' => $competencyid));
        return $relation;
    }

    /**
     * Get multiple user_competency for a user.
     *
     * @param  int $userid
     * @param  array  $competenciesorids Limit search to those competencies, or competency IDs.
     * @return \tool_lp\user_competency[]
     */
    public static function get_multiple($userid, array $competenciesorids = null) {
        global $DB;

        $params = array();
        $params['userid'] = $userid;
        $sql = '1 = 1';

        if (!empty($competenciesorids)) {
            $test = reset($competenciesorids);
            if (is_int($test)) {
                $ids = $competenciesorids;
            } else {
                $ids = array();
                foreach ($competenciesorids as $comp) {
                    $ids[] = $comp->get_id();
                }
            }

            list($insql, $inparams) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
            $params += $inparams;
            $sql = "competencyid $insql";
        }

        return parent::get_records_select("userid = :userid AND $sql", $params);
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

}
