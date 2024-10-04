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
 * Completion test generator
 *
 * @package     core_completion
 * @copyright   2023 Amaia Anabitarte <amaia@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_completion_generator extends component_generator_base {

    /**
     * Create default completion
     *
     * @param array|stdClass $record
     * @return stdClass
     */
    public function create_default_completion($record): stdClass {
        global $DB;

        $record = (array) $record;

        if (!array_key_exists('course', $record) || !is_numeric($record['course'])) {
            throw new moodle_exception('courserequired');
        }
        if (!$DB->get_record('course', ['id' => $record['course']])) {
            throw new moodle_exception('invalidcourseid');
        }

        if (!array_key_exists('module', $record) || !is_numeric($record['module'])) {
            throw new moodle_exception('modulerequired');
        }
        if (!$DB->get_record('modules', ['id' => $record['module']])) {
            throw new moodle_exception('invalidmoduleid', 'error', '', $record['module']);
        }

        $record = (object) array_merge([
            'completion' => 0,
            'completionview' => 0,
            'completionusegrade' => 0,
            'completionpassgrade' => 0,
            'completionexpected' => 0,
            'customrules' => '',
        ], $record);
        $record->id = $DB->insert_record('course_completion_defaults', $record);

        return $record;
    }
}
