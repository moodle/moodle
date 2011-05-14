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
 * Question type class for the drag-and-drop words into sentences question type.
 *
 * @package    qtype
 * @subpackage ddwtos
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/type/gapselect/questiontypebase.php');


/**
 * The drag-and-drop words into sentences question type class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddwtos extends qtype_gapselect_base {
    protected function choice_group_key() {
        return 'draggroup';
    }

    public function requires_qtypes() {
        return array('gapselect');
    }

    protected function choice_options_to_feedback($choice) {
        $output = new stdClass();
        $output->draggroup = $choice['choicegroup'];
        $output->infinite = !empty($choice['infinite']);
        return serialize($output);
    }

    protected function feedback_to_choice_options($feedback) {
        $feedbackobj = unserialize($feedback);
        return array('draggroup' => $feedbackobj->draggroup, 'infinite' => $feedbackobj->infinite);
    }

    protected function make_choice($choicedata) {
        $options = unserialize($choicedata->feedback);
        return new qtype_ddwtos_choice(
                $choicedata->answer, $options->draggroup, $options->infinite);
    }

    public function import_from_xml($data, $question, $format, $extra=null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'ddwtos') {
            return false;
        }

        $question = $format->import_headers($data);
        $question->qtype = 'ddwtos';

        $question->shuffleanswers = $format->trans_single(
                $format->getpath($data, array('#', 'shuffleanswers', 0, '#'), 1));

        if (!empty($data['#']['dragbox'])) {
            // Modern XML format.
            $dragboxes = $data['#']['dragbox'];
            $question->answer = array();
            $question->draggroup = array();
            $question->infinite = array();

            foreach ($data['#']['dragbox'] as $dragboxxml) {
                $question->choices[] = array(
                    'answer' => $format->getpath($dragboxxml, array('#', 'text', 0, '#'), '', true),
                    'choicegroup' => $format->getpath($dragboxxml, array('#', 'group', 0, '#'), 1),
                    'infinite' => array_key_exists('infinite', $dragboxxml['#']),
                );
            }

        } else {
            // Legacy format containing PHP serialisation.
            foreach ($data['#']['answer'] as $answerxml) {
                $ans = $format->import_answer($answerxml);
                $options = unserialize(stripslashes($ans->feedback['text']));
                $question->choices[] = array(
                    'answer' => $ans->answer,
                    'choicegroup' => $options->draggroup,
                    'infinite' => $options->infinite,
                );
            }
        }

        $format->import_combined_feedback($question, $data, true);
        $format->import_hints($question, $data, true);

        return $question;
    }

    public function export_to_xml($question, $format, $extra = null) {
        $output = '';

        $output .= '    <shuffleanswers>' . $question->options->shuffleanswers .
                "</shuffleanswers>\n";

        $output .= $format->write_combined_feedback($question->options);

        foreach ($question->options->answers as $answer) {
            $options = unserialize($answer->feedback);

            $output .= "    <dragbox>\n";
            $output .= $format->writetext($answer->answer, 3);
            $output .= "      <group>{$options->draggroup}</group>\n";
            if ($options->infinite) {
                $output .= "      <infinite/>\n";
            }
            $output .= "    </dragbox>\n";
        }

        return $output;
    }
}
