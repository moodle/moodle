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
 * Puts the plugin actions into the admin settings tree.
 *
 * @package     tool_installaddon
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig and empty($CFG->disableonclickaddoninstall)) {

    $ADMIN->add('modules', new admin_externalpage('tool_installaddon_index',
        get_string('installaddons', 'tool_installaddon'),
        "$CFG->wwwroot/$CFG->admin/tool/installaddon/index.php"), 'modsettings');

    $ADMIN->add('modules', new admin_externalpage('tool_installaddon_validate',
        get_string('validation', 'tool_installaddon'),
        "$CFG->wwwroot/$CFG->admin/tool/installaddon/validate.php",
        'moodle/site:config',
        true), 'modsettings');
}
