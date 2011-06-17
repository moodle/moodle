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
 * Question behaviour that is like the interactive behaviour, but where the
 * student is credited for parts of the question they got right on earlier tries.
 *
 * @package    qbehaviour
 * @subpackage interactivecountback
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../interactive/behaviour.php');


/**
 * Question behaviour for interactive mode with count-back scoring.
 *
 * As an example, suppose we have a matching question with 4 parts, and 3 tries
 * (penalty 1/3), and the question is worth 12 marks (so, 3 marks for each part).
 * Suppose also that:
 *  - on the first try, the student gets the first two parts right, and the
 *    other two wrong.
 *  - on the second try, they are sure they got the first part right, so keep
 *    their answer the same, but they change their answer to the second part.
 *    They also get the answer to the thrid part right on this try, but still
 *    get the 4th part wrong.
 *  - On the final try, they get the first 3 parts right, but the 4th part still
 *    wrong.
 * We want to grade them as follows.
 *  - For the first part, they were right first time, and did not change their
 *    answer, so we credit that part as right first time: 3/3
 *  - For the second part, although they were right first time, they then changed
 *    their mind, an only finally got it right on the third try, so 1/3.
 *  - For the third part, they got it right on the second try, and then did not
 *    change their answer, so 2/3.
 *  - For the last part, they were wrong at the last try, so 0/3.
 * So, total mark is 6/12. (Really, a fraction of 0.5.)
 *
 * Of course, the details of the grading are acutally up to the particular
 * question type. The point is that the final grade can take into account all
 * of the tries the student made.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_interactivecountback extends qbehaviour_interactive {
    const IS_ARCHETYPAL = false;

    public static function get_required_behaviours() {
        return array('interactive');
    }

    public function required_question_definition_type() {
        return 'question_automatically_gradable_with_countback';
    }

    protected function adjust_fraction($fraction, question_attempt_pending_step $pendingstep) {
        $totaltries = $this->qa->get_step(0)->get_behaviour_var('_triesleft');

        $responses = array();
        $lastsave = array();
        foreach ($this->qa->get_step_iterator() as $step) {
            if ($step->has_behaviour_var('submit') &&
                    $step->get_state() != question_state::$invalid) {
                $responses[] = $step->get_qt_data();
                $lastsave = array();
            } else {
                $lastsave = $step->get_qt_data();
            }
        }
        $lastresponse = $pendingstep->get_qt_data();
        if (!empty($lastresponse)) {
            $responses[] = $lastresponse;
        } else if (!empty($lastsave)) {
            $responses[] = $lastsave;
        }

        return $this->question->compute_final_grade($responses, $totaltries);
    }
}
