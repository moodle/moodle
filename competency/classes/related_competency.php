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
 * Class for loading/storing related competencies from the DB.
 *
 * @package    core_competency
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use lang_string;
use stdClass;

/**
 * Class for loading/storing related_competencies from the DB.
 *
 * @package    core_competency
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class related_competency extends persistent {

    const TABLE = 'competency_relatedcomp';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'competencyid' => array(
                'type' => PARAM_INT
            ),
            'relatedcompetencyid' => array(
                'type' => PARAM_INT
            ),
        );
    }

    /**
     * Validate competency ID.
     *
     * @param int $data The competency ID.
     * @return true|lang_string
     */
    protected function validate_competencyid($data) {
        if (!competency::record_exists($data)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Validate related competency ID.
     *
     * @param int $data The related competency ID.
     * @return true|lang_string
     */
    protected function validate_relatedcompetencyid($data) {

        if ($this->get_competencyid() == $data) {
            // A competency cannot be related to itself.
            return new lang_string('invaliddata', 'error');

        } if ($this->get_competencyid() > $data) {
            // The competency ID must be lower than the related competency ID.
            return new lang_string('invaliddata', 'error');

        } else if (!competency::record_exists($data)) {
            return new lang_string('invaliddata', 'error');

        } else if (!competency::share_same_framework(array($data, $this->get_competencyid()))) {
            // The competencies must belong to the same framework.
            return new lang_string('invaliddata', 'error');
        }

        return true;
    }

    /**
     * Get relation specifying both competencies.
     *
     * This does not perform any validation on the data passed. If the relation exists in the database
     * then it is loaded in a the model, if not then it is up to the developer to save the model.
     *
     * @param int $competencyid
     * @param int $relatedcompetencyid
     * @return related_competency
     */
    public static function get_relation($competencyid, $relatedcompetencyid) {
        global $DB;

        // Lower id always as competencyid so we know which one is competencyid and which one relatedcompetencyid.
        $relation = new static();
        if ($competencyid > $relatedcompetencyid) {
            $relation->set_competencyid($relatedcompetencyid);
            $relation->set_relatedcompetencyid($competencyid);
        } else {
            $relation->set_competencyid($competencyid);
            $relation->set_relatedcompetencyid($relatedcompetencyid);
        }

        // We can do it because we have bidirectional relations in the DB.
        $params = array(
            'competencyid' => $relation->get_competencyid(),
            'relatedcompetencyid' => $relation->get_relatedcompetencyid()
        );
        if ($record = $DB->get_record(self::TABLE, $params)) {
            $relation->from_record($record);
        }

        return $relation;
    }

    /**
     * Get the competencies related to a competency.
     *
     * @param  int $competencyid The competency ID.
     * @return competency[]
     */
    public static function get_related_competencies($competencyid) {
        global $DB;

        $fields = competency::get_sql_fields('c', 'c_');
        $sql = "(SELECT $fields, " . $DB->sql_concat('rc.relatedcompetencyid', "'_'", 'rc.competencyid') . " AS rid
                   FROM {" . self::TABLE . "} rc
                   JOIN {" . competency::TABLE . "} c
                     ON c.id = rc.relatedcompetencyid
                  WHERE rc.competencyid = :cid)
              UNION ALL
                (SELECT $fields, " . $DB->sql_concat('rc.competencyid', "'_'", 'rc.relatedcompetencyid') . " AS rid
                   FROM {" . self::TABLE . "} rc
                   JOIN {" . competency::TABLE . "} c
                     ON c.id = rc.competencyid
                  WHERE rc.relatedcompetencyid = :cid2)
               ORDER BY c_path ASC, c_sortorder ASC";

        $competencies = array();
        $records = $DB->get_recordset_sql($sql, array('cid' => $competencyid, 'cid2' => $competencyid));
        foreach ($records as $record) {
            unset($record->rid);
            $competencies[$record->c_id] = new competency(null, competency::extract_record($record, 'c_'));
        }
        $records->close();

        return $competencies;
    }

    /**
     * Get the related competencies from competency ids.
     *
     * @param  int[] $competencyids Array of competency ids.
     * @return related_competency[]
     */
    public static function get_multiple_relations($competencyids) {
        global $DB;

        if (empty($competencyids)) {
            return array();
        }

        list($insql, $params) = $DB->get_in_or_equal($competencyids);

        $records = $DB->get_records_select(self::TABLE,
                                            "competencyid $insql OR relatedcompetencyid $insql",
                                            array_merge($params, $params)
                                            );

        $relatedcompetencies = array();
        foreach ($records as $record) {
            unset($record->id);
            $relatedcompetencies[] = new related_competency(null, $record);
        }
        return $relatedcompetencies;
    }

    /**
     * Delete relations using competencies.
     *
     * @param array $competencyids Array of competencies ids.
     * @return bool True if relations were deleted successfully.
     */
    public static function delete_multiple_relations($competencyids) {
        global $DB;
        if (empty($competencyids)) {
            return true;
        }

        list($insql, $params) = $DB->get_in_or_equal($competencyids);
        return $DB->delete_records_select(self::TABLE,
                                            "competencyid $insql OR relatedcompetencyid $insql",
                                            array_merge($params, $params)
                                            );
    }

}
