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
 * Moodle-specific selectors.
 *
 * @package    core
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Moodle-specific selectors.
 *
 * @package    core
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_component_named_selector {
    /** @var string */
    protected $alias;

    /** @var array List of xpaths */
    protected $xpaths;

    /** @var string */
    protected $istextselector;

    /**
     * Create the selector definition.
     *
     * @param string $alias The 'friendly' name of the thing. This will be prefixed with the component name.
     * @param array $xpaths A list of xpaths
     * @param bool $istextselector Whether this selector can also be used as a text selector.
     */
    public function __construct(string $alias, array $xpaths, bool $istextselector = true) {
        $this->alias = $alias;
        $this->xpaths = $xpaths;
        $this->istextselector = $istextselector;
    }

    /**
     * Whether this is a text selector.
     *
     * @return bool
     */
    public function is_text_selector(): bool {
        return $this->istextselector;
    }

    /**
     * Get the name of the selector.
     * This is a back-end feature and contains a namespaced md5 of the human-readable name.
     *
     * @param string $component
     * @return string
     */
    public function get_name(string $component): string {
        return implode('_', [$component, md5($this->alias)]);
    }

    /**
     * Get the alias of the selector.
     * This is the human-readable name that you would typically interact with.
     *
     * @param string $component
     * @return string
     */
    public function get_alias(string $component): string {
        return implode(" > ", [$component, $this->alias]);;
    }

    /**
     * Get the list of combined xpaths.
     *
     * @return string The list of xpaths combined with the xpath | (OR) operator
     */
    public function get_combined_xpath(): string {
        return implode(' | ', $this->xpaths);
    }
}
