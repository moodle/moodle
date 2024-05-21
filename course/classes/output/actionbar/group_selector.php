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

    /** @var int|bool the active group, false if groups not used. */
    private int|bool $activegroup;

    /**
     * The class constructor.
     *
     * @param stdClass $context The context object.
     * @param bool $participationonly Only include participation groups?
     */
    public function __construct(
        /**
         * @var stdClass The context object.
         */
        private stdClass $context,
        /**
         * @var bool Only include participation groups?
         */
        protected bool $participationonly = true,
    ) {
        $this->activegroup = $this->get_active_group();
        $this->label = $this->get_label();

        // The second and third arguments (buttoncontent and dropdowncontent) need to be rendered here, since the comboboxsearch
        // template expects HTML in its respective context properties. Ideally, children of comboboxsearch would leverage Mustache's
        // blocks pragma, meaning a child template could extend the comboboxsearch, allowing rendering of the child component,
        // instead of needing to inject the child's content HTML as part of rendering the comboboxsearch parent, as is the case
        // here. Achieving this, however, requires a refactor of comboboxsearch. For now, this must be pre-rendered and injected.
        parent::__construct(false, $this->get_button_content(), $this->get_dropdown_content(), 'group-search',
            'groupsearchwidget', 'groupsearchdropdown overflow-auto', null, true, $this->label, 'group',
            $this->activegroup);
    }

    /**
     * Returns the output for the button (trigger) element of the group selector.
     *
     * @return string HTML fragment
     */
    private function get_button_content(): string {
        global $PAGE;
        $groupsselectorbutton = new group_selector_button($this->context, $this->activegroup, $this->label);

        return $PAGE->get_renderer('core', 'course')->render($groupsselectorbutton);
    }

    /**
     * Returns the output of the content rendered within the dropdown (search body area) of the group selector.
     *
     * @return string HTML fragment
     */
    private function get_dropdown_content(): string {
        global $PAGE;
        $groupsdropdownform = new group_selector_dropdown_form($this->context);

        return $PAGE->get_renderer('core', 'course')->render($groupsdropdownform);
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
        } else {
            $cm = null;
            $groupingid = $course->defaultgroupingid;
        }

        $allowedgroups = groups_get_all_groups(
            courseid: $course->id,
            userid: $userid,
            groupingid: $groupingid,
            participationonly: $this->participationonly,
        );

        if ($cm) {
            return groups_get_activity_group($cm, true, $allowedgroups, $this->participationonly);
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
