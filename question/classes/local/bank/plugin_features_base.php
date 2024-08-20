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
 * Base class class for qbank plugins.
 *
 * Every qbank plugin must extent this class.
 *
 * @package    core_question
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\local\bank;

use core\context;
use qbank_columnsortorder\local\qbank\column_action_move;
use qbank_columnsortorder\local\qbank\column_action_remove;
use qbank_columnsortorder\local\qbank\column_action_resize;

/**
 * Class plugin_features_base is the base class for qbank plugins.
 *
 * @package    core_question
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_features_base {

    /**
     * This method will return the array of objects to be rendered as a part of question bank columns.
     *
     * @param view $qbank
     * @return array
     */
    public function get_question_columns(view $qbank): ?array {
        return [];
    }

    /**
     * This method will return the array of objects to be rendered as a part of question bank actions.
     *
     * @param view $qbank
     * @return question_action_base[]
     */
    public function get_question_actions(view $qbank): array {
        return [];
    }

    /**
     * This method will return the object for the navigation node.
     *
     * @return null|navigation_node_base
     */
    public function get_navigation_node(): ?navigation_node_base {
        return null;
    }

    /**
     * This method will return the array objects for the bulk actions ui.
     *
     * @return bulk_action_base[]
     */
    public function get_bulk_actions() {
        return [];
    }

    /**
     * This method will return a column manager object, if this plugin provides one.
     *
     * @return ?column_manager_base
     */
    public function get_column_manager(): ?column_manager_base {
        return null;
    }

    /**
     * This method will return an array of renderable objects, for adding additional controls to the question bank screen.
     *
     * The array returned can include a numeric index for each object, to indicate the position in which it should be displayed
     * relative to other controls. If two plugins return controls with the same position, they will be displayed after one another,
     * based on the alphabetical order of the plugin component names.
     *
     * @param view $qbank The question bank view.
     * @param context $context The current context, for permission checks.
     * @param int $categoryid The current question category ID.
     * @return \renderable[]
     */
    public function get_question_bank_controls(view $qbank, context $context, int $categoryid): array {
        return [];
    }

    /**
     * Return search conditions for the plugin.
     *
     * @param view|null $qbank
     * @return condition[]
     */
    public function get_question_filters(?view $qbank = null): array {
        return [];
    }
}
