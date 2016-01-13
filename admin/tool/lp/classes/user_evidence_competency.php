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
 * User evidence competency persistent.
 *
 * This represent the many to many relationship between evidence of prior
 * learning and competencies.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;
defined('MOODLE_INTERNAL') || die();

use stdClass;

/**
 * User evidence competency persistent class.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_evidence_competency extends persistent {

    const TABLE = 'tool_lp_user_evidence_comp';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'userevidenceid' => array(
                'type' => PARAM_INT
            ),
            'competencyid' => array(
                'type' => PARAM_INT,
            ),
        );
    }

    /**
     * Validate competency ID.
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
     * Validate user evidence ID.
     *
     * @param  int $value ID.
     * @return true|lang_string
     */
    protected function validate_userevidenceid($value) {
        if (!user_evidence::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Get competencies by user evidence ID.
     *
     * @param  int $userevidenceid The user evidence ID.
     * @return competency[]
     */
    public static function get_competencies_by_userevidenceid($userevidenceid) {
        global $DB;
        $sql = "SELECT c.*
                  FROM {" . self::TABLE . "} uec
                  JOIN {" . competency::TABLE . "} c
                    ON uec.userevidenceid = ?
                   AND uec.competencyid = c.id
              ORDER BY c.shortname";
        $competencies = array();
        $records = $DB->get_recordset_sql($sql, array($userevidenceid));
        foreach ($records as $record) {
            $competencies[] = new competency(0, $record);
        }
        $records->close();
        return $competencies;
    }

    /**
     * Get a relation.
     *
     * This does not perform any validation on the data passed. If the relation exists in the database
     * then it is loaded in a the model, if not then it is up to the developer to save the model.
     *
     * @param int $userevidenceid
     * @param int $competencyid
     * @return template_cohort
     */
    public static function get_relation($userevidenceid, $competencyid) {
        global $DB;

        $params = array(
            'userevidenceid' => $userevidenceid,
            'competencyid' => $competencyid
        );

        $relation = new static(null, (object) $params);
        if ($record = $DB->get_record(static::TABLE, $params)) {
            $relation->from_record($record);
        }

        return $relation;
    }

    /**
     * Delete evidences using competencies.
     * 
     * @param array $competencyids Array of competencies ids.
     * @return bool Return true if the delete was successful.
     */
    public static function delete_by_competencyids($competencyids) {
        global $DB;
        list($insql, $params) = $DB->get_in_or_equal($competencyids);
        return $DB->delete_records_select(self::TABLE, "competencyid $insql", $params);
    }

}
