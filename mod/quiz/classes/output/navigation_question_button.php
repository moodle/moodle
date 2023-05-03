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

namespace mod_quiz\output;

use moodle_url;
use renderable;

/**
 * Represents a single link in the navigation panel.
 *
 * @package   mod_quiz
 * @category  output
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navigation_question_button implements renderable {
    /** @var string id="..." to add to the HTML for this button. */
    public $id;
    /** @var bool, if this is a real question, not an info item. */
    public $isrealquestion;
    /** @var string number to display in this button. Either the question number of 'i'. */
    public $number;
    /** @var string class to add to the class="" attribute to represnt the question state. */
    public $stateclass;
    /** @var string Textual description of the question state, e.g. to use as a tool tip. */
    public $statestring;
    /** @var int the page number this question is on. */
    public $page;
    /** @var bool true if this question is on the current page. */
    public $currentpage;
    /** @var bool true if this question has been flagged. */
    public $flagged;
    /** @var moodle_url the link this button goes to, or null if there should not be a link. */
    public $url;
    /** @var int QUIZ_NAVMETHOD_FREE or QUIZ_NAVMETHOD_SEQ. */
    public $navmethod;
}
