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
 * Parent for all webexactivity exceptions.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webexactivity_exception extends \moodle_exception {
    /**
     * Constructor
     *
     * @param string $errorcode The name of the string to print.
     * @param string $link The url where the user will be prompted to continue.
     *                  If no url is provided the user will be directed to the site index page.
     * @param mixed $a Extra words and phrases that might be required in the error string.
     * @param string $debuginfo optional debugging information.
     */
    public function __construct($errorcode, $link='', $a=null, $debuginfo=null) {
        parent::__construct($errorcode, 'mod_webexactivity', $link, $a, $debuginfo);
    }
}
