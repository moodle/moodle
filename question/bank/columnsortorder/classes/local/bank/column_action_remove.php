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
 * Remove a column
 *
 * This action will display a link that will set the current column as hidden, then redirect back the current page.
 *
 * @package   qbank_columnsortorder
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column_action_remove extends column_action_base {
    /** @var bool True if we are changing global config, false for user preferences. */
    protected bool $global;

    /** @var string Label for the Remove action. */
    protected string $remove;

    protected function init(): void {
        $this->global = false;
        $this->remove = get_string('remove');
    }

    /**
     * Set the $global property to indicate whether we are changing global config.
     *
     * This action is used on both the user and admin screens, so requires this additional method.
     *
     * @param bool $global
     * @return void
     */
    public function set_global(bool $global): void {
        $this->global = $global;
    }

    public function get_action_menu_link(column_base $column): ?\action_menu_link {
        $actionurl = new \moodle_url('/question/bank/columnsortorder/actions.php', [
            'column' => $column->get_column_id(),
            'action' => 'remove',
            'sesskey' => sesskey(),
            'returnurl' => new \moodle_url($this->qbank->returnurl),
        ]);
        if ($this->global) {
            $actionurl->param('global', $this->global);
        }
        return new \action_menu_link_secondary(
            $actionurl,
            new \pix_icon('t/delete', ''),
            $this->remove,
            [
                'class' => 'action-link',
                'title' => get_string('removecolumn', 'qbank_columnsortorder', $column->get_title()),
                'data-action' => 'remove',
                'data-column' => $column->get_column_id(),
            ]
        );
    }
}
