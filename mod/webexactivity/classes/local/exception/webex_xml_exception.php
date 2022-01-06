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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_webexactivity\local\exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Exception for WebEx XML processing error.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webex_xml_exception extends webexactivity_exception {
    /**
     * Constructor
     *
     * @param string $code Error code from WebEx.
     * @param string $errormsg Error message from WebEx.
     * @param string $debuginfo Additional info about the error.
     */
    public function __construct($code, $errormsg, $debuginfo=null) {
        $params = array('errorcode' => (string)$code, 'error' => (string)$errormsg);

        if (isset($debuginfo)) {
            // Strip any password field out of the debug info.
            $debuginfo = preg_replace('{<password>(.*?)</password>}is', '<password>*****</password>', $debuginfo);
        }

        parent::__construct('webexxmlexception', '', $params, $debuginfo);
    }
}
