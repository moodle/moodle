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

namespace mod_adaptivequiz\local\questionanalysis\statistics;

use html_writer;
use mod_adaptivequiz\local\questionanalysis\question_analyser;
use moodle_url;
use stdClass;

/**
 * This interface defines the methods required for pluggable statistics that may be added to the question analysis.
 *
 * @package    mod_adaptivequiz
 * @copyright  2013 Middlebury College {@link http://www.middlebury.edu/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class answers_statistic implements question_statistic {
    /**
     * Answer a display-name for this statistic.
     *
     * @return string
     */
    public function get_display_name () {
        return get_string('answers_display_name', 'adaptivequiz');
    }

    /**
     * Calculate this statistic for a question's results.
     *
     * @param question_analyser $analyser
     * @return question_statistic_result
     */
    public function calculate(question_analyser $analyser): question_statistic_result {
        // Sort the results.
        $results = $analyser->get_results();
        foreach ($results as $result) {
            $sortkeys[] = $result->score->measured_ability_in_logits();
        }
        array_multisort($sortkeys, SORT_NUMERIC, SORT_DESC, $results);

        // Sort the results into three arrays based on how far above or below the question-level the users are.
        $high = array();
        $mid = array();
        $low = array();
        foreach ($results as $result) {
            $ceiling = $result->score->measured_ability_in_logits() + $result->score->standard_error_in_logits();
            $floor = $result->score->measured_ability_in_logits() - $result->score->standard_error_in_logits();
            if ($analyser->get_question_level_in_logits() < $floor) {
                // User is significantly above the question-level.
                $high[] = $result;
            } else if ($analyser->get_question_level_in_logits() > $ceiling) {
                // User is significantly below the question-level.
                $low[] = $result;
            } else {
                // User's ability overlaps the question level.
                $mid[] = $result;
            }
        }

        ob_start();
        print html_writer::end_tag('tr');
        print html_writer::start_tag('tr');
        print html_writer::tag('th', get_string('attemptquestion_ability', 'adaptivequiz'));
        print html_writer::tag('th', get_string('user', 'adaptivequiz'));
        print html_writer::tag('th', get_string('result', 'adaptivequiz'));
        print html_writer::tag('th', get_string('answer', 'adaptivequiz'));
        print html_writer::tag('th', '');
        print html_writer::end_tag('tr');
        $headings = ob_get_clean();

        ob_start();
        print html_writer::start_tag('table', array('class' => 'adpq_answers_table'));

        print html_writer::start_tag('thead');
        print html_writer::start_tag('tr');
        print html_writer::tag('th', get_string('highlevelusers', 'adaptivequiz').':',
            array('colspan' => '5', 'class' => 'section'));
        print $headings;
        print html_writer::end_tag('thead');

        print html_writer::start_tag('tbody', array('class' => 'adpq_highlevel'));
        if (count($high)) {
            foreach ($high as $result) {
                $this->print_user_result($result);
            }
        } else {
            $this->print_empty_user_result();
        }
        print html_writer::end_tag('tbody');

        print html_writer::start_tag('thead');
        print html_writer::start_tag('tr');
        print html_writer::tag('th', get_string('midlevelusers', 'adaptivequiz').':',
            array('colspan' => '5', 'class' => 'section'));
        print $headings;
        print html_writer::end_tag('thead');

        print html_writer::start_tag('tbody', array('class' => 'adpq_midlevel'));
        if (count($mid)) {
            foreach ($mid as $result) {
                $this->print_user_result($result);
            }
        } else {
            $this->print_empty_user_result();
        }
        print html_writer::end_tag('tbody');

        print html_writer::start_tag('thead');
        print html_writer::start_tag('tr');
        print html_writer::tag('th', get_string('lowlevelusers', 'adaptivequiz').':',
            array('colspan' => '5', 'class' => 'section'));
        print $headings;
        print html_writer::end_tag('thead');

        print html_writer::start_tag('tbody', array('class' => 'adpq_lowlevel'));
        if (count($low)) {
            foreach ($low as $result) {
                $this->print_user_result($result);
            }
        } else {
            $this->print_empty_user_result();
        }
        print html_writer::end_tag('tbody');

        print html_writer::end_tag('table');

        return new answers_statistic_result(count($results), ob_get_clean());
    }

    /**
     * Print out a user result.
     *
     * @param stdClass $result
     */
    public function print_user_result(stdClass $result): void {
        if ($result->correct) {
            $class = 'adpq_correct';
        } else {
            $class = 'adpq_incorrect';
        }
        $url = new moodle_url('/mod/adaptivequiz/reviewattempt.php', ['attempt' => $result->attemptid]);
        print html_writer::start_tag('tr', ['class' => $class]);
        print html_writer::tag('td', round($result->score->measured_ability_in_scale(), 2));
        print html_writer::tag('td', $result->user->firstname." ".$result->user->lastname);
        print html_writer::tag('td', (($result->correct) ? "correct" : "incorrect"));
        print html_writer::tag('td', $result->answer);
        print html_writer::tag('td', html_writer::link($url, get_string('reviewattempt', 'adaptivequiz')));
        print html_writer::end_tag('tr');
    }

    /**
     * Print out an empty user-result row.
     *
     * @param question_analyser $analyser
     * @param stdClass $result
     * @return void
     */
    public function print_empty_user_result () {
        print html_writer::start_tag('tr');
        print html_writer::tag('td', '');
        print html_writer::tag('td', '');
        print html_writer::tag('td', '');
        print html_writer::tag('td', '');
        print html_writer::tag('td', '');
        print html_writer::end_tag('tr');
    }
}
