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
 * Resize a column
 *
 * This will add an action menu item which will be enhanced by javascript in user_actions.js to show the resize column modal for the
 * current column when clicked.
 *
 * @package   qbank_columnsortorder
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column_action_resize extends column_action_base {
    /** @var string Label for the resize action. */
    protected string $resize;

    protected function init(): void {
        $this->resize = get_string('resize', 'qbank_columnsortorder');
    }

    public function get_action_menu_link(column_base $column): ?\action_menu_link {
        return new \action_menu_link_secondary(
            new \moodle_url('/question/edit.php'),
            new \pix_icon('i/twoway', ''),
            $this->resize,
            [
                'title' => get_string('resizecolumn', 'qbank_columnsortorder', $column->get_title()),
                'data-action' => 'resize',
                'data-column' => get_class($column),
            ]
        );
    }
}
