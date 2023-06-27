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

/**
 * Defines the renderer for the quiz_grading module.
 *
 * @package   quiz_grading
 * @copyright 2018 Huong Nguyen <huongnv13@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The renderer for the quiz_grading module.
 *
 * @copyright  2018 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_grading_renderer extends plugin_renderer_base {

    /**
     * Render no question notification.
     *
     * @param object $quiz The quiz settings.
     * @param object $cm The course-module for this quiz.
     * @param object $context The quiz context.
     * @return string The HTML for the no questions message.
     */
    public function render_quiz_no_question_notification($quiz, $cm, $context) {
        return quiz_no_questions_message($quiz, $cm, $context);
    }

    /**
     * Render no question need to grade notification.
     *
     * @throws coding_exception
     */
    public function render_quiz_no_grade_question_notification() {
        return $this->notification(get_string('nothingfound', 'quiz_grading'));
    }

    /**
     * Render index display.
     *
     * @param string $linktext The text of the link.
     * @param moodle_url $listquestionurl Url of the page that list all questions.
     * @return string The HTML for the display heading.
     * @throws coding_exception
     */
    public function render_display_index_heading($linktext, $listquestionurl) {
        $output = '';

        $output .= $this->heading(get_string('questionsthatneedgrading', 'quiz_grading'), 3);
        $output .= html_writer::tag('p', html_writer::link($listquestionurl, $linktext), ['class' => 'toggleincludeauto']);

        return $output;
    }

    /**
     * Render questions list table.
     *
     * @param bool $includeauto True to show automatically graded questions.
     * @param array $data List of questions.
     * @param array $header List of table headers.
     * @return string The HTML for the question table.
     * @throws coding_exception
     */
    public function render_questions_table($includeauto, $data, $header) {
        if (empty($data)) {
            return $this->render_quiz_no_grade_question_notification();
        }
        $output = '';

        $table = new html_table();
        $table->class = 'generaltable';
        $table->id = 'questionstograde';
        $table->head = $header;
        $table->data = $data;

        $output .= html_writer::table($table);

        return $output;
    }

    /**
     * Render grade link for question.
     *
     * @param object $counts
     * @param string $type Type of grade.
     * @param string $gradestring Lang string.
     * @param moodle_url $gradequestionurl Url to grade question.
     * @return string The HTML for the question grade link.
     * @throws coding_exception
     */
    public function render_grade_link($counts, $type, $gradestring, $gradequestionurl) {
        $output = '';
        if ($counts->$type > 0) {
            $output .= ' ' . html_writer::link(
                            $gradequestionurl,
                            get_string($gradestring, 'quiz_grading'),
                            ['class' => 'gradetheselink']);
        }
        return $output;
    }

    /**
     * Render grading page.
     *
     * @param object $questioninfo Information of a question.
     * @param moodle_url $listquestionsurl Url of the page that list all questions.
     * @param quiz_grading_settings_form $filterform Question filter form.
     * @param object $paginginfo Pagination information.
     * @param object $pagingbar Pagination bar information.
     * @param moodle_url $formaction Form submit url.
     * @param array $hiddeninputs List of hidden input fields.
     * @param string $gradequestioncontent HTML string of question content.
     * @return string The HTML for the grading interface.
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function render_grading_interface($questioninfo, $listquestionsurl, $filterform, $paginginfo, $pagingbar, $formaction,
            $hiddeninputs, $gradequestioncontent) {
        $output = '';

        $output .= question_engine::initialise_js();

        $output .= $this->heading(get_string('gradingquestionx', 'quiz_grading', $questioninfo), 3);

        $output .= html_writer::tag('p', html_writer::link($listquestionsurl,
                get_string('backtothelistofquestions', 'quiz_grading')),
                ['class' => 'mdl-align']);

        $output .= $filterform->render();

        $output .= $this->heading(get_string('gradingattemptsxtoyofz', 'quiz_grading', $paginginfo), 3);

        if ($pagingbar->count > $pagingbar->pagesize && $pagingbar->order != 'random') {
            $output .= $this->paging_bar($pagingbar->count, $pagingbar->page, $pagingbar->pagesize, $pagingbar->pagingurl);
        }

        $output .= html_writer::start_tag('form', [
                'method' => 'post',
                'action' => $formaction,
                'class' => 'mform',
                'id' => 'manualgradingform'
        ]);
        $output .= html_writer::start_tag('div');
        $output .= html_writer::input_hidden_params(new moodle_url('', $hiddeninputs));

        $output .= $gradequestioncontent;

        $output .= html_writer::tag('div', html_writer::empty_tag('input', [
                'type' => 'submit',
                'class' => 'btn btn-primary',
                'value' => get_string('saveandnext', 'quiz_grading')
        ]), ['class' => 'mdl-align']);
        $output .= html_writer::end_tag('div') . html_writer::end_tag('form');

        $this->page->requires->string_for_js('changesmadereallygoaway', 'moodle');
        $this->page->requires->yui_module('moodle-core-formchangechecker',
                'M.core_formchangechecker.init', [['formid' => 'manualgradingform']]);
        return $output;
    }

    /**
     * Render grade question content.
     *
     * @param question_usage_by_activity $questionusage The question usage that need to grade.
     * @param int $slot the number used to identify this question within this usage.
     * @param question_display_options $displayoptions the display options to use.
     * @param int $questionnumber the number of the question to check.
     * @param string $heading the question heading text.
     * @return string The HTML for the question display.
     */
    public function render_grade_question($questionusage, $slot, $displayoptions, $questionnumber, $heading) {
        $output = '';

        if ($heading) {
            $output .= $this->heading($heading, 4);
        }

        $output .= $questionusage->render_question($slot, $displayoptions, $questionnumber);

        return $output;
    }
}
