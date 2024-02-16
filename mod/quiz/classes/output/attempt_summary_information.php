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

use action_link;
use core\output\named_templatable;
use html_writer;
use mod_quiz\grade_calculator;
use mod_quiz\output\grades\grade_out_of;
use mod_quiz\quiz_attempt;
use moodle_url;
use mod_quiz\question\display_options;
use question_display_options;
use renderable;
use renderer_base;
use stdClass;
use user_picture;

/**
 * A summary of a single quiz attempt for rendering.
 *
 * This is used in places like
 * - at the top of the review attempt page (review.php)
 * - at the top of the review single question page (reviewquestion.php)
 * - on the quiz entry page (view.php).
 *
 * @package mod_quiz
 * @copyright 2024 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt_summary_information implements renderable, named_templatable {

    /** @var array[] The rows of summary data. {@see add_item()} should make the structure clear. */
    protected array $summarydata = [];

    /**
     * Add an item to the summary.
     *
     * @param string $shortname unique identifier of this item (not displayed).
     * @param string|renderable $title the title of this item.
     * @param string|renderable $content the content of this item.
     */
    public function add_item(string $shortname, string|renderable $title, string|renderable $content): void {
        $this->summarydata[$shortname] = [
            'title'   => $title,
            'content' => $content,
        ];
    }

    /**
     * Filter the data held, to keep only the information with the given shortnames.
     *
     * @param array $shortnames items to keep.
     */
    public function filter_keeping_only(array $shortnames): void {
        foreach ($this->summarydata as $shortname => $rowdata) {
            if (!in_array($shortname, $shortnames)) {
                unset($this->summarydata[$shortname]);
            }
        }
    }

    /**
     * To aid conversion of old code. This converts the old array format into an instance of this class.
     *
     * @param array $items array of $shortname => [$title, $content].
     * @return static
     */
    public static function create_from_legacy_array(array $items): static {
        $summary = new static();
        foreach ($items as $shortname => $item) {
            $summary->add_item($shortname, $item['title'], $item['content']);
        }
        return $summary;
    }

    /**
     * Initialise an instance of this class for a particular quiz attempt.
     *
     * @param quiz_attempt $attemptobj the attempt to summarise.
     * @param display_options $options options for what can be seen.
     * @param int|null $pageforlinkingtootherattempts if null, no links to other attempsts will be created.
     *      If specified, the URL of this particular page of the attempt, otherwise
     *      the URL will go to the first page.  If -1, deduce $page from $slot.
     * @param bool|null $showall if true, the URL will be to review the entire attempt on one page,
     *      and $page will be ignored. If null, a sensible default will be chosen.
     * @return self summary information.
     */
    public static function create_for_attempt(
        quiz_attempt $attemptobj,
        display_options $options,
        ?int $pageforlinkingtootherattempts = null,
        ?bool $showall = null,
    ): static {
        global $DB, $USER;
        $summary = new static();

        // Prepare summary information about the whole attempt.
        if (!$attemptobj->get_quiz()->showuserpicture && $attemptobj->get_userid() != $USER->id) {
            // If showuserpicture is true, the picture is shown elsewhere, so don't repeat it.
            $student = $DB->get_record('user', ['id' => $attemptobj->get_userid()]);
            $userpicture = new user_picture($student);
            $userpicture->courseid = $attemptobj->get_courseid();
            $summary->add_item('user', $userpicture,
                new action_link(
                    new moodle_url('/user/view.php', ['id' => $student->id, 'course' => $attemptobj->get_courseid()]),
                    fullname($student, true),
                )
            );
        }

        if ($pageforlinkingtootherattempts !== null && $attemptobj->has_capability('mod/quiz:viewreports')) {
            $attemptlist = $attemptobj->links_to_other_attempts(
                $attemptobj->review_url(null, $pageforlinkingtootherattempts, $showall));
            if ($attemptlist) {
                $summary->add_item('attemptlist', get_string('attempts', 'quiz'), $attemptlist);
            }
        }

        // Attempt state.
        $summary->add_item('state', get_string('attemptstate', 'quiz'),
            quiz_attempt::state_name($attemptobj->get_attempt()->state));

        // Timing information.
        $attempt = $attemptobj->get_attempt();
        $quiz = $attemptobj->get_quiz();
        $overtime = 0;

        if ($attempt->state == quiz_attempt::FINISHED) {
            if ($timetaken = ($attempt->timefinish - $attempt->timestart)) {
                if ($quiz->timelimit && $timetaken > ($quiz->timelimit + 60)) {
                    $overtime = $timetaken - $quiz->timelimit;
                    $overtime = format_time($overtime);
                }
                $timetaken = format_time($timetaken);
            } else {
                $timetaken = "-";
            }
        } else {
            $timetaken = get_string('unfinished', 'quiz');
        }

        $summary->add_item('startedon', get_string('startedon', 'quiz'), userdate($attempt->timestart));

        if ($attempt->state == quiz_attempt::FINISHED) {
            $summary->add_item('completedon', get_string('completedon', 'quiz'),
                userdate($attempt->timefinish));
            $summary->add_item('timetaken', get_string('attemptduration', 'quiz'), $timetaken);
        }

        if (!empty($overtime)) {
            $summary->add_item('overdue', get_string('overdue', 'quiz'), $overtime);
        }

        // Show marks (if the user is allowed to see marks at the moment).
        $grade = $summary->add_attempt_grades_if_appropriate($attemptobj, $options);

        // Any additional summary data from the behaviour.
        foreach ($attemptobj->get_additional_summary_data($options) as $shortname => $data) {
            $summary->add_item($shortname, $data['title'], $data['content']);
        }

        // Feedback if there is any, and the user is allowed to see it now.
        $feedback = $attemptobj->get_overall_feedback($grade);
        if ($options->overallfeedback && $feedback) {
            $summary->add_item('feedback', get_string('feedback', 'quiz'), $feedback);
        }

        return $summary;
    }

    /**
     * Add the grade information to this summary information.
     *
     * This is a helper used by {@see create_for_attempt()}.
     *
     * @param quiz_attempt $attemptobj the attempt to summarise.
     * @param display_options $options options for what can be seen.
     * @return float|null the overall attempt grade, if it exists, else null. Raw value, not formatted.
     */
    public function add_attempt_grades_if_appropriate(
        quiz_attempt $attemptobj,
        display_options $options,
    ): ?float {
        $quiz = $attemptobj->get_quiz();
        $grade = quiz_rescale_grade($attemptobj->get_sum_marks(), $quiz, false);

        if ($options->marks < question_display_options::MARK_AND_MAX) {
            // User can't see grades.
            return $grade;
        }

        if (!quiz_has_grades($quiz) || $attemptobj->get_state() != quiz_attempt::FINISHED) {
            // No grades to show.
            return $grade;
        }

        if (is_null($grade)) {
            // Attempt needs ot be graded.
            $this->add_item('grade', get_string('gradenoun'), quiz_format_grade($quiz, $grade));
            return $grade;
        }

        // Grades for extra grade items, if any.
        foreach ($attemptobj->get_grade_item_totals() as $gradeitemid => $gradeoutof) {
            $this->add_item('marks' . $gradeitemid, format_string($gradeoutof->name), $gradeoutof);
        }

        // Show raw marks only if they are different from the grade.
        if ($quiz->grade != $quiz->sumgrades) {
            $this->add_item('marks', get_string('marks', 'quiz'),
                new grade_out_of($quiz, $attemptobj->get_sum_marks(), $quiz->sumgrades, style: grade_out_of::SHORT));
        }

        // Now the scaled grade.
        $this->add_item('grade', get_string('gradenoun'),
            new grade_out_of($quiz, $grade, $quiz->grade,
                style: abs($quiz->grade - 100) < grade_calculator::ALMOST_ZERO ?
                    grade_out_of::NORMAL : grade_out_of::WITH_PERCENT));

        return $grade;
    }

    public function export_for_template(renderer_base $output): array {

        $templatecontext = [
            'hasitems' => !empty($this->summarydata),
            'items' => [],
        ];
        foreach ($this->summarydata as $item) {
            if ($item['title'] instanceof renderable) {
                $title = $output->render($item['title']);
            } else {
                $title = $item['title'];
            }

            if ($item['content'] instanceof renderable) {
                $content = $output->render($item['content']);
            } else {
                $content = $item['content'];
            }

            $templatecontext['items'][] = (object) ['title' => $title, 'content' => $content];
        }

        return $templatecontext;
    }

    public function get_template_name(renderer_base $renderer): string {
        // Only reason we are forced to implement this is that we want the quiz renderer
        // passed to export_for_template, not a core_renderer.
        return 'mod_quiz/attempt_summary_information';
    }
}
