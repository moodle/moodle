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
use core\output\named_templatable;
use core\output\renderer_base;
use core_course\output\actionbar\group_selector;
use core_course\output\actionbar\initials_selector;
use mod_quiz\local\reports\attempts_report_options;
use moodle_url;
use renderable;

/**
 * Renderable class for the quiz navigation bar in the quiz report pages.
 *
 * @package    mod_quiz
 * @copyright  2024 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_report_navigation_bar implements named_templatable, renderable {
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
    /** @var string $firstnameinitial The first name initial the user is filtering by. */
    protected string $firstnameinitial = '';
    /** @var string $lastnameinitial The last name initial the user is filtering by. */
    protected string $lastnameinitial = '';

    /**
     * The class constructor.
     *
     * @param \context $context The context object.
     * @param string $reportmode The quiz report type.
     * @param ?attempts_report_options $options The current report settings.
     * @param null|\moodle_url $url Full report url.
     * @param null|\cm_info $cm The cm object.
     */
    public function __construct(
        \context $context,
        string $reportmode,
        ?attempts_report_options $options = null,
        ?\moodle_url $url = null,
        ?\cm_info $cm = null
    ) {
        global $SESSION;

        $this->context = $context;
        $this->usersearch = $options->usersearch ?? '';
        $this->userid = $options->userid ?? '';
        $this->reportmode = $reportmode;
        $this->options = $options;
        $this->url = $url;
        $this->cm = $cm;
        // Userid = -1 is display all.
        if ($this->userid > 0) {
            $user = \core_user::get_user($this->userid);
            $this->usersearch = fullname($user);
        }

        // The initials filter is stashed in $SESSION rather than passed as a parameter, to match
        // standard Moodle behaviour (e.g. the gradebook initials bar). report.php writes these
        // values whenever the initials filter form is submitted.
        $this->firstnameinitial = $SESSION->{$this->reportmode . 'report'}["filterfirstname-{$this->context->id}"] ?? '';
        $this->lastnameinitial  = $SESSION->{$this->reportmode . 'report'}["filtersurname-{$this->context->id}"] ?? '';
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     * @throws \moodle_exception
     */
    public function export_for_template(\renderer_base $output): array {
        global $OUTPUT, $USER, $PAGE;

        if (is_null($this->options)) {
            $cm = $this->cm;
        } else {
            $cm = $this->options->cm;
        }
        $course = $cm->get_course();
        // Get the data used to output the quiz report selectors.
        $generalnavselector = new quiz_report_action_selector($this->context);
        $data = $generalnavselector->export_for_template($output);

        // Get the data used to output initial bars.
        if (!is_null($this->options)) {
            // Prepare url param.
            $url = $this->options->get_url();
            $urlparam = $url->params();
            // Set up data for initials bar.
            $filter = new \stdClass();
            $filter->usersearch = $this->usersearch;
            $filter->userid = $this->userid;
            $additionalparams = [];

            if ($this->userid > 0) {
                $additionalparams['userid'] = $this->userid;
            } else if (!empty($this->usersearch)) {
                $additionalparams['search'] = $this->usersearch;
            }

            $firstnameinitial = $this->firstnameinitial;
            $lastnameinitial  = $this->lastnameinitial;
            $initialselector = new initials_selector(
                course: $course,
                targeturl: '/mod/quiz/report.php',
                firstinitial: $firstnameinitial,
                lastinitial: $lastnameinitial,
                additionalparams: [...$additionalparams, ...$urlparam],
            );
            $data['initialselector'] = $initialselector->export_for_template($output);
        }

        $params = new \stdClass();
        $params->path = '/mod/quiz/report.php';
        $params->reportmode = $this->reportmode;
        $params->cmid = $cm->id;
        $params->params = $this->url ? $this->url->params() : [];

        // Get the data used to output group selectors.
        if (groups_get_activity_groupmode($this->cm)) {
            // Note: group_selector queries course-level groups via AJAX, which can cause non-participation
            // or activity-restricted groups to be displayed regardless of the $participationonly flag.
            $gs = new group_selector($this->context, true);
            $data['groupselector'] = $gs->export_for_template($output);
            $baseurl = new \moodle_url('/mod/quiz/report.php', ['id' => $params->cmid, 'mode' => $this->reportmode]);
            if (!is_null($this->options)) {
                $baseurl = $this->options->get_url();
            }
            $PAGE->requires->js_call_amd('core_course/actionbar/group', 'init', [$baseurl->out(false), $params->cmid, $params]);
        }

        // Get the data used to user search selector.
        if (!is_null($this->options)) {
            $courseid = $cm->course;
            // Reset link.
            $resetparams = $this->options->get_url()->params();
            $resetparams['search'] = '';
            $resetparams['userid'] = -1;
            $resetlink = new moodle_url('/mod/quiz/report.php', $resetparams);
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
            $params->params = $this->options->get_url()->params();
            $PAGE->requires->js_call_amd('mod_quiz/searchwidget/user', 'init', [$params]);
        }

        // Get the data used to clear all button.
        if ($course->groupmode == VISIBLEGROUPS || has_capability('moodle/site:accessallgroups', $this->context)) {
            $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
        } else {
            $allowedgroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
        }
        if (
            !empty($this->firstnameinitial) || !empty($this->lastnameinitial) ||
            groups_get_course_group($course, true, $allowedgroups) || $this->usersearch
        ) {
            if (is_null($this->options)) {
                $params = $this->url ? $this->url->params() : [];
            } else {
                $params = [...$this->options->get_url()->params(), ...['sifirst' => '',
                    'silast' => '', 'search' => ''],
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

    /**
     * Returns the template for the bar.
     *
     * @param renderer_base $renderer The renderer requesting the template name
     * @return string
     */
    public function get_template_name(renderer_base $renderer): string {
        return 'core/action_bar';
    }
}
