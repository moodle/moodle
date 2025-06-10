<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'tool_usersuspension', language 'en_us', version '4.1'.
 *
 * @package     tool_usersuspension
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['form:static:uploadfile:desc'] = 'Upload your user suspension file here<br/>
The uploaded CSV file can be configured as follows:<br/>
<ol>
    <li>\'simple\' file containing ONLY email addresses, one per line</li>
    <li>\'smart\' file containing 2 columns, indicating the type and the value.<br/>
        Possible values for the type column are
        <ul>
            <li>email: value column indicates user account\'s e-mail address</li>
            <li>idnumber: value column indicates user account\'s idnumber</li>
            <li>username: value column indicates user account\'s username</li>
        </ul>
    </li>
</ol>';
$string['notify:load-file'] = 'Opening file \'{$a}\'';
$string['notify:unknown-suspend-type'] = 'Unknown suspension type identifier \'{$a}\'';
