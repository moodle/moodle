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

declare(strict_types=1);

require_once(__DIR__  . '/behat_form_field.php');

use \Behat\Mink\Element\NodeElement;

/**
 * Custom interaction with select_menu elements
 *
 * @package   core_form
 * @copyright 2022 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_select_menu extends behat_form_field {

    /**
     * Sets the value of the select menu field.
     *
     * @param string $value The string that is used to identify an option within the select menu. If the string
     *                      has two items separated by '>' (ex. "Group > Option"), the first item ("Group") will be
     *                      used to identify a particular group within the select menu, while the second ("Option")
     *                      will be used to identify an option within that group. Otherwise, a string with a single
     *                      item (ex. "Option") will be used to identify an option within the select menu regardless
     *                      of any existing groups.
     */
    public function set_value($value) {
        self::require_javascript();

        $this->field->click();
        $option = $this->find_option($value);
        $option->click();
    }

    public function get_value() {
        $rootnode = $this->field->getParent();
        $input = $rootnode->find('css', 'input');
        return $input->getValue();
    }

    /**
     * Checks whether a given option exists in the select menu field.
     *
     * @param string $option The string that is used to identify an option within the select menu. If the string
     *                       has two items separated by '>' (ex. "Group > Option"), the first item ("Group") will be
     *                       used to identify a particular group within the select menu, while the second ("Option")
     *                       will be used to identify an option within that group. Otherwise, a string with a single
     *                       item (ex. "Option") will be used to identify an option within the select menu regardless
     *                       of any existing groups.
     * @return bool Whether the option exists in the select menu field or not.
     */
    public function has_option(string $option): bool {
        if ($this->find_option($option)) {
            return true;
        }
        return false;
    }

    /**
     * Finds and returns a given option from the select menu field.
     *
     * @param string $option The string that is used to identify an option within the select menu. If the string
     *                       has two items separated by '>' (ex. "Group > Option"), the first item ("Group") will be
     *                       used to identify a particular group within the select menu, while the second ("Option")
     *                       will be used to identify an option within that group. Otherwise, a string with a single
     *                       item (ex. "Option") will be used to identify an option within the select menu regardless
     *                       of any existing groups.
     * @return NodeElement|null The option element or null if it cannot be found.
     */
    private function find_option(string $option): ?NodeElement {
        // Split the value string by ">" to determine whether a group has been specified.
        $path = preg_split('/\s*>\s*/', trim($option));

        if (count($path) > 1) { // Group has been specified.
            $optionxpath = '//li[contains(@role, "presentation") and normalize-space(text()) = "' .
                $this->escape($path[0]) . '"]' .
                '/following-sibling::li[contains(@role, "option") and normalize-space(text()) = "' .
                $this->escape($path[1]) . '"]';
        } else { // Group has not been specified.
            $optionxpath = '//li[contains(@role, "option") and normalize-space(text()) = "' .
                $this->escape($path[0]) . '"]';
        }

        return $this->field->getParent()->find('xpath', $optionxpath);
    }
}
