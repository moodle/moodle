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

namespace qbank_columnsortorder;

use core\context;
use core_question\local\bank\column_manager_base;
use core_question\local\bank\plugin_features_base;
use core_question\local\bank\view;
use qbank_columnsortorder\output\add_column;
use qbank_columnsortorder\output\reset_columns;

/**
 * Plugin features for qbank_columnsortorder
 *
 * @package   qbank_columnsortorder
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_feature extends plugin_features_base {
    /**
     * Override the default column manager.
     *
     * This will set the column order, size and visibility based on the global settings defined on the admin screen, or on the
     * current user's preference if they have set one.
     *
     * @return ?column_manager_base
     */
    public function get_column_manager(): ?column_manager_base {
        return new column_manager();
    }

    /**
     * Return add and reset column controls.
     *
     * @param view $qbank The question bank view.
     * @param context $context The current context, for permission checks.
     * @param int $categoryid The current question category ID.
     * @return \renderable[]
     */
    public function get_question_bank_controls(view $qbank, context $context, int $categoryid): array {
        global $PAGE;
        $PAGE->requires->js_call_amd('qbank_columnsortorder/user_actions', 'init');
        $returnurl = new \moodle_url($qbank->returnurl);
        return [
            200 => new add_column(new column_manager(), $returnurl),
            300 => new reset_columns($returnurl),
        ];
    }
}
