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
 * Class for plan_competency persistence.
 *
 * @package    core_competency
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use lang_string;

/**
 * Class for managing competencies in the plan (add/remove competencies for given plan).
 *
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan_competency extends persistent {

    /** Table name for plan_competency persistency */
    const TABLE = 'competency_plancomp';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'planid' => array(
                'type' => PARAM_INT,
            ),
            'competencyid' => array(
                'type' => PARAM_INT,
            ),
            'sortorder' => array(
                'type' => PARAM_INT,
                'default' => null,
            ),
        );
    }

    /**
     * List the competencies in this plan.
     *
     * @param int $planid The plan id
     * @return array[competency]
     */
    public static function list_competencies($planid) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} plancomp
                    ON plancomp.competencyid = comp.id
                 WHERE plancomp.planid = ?
              ORDER BY plancomp.sortorder ASC,
                       plancomp.id ASC';
        $params = array($planid);

        // TODO MDL-52229 Handle hidden competencies.
        $results = $DB->get_records_sql($sql, $params);

        $instances = array();
        foreach ($results as $result) {
            array_push($instances, new competency(0, $result));
        }

        return $instances;
    }

    /**
     * Get a single competency from the plan (only if it is really in the plan).
     *
     * @param int $planid The plan id
     * @param int $competencyid The competency id
     * @return competency
     */
    public static function get_competency($planid, $competencyid) {
        global $DB;

        $sql = 'SELECT comp.*
                  FROM {' . competency::TABLE . '} comp
                  JOIN {' . self::TABLE . '} plncomp
                    ON plncomp.competencyid = comp.id
                 WHERE plncomp.planid = ? AND plncomp.competencyid = ?';
        $params = array($planid, $competencyid);

        $result = $DB->get_record_sql($sql, $params);
        if (!$result) {
            throw new \coding_exception('The competency does not belong to this plan: ' . $competencyid . ', ' . $planid);
        }

        return new competency(0, $result);
    }

    /**
     * Hook to execute before validate.
     *
     * @return void
     */
    protected function before_validate() {
        if (($this->get('id') && $this->get('sortorder') === null) || !$this->get('id')) {
            $this->set('sortorder', $this->count_records(array('planid' => $this->get('planid'))));
        }
    }

    /**
     * Validate competencyid.
     *
     * @param  int $value ID.
     * @return true|lang_string
     */
    protected function validate_competencyid($value) {
        if (!competency::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Validate planid.
     *
     * @param  int $value ID.
     * @return true|lang_string
     */
    protected function validate_planid($value) {
        if (!plan::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Hook to execute after delete.
     *
     * @param bool $result Whether or not the delete was successful.
     * @return void
     */
    protected function after_delete($result) {
        global $DB;
        if (!$result) {
            return;
        }

        $table = '{' . self::TABLE . '}';
        $sql = "UPDATE $table SET sortorder = sortorder -1  WHERE planid = ? AND sortorder > ?";
        $DB->execute($sql, array($this->get('planid'), $this->get('sortorder')));
    }

    /**
     * Check if plan competency has records for competencies.
     *
     * @param array $competencyids The competences IDs
     * @return boolean
     */
    public static function has_records_for_competencies($competencyids) {
        global $DB;
        list($insql, $params) = $DB->get_in_or_equal($competencyids, SQL_PARAMS_NAMED);
        return self::record_exists_select("competencyid $insql", $params);
    }

}
