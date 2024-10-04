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

namespace core_course\output\actionbar;

use core\output\named_templatable;
use core\output\renderable;
use core\output\renderer_base;
use core\url;
use stdClass;

/**
 * Renderable class for the user_selector_button.
 *
 * This is the button content for the user_selector renderable, which itself is an extension of the comboboxsearch component.
 * {@see initials_selector}.
 *
 * @package    core_course
 * @copyright  2024 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_selector_button implements renderable, named_templatable {

    /**
     * The class constructor.
     */
    public function __construct(
        private stdClass $course,
        private url $resetlink,
        private ?int $userid = null,
        private ?int $groupid = null,
        private string $usersearch = '',
        private ?int $instanceid = null
    ) {
        // If the user ID is set, it indicates that a user has been selected. In this case, override the user search
        // string with the full name of the selected user.
        if ($this->userid) {
            $this->usersearch = fullname(\core_user::get_user($this->userid));
        }
    }

    public function export_for_template(renderer_base $output) {
        return [
            'currentvalue' => $this->usersearch,
            'courseid' => $this->course->id,
            'instance' => $this->instanceid ?? rand(),
            'resetlink' => $this->resetlink->out(false),
            'group' => $this->groupid ?? 0,
            'name' => 'usersearch',
            'value' => json_encode([
                'userid' => $this->userid,
                'search' => $this->usersearch,
            ]),
        ];
    }

    public function get_template_name(renderer_base $renderer): string {
        return 'core_user/comboboxsearch/user_selector';
    }
}
