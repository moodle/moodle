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
 * Site configuration settings for the gradepenalty_duedate plugin
 *
 * @package   gradepenalty_duedate
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\url;

defined('MOODLE_INTERNAL') || die();

// New category for the plugin.
$ADMIN->add('gradepenalty', new admin_category('gradepenalty_duedate', new lang_string('pluginname', 'gradepenalty_duedate')));

$capabilities = ['gradepenalty/duedate:manage'];

if ($hassiteconfig || has_any_capability($capabilities, core\context\system::instance())) {

    // External page to manage the duedate rules.
    $temp = new admin_externalpage(
        'duedaterule',
        get_string('duedaterule', 'gradepenalty_duedate'),
        new url('/grade/penalty/duedate/manage_penalty_rule.php', ['contextid' => context_system::instance()->id]),
        'gradepenalty/duedate:manage'
    );

    // Add the external page to the plugin category.
    $ADMIN->add('gradepenalty_duedate', $temp);
}
