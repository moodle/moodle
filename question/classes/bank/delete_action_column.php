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
 * action to delete (or hide) a question, or restore a previously hidden question.
 *
 * @package   core_question
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\bank;
defined('MOODLE_INTERNAL') || die();


/**
 * action to delete (or hide) a question, or restore a previously hidden question.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_action_column extends menu_action_column_base {
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

    /**
     * Work out the info required to display this action, if appropriate.
     *
     * If the action is not appropriate to this question, return [null, null, null].
     *
     * Otherwise return an array with three elements:
     * moodel_url $url the URL to perform the action.
     * string $icon the icon name. E.g. 't/delete'.
     * string $label the label to display.
     *
     * @param object $question the row from the $question table, augmented with extra information.
     * @return array [$url, $label, $icon] as above.
     */
    protected function get_url_icon_and_label(\stdClass $question): array {
        if (!question_has_capability_on($question, 'edit')) {
            return [null, null, null];
        }
        if ($question->hidden) {
            $url = new \moodle_url($this->qbank->base_url(), array('unhide' => $question->id, 'sesskey' => sesskey()));
            return [$url, 't/restore', $this->strrestore];
        } else {
            $url = new \moodle_url($this->qbank->base_url(), array('deleteselected' => $question->id, 'q' . $question->id => 1,
                    'sesskey' => sesskey()));
            return [$url, 't/delete', $this->strdelete];
        }
    }

    public function get_required_fields() {
        $required = parent::get_required_fields();
        $required[] = 'q.hidden';
        return $required;
    }
}
