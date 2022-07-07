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
 * Behat qtype_multichoice-related steps definitions.
 *
 * @package    qtype_multichoice
 * @category   test
 * @copyright  2020 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Behat custom step definitions and partial named selectors for qtype_multichoice.
 *
 * @package    qtype_multichoice
 * @category   test
 * @copyright  2020 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qtype_multichoice extends behat_base {

    /**
     * Return the list of partial named selectors for this plugin.
     *
     * @return behat_component_named_selector[]
     */
    public static function get_partial_named_selectors(): array {
        return [
            new behat_component_named_selector(
                'Answer', [
                    <<<XPATH
    .//div[@data-region='answer-label']//*[contains(text(), %locator%)]
XPATH
                ]
            ),
        ];
    }
}
