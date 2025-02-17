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

namespace gradereport_grader\output;

use core\output\comboboxsearch;
use core_course\output\actionbar\group_selector;
use core_course\output\actionbar\initials_selector;
use core_course\output\actionbar\user_selector;
use core_grades\output\general_action_bar;
use moodle_url;

/**
 * Renderable class for the action bar elements in the grader report.
 *
 * @package    gradereport_grader
 * @copyright  2022 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_bar extends \core_grades\output\action_bar {

    /** @var string $usersearch The content that the current user is looking for. */
    protected string $usersearch = '';

    /** @var int $userid The ID of the user that the current user is looking for. */
    protected int $userid = 0;

    /**
     * The class constructor.
     *
     * @param \context_course $context The context object.
     */
    public function __construct(\context_course $context) {
        parent::__construct($context);

        $this->userid = optional_param('gpr_userid', 0, PARAM_INT);
        $this->usersearch = optional_param('gpr_search', '', PARAM_NOTAGS);

        if ($this->userid) {
            $user = \core_user::get_user($this->userid);
            $this->usersearch = fullname($user);
        }
    }

    /**
     * Returns the template for the action bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'gradereport_grader/action_bar';
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     * @throws \moodle_exception
     */
    public function export_for_template(\renderer_base $output): array {
        global $SESSION, $USER;
        // If in the course context, we should display the general navigation selector in gradebook.
        $courseid = $this->context->instanceid;
        // Get the data used to output the general navigation selector.
        $generalnavselector = new general_action_bar($this->context,
            new moodle_url('/grade/report/grader/index.php', ['id' => $courseid]), 'gradereport', 'grader');

        $data = $generalnavselector->export_for_template($output);

        // If the user has the capability to view all grades, display the group selector (if applicable), the user selector
        // and the view mode selector (if applicable).
        if (has_capability('moodle/grade:viewall', $this->context)) {
            $course = get_course($courseid);

            $firstnameinitial = $SESSION->gradereport["filterfirstname-{$this->context->id}"] ?? '';
            $lastnameinitial  = $SESSION->gradereport["filtersurname-{$this->context->id}"] ?? '';
            $additionalparams = [];

            if ($this->userid > 0) {
                $additionalparams['gpr_userid'] = $this->userid;
            } else if (!empty($this->usersearch)) {
                $additionalparams['gpr_search'] = $this->usersearch;
            }

            $initialselector = new initials_selector(
                course: $course,
                targeturl: '/grade/report/grader/index.php',
                firstinitial: $firstnameinitial,
                lastinitial: $lastnameinitial,
                additionalparams: $additionalparams,
            );
            $data['initialselector'] = $initialselector->export_for_template($output);

            if ($course->groupmode) {
                $gs = new group_selector($this->context);
                $data['groupselector'] = $gs->export_for_template($output);
            }

            $resetlink = new moodle_url('/grade/report/grader/index.php', ['id' => $courseid]);
            $userselector = new user_selector(
                course: $course,
                resetlink: $resetlink,
                userid: $this->userid,
                groupid: 0,
                usersearch: $this->usersearch
            );
            $data['searchdropdown'] = $userselector->export_for_template($output);
            // The collapsed column dialog is aligned to the edge of the screen, we need to place it such that it also aligns.
            $collapsemenudirection = right_to_left() ? 'dropdown-menu-start' : 'dropdown-menu-end';

            $collapse = new comboboxsearch(
                true,
                get_string('collapsedcolumns', 'gradereport_grader', 0),
                null,
                'collapse-columns',
                'collapsecolumn',
                'collapsecolumndropdown p-3 flex-column ' . $collapsemenudirection,
                null,
                true,
                get_string('aria:dropdowncolumns', 'gradereport_grader'),
                'collapsedcolumns'
            );
            $data['collapsedcolumns'] = [
                'classes' => 'd-none',
                'content' => $collapse->export_for_template($output)
            ];

            if ($course->groupmode == VISIBLEGROUPS || has_capability('moodle/site:accessallgroups', $this->context)) {
                $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
            } else {
                $allowedgroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
            }

            if (
                $firstnameinitial ||
                $lastnameinitial ||
                groups_get_course_group($course, true, $allowedgroups) ||
                $this->usersearch
            ) {
                $reset = new moodle_url('/grade/report/grader/index.php', [
                    'id' => $courseid,
                    'group' => 0,
                    'sifirst' => '',
                    'silast' => ''
                ]);
                $data['pagereset'] = $reset->out(false);
            }
        }

        return $data;
    }
}
