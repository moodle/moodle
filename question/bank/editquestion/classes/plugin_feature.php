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
 * Plugin entrypoint for columns.
 *
 * @package    qbank_editquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbank_editquestion;

use core\context;
use core_question\local\bank\view;
use qbank_editquestion\output\add_new_question;

/**
 * Class columns is the entrypoint for the columns.
 *
 * @package    qbank_editquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_feature extends \core_question\local\bank\plugin_features_base {

    public function get_question_columns($qbank): array {
        return [
            new question_status_column($qbank),
        ];
    }

    public function get_question_actions(view $qbank): array {
        return [
            new edit_action($qbank),
            new copy_action($qbank),
        ];
    }

    /**
     * Return "Add new question" control.
     *
     * @param view $qbank The question bank view.
     * @param context $context The current context, for permission checks.
     * @param int $categoryid The current question category ID.
     * @return \renderable[]
     */
    public function get_question_bank_controls(view $qbank, context $context, int $categoryid): array {
        if (!$qbank->allow_add_questions()) {
            return [];
        }
        $canadd = has_capability('moodle/question:add', $context);
        $urlparams = (new edit_action($qbank))->editquestionurl->params();
        return [
            100 => new add_new_question($categoryid, $urlparams, $canadd),
        ];
    }

}
