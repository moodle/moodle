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
 * Upgrade library code for the numerical question type.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Class for converting attempt data for numerical questions when upgrading
 * attempts to the new question engine.
 *
 * This class is used by the code in question/engine/upgrade/upgradelib.php.
 *
 * TODO update for the changes in Moodle 2.0.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_qe2_attempt_updater extends question_qtype_attempt_updater {
    public function right_answer() {
        foreach ($this->question->options->answers as $ans) {
            if ($ans->fraction > 0.999) {
                return $ans->answer;
            }
        }
    }

    public function response_summary($state) {
        if (!empty($state->answer)) {
            return $state->answer;
        } else {
            return null;
        }
    }

    public function was_answered($state) {
        return !empty($state->answer);
    }

    public function set_first_step_data_elements($state, &$data) {
        $data['_separators'] = '.$,';
    }

    public function supply_missing_first_step_data(&$data) {
        $data['_separators'] = '.$,';
    }

    public function set_data_elements_for_step($state, &$data) {
        if (empty($state->answer)) {
            return;
        }
        if (strpos($state->answer, '|||||') === false) {
            $data['answer'] = $state->answer;
        } else {
            list($answer, $unit) = explode('|||||', $state->answer, 2);
            if ($this->question->options->showunits == 1) {
                // Multichoice units.
                $data['answer'] = $answer;
                $data['unit'] = $unit;
            } else if (!empty($this->question->options->unitsleft)) {
                if (!empty($unit)) {
                    $data['answer'] = $unit . ' ' . $answer;
                } else {
                    $data['answer'] = $answer;
                }
            } else {
                if (!empty($unit)) {
                    $data['answer'] = $answer . ' ' . $unit;
                } else {
                    $data['answer'] = $answer;
                }
            }
        }
    }
}
