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
 * Drag-and-drop words into sentences question definition class.
 *
 * @package    qtype
 * @subpackage ddmarker
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/ddimageortext/questionbase.php');
require_once($CFG->dirroot . '/question/type/ddmarker/shapes.php');


/**
 * Represents a drag-and-drop images to image question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_question extends qtype_ddtoimage_question_base {

    public $showmisplaced;

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($filearea == 'bgimage') {
            $validfilearea = true;
        } else {
            $validfilearea = false;
        }
        if ($component == 'qtype_ddmarker' && $validfilearea) {
            $question = $qa->get_question();
            $itemid = reset($args);
            return $itemid == $question->id;
        } else {
            return parent::check_file_access($qa, $options, $component,
                                                                $filearea, $args, $forcedownload);
        }
    }
    /**
     * @param int $key stem number
     * @return string the question-type variable name.
     */
    public function choice($choice) {
        return 'c' . $choice;
    }

    public function get_expected_data() {
        $vars = array();
        foreach ($this->choices[1] as $choice => $notused) {
            $vars[$this->choice($choice)] = PARAM_NOTAGS;
        }
        return $vars;
    }
    public function is_complete_response(array $response) {
        foreach ($this->choices[1] as $choice => $notused) {
            if ('' != trim($response[$this->choice($choice)])) {
                return true;
            }
        }
        return false;
    }
    public function is_gradable_response(array $response) {
        return $this->is_complete_response($response);
    }
    public function is_same_response(array $prevresponse, array $newresponse) {
        foreach ($this->choices[1] as $choice => $notused) {
            $fieldname = $this->choice($choice);
            if (!$this->arrays_same_at_key_integer(
                    $prevresponse, $newresponse, $fieldname)) {
                return false;
            }
        }
        return true;
    }
    /**
     * Tests to see whether two arrays have the same set of coords at a particular key. Coords
     * can be in any order.
     * @param array $array1 the first array.
     * @param array $array2 the second array.
     * @param string $key an array key.
     * @return bool whether the two arrays have the same set of coords (or lack of them)
     * for a given key.
     */
    public function arrays_same_at_key_integer(
            array $array1, array $array2, $key) {
        if (array_key_exists($key, $array1)) {
            $value1 = $array1[$key];
        } else {
            $value1 = '';
        }
        if (array_key_exists($key, $array2)) {
            $value2 = $array2[$key];
        } else {
            $value2 = '';
        }
        $coords1 = explode(';', $value1);
        $coords2 = explode(';', $value2);
        if (count($coords1) !== count($coords1)) {
            return false;
        } else if (count($coords1) === 0) {
            return true;
        } else {
            $valuesinbotharrays = array_intersect($coords1, $coords2);
            return (count($valuesinbotharrays) == count($coords1) &&
                                                count($valuesinbotharrays) == count($coords2));
        }
    }
    public function get_validation_error(array $response) {
        if ($this->is_complete_response($response)) {
            return '';
        }
        return get_string('pleasedragatleastonemarker', 'qtype_ddmarker');
    }
    public function get_num_parts_right(array $response) {
        $chosenhits = $this->choose_hits($response);
        $divisor = max(count($this->rightchoices), $this->total_number_of_items_dragged($response));
        return array(count($chosenhits), $divisor);
    }
    /**
     * Choose hits to maximize grade where drop targets may have more than one hit and drop targets
     * can overlap.
     * @param array $response
     * @return array chosen hits
     */
    protected function choose_hits(array $response) {
        $allhits = $this->get_all_hits($response);
        $chosenhits = array();
        foreach ($allhits as $placeno => $hits) {
            foreach ($hits as $itemno => $hit) {
                $choice = $this->get_right_choice_for($placeno);
                $choiceitem = "$choice $itemno";
                if (!in_array($choiceitem, $chosenhits)) {
                    $chosenhits[$placeno] = $choiceitem;
                    break;
                }
            }
        }
        return $chosenhits;
    }
    public function total_number_of_items_dragged(array $response) {
        $total = 0;
        foreach ($this->choiceorder[1] as $choice) {
            $choicekey = $this->choice($choice);
            if (array_key_exists($choicekey, $response) && trim($response[$choicekey] !== '')) {
                $total += count(explode(';', $response[$choicekey]));
            }
        }
        return $total;
    }

    /**
     * Get's an array of all hits on drop targets. Needs further processing to find which hits
     * to select in the general case that drop targets may have more than one hit and drop targets
     * can overlap.
     * @param array $response
     * @return array all hits
     */
    protected function get_all_hits(array $response) {
        $hits = array();
        foreach ($this->places as $placeno => $place) {
            $rightchoice = $this->get_right_choice_for($placeno);
            $rightchoicekey = $this->choice($rightchoice);
            if (!array_key_exists($rightchoicekey, $response)) {
                continue;
            }
            $choicecoords = $response[$rightchoicekey];
            $coords = explode(';', $choicecoords);
            foreach ($coords as $itemno => $coord) {
                if (trim($coord) === '') {
                    continue;
                }
                $pointxy = explode(',', $coord);
                if ($place->drop_hit($pointxy)) {
                    if (!isset($hits[$placeno])) {
                        $hits[$placeno] = array();
                    }
                    $hits[$placeno][$itemno] = $coord;
                }
            }
        }
        //reverse sort in order of number of hits per place
        //(if two or more hits per place then we want to make sure hits do not hit elsewhere)
        $sortcomparison = function ($a1, $a2){
            return (count($a1) - count($a2));
        };
        uasort($hits, $sortcomparison);
        return $hits;
    }

    public function get_right_choice_for($place) {
        $group = $this->places[$place]->group;
        foreach ($this->choiceorder[$group] as $choicekey => $choiceid) {
            if ($this->rightchoices[$place] == $choiceid) {
                return $choicekey;
            }
        }
        return null;
    }
    public function grade_response(array $response) {
        list($right, $total) = $this->get_num_parts_right($response);
        $fraction = $right / $total;
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function compute_final_grade($responses, $totaltries) {
        $maxitemsdragged = 0;
        $wrongtries = array();
        foreach ($responses as $i => $response) {
            $maxitemsdragged = max($maxitemsdragged,
                                                $this->total_number_of_items_dragged($response));
            $hits = $this->choose_hits($response);
            foreach ($hits as $place => $choiceitem) {
                if (!isset($wrongtries[$place])) {
                    $wrongtries[$place] = $i;
                }
            }
            foreach ($wrongtries as $place => $notused) {
                if (!isset($hits[$place])) {
                    unset($wrongtries[$place]);
                }
            }
        }
        $numtries = count($responses);
        $numright = count($wrongtries);
        $penalty = array_sum($wrongtries) * $this->penalty;
        $grade = ($numright - $penalty) / (max($maxitemsdragged, count($this->places)));
        return $grade;
    }
    public function clear_wrong_from_response(array $response) {
        $hits = $this->choose_hits($response);

        $cleanedresponse = array();
        foreach ($response as $choicekey => $coords) {
            $choice = (int)substr($choicekey, 1);
            $choiceresponse = array();
            $coordparts = explode(';', $coords);
            foreach ($coordparts as $itemno => $coord) {
                if (in_array("$choice $itemno", $hits)) {
                    $choiceresponse[] = $coord;
                }
            }
            $cleanedresponse[$choicekey] = join(';', $choiceresponse);
        }
        return $cleanedresponse;
    }
    public function get_wrong_drags(array $response) {
        $hits = $this->choose_hits($response);
        $wrong = array();
        foreach ($response as $choicekey => $coords) {
            $choice = (int)substr($choicekey, 1);
            if ($coords != '') {
            $coordparts = explode(';', $coords);
                foreach ($coordparts as $itemno => $coord) {
                    if (!in_array("$choice $itemno", $hits)) {
                        $wrong[] = $this->get_selected_choice(1, $choice)->text;
                    }
                }
            }
        }
        return $wrong;
    }


    public function get_drop_zones_without_hit(array $response) {
        $hits = $this->choose_hits($response);

        $nohits = array();
        foreach ($this->places as $placeno => $place) {
            $choice = $this->get_right_choice_for($placeno);
            if (!isset($hits[$placeno])) {
                $nohit = new stdClass();
                $nohit->coords = $place->coords;
                $nohit->shape = $place->shape->name();
                $nohit->markertext = $this->choices[1][$choice]->text;
                $nohits[] = $nohit;
            }
        }
        return $nohits;
    }

    public function classify_response(array $response) {
        $parts = array();
        foreach ($this->places as $place => $group) {
            if (!array_key_exists($this->field($place), $response) ||
                    !$response[$this->field($place)]) {
                $parts[$place] = question_classified_response::no_response();
                continue;
            }

            $fieldname = $this->field($place);
            $choiceno = $this->choiceorder[$group][$response[$fieldname]];
            $choice = $this->choices[$group][$choiceno];
            $parts[$place] = new question_classified_response(
                    $choiceno, html_to_text($choice->text, 0, false),
                    ($this->get_right_choice_for($place) == $response[$fieldname])
                            / count($this->places));
        }
        return $parts;
    }

    public function get_correct_response() {
        $responsecoords = array();
        foreach ($this->places as $placeno => $place) {
            $rightchoice = $this->get_right_choice_for($placeno);
            if ($rightchoice !== null) {
                $rightchoicekey = $this->choice($rightchoice);
                $correctcoords = $place->correct_coords();
                if ($correctcoords !== null) {
                    if (!isset($responsecoords[$rightchoicekey])) {
                        $responsecoords[$rightchoicekey] = array();
                    }
                    $responsecoords[$rightchoicekey][] = join(',', $correctcoords);
                }
            }
        }
        $response = array();
        foreach ($responsecoords as $choicekey => $coords) {
            $response[$choicekey] = join(';', $coords);
        }
        return $response;
    }
}

/**
 * Represents one of the choices (draggable markers).
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_drag_item {
    public $text;
    public $no;
    public $infinite;

    public function __construct($label, $no, $infinite) {
        $this->text = $label;
        $this->infinite = $infinite;
        $this->no = $no;
    }
    public function choice_group() {
        return 1;
    }

    public function summarise() {
        return $this->text;
    }
}
/**
 * Represents one of the places (drop zones).
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_drop_zone {
    public $group = 1;
    public $no;
    public $shape;
    public $coords;

    public function __construct($no, $shape, $coords) {
        $this->no = $no;
        $this->shape = qtype_ddmarker_shape::create($shape, $coords);
        $this->coords = $coords;
    }

    public function summarise() {
        return get_string('summariseplaceno', 'qtype_ddmarker', $this->no);
    }

    public function drop_hit($xy) {
        return $this->shape->is_point_in_shape($xy);
    }

    public function correct_coords() {
        return $this->shape->center_point();
    }
}
