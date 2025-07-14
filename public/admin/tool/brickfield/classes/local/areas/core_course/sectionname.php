<?php
// This file is part of the Query submission plugin
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

namespace tool_brickfield\local\areas\core_course;

use core\event\course_section_created;
use core\event\course_section_updated;
use tool_brickfield\area_base;

/**
 * Course section name observer.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sectionname extends area_base {

    /**
     * Get table name.
     * @return string
     */
    public function get_tablename(): string {
        return 'course_sections';
    }

    /**
     * Get field name.
     * @return string
     */
    public function get_fieldname(): string {
        return 'name';
    }

    /**
     * Get table name reference.
     * @return string
     */
    public function get_ref_tablename(): string {
        return 'course';
    }

    /**
     * Find recordset of the relevant areas.
     * @param \core\event\base $event
     * @return \moodle_recordset|null
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function find_relevant_areas(\core\event\base $event): ?\moodle_recordset {
        if ($event instanceof course_section_created) {
            return $this->find_fields_in_course_sections_table(['courseid' => $event->courseid, 'sectionid' => $event->objectid]);
        } else if ($event instanceof course_section_updated) {
            return $this->find_fields_in_course_sections_table(['courseid' => $event->courseid, 'sectionid' => $event->objectid]);
        }
        return null;
    }

    /**
     * Find recordset of the course areas.
     * @param int $courseid
     * @return \moodle_recordset
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function find_course_areas(int $courseid): ?\moodle_recordset {
        return $this->find_fields_in_course_sections_table(['courseid' => $courseid]);
    }

    /**
     * Helper method that can be used by the classes that define a field in the 'course_sections' table
     *
     * @param array $params
     * @return \moodle_recordset
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function find_fields_in_course_sections_table(array $params = []): \moodle_recordset {
        global $DB;
        $where = [];
        if (!empty($params['courseid'])) {
            $where[] = 't.course = :courseid';
        }
        if (!empty($params['sectionid'])) {
            $where[] = 't.id = :sectionid';
        }

        // Filter against approved / non-approved course category listings.
        $this->filterfieldname = 'co.id';
        $this->get_courseid_filtering();
        if ($this->filter != '') {
            $params = $params + $this->filterparams;
        }

        $rs = $DB->get_recordset_sql('SELECT
            ' . $this->get_type() . ' AS type,
            ctx.id AS contextid,
            ' . $this->get_standard_area_fields_sql() . '
            t.id AS itemid,
            ' . $this->get_reftable_field_sql() . '
            t.course AS refid,
            t.course AS courseid,
            t.'.$this->get_fieldname().' AS content
        FROM {'.$this->get_tablename().'} t
        JOIN {course} co ON co.id = t.course
        JOIN {context} ctx ON ctx.instanceid = co.id AND ctx.contextlevel = :pctxlevelcourse '.
            ($where ? 'WHERE ' . join(' AND ', $where) : '') . $this->filter . '
        ORDER BY t.course',
            ['pctxlevelcourse' => CONTEXT_COURSE] + $params);

        return $rs;
    }
}
