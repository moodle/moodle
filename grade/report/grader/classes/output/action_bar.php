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

    /**
     * The class constructor.
     *
     * @param \context_course $context The context object.
     */
    public function __construct(\context_course $context) {
        parent::__construct($context);

        $this->usersearch = optional_param('gpr_search', '', PARAM_NOTAGS);
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
        global $PAGE, $OUTPUT, $SESSION, $USER;
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
            $gradesrenderer = $PAGE->get_renderer('core_grades');

            $initialscontent = $gradesrenderer->initials_selector(
                $course,
                $this->context,
                '/grade/report/grader/index.php'
            );
            $initialselector = new comboboxsearch(
                false,
                $initialscontent->buttoncontent,
                $initialscontent->dropdowncontent,
                'initials-selector',
                'initialswidget',
                'initialsdropdown',
                $initialscontent->buttonheader,
            );
            $data['initialselector'] = $initialselector->export_for_template($output);
            $data['groupselector'] = $gradesrenderer->group_selector($course);

            $resetlink = new moodle_url('/grade/report/grader/index.php', ['id' => $courseid]);
            $searchinput = $OUTPUT->render_from_template('core_user/comboboxsearch/user_selector', [
                'currentvalue' => $this->usersearch,
                'courseid' => $courseid,
                'resetlink' => $resetlink->out(false),
                'group' => 0,
            ]);
            $searchdropdown = new comboboxsearch(
                true,
                $searchinput,
                null,
                'user-search dropdown d-flex',
                null,
                'usersearchdropdown overflow-auto',
                null,
                false,
            );
            $data['searchdropdown'] = $searchdropdown->export_for_template($output);

            // The collapsed column dialog is aligned to the edge of the screen, we need to place it such that it also aligns.
            $collapsemenudirection = right_to_left() ? 'dropdown-menu-left' : 'dropdown-menu-right';

            $collapse = new comboboxsearch(
                true,
                get_string('collapsedcolumns', 'gradereport_grader', 0),
                null,
                'collapse-columns',
                'collapsecolumn',
                'collapsecolumndropdown p-3 flex-column ' . $collapsemenudirection,
                null,
                true,
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

            if (!empty($SESSION->gradereport["filterfirstname-{$this->context->id}"]) ||
                !empty($SESSION->gradereport["filterlastname-{$this->context->id}"]) ||
                groups_get_course_group($course, true, $allowedgroups) ||
                $this->usersearch) {
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
