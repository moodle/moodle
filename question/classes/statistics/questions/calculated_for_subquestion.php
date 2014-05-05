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
 * Class for storing calculated sub question statistics and intermediate calculation values.
 *
 * @package    core_question
 * @copyright  2013 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\questions;
defined('MOODLE_INTERNAL') || die();

/**
 * A class to store calculated stats for a sub question.
 *
 * @package    core_question
 * @copyright  2013 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calculated_for_subquestion extends calculated {
    public $subquestion = true;

    /**
     * @var array What slots is this sub question used in?
     */
    public $usedin = array();

    /**
     * @var bool Have the slots this sub question has been used in got different grades?
     */
    public $differentweights = false;

    public $negcovar = 0;

    /**
     * @var int only set immediately before display in the table. The order of display in the table.
     */
    public $subqdisplayorder;

    /**
     * Constructor.
     *
     * @param object|null $step the step data for the step that this sub-question was first encountered in.
     * @param int|null $variant the variant no
     */
    public function __construct($step = null, $variant = null) {
        if ($step !== null) {
            $this->questionid = $step->questionid;
            $this->maxmark = $step->maxmark;
        }
        $this->variant = $variant;
    }
}
