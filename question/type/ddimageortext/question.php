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
 * @subpackage ddimageortext
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/ddimageortext/questionbase.php');


/**
 * Represents a drag-and-drop images to image question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddimageortext_question extends qtype_ddtoimage_question_base {

}


/**
 * Represents one of the choices (draggable images).
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddimageortext_drag_item {
    public $id;
    public $text;
    public $no;
    public $group;
    public $isinfinite;

    public function __construct($alttextlabel, $no, $group = 1, $isinfinite = false, $id = 0) {
        $this->id = $id;
        $this->text = $alttextlabel;
        $this->no = $no;
        $this->group = $group;
        $this->isinfinite = $isinfinite;
    }
    public function choice_group() {
        return $this->group;
    }

    public function summarise() {
        if (trim($this->text) != '') {
            return get_string('summarisechoice', 'qtype_ddimageortext', $this);
        } else {
            return get_string('summarisechoiceno', 'qtype_ddimageortext', $this->no);
        }
    }
}
/**
 * Represents one of the places (drop zones).
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddimageortext_drop_zone {
    public $no;
    public $text;
    public $group;
    public $xy;

    public function __construct($alttextlabel, $no, $group = 1, $x = '', $y = '') {
        $this->no = $no;
        $this->text = $alttextlabel;
        $this->group = $group;
        $this->xy = array($x, $y);
    }

    public function summarise() {
        if (trim($this->text) != '') {
            $summariseplace =
                        get_string('summariseplace', 'qtype_ddimageortext', $this);
        } else {
            $summariseplace =
                    get_string('summariseplaceno', 'qtype_ddimageortext', $this->no);
        }
        return $summariseplace;
    }
}