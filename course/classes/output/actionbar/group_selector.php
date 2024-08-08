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
use stdClass;

/**
 * Renderable class for the group selector element in the action bar.
 *
 * @package    core_course
 * @copyright  2024 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group_selector extends comboboxsearch {

    /**
     * @var stdClass The context object.
     */
    private stdClass $context;

    /**
     * The class constructor.
     *
     * @param null|stdClass $course This parameter has been deprecated since Moodle 4.5 and should not be used anymore.
     * @param stdClass $context The context object.
     */
    public function __construct(?stdClass $course, stdClass $context) {
        if ($course !== null) {
            debugging(
                'The course argument has been deprecated. Please remove it from your group_selector class instances.',
                DEBUG_DEVELOPER,
            );
        }
        $this->context = $context;
        parent::__construct(false, $this->get_button_content(), $this->get_dropdown_content(), 'group-search',
            'groupsearchwidget', 'groupsearchdropdown overflow-auto', null, true, $this->get_label(), 'group',
            $this->get_active_group());
    }

    /**
     * Returns the output for the button (trigger) element of the group selector.
     *
     * @return string HTML fragment
     */
    private function get_button_content(): string {
        global $OUTPUT;

        $activegroup = $this->get_active_group();
        $buttondata = [
            'label' => $this->get_label(),
            'group' => $activegroup,
        ];

        if ($activegroup) {
            $group = groups_get_group($activegroup);
            $buttondata['selectedgroup'] = format_string($group->name, true,
                ['context' => $this->context->get_course_context()]);
        } else if ($activegroup === 0) {
            $buttondata['selectedgroup'] = get_string('allparticipants');
        }

        return $OUTPUT->render_from_template('core_group/comboboxsearch/group_selector', $buttondata);
    }

    /**
     * Returns the output of the content rendered within the dropdown (search body area) of the group selector.
     *
     * @return string HTML fragment
     */
    private function get_dropdown_content(): string {
        global $OUTPUT;

        return $OUTPUT->render_from_template('core_group/comboboxsearch/searchbody', [
            'courseid' => $this->context->get_course_context()->instanceid,
            'currentvalue' => optional_param('groupsearchvalue', '', PARAM_NOTAGS),
            'instance' => rand(),
        ]);
    }

    /**
     * Returns the label text for the group selector based on specified group mode.
     *
     * @return string
     */
    private function get_label(): string {
        return $this->get_group_mode() === VISIBLEGROUPS ? get_string('selectgroupsvisible') :
            get_string('selectgroupsseparate');
    }

    /**
     * Returns the active group based on the context level.
     *
     * @return int|bool The active group (false if groups not used, int if groups used)
     */
    private function get_active_group(): int|bool {
        global $USER;

        $canaccessallgroups = has_capability('moodle/site:accessallgroups', $this->context);
        $userid = $this->get_group_mode() == VISIBLEGROUPS || $canaccessallgroups ? 0 : $USER->id;
        $course = get_course($this->context->get_course_context()->instanceid);
        // Based on the current context level, retrieve the correct grouping ID and specify whether only groups with the
        // participation field set to true should be returned.
        if ($this->context->contextlevel === CONTEXT_MODULE) {
            $cm = get_coursemodule_from_id(false, $this->context->instanceid);
            $groupingid = $cm->groupingid;
            $participationonly = true;
        } else {
            $cm = null;
            $groupingid = $course->defaultgroupingid;
            $participationonly = false;
        }

        $allowedgroups = groups_get_all_groups(
            courseid: $course->id,
            userid: $userid,
            groupingid: $groupingid,
            participationonly: $participationonly
        );

        if ($cm) {
            return groups_get_activity_group($cm, true, $allowedgroups);
        }
        return groups_get_course_group($course, true, $allowedgroups);
    }

    /**
     * Returns the group mode based on the context level.
     *
     * @return int The group mode
     */
    private function get_group_mode(): int {
        if ($this->context->contextlevel == CONTEXT_MODULE) {
            $cm = get_coursemodule_from_id(false, $this->context->instanceid);
            return groups_get_activity_groupmode($cm);
        }
        $course = get_course($this->context->instanceid);
        return $course->groupmode;
    }
}
