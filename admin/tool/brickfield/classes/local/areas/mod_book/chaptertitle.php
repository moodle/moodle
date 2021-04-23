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

namespace tool_brickfield\local\areas\mod_book;

use tool_brickfield\area_base;

/**
 * Book chapter title observer.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chaptertitle extends area_base {

    /**
     * Get table name.
     * @return string
     */
    public function get_tablename(): string {
        return 'book_chapters';
    }

    /**
     * Get field name.
     * @return string
     */
    public function get_fieldname(): string {
        return 'title';
    }

    /**
     * Get table name reference.
     * @return string
     */
    public function get_ref_tablename(): string {
        return 'book';
    }

    /**
     * Find recordset of the relevant areas.
     * @param \core\event\base $event
     * @return \moodle_recordset|null
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function find_relevant_areas(\core\event\base $event): ?\moodle_recordset {
        if ($event->eventname == '\mod_book\event\chapter_updated' || $event->eventname == '\mod_book\event\chapter_created') {
            return $this->find_areas(['contextid' => $event->contextid, 'objectid' => $event->objectid]);
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
        return $this->find_areas(['courseid' => $courseid]);
    }

    /**
     * Find recordset of areas.
     * @param array $params
     * @return \moodle_recordset
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function find_areas(array $params = []): \moodle_recordset {
        global $DB;

        $where = [];
        if (!empty($params['contextid'])) {
            $where[] = 'ctx.id = :contextid';
            $where[] = 'co.id = :objectid';
        }
        if (!empty($params['courseid'])) {
            $where[] = 'cm.course = :courseid';
        }

        // Filter against approved / non-approved course category listings.
        $this->filterfieldname = 'cm.course';
        $this->get_courseid_filtering();
        if ($this->filter != '') {
            $params = $params + $this->filterparams;
        }

        $rs = $DB->get_recordset_sql('SELECT
          ' . $this->get_type() . ' AS type,
          ctx.id AS contextid,
          ' . $this->get_standard_area_fields_sql() . '
          co.id AS itemid,
          ' . $this->get_reftable_field_sql() . '
          t.id AS refid,
          cm.id AS cmid,
          cm.course AS courseid,
          co.'.$this->get_fieldname().' AS content
        FROM {book} t
        JOIN {course_modules} cm ON cm.instance = t.id
        JOIN {modules} m ON m.id = cm.module AND m.name = :preftablename2
        JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :pctxlevelmodule
        JOIN {'.$this->get_tablename().'} co ON co.bookid = t.id ' .
        ($where ? 'WHERE ' . join(' AND ', $where) : '') . $this->filter . '
        ORDER BY t.id, co.id',
            ['pctxlevelmodule' => CONTEXT_MODULE,
                'preftablename2' => $this->get_ref_tablename(),
            ] + $params);

        return $rs;
    }
}
