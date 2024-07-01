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

namespace mod_quiz\output;

use core\output\comboboxsearch;
use mod_quiz\local\reports\attempts_report_options;
use moodle_url;
use templatable;
use renderable;

/**
 * Renderable class for the quiz navigation bar in the quiz report pages.
 *
 * @package    mod_quiz
 * @copyright  2024 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_navigation_bar implements templatable, renderable {

    /** @var string $usersearch The content that the current user is looking for. */
    protected string $usersearch = '';
    /** @var string $userid The userid of the current user is looking for. */
    protected string $userid = '';
    /** @var string $reportmode The quiz report mode. */
    protected string $reportmode;
    /** @var ?attempts_report_options $options the current report settings. */
    protected null|attempts_report_options $options;
    /** @var \context $context The context object. */
    protected \context $context;
    /** @var null|\moodle_url $url Full report url. */
    protected ?moodle_url $url;
    /** @var \cm_info $cm The cm object. */
    protected null|\cm_info $cm;

    /**
     * The class constructor.
     *
     * @param \context $context The context object.
     * @param string $reportmode The quiz report type.
     * @param ?attempts_report_options $options The current report settings.
     * @param null|\moodle_url $url Full report url.
     * @param null|\cm_info $cm The cm object.
     */
    public function __construct(\context $context, string $reportmode, ?attempts_report_options $options = null,
            ?\moodle_url $url = null, ?\cm_info $cm = null) {
        $this->context = $context;
        $this->usersearch = optional_param('gpr_search', '', PARAM_NOTAGS);
        $this->userid = optional_param('gpr_userid', '', PARAM_INT);
        $this->reportmode = $reportmode;
        $this->options = $options;
        $this->url = $url;
        $this->cm = $cm;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     * @throws \moodle_exception
     */
    public function export_for_template(\renderer_base $output): array {
        global $OUTPUT, $USER, $SESSION, $PAGE;
        $firstnameinitial = '';
        $lastnameinitial = '';
        if (is_null($this->options)) {
            $cm = $this->cm;
        } else {
            $cm = $this->options->cm;
        }
        $cmid = $this->context->instanceid;
        $course = $cm->get_course();
        // Get the data used to output the general navigation selector.
        $generalnavselector = new action_selector($this->context);
        $data = $generalnavselector->export_for_template($output);
        if (!is_null($this->options)) {
            // Prepare url param.
            $url = $this->options->get_url();
            $urlparam = $url->params();
            // Set up data for initials bar.
            $filter = new \stdClass();
            $filter->usersearch = $this->usersearch;
            $filter->userid = $this->userid;
            $initialselector = new \core\output\name_filter_bar($course,  $this->context, '/mod/quiz/report.php',
                $urlparam, $filter, $this->reportmode, $cmid);
            $firstnameinitial = $SESSION->{$this->reportmode . 'report'}["filterfirstname-{$this->context->id}"] ?? '';
            $lastnameinitial  = $SESSION->{$this->reportmode . 'report'}["filtersurname-{$this->context->id}"] ?? '';
            $data['initialselector'] = $initialselector->export_for_template($output);
        }

        // Set up data for group selector.
        if (groups_get_activity_groupmode($this->cm)) {
            $actionbarrenderer = $PAGE->get_renderer('core_course', 'actionbar');
            $data['groupselector'] = $actionbarrenderer->render(new \core_course\output\actionbar\group_selector($course, $cm));
        }

        if (!is_null($this->options)) {
            $courseid = $cm->course;
            // Reset link.
            $resetlink = new moodle_url('/mod/quiz/report.php', ['id' => $cm->id, 'mode' => $this->reportmode]);
            // User search.
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
        }

        if ($course->groupmode == VISIBLEGROUPS || has_capability('moodle/site:accessallgroups', $this->context)) {
            $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
        } else {
            $allowedgroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
        }

        if (!empty($firstnameinitial) || !empty($lastnameinitial) ||
            groups_get_course_group($course, true, $allowedgroups) || $this->usersearch) {
            if (is_null($this->options)) {
                $params = $this->url->params();
            } else {
                $params = [...$this->options->get_url()->params(), ...['sifirst' => '',
                    'silast' => '', 'gpr_search' => ''],
                ];
            }
            $resetparam = array_merge($params, [
                'group' => 0,
            ]);

            $reset = new moodle_url('/mod/quiz/report.php', $resetparam);
            $data['pagereset'] = $reset->out(false);
        }

        return $data;
    }
}
