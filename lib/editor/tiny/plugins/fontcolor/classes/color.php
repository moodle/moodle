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
 * Class that contains function for handling a single color item.
 *
 * @package     tiny_fontcolor
 * @copyright   2025 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace tiny_fontcolor;

 /**
  * Tiny Font color plugin utility class for a single color item.
  *
  * @package     tiny_fontcolor
  * @copyright   2025 Stephan Robotta <stephan.robotta@bfh.ch>
  * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
  */
class color {

    /**
     * Descriptive name of the color (might contain HTML to have multilang capability).
     * @var string
     */
    private $name;

    /**
     * Color code in hex notation.
     * @var string
     */
    private $value;

    /**
     * Instantiate the color by a name and value.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct(string $name, string $value) {
        $this->name = trim($name);
        $this->value = strtoupper(trim($value));
        // Prefix value with # if the hex annotation is used.
        if (substr($this->value, 0, 1) !== '#' && preg_match('/^[0-9A-F]{6}/', $this->value)) {
            $this->value = '#' . $this->value;
        }
    }

    /**
     * Get the name of the color.
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get the color value (hex code).
     *
     * @return string
     */
    public function get_value(): string {
        return $this->value;
    }

    /**
     * Check if the name is valid (must not be empty).
     *
     * @return bool
     */
    public function has_name_error(): bool {
        return empty($this->name);
    }

    /**
     * Check if the color code value is valid.
     *
     * @return bool
     */
    public function has_value_error(): bool {
        return !(bool)preg_match('/^#?[0-9a-f]{6}([0-9a-f]{2})?$/i', $this->value);
    }

    /**
     * Check if both name and color code are valid.
     *
     * @return bool
     */
    public function is_valid(): bool {
        return !($this->has_name_error() || $this->has_value_error());
    }
}

