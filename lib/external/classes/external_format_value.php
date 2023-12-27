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

namespace core_external;

/**
 * A pre-filled external_value class for text format.
 *
 * Default is FORMAT_HTML
 * This should be used all the time in external xxx_params()/xxx_returns functions
 * as it is the standard way to implement text format param/return values.
 *
 * @package    core_webservice
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.3
 */
class external_format_value extends external_value {

    /**
     * Constructor
     *
     * @param string $textfieldname Name of the text field
     * @param int $required if VALUE_REQUIRED then set standard default FORMAT_HTML
     * @param int $default Default value.
     */
    public function __construct($textfieldname, $required = VALUE_REQUIRED, $default = null) {
        if ($default == null && $required == VALUE_DEFAULT) {
            $default = FORMAT_HTML;
        }

        $desc = sprintf(
            "%s format (%s = HTML, %s = MOODLE, %s = PLAIN, or %s = MARKDOWN",
            $textfieldname,
            FORMAT_HTML,
            FORMAT_MOODLE,
            FORMAT_PLAIN,
            FORMAT_MARKDOWN,
        );

        parent::__construct(PARAM_INT, $desc, $required, $default);
    }
}
