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
 * @package   qtype_ddwtos
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/gapselect/questionbase.php');


/**
 * Represents a drag-and-drop words into sentences question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddwtos_question extends qtype_gapselect_question_base {

    public function summarise_choice($choice) {
        return $this->html_to_text($choice->text, FORMAT_HTML);
    }
}


/**
 * Represents one of the choices (draggable boxes).
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddwtos_choice {
    /** @var string Text for the choice */
    public $text;

    /** @var int Group of the choice */
    public $draggroup;

    /** @var bool If the choice can be used an unlimited number of times */
    public $infinite;

    /**
     * Initialize a choice object.
     *
     * @param string $text The text of the choice
     * @param int $draggroup Group of the drop choice
     * @param bool $infinite True if the item can be used an unlimited number of times
     */
    public function __construct($text, $draggroup = 1, $infinite = false) {
        $this->text = $text;
        $this->draggroup = $draggroup;
        $this->infinite = $infinite;
    }

    /**
     * Returns the group of this item.
     *
     * @return int
     */
    public function choice_group() {
        return $this->draggroup;
    }
}
