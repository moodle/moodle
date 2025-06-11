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
 * Manage group custom fields
 *
 * @package   core_group
 * @author    Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @copyright 2023 Catalyst IT Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_group\customfield\group_handler;
use core_customfield\output\management;

require_once('../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('group_customfield');

$output = $PAGE->get_renderer('core_customfield');
$handler = group_handler::create();
$outputpage = new management($handler);

echo $output->header(),
     $output->heading(new lang_string('group_customfield', 'admin')),
     $output->render($outputpage),
     $output->footer();
