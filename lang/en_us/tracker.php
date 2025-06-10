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
 * Strings for component 'tracker', language 'en_us', version '4.1'.
 *
 * @package     tracker
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['supportmode_help'] = 'Support mode applies some predefined settings and role overides on the tracker to achieved a preset behavior.

* Bug report: Reporters have access to the whole ticket list for reading the issues in a collaborative way. All states are enabled for a complete
technical operation workflow, including operations on preprod test systems.

* User support/Ticketing: Reporters usually have only access to the tickets they have posted and cannot access to the ticket browsing mode. Some states
have been disabled, that are more commonly used for technical operations.

* Task distribution: Reporters can have or not access to the whole distributed ticket list. Workers can only have access to the tickets they are assigned to
through the "My work" screen. They will NOT have access to the browse function. some intermediate states have beed disabled for a simpler marking of task states.

* Customized: When customized, the activity editor can choose states and overrides to apply to the tracker. This is the most flexible setting, but needs a correct knowledge of Moodle roles and setting management.';
$string['unmatchingelements'] = 'Both tracker definition do not match. This may result in unexpected behavior when cascading support tickets.';
