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
 * Upgrade library code for the shortanswer question type.
 *
 * @package    qtype_regexp
 * @copyright  2011 Joseph Rézeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class for converting attempt data for shortanswer questions when upgrading
 * attempts to the new question engine.
 *
 * This class is used by the code in question/engine/upgrade/upgradelib.php.
 *
 * @copyright  2011 Joseph Rézeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_regexp_qe2_attempt_updater extends question_qtype_attempt_updater {
    /**
     * Returns right answer
     */
    public function right_answer() {
        return $this->question->options->correctanswer;
    }

    /**
     * Get state.
     * @param array $state
     * @return array
     */
    public function was_answered($state) {
        return !empty($state->answer);
    }

    /**
     * Get state.
     * @param array $state
     * @return array
     */
    public function response_summary($state) {
        if (!empty($state->answer)) {
            return $state->answer;
        } else {
            return null;
        }
    }

    /**
     * Get state.
     * @param array $state
     * @param array $data
     */
    public function set_first_step_data_elements($state, &$data) {
    }

    /**
     * Get state.
     * @param array $data
     */
    public function supply_missing_first_step_data(&$data) {
    }

    /**
     * Get state.
     * @param array $state
     * @param array $data
     */
    public function set_data_elements_for_step($state, &$data) {
        if (!empty($state->answer)) {
            $data['answer'] = $state->answer;
        }
    }
}
