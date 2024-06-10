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

namespace core\exception;
/**
 * Web service parameter exception class.
 *
 * @deprecated since Moodle 2.2 - use moodle exception instead
 * This exception must be thrown to the web service client when a web service parameter is invalid
 * The error string is gotten from webservice.php
 * @package    core
 * @subpackage exception
 * @copyright  Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webservice_parameter_exception extends moodle_exception {
    /**
     * Constructor.
     *
     * @param string $errorcode The name of the string from webservice.php to print
     * @param string $a The name of the parameter
     * @param string $debuginfo Optional information to aid debugging
     */
    public function __construct($errorcode = null, $a = '', $debuginfo = null) {
        parent::__construct($errorcode, 'webservice', '', $a, $debuginfo);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(webservice_parameter_exception::class, \webservice_parameter_exception::class);
