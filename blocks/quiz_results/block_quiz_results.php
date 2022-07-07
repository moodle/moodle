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
 * Classes to enforce the various access rules that can apply to a quiz.
 *
 * @package    block_quiz_results
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/lib.php');

/**
 * Block quiz_results class definition.
 *
 * This block can be added to a course page or a quiz page to display of list of
 * the best/worst students/groups in a particular quiz.
 *
 * @package    block_quiz_results
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_quiz_results extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_quiz_results');
    }

    function applicable_formats() {
        return array('mod-quiz' => true);
    }

    function instance_config_save($data, $nolongerused = false) {
        parent::instance_config_save($data);
    }

    function get_content() {
        return $this->content;
    }

    function instance_allow_multiple() {
        return true;
    }
}


