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
 * Deploy the validated contents of the ZIP package to the $CFG->dirroot
 *
 * @package     tool_installaddon
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once(dirname(__FILE__).'/classes/installer.php');
require_once(dirname(__FILE__).'/classes/validator.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

if (!empty($CFG->disableonclickaddoninstall)) {
    notice(get_string('featuredisabled', 'tool_installaddon'));
}

require_sesskey();

$jobid = required_param('jobid', PARAM_ALPHANUM);
$plugintype = required_param('type', PARAM_ALPHANUMEXT);
$pluginname = required_param('name', PARAM_PLUGIN);

$zipcontentpath = $CFG->tempdir.'/tool_installaddon/'.$jobid.'/contents';

if (!is_dir($zipcontentpath)) {
    debugging('Invalid location of the extracted ZIP package: '.s($zipcontentpath), DEBUG_DEVELOPER);
    redirect(new moodle_url('/admin/tool/installaddon/index.php'),
        get_string('invaliddata', 'core_error'));
}

if (!is_dir($zipcontentpath.'/'.$pluginname)) {
    debugging('Invalid location of the plugin root directory: '.$zipcontentpath.'/'.$pluginname, DEBUG_DEVELOPER);
    redirect(new moodle_url('/admin/tool/installaddon/index.php'),
        get_string('invaliddata', 'core_error'));
}

$installer = tool_installaddon_installer::instance();

if (!$installer->is_plugintype_writable($plugintype)) {
    debugging('Plugin type location not writable', DEBUG_DEVELOPER);
    redirect(new moodle_url('/admin/tool/installaddon/index.php'),
        get_string('invaliddata', 'core_error'));
}

$plugintypepath = $installer->get_plugintype_root($plugintype);

if (file_exists($plugintypepath.'/'.$pluginname)) {
    debugging('Target location already exists', DEBUG_DEVELOPER);
    redirect(new moodle_url('/admin/tool/installaddon/index.php'),
        get_string('invaliddata', 'core_error'));
}

$installer->move_directory($zipcontentpath.'/'.$pluginname, $plugintypepath.'/'.$pluginname);
fulldelete($CFG->tempdir.'/tool_installaddon/'.$jobid);
redirect(new moodle_url('/admin'));
