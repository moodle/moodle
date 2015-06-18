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
 * @package    tool_lp
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use stdClass;

/**
 * Class for loading/storing related_competencies from the DB.
 *
 * @package    tool_lp
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class related_competency extends persistent {

    /** @var int $competencyid The competency id */
    private $competencyid = 0;

    /** @var int $relatedcompetencyid The related competency id */
    private $relatedcompetencyid = 0;

    /**
     * Method that provides the table name matching this class.
     *
     * @return string
     */
    public function get_table_name() {
        return 'tool_lp_related_competency';
    }

    /**
     * Get the competency id
     *
     * @return int The competency id
     */
    public function get_competencyid() {
        return $this->competencyid;
    }

    /**
     * Set the competency id
     *
     * @param int $competencyid The competency id
     */
    public function set_competencyid($competencyid) {
        $this->competencyid = $competencyid;
    }

    /**
     * Get the related competency id.
     *
     * @return int The related competency id.
     */
    public function get_relatedcompetencyid() {
        return $this->relatedcompetencyid;
    }

    /**
     * Set the related competency id.
     *
     * @param int $relatedcompetencyid The related competency id.
     */
    public function set_relatedcompetencyid($relatedcompetencyid) {
        $this->relatedcompetencyid = $relatedcompetencyid;
    }

    /**
     * Populate this class with data from a DB record.
     *
     * @param stdClass $record A DB record.
     * @return course_competency
     */
    public function from_record($record) {
        if (isset($record->id)) {
            $this->set_id($record->id);
        }
        if (isset($record->competencyid)) {
            $this->set_competencyid($record->competencyid);
        }
        if (isset($record->relatedcompetencyid)) {
            $this->set_relatedcompetencyid($record->relatedcompetencyid);
        }
        if (isset($record->timecreated)) {
            $this->set_timecreated($record->timecreated);
        }
        if (isset($record->usermodified)) {
            $this->set_usermodified($record->usermodified);
        }
        return $this;
    }

    /**
     * Create a DB record from this class.
     *
     * @return stdClass
     */
    public function to_record() {
        $record = new stdClass();
        $record->id = $this->get_id();
        $record->competencyid = $this->get_competencyid();
        $record->relatedcompetencyid = $this->get_relatedcompetencyid();
        $record->timecreated = $this->get_timecreated();
        $record->timemodified = $this->get_timemodified();
        $record->usermodified = $this->get_usermodified();

        return $record;
    }

    /**
     * Loads a single relation specifying both competencies.
     *
     * @param int $competencyid
     * @param int $relatedcompetencyid
     * @return bool
     */
    public function load_relation($competencyid, $relatedcompetencyid) {
        global $DB;

        // Lower id always as competencyid so we know which one is competencyid and which one relatedcompetencyid.
        if ($competencyid > $relatedcompetencyid) {
            $this->set_competencyid($relatedcompetencyid);
            $this->set_relatedcompetencyid($competencyid);
        } else {
            $this->set_competencyid($competencyid);
            $this->set_relatedcompetencyid($relatedcompetencyid);
        }

        // We can do it because we have bidirectional relations in the DB.
        $params = array('competencyid' => $this->get_competencyid(), 'relatedcompetencyid' => $this->get_relatedcompetencyid());
        if (!$record = $DB->get_record($this->get_table_name(), $params)) {
            return false;
        }
        $this->from_record($record);

        return true;
    }

    /**
     * Returns all related competencies.
     *
     * @param int $competencyid
     * @return related_competency[]
     */
    public function list_relations($competencyid) {
        $relatedcompetencies = $this->list_relations_many(array($competencyid));
        if (empty($relatedcompetencies) || empty($relatedcompetencies[$competencyid])) {
            return array();
        }

        return $relatedcompetencies[$competencyid];
    }

    /**
     * Returns related competencies Load relations accepting an array of competency ids.
     *
     * @param array $competencyids
     * @return array Indexed as array[competency][relatedcompetency] = \tool_lp\competency.
     */
    public function list_relations_many(array $competencyids) {
        global $DB;

        list($competencysql, $params) = $DB->get_in_or_equal($competencyids);
        $sql = "(SELECT " . $DB->sql_concat('rc.relatedcompetencyid', "'_'", 'rc.competencyid') . " AS rid,
                    rc.competencyid AS relatedcompetencyid, c.*
                    FROM {tool_lp_related_competency} rc
                    JOIN {tool_lp_competency} c
                    ON c.id = rc.relatedcompetencyid
                    WHERE rc.competencyid $competencysql)
                UNION ALL
                (SELECT " . $DB->sql_concat('rc.competencyid', "'_'", 'rc.relatedcompetencyid') . " AS rid,
                    rc.relatedcompetencyid AS relatedcompetencyid, c.*
                    FROM {tool_lp_related_competency} rc
                    JOIN {tool_lp_competency} c
                    ON c.id = rc.competencyid
                    WHERE rc.relatedcompetencyid $competencysql)
                ORDER BY path, sortorder ASC";
        $records = $DB->get_recordset_sql($sql, array_merge($params, $params));

        $relations = array();
        if ($records->valid()) {
            $competencyids = array_flip($competencyids);
            foreach ($records as $record) {
                $relatedid = $record->relatedcompetencyid;
                if (empty($relations[$relatedid])) {
                    $relations[$relatedid] = array();
                }
                unset($record->rid);
                unset($record->relatedcompetencyid);
                $relations[$relatedid][$record->id] = new competency(null, $record);
            }
        }
        $records->close();

        return $relations;
    }

    /**
     * Creates a relation between competencies.
     *
     * Overwriting the base class as we need to save it bidirectionally.
     *
     * @return related_competency
     */
    public function create() {

        // The lower id always competencyid.
        if ($this->competencyid > $this->relatedcompetencyid) {
            $competencyid = $this->competencyid;
            $this->competencyid = $this->relatedcompetencyid;
            $this->relatedcompetencyid = $competencyid;
        }
        return parent::create();
    }
}
