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
 * Settings for local_coursematrix
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    // Create a category for Course Matrix.
    $ADMIN->add('localplugins', new admin_category(
        'local_coursematrix_category',
        get_string('pluginname', 'local_coursematrix')
    ));

    // Dashboard page.
    $ADMIN->add('local_coursematrix_category', new admin_externalpage(
        'local_coursematrix_dashboard',
        get_string('dashboard', 'local_coursematrix'),
        new moodle_url('/local/coursematrix/dashboard.php'),
        'local/coursematrix:viewdashboard'
    ));

    // Matrix rules page.
    $ADMIN->add('local_coursematrix_category', new admin_externalpage(
        'local_coursematrix',
        get_string('coursematrix', 'local_coursematrix'),
        new moodle_url('/local/coursematrix/index.php'),
        'local/coursematrix:manage'
    ));

    // Learning Plans management page.
    $ADMIN->add('local_coursematrix_category', new admin_externalpage(
        'local_coursematrix_plans',
        get_string('learningplans', 'local_coursematrix'),
        new moodle_url('/local/coursematrix/plans.php'),
        'local/coursematrix:manage'
    ));

    // Assign users page.
    $ADMIN->add('local_coursematrix_category', new admin_externalpage(
        'local_coursematrix_assign',
        get_string('assignusers', 'local_coursematrix'),
        new moodle_url('/local/coursematrix/assign_users.php'),
        'local/coursematrix:assignplans'
    ));
}
