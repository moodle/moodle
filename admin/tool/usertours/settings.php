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
 * Settings page.
 *
 * @package   tool_usertours
 * @copyright 2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$capabilities = [
    'tool/usertours:managetours',
];
if ($hassiteconfig || has_any_capability($capabilities, context_system::instance())) {
    $ADMIN->add(
        'appearance',
        new admin_externalpage(
            'tool_usertours/tours',
            get_string('usertours', 'tool_usertours'),
            new moodle_url('/admin/tool/usertours/configure.php'),
            'tool/usertours:managetours'
        )
    );
}
