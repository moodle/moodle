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
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Renderable class for the group selector element in the action bar.
 *
 * @package    core_course
 * @copyright  2024 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group_selector implements renderable, templatable {

    /**
     * @var stdClass The course object.
     */
    protected $course;

    /**
     * The class constructor.
     *
     * @param stdClass $course The course object.
     */
    public function __construct(stdClass $course) {
        $this->course = $course;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param renderer_base $output The renderer that will be used to render the output.
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        global $USER, $OUTPUT;

        $course = $this->course;
        $groupmode = $course->groupmode;

        $sbody = $OUTPUT->render_from_template('core_group/comboboxsearch/searchbody', [
            'courseid' => $course->id,
            'currentvalue' => optional_param('groupsearchvalue', '', PARAM_NOTAGS),
            'instance' => rand(),
        ]);

        $label = $groupmode == VISIBLEGROUPS ? get_string('selectgroupsvisible') : get_string('selectgroupsseparate');

        $buttondata = ['label' => $label];

        $context = \context_course::instance($course->id);

        if ($groupmode == VISIBLEGROUPS || has_capability('moodle/site:accessallgroups', $context)) {
            $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
        } else {
            $allowedgroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
        }

        $activegroup = groups_get_course_group($course, true, $allowedgroups);
        $buttondata['group'] = $activegroup;

        if ($activegroup) {
            $group = groups_get_group($activegroup);
            $buttondata['selectedgroup'] = format_string($group->name, true, ['context' => $context]);
        } else if ($activegroup === 0) {
            $buttondata['selectedgroup'] = get_string('allparticipants');
        }

        $groupdropdown = new comboboxsearch(
            false,
            $OUTPUT->render_from_template('core_group/comboboxsearch/group_selector', $buttondata),
            $sbody,
            'group-search',
            'groupsearchwidget',
            'groupsearchdropdown overflow-auto',
            null,
            true,
            $label,
            'group',
            $activegroup
        );

        return $groupdropdown->export_for_template($OUTPUT);
    }

    /**
     * Returns the template for the group selector.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core/comboboxsearch';
    }
}
