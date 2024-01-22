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

use core\event\course_created;
use core\event\course_updated;
use core\event\course_restored;
use tool_brickfield\area_base;

/**
 * Base class for various course-related areas
 *
 * This is an abstract class so it will be skipped by manager when it finds all areas
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base extends area_base {

    /**
     * Find recordset of the relevant areas.
     * @param \core\event\base $event
     * @return \moodle_recordset|null
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function find_relevant_areas(\core\event\base $event): ?\moodle_recordset {
        if ($event instanceof course_created) {
            return $this->find_fields_in_course_table(['courseid' => $event->courseid]);
        } else if ($event instanceof course_restored) {
            return $this->find_fields_in_course_table(['courseid' => $event->courseid]);
        } else if ($event instanceof course_updated) {
            return $this->find_fields_in_course_table(['courseid' => $event->courseid]);
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
        return $this->find_fields_in_course_table(['courseid' => $courseid]);
    }

    /**
     * Return an array of area objects that contain content at the site and system levels only.
     * @return mixed
     */
    public function find_system_areas(): ?\moodle_recordset {
        return null;
    }

    /**
     * Helper method that can be used by the classes that define a field in the 'course' table
     *
     * @param array $params
     * @return \moodle_recordset
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function find_fields_in_course_table(array $params = []): \moodle_recordset {
        global $DB;
        $where = [];

        if (!empty($params['courseid'])) {
            $where[] = 't.id = :courseid';
        }

        $rs = $DB->get_recordset_sql('SELECT
          ' . $this->get_type() . ' AS type,
          ctx.id AS contextid,
          ' . $this->get_standard_area_fields_sql() . '
          t.id AS itemid,
          t.id AS courseid,
          t.'.$this->get_fieldname().' AS content
        FROM {'.$this->get_tablename().'} t
        JOIN {context} ctx ON ctx.instanceid = t.id AND ctx.contextlevel = :pctxlevelcourse '.
            ($where ? 'WHERE ' . join(' AND ', $where) : '') . '
        ORDER BY t.id',
            ['pctxlevelcourse' => CONTEXT_COURSE] + $params);

        return $rs;
    }

    /**
     * Returns the moodle_url of the page to edit the error.
     * @param \stdClass $componentinfo
     * @return \moodle_url
     */
    public static function get_edit_url(\stdClass $componentinfo): \moodle_url {
        if ($componentinfo->tablename == 'course_sections') {
            return new \moodle_url('/course/editsection.php', ['id' => $componentinfo->itemid]);
        } else if ($componentinfo->tablename == 'course_categories') {
            return new \moodle_url('/course/editcategory.php', ['id' => $componentinfo->itemid]);
        } else {
            return new \moodle_url('/course/edit.php', ['id' => $componentinfo->courseid]);
        }
    }
}
