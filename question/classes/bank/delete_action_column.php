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

namespace core_question\bank;

/**
 * action to delete (or hide) a question, or restore a previously hidden question.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class delete_action_column extends action_column_base {
    protected $strdelete;
    protected $strrestore;

    public function init() {
        parent::init();
        $this->strdelete = get_string('delete');
        $this->strrestore = get_string('restore');
    }

    public function get_name() {
        return 'deleteaction';
    }

    protected function display_content($question, $rowclasses) {
        if (question_has_capability_on($question, 'edit')) {
            if ($question->hidden) {
                $url = new \moodle_url($this->qbank->base_url(), array('unhide' => $question->id, 'sesskey' => sesskey()));
                $this->print_icon('t/restore', $this->strrestore, $url);
            } else {
                $url = new \moodle_url($this->qbank->base_url(), array('deleteselected' => $question->id, 'q' . $question->id => 1,
                                              'sesskey' => sesskey()));
                $this->print_icon('t/delete', $this->strdelete, $url);
            }
        }
    }

    public function get_required_fields() {
        return array('q.id', 'q.hidden');
    }
}
