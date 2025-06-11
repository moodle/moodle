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

namespace qbank_columnsortorder\local\bank;

use core_question\local\bank\column_action_base;
use core_question\local\bank\column_base;

/**
 * Move a column
 *
 * This will add an action menu item which will be enhanced by javascript in user_actions.js to show the move column modal for the
 * current column when clicked.
 *
 * @package   qbank_columnsortorder
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column_action_move extends column_action_base {
    /** @var string Label for the Move action.  */
    protected string $move;

    protected function init(): void {
        $this->move = get_string('move');
    }

    public function get_action_menu_link(column_base $column): ?\action_menu_link {
        return new \action_menu_link_secondary(
            new \moodle_url('/question/edit.php'),
            new \pix_icon('i/dragdrop', ''),
            $this->move,
            [
                'title' => get_string('movecolumn', 'qbank_columnsortorder', $column->get_title()),
                'data-action' => 'move',
                'data-column' => get_class($column),
            ]
        );
    }
}
