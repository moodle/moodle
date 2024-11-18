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
 * An adhoc task for local Iomad
 *
 * @package    local_iomad
 * @copyright  2024 E-Learn Design https://www.e-learndesign.co.uk
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_iomad\task;

defined('MOODLE_INTERNAL') || die();

use core\task\adhoc_task;

class resetrolestask extends adhoc_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('resetroles', 'local_iomad');
    }

    /**
     * Run fixtracklicensestask
     */
    public function execute() {
        global $DB;

        $allroles = ['companymanager',
                     'companydepartmentmanager',
                     'companycourseeditor',
                     'companycoursenoneditor',
                     'companyreporter',
                     'clientreporter'];

        // Get the roles and reset them.
        foreach ($allroles as $role) {
            if ($rolerec = $DB->get_record('role', array('shortname' => $role))) {
                reset_role_capabilities($rolerec->id);
            }
        }
    }

    /**
     * Queues the task.
     *
     */
    public static function queue_task() {

        // Let's set up the adhoc task.
        $task = new \local_iomad\task\resetrolestask();
        \core\task\manager::queue_adhoc_task($task, true);
    }
}
