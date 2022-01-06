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
 * Variable Envelope Return Path message processing failure exception.
 *
 * @package    core_message
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\message\inbound;

defined('MOODLE_INTERNAL') || die();

/**
 * Variable Envelope Return Path message processing failure exception.
 *
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class processing_failed_exception extends \moodle_exception {
    /**
     * Constructor
     *
     * @param string $identifier The string identifier to use when displaying this exception.
     * @param string $component The string component
     * @param \stdClass $data The data to pass to get_string
     */
    public function __construct($identifier, $component, \stdClass $data = null) {
        return parent::__construct($identifier, $component, '', $data);
    }
}
