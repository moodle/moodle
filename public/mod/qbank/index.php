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
 * This redirect to /question/banks.php to list all the instances of mod_qbank in a particular course.
 *
 * @package     mod_qbank
 * @copyright   2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author      Simon Adams <simon.adams@catalyst-eu.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

$id = required_param('id', PARAM_INT);
$course = get_course($id);
require_login($course);

// Redirect to /question/banks.php as that page shows all the available banks on the course.
redirect(new moodle_url('/question/banks.php', ['courseid' => $course->id]));
