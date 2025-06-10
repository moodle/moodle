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
 * @package    block_backadel
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// The settings page.
if ($ADMIN->fulltree) {
    require_once($CFG->dirroot. '/blocks/backadel/settingslib.php');

    $suffixchoices = array(
        'username' => 'username',
        'idnumber' => 'idnumber',
        'fullname' => 'fullname'
    );

    $schedurl = new moodle_url('/admin/settings.php?section=automated');
    $schedulelink = html_writer::link($schedurl,
        get_string('sched_config', 'block_backadel'));

    $settings->add(new backadel_path_setting('block_backadel/path',
        get_string('config_path', 'block_backadel'),
        get_string('config_path_desc', 'block_backadel', $CFG->dataroot), ''));
    $settings->add(new admin_setting_configselect('block_backadel/suffix',
        get_string('config_pattern', 'block_backadel'),
        get_string('config_pattern_desc', 'block_backadel'), 0, $suffixchoices));
    $settings->add(new admin_setting_configtext('block_backadel/size_limit',
        get_string('config_size_limit', 'block_backadel'),
        get_string('config_size_limit_desc', 'block_backadel'), ''));
    $settings->add(new admin_setting_pickroles('block_backadel/roles',
        get_string('config_roles', 'block_backadel'),
        get_string('config_roles_desc', 'block_backadel'), array()));
    $settings->add(new admin_setting_heading('block_backadel/sched_options', '', $schedulelink));
}
