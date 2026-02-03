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
 * Output class file.
 *
 * @package    qbank_bulkmove
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbank_bulkmove\output;

use cm_info;
use core_question\local\bank\question_bank_helper;
use core_question\output\question_category_selector;
use moodle_url;
use renderer_base;
use single_button;

/**
 * Output class to create a modal template with selects for question banks, question categories, and a move button.
 */
class bulk_move implements \renderable, \templatable {

    /** @var int The question bank id you are currently moving the question(s) from */
    protected int $currentbankid;

    /** @var int The question category id you are moving the question(s) from */
    protected int $currentcategoryid;

    /**
     * Instantiate the output class.
     *
     * @param int $currentbankid
     * @param int $currentcategoryid
     */
    public function __construct(int $currentbankid, int $currentcategoryid) {
        $this->currentbankid = $currentbankid;
        $this->currentcategoryid = $currentcategoryid;
    }

    /**
     * Export data for use by the template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {

        [, $cmrec] = get_module_from_cmid($this->currentbankid);
        $currentbankcm = cm_info::create($cmrec);

        // Get the current bank and its categories. All other banks and categories will be loaded dynamically.
        if (plugin_supports('mod', $currentbankcm->modname, FEATURE_PUBLISHES_QUESTIONS, false)) {
            $banktorender = question_bank_helper::get_activity_instances_with_shareable_questions(
                havingcap: ['moodle/question:add'],
                currentbankid: $this->currentbankid,
                filtercontext: $currentbankcm->context,
                limit: 1,
            )[0];
        } else {
            $banktorender = question_bank_helper::get_activity_instances_with_private_questions(
                incourseids: [$currentbankcm->course],
                havingcap: ['moodle/question:add'],
                currentbankid: $this->currentbankid,
                filtercontext: $currentbankcm->context,
            )[0];
        }

        $categoryselector = new question_category_selector(
            [$currentbankcm->context],
            selected: "{$this->currentcategoryid},{$currentbankcm->context->id}",
            autocomplete: true,
        );

        $savebutton = new single_button(
            new moodle_url('#'),
            get_string('movequestions', 'qbank_bulkmove'),
            'post',
            single_button::BUTTON_PRIMARY,
            [
                'data-action' => 'bulkmovesave',
                'disabled' => 'disabled',
            ]
        );

        return [
            'bank' => $banktorender,
            'categories' => $categoryselector->export_for_template($output),
            'save' => $savebutton->export_for_template($output),
            'contextid' => $currentbankcm->context->id,
        ];
    }
}
