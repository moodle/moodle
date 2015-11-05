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
 * Class for user_competency_plan persistence.
 *
 * @package    tool_lp
 * @copyright  2015 Serge Gauthier
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use lang_string;

/**
 * Class for loading/storing user_competency_plan from the DB.
 *
 * @copyright  2015 Serge Gauthier
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency_plan extends persistent {

    /** Table name for user_competency_plan persistency */
    const TABLE = 'tool_lp_user_competency_plan';

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
            'planid' => array(
                'type' => PARAM_INT,
            ),
        );
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
     * Validate the plan ID.
     *
     * @param int $value The value.
     * @return true|lang_string
     */
    protected function validate_planid($value) {
        global $DB;

        if (!plan::record_exists($value) ) {
            return new lang_string('invalidplan', 'tool_lp');
        }

        return true;
    }

    /**
     * Create a new user_competency_plan object.
     *
     * Note, this is intended to be used to create a blank relation, for instance when
     * the record was not found in the database. This does not save the model.
     *
     * @param int $userid The user ID.
     * @param int $competencyid The competency ID.
     * @param int $planid The plan ID.
     * @return \tool_lp\user_competency_plan
     */
    public static function create_relation($userid, $competencyid, $planid) {
        $relation = new user_competency_plan(0, (object) array('userid' => $userid, 'competencyid' => $competencyid,
                'planid' => $planid));
        return $relation;
    }

    /**
     * Get multiple user_competency_plan for a user.
     *
     * @param int $userid The user ID.
     * @param int $planid The plan ID.
     * @param array  $competenciesorids Limit search to those competencies, or competency IDs.
     * @return \tool_lp\user_competency_plan[]
     */
    public static function get_multiple($userid, $planid, array $competenciesorids = null) {
        global $DB;

        $params = array();
        $params['userid'] = $userid;
        $params['planid'] = $planid;
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

        return static::get_records_select("userid = :userid AND planid = :planid AND $sql", $params);
    }

}
