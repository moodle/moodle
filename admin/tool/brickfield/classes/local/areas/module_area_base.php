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

namespace tool_brickfield\local\areas;

use core\event\course_module_created;
use core\event\course_module_updated;
use tool_brickfield\area_base;

/**
 * Base class for all areas that represent a field from the module table (such as 'intro' or 'name')
 *
 * @package    tool_brickfield
 * @copyright  2020 Brickfield Education Labs https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class module_area_base extends area_base {

    /**
     * Find recordset of the relevant areas.
     * @param \core\event\base $event
     * @return \moodle_recordset|null
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function find_relevant_areas(\core\event\base $event): ?\moodle_recordset {
        if ($event instanceof course_module_updated || $event instanceof course_module_created) {
            if ($event->other['modulename'] === $this->get_tablename()) {
                return $this->find_fields_in_module_table(['itemid' => $event->other['instanceid']]);
            }
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
        return $this->find_fields_in_module_table(['courseid' => $courseid]);
    }

    /**
     * Helper method that can be used by the classes that define a field in the respective module table
     *
     * @param array $params
     * @return \moodle_recordset
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function find_fields_in_module_table(array $params = []): \moodle_recordset {
        global $DB;
        $where = [];
        if (!empty($params['itemid'])) {
            $where[] = 't.id = :itemid';
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
          t.id AS itemid,
          cm.id AS cmid,
          cm.course AS courseid,
          t.'.$this->get_fieldname().' AS content
        FROM {'.$this->get_tablename().'} t
        JOIN {course_modules} cm ON cm.instance = t.id
        JOIN {modules} m ON m.id = cm.module AND m.name = :ptablename2
        JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :pctxlevelmodule '.
            ($where ? 'WHERE ' . join(' AND ', $where) : '') . $this->filter . '
        ORDER BY t.id',
            ['pctxlevelmodule' => CONTEXT_MODULE,
                'ptablename2' => $this->get_tablename(),
            ] + $params);

        return $rs;
    }

    /**
     * Returns the moodle_url of the page to edit the error.
     * @param \stdClass $componentinfo
     * @return \moodle_url
     */
    public static function get_edit_url(\stdClass $componentinfo): \moodle_url {
        return new \moodle_url('/course/mod.php', ['update' => $componentinfo->cmid, 'sr' => 0, 'sesskey' => sesskey()]);
    }
}
