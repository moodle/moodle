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
 * Adhoc task handling course module deletion.
 *
 * @package    core_course
 * @copyright  2016 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\task;

defined('MOODLE_INTERNAL') || die();
/**
 * Class handling course module deletion.
 *
 * This task supports an array of course module object as custom_data, and calls course_delete_module() in synchronous deletion
 * mode for each of them.
 * This will:
 * 1. call any 'mod_xxx_pre_course_module_deleted' functions (e.g. Recycle bin)
 * 2. delete the module
 * 3. fire the deletion event
 *
 * @package core_course
 * @copyright 2016 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_delete_modules extends \core\task\adhoc_task {

    /**
     * Run the deletion task.
     *
     * @throws \coding_exception if the module could not be removed.
     */
    public function execute() {
        global $CFG;
        require_once($CFG->dirroot. '/course/lib.php');

        // Set the proper user.
        if ($this->get_custom_data()->userid !== $this->get_custom_data()->realuserid) {
            $realuser = \core_user::get_user($this->get_custom_data()->realuserid, '*', MUST_EXIST);
            cron_setup_user($realuser);
            \core\session\manager::loginas($this->get_custom_data()->userid, \context_system::instance(), false);
        } else {
            $user = \core_user::get_user($this->get_custom_data()->userid, '*', MUST_EXIST);
            cron_setup_user($user);
        }

        $cms = $this->get_custom_data()->cms;
        foreach ($cms as $cm) {
            try {
                course_delete_module($cm->id);
            } catch (\Exception $e) {
                throw new \coding_exception("The course module {$cm->id} could not be deleted. {$e->getTraceAsString()}");
            }
        }
    }
}
