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
        foreach ($this->choices as $choice => $notused) {
            $vars[$this->choice($choice)] = PARAM_INTEGER;
        }
        return $vars;
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
        $this->shape = $shape;
        $this->coords = $coords;
    }

    public function summarise() {
        return get_string('summariseplaceno', 'qtype_ddmarker', $this->no);
    }
}
