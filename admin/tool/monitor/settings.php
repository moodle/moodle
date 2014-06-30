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
 * Links and settings
 *
 * This file contains links and settings used by tool_monitor
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('reports', new admin_category('toolmonitor', new lang_string('pluginname', 'tool_monitor')));

    // Manage rules page.
    $url = new moodle_url('/admin/tool/monitor/managerules.php', array('courseid' => 0));
    $temp = new admin_externalpage('toolmonitorrules', get_string('managerules', 'tool_monitor'), $url,
        'tool/monitor:managerules');
    $ADMIN->add('toolmonitor', $temp);

    // Manage subscriptions page.
    $url = new moodle_url('/admin/tool/monitor/index.php', array('courseid' => 0));
    $temp = new admin_externalpage('toolmonitorsubscriptions', get_string('managesubscriptions', 'tool_monitor'), $url,
        'tool/monitor:subscribe');
    $ADMIN->add('toolmonitor', $temp);

    $settings = null;
}
