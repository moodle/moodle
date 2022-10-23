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
 * Renderer for the grade single view report.
 *
 * @package   gradereport_singleview
 * @copyright 2022 Mihail Geshoski <mihail@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Custom renderer for the single view report.
 *
 * To get an instance of this use the following code:
 * $renderer = $PAGE->get_renderer('gradereport_singleview');
 *
 * @copyright 2022 Mihail Geshoski <mihail@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradereport_singleview_renderer extends plugin_renderer_base {

    /**
     * Renders the user selector trigger element.
     *
     * @param object $course The course object.
     * @param int|null $userid The user ID.
     * @param int|null $groupid The group ID.
     * @return string The raw HTML to render.
     */
    public function users_selector(object $course, ?int $userid = null, ?int $groupid = null): string {

        $data = [
            'courseid' => $course->id,
            'groupid' => $groupid ?? 0,
        ];

        // If a particular user option is selected (not in zero state).
        if ($userid) { // A single user selected.
            $user = core_user::get_user($userid);
            $data['selectedoption'] = [
                'image' => $this->user_picture($user, ['size' => 40, 'link' => false]),
                'text' => fullname($user),
                'additionaltext' => $user->email,
            ];
        }

        $this->page->requires->js_call_amd('gradereport_singleview/user', 'init');
        return $this->render_from_template('gradereport_singleview/user_selector', $data);
    }

    /**
     * Renders the grade items selector trigger element.
     *
     * @param object $course The course object.
     * @param int|null $gradeitemid The grade item ID.
     * @return string The raw HTML to render.
     */
    public function grade_items_selector(object $course, ?int $gradeitemid = null): string {

        $data = [
            'courseid' => $course->id,
        ];

        // If a particular grade item option is selected (not in zero state).
        if ($gradeitemid) {
            $gradeitemname = grade_item::fetch(['id' => $gradeitemid])->get_name(true);
            $data['selectedoption'] = [
                'text' => $gradeitemname,
            ];
        }

        $this->page->requires->js_call_amd('gradereport_singleview/grade', 'init');
        return $this->render_from_template('gradereport_singleview/grade_item_selector', $data);
    }
}
