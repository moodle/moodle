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

namespace mod_quiz\local\reports;

use context;
use context_module;
use stdClass;

/**
 * Base class for quiz report plugins.
 *
 * Doesn't do anything on its own -- it needs to be extended.
 * This class displays quiz reports.  Because it is called from
 * within /mod/quiz/report.php you can assume that the page header
 * and footer are taken care of.
 *
 * This file can refer to itself as report.php to pass variables
 * to itself - all these will also be globally available.  You must
 * pass "id=$cm->id" or q=$quiz->id", and "mode=reportname".
 *
 * @package   mod_quiz
 * @copyright 1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class report_base {
    /** @var int special value used in place of groupid, to mean the use cannot access any groups. */
    const NO_GROUPS_ALLOWED = -2;

    /**
     * Override this function to display the report.
     *
     * @param stdClass $quiz this quiz.
     * @param stdClass $cm the course-module for this quiz.
     * @param stdClass $course the coures we are in.
     */
    abstract public function display($quiz, $cm, $course);

    /**
     * Initialise some parts of $PAGE and start output.
     *
     * @param stdClass $cm the course_module information.
     * @param stdClass $course the course settings.
     * @param stdClass $quiz the quiz settings.
     * @param string $reportmode the report name.
     */
    public function print_header_and_tabs($cm, $course, $quiz, $reportmode = 'overview') {
        global $PAGE, $OUTPUT, $CFG;

        // Print the page header.
        $PAGE->set_title($quiz->name);
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();
        $context = context_module::instance($cm->id);
        if (!$PAGE->has_secondary_navigation()) {
            echo $OUTPUT->heading(format_string($quiz->name, true, ['context' => $context]));
        }
        if (!empty($CFG->enableplagiarism)) {
            require_once($CFG->libdir . '/plagiarismlib.php');
            echo plagiarism_update_status($course, $cm);
        }
    }

    /**
     * Get the current group for the user user looking at the report.
     *
     * @param stdClass $cm the course_module information.
     * @param stdClass $course the course settings.
     * @param context $context the quiz context.
     * @return int the current group id, if applicable. 0 for all users,
     *      NO_GROUPS_ALLOWED if the user cannot see any group.
     */
    public function get_current_group($cm, $course, $context) {
        $groupmode = groups_get_activity_groupmode($cm, $course);
        $currentgroup = groups_get_activity_group($cm, true);

        if ($groupmode == SEPARATEGROUPS && !$currentgroup &&
                !has_capability('moodle/site:accessallgroups', $context)) {
            $currentgroup = self::NO_GROUPS_ALLOWED;
        }

        return $currentgroup;
    }

    /**
     * Print action bar filter.
     *
     * @param string $reportmode The quiz report type.
     * @param attempts_report_options $options The current report settings.
     * @param mixed $table The table class for each report type.
     *      With each type of report, the table's type varies, so 'mixed' is selected.
     * @param \cm_info $cm Course-module object.
     */
    public function print_action_bar(string $reportmode, attempts_report_options $options, mixed $table,
            \cm_info $cm = null): void {
        global $PAGE;
        $renderer = $PAGE->get_renderer('mod_quiz');
        $params = new stdClass();
        $params->path = '/mod/quiz/report.php';
        $params->params = $options->get_url()->params();
        $params->reportmode = $reportmode;
        $params->cmid = $cm->id;
        $params->optionclass = get_class($options);
        $params->service = 'mod_quiz_get_users_in_report';
        $params->tableclass = get_class($table);
        // Conditionally add the group JS if we have groups enabled.
        if (groups_get_activity_groupmode($cm)) {
            $PAGE->requires->js_call_amd('core/comboboxsearch/group', 'init', [$params]);
        }
        $PAGE->requires->js_call_amd('core/searchwidget/user', 'init', [$params]);
        $actionbar = new \mod_quiz\output\quiz_action_bar(\context_module::instance($cm->id),
            $options, $reportmode, $table);
        echo $renderer->render_action_bar($actionbar);
    }
}
