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

namespace mod_subsection\task;

use core\task\scheduled_task;

/**
 * A scheduled task to remove existing descriptions from subsection instances.
 *
 * @package    mod_subsection
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remove_existing_descriptions_task extends scheduled_task {
    /**
     * Return the task name.
     *
     * @return string The name of the task.
     */
    public function get_name(): string {
        return get_string('removeexistingdescriptions', 'mod_subsection');
    }

    /**
     * Execute the task.
     */
    public function execute(): void {
        global $DB;

        $DB->set_field('course_sections', 'summary', '', ['component' => 'mod_subsection']);
    }
}
