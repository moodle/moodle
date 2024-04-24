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

namespace core\output;
/**
 * This class sets a general groups bar on the action bar menu.
 *
 * @package    core
 * @category   output
 * @copyright  2024 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class groups_bar {

    /**
     * Renders the group selector trigger element.
     *
     * @param \stdClass $course The course object.
     * @param mixed $output Output object.
     * @param \cm_info|null $cm cm info object.
     * @param string|null $groupactionbaseurl The base URL for the group action.
     * @return string|null The raw HTML to render.
     */
    public static function group_selector(\stdClass $course, mixed $output, \cm_info $cm = null,
            ?string $groupactionbaseurl = null): ?string {

        if ($groupactionbaseurl !== null) {
            debugging(
                'The $groupactionbaseurl argument has been deprecated. Please remove it from your method calls.',
                DEBUG_DEVELOPER,
            );
        }

        if (is_null($cm)) {
            $groupmode = $course->groupmode;
        } else {
            $groupmode = groups_get_activity_groupmode($cm);
        }
        // Make sure that group mode is enabled.
        if (!$groupmode) {
            return null;
        }

        $sbody = $output->render_from_template('core_group/comboboxsearch/searchbody', [
            'courseid' => $course->id,
            'cmid' => $cm->id ?? null,
            'currentvalue' => optional_param('groupsearchvalue', '', PARAM_NOTAGS),
            'instance' => rand(),
        ]);

        $label = $groupmode == VISIBLEGROUPS ? get_string('selectgroupsvisible') :
            get_string('selectgroupsseparate');

        $buttondata = ['label' => $label];

        [$context, $activegroup] = self::get_group_info($course, $cm, $groupmode);

        $buttondata['group'] = $activegroup;

        if ($activegroup) {
            $group = groups_get_group($activegroup);
            $buttondata['selectedgroup'] = format_string($group->name, true, ['context' => $context]);
        } else if ($activegroup === 0) {
            $buttondata['selectedgroup'] = get_string('allparticipants');
        }

        $groupdropdown = new comboboxsearch(
            false,
            $output->render_from_template('core_group/comboboxsearch/group_selector', $buttondata),
            $sbody,
            'group-search',
            'groupsearchwidget',
            'groupsearchdropdown overflow-auto',
            null,
            true,
            $label,
            'group',
            $activegroup,
        );

        return $output->render_from_template($groupdropdown->get_template(),
            $groupdropdown->export_for_template($output));
    }

    /**
     * Retrieve group info contains context (course or module) and group active.
     *
     * @param \stdClass $course The course object.
     * @param null|\cm_info $cm Course module info.
     * @param int $groupmode Group mode data.
     * @return array Group info data context (course or module) and group active.
     */
    private static function get_group_info(\stdClass $course, ?\cm_info $cm, int $groupmode): array {
        global $USER;

        // Determine the context based on $cm.
        if (is_null($cm)) {
            $context = \context_course::instance($course->id);
        } else {
            $context = \context_module::instance($cm->id);
        }

        // Check if the user can access all groups.
        $canaccessallgroups = has_capability('moodle/site:accessallgroups', $context);
        $groupingid = ($cm === null) ? $course->defaultgroupingid : $cm->groupingid;

        // Determine the allowed groups based on $cm and $groupmode.
        if ($groupmode == VISIBLEGROUPS || $canaccessallgroups) {
            $allowedgroups = groups_get_all_groups($course->id, 0, $groupingid, 'g.*', false, !is_null($cm));
        } else {
            $allowedgroups = groups_get_all_groups($cm->course, $USER->id, $groupingid, 'g.*', false, !is_null($cm));
        }

        // Determine the active group based on $cm.
        if (is_null($cm)) {
            $activegroup = groups_get_course_group($course, true, $allowedgroups);
        } else {
            $activegroup = groups_get_activity_group($cm, true, $allowedgroups);
        }

        return [$context, $activegroup];
    }
}
