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
 * Base class for question bank columns that just contain an action icon.
 *
 * @package   core_question
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\bank;
defined('MOODLE_INTERNAL') || die();


/**
 * Base class for question bank columns that just contain an action icon.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_action_column extends menu_action_column_base {
    protected $stredit;
    protected $strview;

    public function init() {
        parent::init();
        $this->stredit = get_string('editquestion', 'question');
        $this->strview = get_string('view');
    }

    public function get_name() {
        return 'editaction';
    }

    protected function get_url_icon_and_label(\stdClass $question): array {
        if (question_has_capability_on($question, 'edit')) {
            return [$this->qbank->edit_question_moodle_url($question->id), 't/edit', $this->stredit];
        } else if (question_has_capability_on($question, 'view')) {
            return [$this->qbank->edit_question_moodle_url($question->id), 'i/info', $this->strview];
        } else {
            return [null, null, null];
        }
    }
}
