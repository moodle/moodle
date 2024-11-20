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

namespace tool_brickfield\local\areas\core_course;

use core\event\course_category_created;
use core\event\course_category_updated;
use tool_brickfield\area_base;

/**
 * Base class for all areas that represent a field from the course_categories table (such as 'intro' or 'name')
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class category_base extends area_base {

    /**
     * Find recordset of the relevant areas.
     * @param \core\event\base $event
     * @return \moodle_recordset|null
     * @throws \dml_exception
     */
    public function find_relevant_areas(\core\event\base $event): ?\moodle_recordset {
        if ($event instanceof course_category_updated || $event instanceof course_category_created) {
            return $this->find_fields_in_course_categories_table(['itemid' => $event->objectid]);
        }
        return null;
    }

    /**
     * Find recordset of the course areas.
     * @param int $courseid
     * @return \moodle_recordset|null
     */
    public function find_course_areas(int $courseid): ?\moodle_recordset {
        return null;
    }

    /**
     * Return an array of area objects that contain content at the site and system levels only. All category content is considered
     * system level.
     * @return mixed
     */
    public function find_system_areas(): ?\moodle_recordset {
        return $this->find_fields_in_course_categories_table();
    }

    /**
     * Helper method that can be used by the classes that define a field in the respective module table
     *
     * @param array $params
     * @return \moodle_recordset
     * @throws \dml_exception
     */
    protected function find_fields_in_course_categories_table(array $params = []): \moodle_recordset {
        global $DB;
        $where = [];
        if (!empty($params['itemid'])) {
            $where[] = 't.id = :itemid';
        }

        $rs = $DB->get_recordset_sql('SELECT
          ' . $this->get_type() . ' AS type,
          ctx.id AS contextid,
          ' . $this->get_standard_area_fields_sql() . '
          t.id AS itemid,
          t.id AS categoryid,
          t.'.$this->get_fieldname().' AS content
        FROM {'.$this->get_tablename().'} t
        JOIN {context} ctx ON ctx.instanceid = t.id AND ctx.contextlevel = :pctxlevelcategory '.
            ($where ? 'WHERE ' . join(' AND ', $where) : '') . '
        ORDER BY t.id',
            ['pctxlevelcategory' => CONTEXT_COURSECAT] + $params);

        return $rs;
    }
}
