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
 * Test helpers for the drag-and-drop onto image question type.
 *
 * @package    qtype_ddimageortext
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the drag-and-drop onto image question type.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddimageortext_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('fox', 'maths');
    }

    /**
     * @return qtype_ddimageortext_question
     */
    public function make_ddimageortext_question_fox() {
        question_bank::load_question_definition_classes('ddimageortext');
        $dd = new qtype_ddimageortext_question();

        test_question_maker::initialise_a_question($dd);

        $dd->name = 'Drag-and-drop onto image question';
        $dd->questiontext = 'The quick brown fox jumped over the lazy dog.';
        $dd->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $dd->qtype = question_bank::get_qtype('ddimageortext');

        $dd->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($dd);

        $dd->choices = $this->make_choice_structure(array(
                    new qtype_ddimageortext_drag_item('quick', 1, 1),
                    new qtype_ddimageortext_drag_item('fox', 2, 1),
                    new qtype_ddimageortext_drag_item('lazy', 1, 2),
                    new qtype_ddimageortext_drag_item('dog', 2, 2)

        ));

        $dd->places = $this->make_place_structure(array(
                            new qtype_ddimageortext_drop_zone('', 1, 1),
                            new qtype_ddimageortext_drop_zone('', 2, 1),
                            new qtype_ddimageortext_drop_zone('', 3, 2),
                            new qtype_ddimageortext_drop_zone('', 4, 2)
        ));
        $dd->rightchoices = array(1 => 1, 2 => 2, 3 => 1, 4 => 2);

        return $dd;
    }

    protected function make_choice_structure($choices) {
        $choicestructure = array();
        foreach ($choices as $choice) {
            if (!isset($choicestructure[$choice->group])) {
                $choicestructure[$choice->group] = array();
            }
            $choicestructure[$choice->group][$choice->no] = $choice;
        }
        return $choicestructure;
    }

    protected function make_place_structure($places) {
        $placestructure = array();
        foreach ($places as $place) {
            $placestructure[$place->no] = $place;
        }
        return $placestructure;
    }

    /**
     * @return qtype_ddimageortext_question
     */
    public function make_ddimageortext_question_maths() {
        question_bank::load_question_definition_classes('ddimageortext');
        $dd = new qtype_ddimageortext_question();

        test_question_maker::initialise_a_question($dd);

        $dd->name = 'Drag-and-drop onto image question';
        $dd->questiontext = 'Fill in the operators to make this equation work: ' .
                '7 [[1]] 11 [[2]] 13 [[1]] 17 [[2]] 19 = 3';
        $dd->generalfeedback = 'This sentence uses each letter of the alphabet.';
        $dd->qtype = question_bank::get_qtype('ddimageortext');

        $dd->shufflechoices = true;

        test_question_maker::set_standard_combined_feedback_fields($dd);

        $dd->choices = $this->make_choice_structure(array(
                new qtype_ddimageortext_drag_item('+', 1, 1),
                new qtype_ddimageortext_drag_item('-', 2, 1)
        ));

        $dd->places = $this->make_place_structure(array(
                            new qtype_ddimageortext_drop_zone('', 1, 1),
                            new qtype_ddimageortext_drop_zone('', 2, 1),
                            new qtype_ddimageortext_drop_zone('', 3, 1),
                            new qtype_ddimageortext_drop_zone('', 4, 1)
        ));
        $dd->rightchoices = array(1 => 1, 2 => 2, 3 => 1, 4 => 2);

        return $dd;
    }
}
