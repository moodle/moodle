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
 * A class for recording the definition of Mink replacements.
 *
 * @package    core
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A class for recording the definition of Mink replacements for use in Mink selectors.
 *
 * These are comprised of a source string, and a replacement.
 *
 * During use the source string is converted from the string to be in the format:
 *
 *      %[component]/[string]%
 *
 * For example:
 *
 *      %mod_forum/title%
 *
 * Mink replacements are used in xpath translation to translate regularly used items such as title.
 * Here is an example from the upstream Mink project:
 *
 * '%tagTextMatch%' => 'contains(normalize-space(string(.)), %locator%)'
 *
 * And can be used in an xpath:
 *
 *      .//label[%tagTextMatch%]
 *
 * This would be expanded to:
 *
 *      .//label[contains(normalize-space(string(.)), %locator%)]
 *
 * Replacements can also be used in other replacements, as long as that replacement is defined later.
 *
 *      '%linkMatch%' => '(%idMatch% or %tagTextMatch% or %titleMatch% or %relMatch%)'
 *
 * @package    core
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_component_named_replacement {
    /** @var string */
    protected $from;

    /** @var string */
    protected $to;

    /**
     * Create the replacement.
     *
     * @param string $from this is the old selector that should no longer be used.
     *      For example 'group_message'.
     * @param string $to this is the new equivalent that should be used instead.
     *      For example 'core_message > Message'.
     */
    public function __construct(string $from, string $to) {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Get the 'from' part of the replacement, formatted for the component.
     *
     * @param string $component
     * @return string
     */
    public function get_from(string $component): string {
        return "%{$component}/{$this->from}%";
    }

    /**
     * Get the 'to' part of the replacement.
     *
     * @return string Target xpath
     */
    public function get_to(): string {
        return $this->to;
    }
}
