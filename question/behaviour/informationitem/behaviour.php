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
 * This behaviour is for information items.
 *
 * @package    qbehaviour
 * @subpackage informationitem
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Question behaviour informaiton items.
 *
 * For example for the 'Description' 'Question type'. There is no grade,
 * and the question type is marked complete the first time the user navigates
 * away from a page that contains that question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_informationitem extends question_behaviour {

    public function is_compatible_question(question_definition $question) {
        return true;
    }

    public function get_expected_data() {
        if ($this->qa->get_state() == question_state::$todo) {
            return array('seen' => PARAM_BOOL);
        }
        return parent::get_expected_data();
    }

    public function get_correct_response() {
        if ($this->qa->get_state() == question_state::$todo) {
            return array('seen' => 1);
        }
        return array();
    }

    public function adjust_display_options(question_display_options $options) {
        parent::adjust_display_options($options);

        $options->marks = question_display_options::HIDDEN;

        // At the moment, the code exists to process a manual comment on an
        // information item, but we don't display the UI unless there is already
        // a comment.
        if (!$this->qa->get_state()->is_commented()) {
            $options->manualcomment = question_display_options::HIDDEN;
        }
    }

    public function get_state_string($showcorrectness) {
        return '';
    }

    public function process_action(question_attempt_pending_step $pendingstep) {
        if ($pendingstep->has_behaviour_var('comment')) {
            return $this->process_comment($pendingstep);
        } else if ($pendingstep->has_behaviour_var('finish')) {
            return $this->process_finish($pendingstep);
        } else if ($pendingstep->has_behaviour_var('seen')) {
            return $this->process_seen($pendingstep);
        } else {
            return question_attempt::DISCARD;
        }
    }

    public function summarise_action(question_attempt_step $step) {
        if ($step->has_behaviour_var('comment')) {
            return $this->summarise_manual_comment($step);
        } else if ($step->has_behaviour_var('finish')) {
            return $this->summarise_finish($step);
        } else if ($step->has_behaviour_var('seen')) {
            return get_string('seen', 'qbehaviour_informationitem');
        }
        return $this->summarise_start($step);
    }

    public function process_comment(question_attempt_pending_step $pendingstep) {
        if ($pendingstep->has_behaviour_var('mark')) {
            throw new coding_exception('Information items cannot be graded.');
        }
        return parent::process_comment($pendingstep);
    }

    public function process_finish(question_attempt_pending_step $pendingstep) {
        $pendingstep->set_state(question_state::$finished);
        return question_attempt::KEEP;
    }

    public function process_seen(question_attempt_pending_step $pendingstep) {
        $pendingstep->set_state(question_state::$complete);
        return question_attempt::KEEP;
    }
}
