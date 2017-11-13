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
 * Link to http -> https replace script.
 *
 * @package tool_httpsreplace
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    $pluginname = get_string('pluginname', 'tool_httpsreplace');
    $url = $CFG->wwwroot.'/'.$CFG->admin.'/tool/httpsreplace/index.php';
    $ADMIN->add('security', new admin_externalpage('toolhttpsreplace', $pluginname, $url, 'moodle/site:config', true));

    $httpsreplaceurl = $CFG->wwwroot.'/'.$CFG->admin.'/tool/httpsreplace/index.php';
    $ADMIN->locate('httpsecurity')->add(
        new admin_setting_heading(
            'tool_httpsreplaceheader',
            new lang_string('pluginname', 'tool_httpsreplace'),
            new lang_string('toolintro', 'tool_httpsreplace', $httpsreplaceurl)
        )
    );
}
