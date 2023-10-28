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

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

/**
 * Availability related behat steps and selectors definitions.
 *
 * @package    core_availability
 * @category   test
 * @copyright  2023 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_availability extends behat_base {

    /**
     * Return the list of partial named selectors.
     *
     * @return array
     */
    public static function get_partial_named_selectors(): array {
        return [
            new behat_component_named_selector(
                'Activity availability', [
                    ".//li[contains(concat(' ', normalize-space(@class), ' '), ' activity ')]"
                    . "[descendant::*[contains(normalize-space(.), %locator%)]]//div[@data-region='availabilityinfo']",
                ]
            ),
            new behat_component_named_selector(
                'Section availability', [".//li[@id = %locator%]//div[@data-region='availabilityinfo']"],
            ),
            new behat_component_named_selector(
                'Set Of Restrictions', ["//div[h3[@data-restriction-order=%locator% and contains(text(), 'Set of')]]"],
            ),
        ];
    }

    /**
     * Return the list of exact named selectors
     *
     * @return array
     */
    public static function get_exact_named_selectors(): array {
        return [
            new behat_component_named_selector(
                'Availability Button Area',
                [
                    "//h3[@data-restriction-order=%locator%]/following-sibling::div[contains(@class,'availability-inner')]/"
                    . "div[contains(@class,'availability-button')]",
                ],
            ),
        ];
    }
}
