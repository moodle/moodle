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

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../deferredfeedback/behaviour.php');

/**
 * Question behaviour for deferred feedback with explanation.
 *
 * This is like the standard deferred feedback behaviour, but with an extra
 * text input box where the student can explain their reasoning. That part is
 * un-graded, but the teacher could read it later and manually adjust the marks
 * based on it. The student can also review it later, to be reminded what they
 * were thinking at the time they answered the question.
 *
 * @package   qbehaviour_deferredfeedbackexplain
 * @copyright 2014 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_deferredfeedbackexplain extends qbehaviour_deferredfeedback {
    public function get_expected_data(): array {
        if ($this->qa->get_state()->is_active()) {
            return [
                'explanation'       => PARAM_RAW,
                'explanationformat' => PARAM_ALPHANUMEXT
            ];
        }
        return parent::get_expected_data();
    }

    protected function get_our_resume_data(): array {
        $lastexplanation = $this->qa->get_last_behaviour_var('explanation');
        if ($lastexplanation) {
            return [
                '-explanation'       => $lastexplanation,
                '-explanationformat' => $this->qa->get_last_behaviour_var('explanationformat'),
            ];
        } else {
            return [];
        }
    }

    protected function is_same_response(question_attempt_step $pendingstep): bool {
        return parent::is_same_response($pendingstep) &&
                $this->qa->get_last_behaviour_var('explanation') == $pendingstep->get_behaviour_var('explanation') &&
                $this->qa->get_last_behaviour_var('explanationformat') == $pendingstep->get_behaviour_var('explanationformat');
    }

    public function summarise_action(question_attempt_step $step): string {
        return $this->add_explanation(parent::summarise_action($step), $step);
    }

    public function process_action(question_attempt_pending_step $pendingstep): bool {
        $result = parent::process_action($pendingstep);

        if ($result == question_attempt::KEEP && $pendingstep->response_summary_changed()) {
            $explanationstep = $this->qa->get_last_step_with_behaviour_var('explanation');
            $pendingstep->set_new_response_summary($this->add_explanation(
                    $pendingstep->get_new_response_summary(), $explanationstep));
        }

        return $result;
    }

    protected function add_explanation($text, question_attempt_step $step): string {
        $explanation = $step->get_behaviour_var('explanation');
        if (!$explanation) {
            return $text;
        }

        $a = new stdClass();
        $a->response = $text;
        $a->explanation = question_utils::to_plain_text($explanation,
                $step->get_behaviour_var('explanationformat'), ['para' => false]);
        return get_string('responsewithreason', 'qbehaviour_deferredfeedbackexplain', $a);
    }
}
