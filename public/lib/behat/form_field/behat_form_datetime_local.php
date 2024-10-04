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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__  . '/behat_form_text.php');

/**
 * Class for <input type="datetime-local"> fields.
 *
 * @package    core
 * @category   test
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @copyright  2024 Catalyst IT Europe Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_datetime_local extends behat_form_text {
    /**
     * Sets the datetime-local value.
     *
     * Typing the value in like a text field isn't reliable cross-browser, so instead we need to set the value directly with
     * Javascript.
     *
     * @param string $value Date and time in a format like 2024-10-01T10:00
     * @return void
     */
    public function set_value($value): void {
        $this->require_javascript('Setting a datatime-local field requires Javascript.');
        $this->execute_js_on_node($this->field, "{{ELEMENT}}.value = '{$value}'");
    }
}
