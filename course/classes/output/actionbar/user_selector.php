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

use core\output\comboboxsearch;
use moodle_url;
use stdClass;

/**
 * Renderable class for the user selector element in the action bar.
 *
 * @package    core_course
 * @copyright  2024 Ilya Tregubov <ilyatregubov@proton.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_selector extends comboboxsearch {

    /**
     * The class constructor.
     *
     * @param stdClass $course The course object.
     * @param moodle_url $resetlink The reset link.
     * @param int|null $userid The user ID.
     * @param int|null $groupid The group ID.
     * @param string $usersearch The user search query.
     * @param int|null $instanceid The instance ID.
     */
    public function __construct(
        stdClass $course,
        moodle_url $resetlink,
        ?int $userid = null,
        ?int $groupid = null,
        string $usersearch = '',
        ?int $instanceid = null
    ) {

        $userselectorontent = $this->user_selector_output($course, $resetlink, $userid, $groupid, $usersearch, $instanceid);
        parent::__construct(true, $userselectorontent, null, 'user-search d-flex',
            null, 'usersearchdropdown overflow-auto', null, false);
    }

    /**
     * Method that generates the output for the user selector.
     *
     * @param stdClass $course The course object.
     * @param moodle_url|null $resetlink The reset link.
     * @param int|null $userid The user ID.
     * @param int|null $groupid The group ID.
     * @param string $usersearch The user search query.
     * @param int|null $instanceid The instance ID.
     * @return string The HTML output.
     */
    private function user_selector_output(
        stdClass $course,
        ?moodle_url $resetlink = null,
        ?int $userid = null,
        ?int $groupid = null,
        string $usersearch = '',
        ?int $instanceid = null
    ): string {
        global $OUTPUT;

        // If the user ID is set, it indicates that a user has been selected. In this case, override the user search
        // string with the full name of the selected user.
        if ($userid) {
            $usersearch = fullname(\core_user::get_user($userid));
        }

        return $OUTPUT->render_from_template('core_user/comboboxsearch/user_selector', [
            'currentvalue' => $usersearch,
            'courseid' => $course->id,
            'instance' => $instanceid ?? rand(),
            'resetlink' => $resetlink->out(false),
            'group' => $groupid ?? 0,
            'name' => 'usersearch',
            'value' => json_encode([
                'userid' => $userid,
                'search' => $usersearch,
            ]),
        ]);
    }
}
