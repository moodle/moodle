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
 * Description value object.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\value_objects;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing a description value object.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_description implements description_interface {
    /**
     * @var string $value The description's text.
     */
    protected $value;

    /**
     * @var int $format The description's format.
     */
    protected $format;

    /**
     * Constructor.
     *
     * @param string $value  The description's value.
     * @param int    $format The description's format.
     */
    public function __construct($value, $format) {
        $this->value = $value;
        $this->format = $format;
    }

    public function get_value() {
        return $this->value;
    }

    public function get_format() {
        return $this->format;
    }
}
