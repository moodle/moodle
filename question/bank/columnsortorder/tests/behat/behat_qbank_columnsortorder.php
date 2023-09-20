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

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Steps definitions related with the drag and drop header.
 * @package    qbank_columnsortorder
 * @category   test
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Nathan Nguyen <nathannguyen@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qbank_columnsortorder extends behat_base {
    public static function get_exact_named_selectors(): array {
        return [
            new behat_component_named_selector('column header', [
                "//th[contains(@data-name, %locator%)]",
            ]),
            new behat_component_named_selector('column move handle', [
                "//*[self::th or self::tr][contains(@data-name, %locator%)]//span[contains(@data-drag-type, 'move')]",
            ]),
            new behat_component_named_selector('column resize handle', [
                "//th[contains(@data-name, %locator%)]//span[contains(@data-action, 'resize')]",
            ]),
        ];
    }
}
