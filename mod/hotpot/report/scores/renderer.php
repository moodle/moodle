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
 * Render the scores report for a given HotPot activity
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/report/renderer.php');

/**
 * mod_hotpot_report_scores_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_report_scores_renderer extends mod_hotpot_report_renderer {
    public $mode = 'scores';

    public $tablecolumns = array(
        'picture', 'fullname', 'grade', 'selected', 'attempt',
        'timemodified', 'status', 'duration', 'penalties', 'score'
    );

    public $filterfields = array(
        'group'=>0, 'realname'=>0, // 'lastname'=>1, 'firstname'=>1, 'username'=>1,
        'grade'=>1, 'timemodified'=>1, 'status'=>1, 'duration'=>1, 'penalties'=>1, 'score'=>1
    );

    public $has_questioncolumns = true;

    /**
     * add_response_to_rawdata
     *
     * @param xxx $table (passed by reference)
     * @param xxx $attemptid
     * @param xxx $column
     * @param xxx $response
     */
    function add_response_to_rawdata(&$table, $attemptid, $column, $response)  {
        $table->rawdata[$attemptid]->$column = $response->score;
    }
}
