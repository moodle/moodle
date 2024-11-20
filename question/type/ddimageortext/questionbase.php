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
 * Drag-and-drop onto image question definition class.
 *
 * @package    qtype_ddimageortext
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/questionbase.php');
require_once($CFG->dirroot . '/question/type/gapselect/questionbase.php');


/**
 * Represents a drag-and-drop onto image question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddtoimage_question_base extends qtype_gapselect_question_base {
    public function clear_wrong_from_response(array $response) {
        foreach ($this->places as $place => $notused) {
            if (array_key_exists($this->field($place), $response) &&
                    $response[$this->field($place)] != $this->get_right_choice_for($place)) {
                $response[$this->field($place)] = '';
            }
        }
        return $response;
    }

    public function get_right_choice_for($placeno) {
        $place = $this->places[$placeno];
        foreach ($this->choiceorder[$place->group] as $choicekey => $choiceid) {
            if ($this->rightchoices[$placeno] == $choiceid) {
                return $choicekey;
            }
        }
    }
    public function summarise_response(array $response) {
        $allblank = true;
        foreach ($this->places as $placeno => $place) {
            $summariseplace = $place->summarise();
            if (array_key_exists($this->field($placeno), $response) &&
                                                                $response[$this->field($placeno)]) {
                $selected = $this->get_selected_choice($place->group,
                                                                $response[$this->field($placeno)]);
                if (isset($selected)) {
                    $summarisechoice = $selected->summarise();
                } else {
                    $summarisechoice = get_string('deletedchoice', 'qtype_ddimageortext');
                }
                $allblank = false;
            } else {
                $summarisechoice = '';
            }
            $choices[] = "$summariseplace -> {{$summarisechoice}}";
        }
        if ($allblank) {
            return null;
        }
        return implode(' ', $choices);
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($filearea == 'bgimage' || $filearea == 'dragimage') {
            $validfilearea = true;
        } else {
            $validfilearea = false;
        }
        if ($component == 'qtype_ddimageortext' && $validfilearea) {
            $question = $qa->get_question();
            $itemid = reset($args);
            if ($filearea == 'bgimage') {
                return $itemid == $question->id;
            } else if ($filearea == 'dragimage') {
                foreach ($question->choices as $group) {
                    foreach ($group as $drag) {
                        if ($drag->id == $itemid) {
                            return true;
                        }
                    }
                }
                return false;
            }
        } else {
            return parent::check_file_access($qa, $options, $component,
                                                                $filearea, $args, $forcedownload);
        }
    }
    public function get_validation_error(array $response) {
        if ($this->is_complete_response($response)) {
            return '';
        }
        return get_string('pleasedraganimagetoeachdropregion', 'qtype_ddimageortext');
    }

    public function classify_response(array $response) {
        $parts = array();
        foreach ($this->places as $placeno => $place) {
            $group = $place->group;
            if (!array_key_exists($this->field($placeno), $response) ||
                    !$response[$this->field($placeno)]) {
                $parts[$placeno] = question_classified_response::no_response();
                continue;
            }

            $fieldname = $this->field($placeno);
            $choicekey = $this->choiceorder[$group][$response[$fieldname]];
            $choice = $this->choices[$group][$choicekey];

            $correct = $this->get_right_choice_for($placeno) == $response[$fieldname];
            if ($correct) {
                $grade = 1;
            } else {
                $grade = 0;
            }
            $parts[$placeno] = new question_classified_response($choice->no, $choice->summarise(), $grade);
        }
        return $parts;
    }

    public function get_random_guess_score() {
        if (empty($this->places)) {
            // Having no places would be nonsensical. However, it used to be possible
            // to create questions like that, so avoid errors in this case.
            return null;
        }

        $accum = 0;
        foreach ($this->places as $place) {
            foreach ($this->choices[$place->group] as $choice) {
                if ($choice->infinite) {
                    return null;
                }
            }
            $accum += 1 / count($this->choices[$place->group]);
        }

        return $accum / count($this->places);
    }


    public function get_question_summary() {
        $summary = '';
        if (!html_is_blank($this->questiontext)) {
            $question = $this->html_to_text($this->questiontext, $this->questiontextformat);
            $summary .= $question . '; ';
        }
        $places = array();
        foreach ($this->places as $place) {
            $cs = array();
            foreach ($this->choices[$place->group] as $choice) {
                $cs[] = $choice->summarise();
            }
            $places[] = '[[' . $place->summarise() . ']] -> {' . implode(' / ', $cs) . '}';
        }
        $summary .= implode('; ', $places);
        return $summary;
    }
}
