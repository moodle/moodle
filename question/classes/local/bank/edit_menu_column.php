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
 * A question bank column which gathers together all the actions into a menu.
 *
 * @package   core_question
 * @copyright 2019 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\local\bank;

use \core\plugininfo\qbank;

/**
 * A question bank column which gathers together all the actions into a menu.
 *
 * This question bank column, if added to the question bank, will
 * replace all of the other columns which implement the
 * {@see menu_action_column_base} interface and replace them with a single
 * column containing an Edit menu.
 *
 * @copyright 2019 The Open University
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_menu_column extends column_base {
    public function get_title() {
        return get_string('actions');
    }

    public function get_name(): string {
        return 'editmenu';
    }

    protected function display_content($question, $rowclasses): void {
        global $OUTPUT;
        $actions = $this->qbank->get_question_actions();

        $menu = new \action_menu();
        $menu->set_menu_trigger(get_string('edit'));
        foreach ($actions as $action) {
            $action = $action->get_action_menu_link($question);
            if ($action) {
                $menu->add($action);
            }
        }

        $qtypeactions = \question_bank::get_qtype($question->qtype, false)
                ->get_extra_question_bank_actions($question);
        foreach ($qtypeactions as $action) {
            $menu->add($action);
        }

        echo $OUTPUT->render($menu);
    }

    public function get_required_fields(): array {
        return ['q.qtype'];
    }

    /**
     * Get menuable actions.
     *
     * @return menu_action_column_base Menuable actions.
     */
    public function get_actions(): array {
        return $this->actions;
    }

    public function get_extra_classes(): array {
        return ['pr-3'];
    }

}
