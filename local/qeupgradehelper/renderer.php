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
 * Defines the renderer for the question engine upgrade helper plugin.
 *
 * @package    local
 * @subpackage qeupgradehelper
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Renderer for the question engine upgrade helper plugin.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_qeupgradehelper_renderer extends plugin_renderer_base {

    /**
     * Render the index page.
     * @param string $detected information about what sort of site was detected.
     * @param array $actions list of actions to show on this page.
     * @return string html to output.
     */
    public function index_page($detected, array $actions) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading(get_string('pluginname', 'local_qeupgradehelper'));
        $output .= $this->box($detected);
        $output .= html_writer::start_tag('ul');
        foreach ($actions as $action) {
            $output .= html_writer::tag('li',
                    html_writer::link($action->url, $action->name) . ' - ' .
                    $action->description);
        }
        $output .= html_writer::end_tag('ul');
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render a page that is just a simple message.
     * @param string $message the message to display.
     * @return string html to output.
     */
    public function simple_message_page($message) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading($message);
        $output .= $this->back_to_index();
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render a table listing quizzes with other information.
     * @param array $quizzes of data about quizzes.
     * @param string $countheader
     * @param unknown_type $actionscript
     * @return string
     */
    public function quizzes_table($quizzes, $countheader, $actionscript = null) {
        $table = new html_table();
        $table->head = array(
            get_string('quizid', 'local_qeupgradehelper'),
            get_string('course'),
            get_string('pluginname', 'quiz'),
            $countheader,
        );
        if ($actionscript) {
            $table->head[] = get_string('actions', 'local_qeupgradehelper');
        }

        foreach ($quizzes as $quiz) {
            $row = array(
                $quiz->id,
                html_writer::link(new moodle_url('/course/view.php',
                        array('id' => $quiz->courseid)), format_string($quiz->shortname)),
                html_writer::link(new moodle_url('/mod/quiz/view.php',
                        array('id' => $quiz->name)), format_string($quiz->name)),
                $quiz->attemptcount,
            );
            if ($actionscript) {
                $row[] = html_writer::link(local_qeupgradehelper_url($actionscript, array('quizid' => $quiz->id)),
                        get_string($actionscript, 'local_qeupgradehelper'));
            }
            $table->data[] = $row;
        }

        return html_writer::table($table);
    }

    /**
     * Render the list of quizzes that still need to be upgraded page.
     * @param array $quizzes of data about quizzes.
     * @return string html to output.
     */
    public function list_quizzes_todo($quizzes) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading(get_string('quizzeswithunconverted', 'local_qeupgradehelper'));
        $output .= $this->box(get_string('listtodointro', 'local_qeupgradehelper'));

        $output .= $this->quizzes_table($quizzes,
                get_string('attemptstoconvert', 'local_qeupgradehelper'), 'convertquiz');

        $output .= $this->back_to_index();
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render the list of quizzes that can have the upgrade reset.
     * @param array $quizzes of data about quizzes.
     * @return string html to output.
     */
    public function list_quizzes_upgraded($quizzes) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading(get_string('quizzesthatcanbereset', 'local_qeupgradehelper'));
        $output .= $this->box(get_string('listupgradedintro', 'local_qeupgradehelper'));

        $output .= $this->quizzes_table($quizzes,
                get_string('convertedattempts', 'local_qeupgradehelper'), 'resetattempts');

        $output .= $this->back_to_index();
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render the list of quizzes that will need to be processed during the upgrade.
     * @param array $quizzes of data about quizzes.
     * @return string html to output.
     */
    public function list_quizzes_pre_upgrade($quizzes) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading(get_string('quizzestobeupgraded', 'local_qeupgradehelper'));
        $output .= $this->box(get_string('listpreupgradeintro', 'local_qeupgradehelper'));

        $output .= $this->quizzes_table($quizzes,
                get_string('numberofattempts', 'local_qeupgradehelper'));

        $output .= $this->back_to_index();
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render the are-you-sure page to confirm a manual upgrade.
     * @param object $quizsummary data about the quiz to upgrade.
     * @return string html to output.
     */
    public function convert_quiz_are_you_sure($quizsummary) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading(get_string('areyousure', 'local_qeupgradehelper'));

        $params = array('quizid' => $quizsummary->id, 'confirmed' => 1, 'sesskey' => sesskey());
        $output .= $this->confirm(get_string('areyousuremessage', 'local_qeupgradehelper', $quizsummary),
                new single_button(local_qeupgradehelper_url('convertquiz', $params), get_string('yes')),
                local_qeupgradehelper_url('listtodo'));

        $output .= $this->footer();
        return $output;
    }

    /**
     * Render the are-you-sure page to confirm a manual reset.
     * @param object $quizsummary data about the quiz to reset.
     * @return string html to output.
     */
    public function reset_quiz_are_you_sure($quizsummary) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading(get_string('areyousure', 'local_qeupgradehelper'));

        $params = array('quizid' => $quizsummary->id, 'confirmed' => 1, 'sesskey' => sesskey());
        $output .= $this->confirm(get_string('areyousureresetmessage', 'local_qeupgradehelper', $quizsummary),
                new single_button(local_qeupgradehelper_url('resetquiz', $params), get_string('yes')),
                local_qeupgradehelper_url('listupgraded'));

        $output .= $this->footer();
    }

    /**
     * Render a link in a div, such as the 'Back to plugin main page' link.
     * @param $url the link URL.
     * @param $text the link text.
     * @return string html to output.
     */
    public function end_of_page_link($url, $text) {
        return html_writer::tag('div', html_writer::link($url ,$text),
                array('class' => 'mdl-align'));
    }

    /**
     * Output a link back to the plugin index page.
     * @return string html to output.
     */
    public function back_to_index() {
        return $this->end_of_page_link(local_qeupgradehelper_url('index'),
                get_string('backtoindex', 'local_qeupgradehelper'));
    }
}
