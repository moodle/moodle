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

defined('MOODLE_INTERNAL') || die;

use \core_grades\output\action_bar;

/**
 * Renderer class for the grade pages.
 *
 * @package    core_grades
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_grades_renderer extends plugin_renderer_base {

    /**
     * Renders the action bar for a given page.
     *
     * @param action_bar $actionbar
     * @return string The HTML output
     */
    public function render_action_bar(action_bar $actionbar): string {
        $data = $actionbar->export_for_template($this);
        return $this->render_from_template($actionbar->get_template(), $data);
    }

    /**
     * Renders the group selector trigger element.
     *
     * @param object $course The course object.
     * @param string|null $groupactionbaseurl The base URL for the group action.
     * @return string|null The raw HTML to render.
     */
    public function group_selector(object $course, ?string $groupactionbaseurl = null): ?string {
        global $USER;

        // Make sure that group mode is enabled.
        if (!$groupmode = $course->groupmode) {
            return null;
        }

        $label = $groupmode == VISIBLEGROUPS ? get_string('selectgroupsvisible') :
            get_string('selectgroupsseparate');

        $data = [
            'label' => $label,
            'courseid' => $course->id,
            'groupactionbaseurl' => $groupactionbaseurl
        ];

        $context = context_course::instance($course->id);

        if ($groupmode == VISIBLEGROUPS || has_capability('moodle/site:accessallgroups', $context)) {
            $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
        } else {
            $allowedgroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
        }

        $activegroup = groups_get_course_group($course, true, $allowedgroups);

        if ($activegroup) {
            $group = groups_get_group($activegroup);
            $data['selectedgroup'] = $group->name;
        } else if ($activegroup === 0) {
            $data['selectedgroup'] = get_string('allparticipants');
        }

        $this->page->requires->js_call_amd('core_grades/searchwidget/group', 'init');
        return $this->render_from_template('core_grades/group_selector', $data);
    }
}
