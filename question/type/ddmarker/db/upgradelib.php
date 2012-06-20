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
 * Upgrade library code for the ddmarker question type. This will only get triggered by the code
 * to convert imagetarget questions to ddmarker.
 *
 * @package    qtype
 * @subpackage ddmarker
 * @copyright  2012 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

/**
 * Class for converting attempt data from imagetarget questions when converting
 * attempts to the new question engine.
 *
 * This class is used by the code in question/engine/upgrade/upgradelib.php.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_qe2_attempt_updater extends question_qtype_attempt_updater {
    public function right_answer() {
        $drag = reset($this->question->options->drags);
        return '{'.get_string('dropzone', 'qtype_ddmarker', '1')." -> ".$drag->label.'}';
    }

    public function was_answered($state) {
        return !empty($state->answer);
    }

    public function response_summary($state) {
        if (!empty($state->answer)) {
            $drag = reset($this->question->options->drags);
            foreach ($this->question->options->drops as $drop) {
                list($xy, $wh) = explode(';', $drop->coords);
                list($x, $y) = explode(',', $xy);
                list($w, $h) = explode(',', $wh);
                list($answerx, $answery) = explode(',', $state->answer);
                if (($answerx >= $x && $answerx <= ($x + $w)) && ($answery >= $y && $answery <= ($y + $h))) {
                    return '{'.get_string('dropzone', 'qtype_ddmarker', $drop->no)." -> ".$drag->label.'}';
                }
            }
            return '';
        } else {
            return null;
        }
    }

    public function question_summary() {
        $drag = reset($this->question->options->drags);
        return parent::question_summary().'[['.get_string('dropzone', 'qtype_ddmarker', '1')."]] -> {".$drag->label.'}';
    }

    public function set_first_step_data_elements($state, &$data) {
        $data['_choiceorder1'] = '1';
    }

    public function supply_missing_first_step_data(&$data) {
    }

    public function set_data_elements_for_step($state, &$data) {
        if (!empty($state->answer)) {
            $data['c1'] = $state->answer;
        }
    }
}
