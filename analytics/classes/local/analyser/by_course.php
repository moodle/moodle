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
 * Abstract analyser in course basis.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\analyser;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract analyser in course basis.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class by_course extends base {

    /**
     * Return the list of courses to analyse.
     *
     * @param string|null $action 'prediction', 'training' or null if no specific action needed.
     * @return \Iterator
     */
    public function get_analysables_iterator(?string $action = null) {
        global $DB;

        list($sql, $params) = $this->get_iterator_sql('course', CONTEXT_COURSE, $action, 'c');

        // This will be updated to filter by context as part of MDL-64739.
        if (!empty($this->options['filter'])) {
            $courses = array();
            foreach ($this->options['filter'] as $courseid) {
                $courses[$courseid] = intval($courseid);
            }

            list($coursesql, $courseparams) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
            $sql .= " AND c.id $coursesql";
            $params = $params + $courseparams;
        }

        $ordersql = $this->order_sql('sortorder', 'ASC', 'c');

        $recordset = $DB->get_recordset_sql($sql . $ordersql, $params);

        if (!$recordset->valid()) {
            $this->add_log(get_string('nocourses', 'analytics'));
            return new \ArrayIterator([]);
        }

        return new \core\dml\recordset_walk($recordset, function($record) {

            if ($record->id == SITEID) {
                return false;
            }
            $context = \context_helper::preload_from_record($record);
            return \core_analytics\course::instance($record, $context);
        });
    }
}