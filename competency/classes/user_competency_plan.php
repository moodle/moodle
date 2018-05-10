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
 * @package    core_competency
 * @copyright  2015 Serge Gauthier
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use lang_string;
use context_user;

/**
 * Class for loading/storing user_competency_plan from the DB.
 *
 * @copyright  2015 Serge Gauthier
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_competency_plan extends persistent {

    /** Table name for user_competency_plan persistency */
    const TABLE = 'competency_usercompplan';

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
            'sortorder' => array(
                'type' => PARAM_INT,
                'default' => 0,
            ),
        );
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
     * @return context The context.
     */
    public function get_context() {
        return context_user::instance($this->get('userid'));
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
            if (!array_key_exists($value - 1, $competency->get_scale()->scale_items)) {
                return new lang_string('invalidgrade', 'core_competency');
            }
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
        if (!plan::record_exists($value) ) {
            return new lang_string('invalidplan', 'core_competency');
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
     * @return \core_competency\user_competency_plan
     */
    public static function create_relation($userid, $competencyid, $planid) {
        $relation = new user_competency_plan(0, (object) array('userid' => $userid, 'competencyid' => $competencyid,
                'planid' => $planid));
        return $relation;
    }

    /**
     * List the competencies in this plan.
     *
     * @param int $planid The plan ID
     * @param int $userid The user ID
     * @return competency[]
     */
    public static function list_competencies($planid, $userid) {
        global $DB;

        $sql = 'SELECT c.*
                  FROM {' . competency::TABLE . '} c
                  JOIN {' . self::TABLE . '} ucp
                    ON ucp.competencyid = c.id
                   AND ucp.userid = :userid
                 WHERE ucp.planid = :planid
              ORDER BY ucp.sortorder ASC,
                       ucp.id ASC';
        $params = array('userid' => $userid, 'planid' => $planid);

        // IOMAD.  Set up the user's companyid.
        if (!\iomad::has_capability('block/iomad_company_admin:company_view_all', \context_system::instance())) {
            $companyid = \iomad::get_my_companyid(\context_system::instance());
            $companyframeworks = \iomad::get_company_frameworkids($companyid);
            if (!empty($companyframeworks)) {
                $sql .= " AND c.competencyframeworkid IN (" . implode(',', array_keys($companytemplates)) . ")";
            } else {
                $sql .= " AND 1 = 2";
            }
        }

        $results = $DB->get_recordset_sql($sql, $params);
        $instances = array();
        foreach ($results as $key => $result) {
             $instances[$key] = new competency(0, $result);
        }
        $results->close();

        return $instances;
    }

    /**
     * Fetch a competency by plan ID.
     *
     * @param  int $id The plan ID.
     * @return competency
     */
    public static function get_competency_by_planid($planid, $competencyid) {
        global $DB;

        $sql = "SELECT c.*
                  FROM {" . self::TABLE . "} ucp
                  JOIN {" . competency::TABLE . "} c
                    ON c.id = ucp.competencyid
                 WHERE ucp.planid = ?
                   AND ucp.competencyid = ?";
        $record = $DB->get_record_sql($sql, array($planid, $competencyid));

        if (!$record) {
            throw new \coding_exception('The competency does not belong to this plan: ' . $competencyid . ', ' . $planid);
        }

        return new competency(0, $record);
    }

    /**
     * Get multiple user_competency_plan for a user.
     *
     * @param int $userid The user ID.
     * @param int $planid The plan ID.
     * @param array  $competenciesorids Limit search to those competencies, or competency IDs.
     * @return \core_competency\user_competency_plan[]
     */
    public static function get_multiple($userid, $planid, array $competenciesorids = null) {
        global $DB;

        $params = array();
        $params['userid'] = $userid;
        $params['planid'] = $planid;
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
        return static::get_records_select("userid = :userid AND planid = :planid AND $sql", $params, 'id ASC');
    }

    /**
     * Checks if a competency has user competency plan records.
     *
     * @param  int $competencyid The competency ID
     * @return boolean
     */
    public static function has_records_for_competency($competencyid) {
        return self::record_exists_select('competencyid = ?', array($competencyid));
    }

    /**
     * Checks if any of the competencies of a framework has a user competency plan record.
     *
     * @param  int $frameworkid The competency framework ID.
     * @return boolean
     */
    public static function has_records_for_framework($frameworkid) {
        global $DB;

        $sql = "SELECT 'x'
                  FROM {" . self::TABLE . "} ucp
                  JOIN {" . competency::TABLE . "} c
                    ON ucp.competencyid = c.id
                 WHERE c.competencyframeworkid = ?";
        $params = array($frameworkid);

        return $DB->record_exists_sql($sql, $params);
    }

    /**
     * Check if user competency plan has records for competencies.
     *
     * @param array $competencyids The competences IDs
     * @return boolean
     */
    public static function has_records_for_competencies($competencyids) {
        global $DB;
        list($insql, $params) = $DB->get_in_or_equal($competencyids, SQL_PARAMS_NAMED);
        return self::record_exists_select("competencyid $insql", $params);
    }

    /**
     * Count the number of records matching a specific template, optionally filtered by proficient values.
     *
     * @param int $templateid
     * @param mixed $proficiency - If true - filter by proficiency, if false filter by not proficient, if null - do not filter.
     * @return int
     */
    public static function count_records_for_template($templateid, $proficiency=null) {
        global $DB;

        $params = array('templateid' => $templateid);
        $sql = 'SELECT ' . " COUNT('x') " .
                 'FROM {' . self::TABLE . '} ucp
                  JOIN {' . plan::TABLE . '} p
                    ON ucp.planid = p.id
                 WHERE p.templateid = :templateid';
        if ($proficiency === true) {
            $sql .= ' AND ucp.proficiency = :proficiency';
            $params['proficiency'] = true;
        } else if ($proficiency === false) {
            $sql .= ' AND (ucp.proficiency = :proficiency OR ucp.proficiency IS NULL)';
            $params['proficiency'] = false;
        }

        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Get the list of competencies that were completed the least times (in completed plans) from a template.
     *
     * @param int $templateid
     * @param int $skip The number of competencies to skip
     * @param int $limit The max number of competencies to return
     * @return competency[]
     */
    public static function get_least_proficient_competencies_for_template($templateid, $skip = 0, $limit = 0) {
        global $DB;

        $fields = competency::get_sql_fields('c', 'c_');
        $params = array('templateid' => $templateid, 'notproficient' => false);
        $sql = 'SELECT ' . $fields . '
                  FROM (SELECT ucp.competencyid, COUNT(ucp.competencyid) AS timesnotproficient
                          FROM {' . self::TABLE . '} ucp
                          JOIN {' . plan::TABLE . '} p
                               ON p.id = ucp.planid
                         WHERE p.templateid = :templateid
                               AND (ucp.proficiency = :notproficient OR ucp.proficiency IS NULL)
                      GROUP BY ucp.competencyid
                     ) p
                  JOIN {' . competency::TABLE . '} c
                    ON c.id = p.competencyid
              ORDER BY p.timesnotproficient DESC, c.id ASC';

        $results = $DB->get_records_sql($sql, $params, $skip, $limit);

        $comps = array();
        foreach ($results as $r) {
            $c = competency::extract_record($r, 'c_');
            $comps[] = new competency(0, $c);
        }
        return $comps;
    }
}
