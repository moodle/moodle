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
 * Moodle renderer used to display special elements of the book module
 *
 * @package mod_book
 * @copyright  2021 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_book\output;

/**
 * Moodle renderer used to display special elements of the book module
 *
 * @package mod_book
 * @copyright  2021 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Renderers the main action menu.
     *
     * @param main_action_menu $actionmenu Main action menu object.
     * @return string The rendered html.
     */
    public function render_main_action_menu(main_action_menu $actionmenu): string {
        $context = $actionmenu->export_for_template($this);
        return $this->render_from_template('mod_book/main_action_menu', $context);
    }
}
