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

namespace qbank_bulkmove;

use moodle_exception;

/**
 * Class bulk_move_action is the base class for moving questions.
 *
 * @package    qbank_bulkmove
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bulk_move_action extends \core_question\local\bank\bulk_action_base {

    public function get_bulk_action_title(): string {
        return get_string('movetobulkaction', 'qbank_bulkmove');
    }

    public function get_key(): string {
        return 'move';
    }

    public function get_bulk_action_url(): \moodle_url {
        return new \moodle_url('/question/bank/bulkmove/move.php');
    }

    public function get_bulk_action_capabilities(): ?array {
        return [
            'moodle/question:moveall',
            'moodle/question:add',
        ];
    }

    /**
     * Initialise the modal js with the current bank context id and question category id.
     * @return void
     */
    public function initialise_javascript(): void {
        global $PAGE;

        $category = $this->qbank->get_pagevars('cat');

        if (!empty($category)) {
            [$categoryid, $contextid] = explode(',', $category);
        } else {
            $defaultcategory = question_get_default_category($this->qbank->cm->context->id, true);
            $categoryid = $defaultcategory->id;
            $contextid = $defaultcategory->contextid;
        }

        $PAGE->requires->js_call_amd(
            'qbank_bulkmove/modal_question_bank_bulkmove',
            'init',
            [
                'contextid' => $contextid,
                'categoryid' => $categoryid,
            ]
        );
    }
}
