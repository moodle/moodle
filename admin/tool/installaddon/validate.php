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
 * The ZIP package validation.
 *
 * @package     tool_installaddon
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/filelib.php');
require_once(dirname(__FILE__).'/classes/installer.php');
require_once(dirname(__FILE__).'/classes/validator.php');

navigation_node::override_active_url(new moodle_url('/admin/tool/installaddon/index.php'));
admin_externalpage_setup('tool_installaddon_validate');

if (!empty($CFG->disableonclickaddoninstall)) {
    notice(get_string('featuredisabled', 'tool_installaddon'));
}

require_sesskey();

$jobid = required_param('jobid', PARAM_ALPHANUM);
$zipfilename = required_param('zip', PARAM_FILE);
$plugintype = required_param('type', PARAM_ALPHANUMEXT);
$rootdir = optional_param('rootdir', '', PARAM_PLUGIN);

$zipfilepath = $CFG->tempdir.'/tool_installaddon/'.$jobid.'/source/'.$zipfilename;
if (!file_exists($zipfilepath)) {
    redirect(new moodle_url('/admin/tool/installaddon/index.php'),
        get_string('invaliddata', 'core_error'));
}

$installer = tool_installaddon_installer::instance();

// Extract the ZIP contents.
fulldelete($CFG->tempdir.'/tool_installaddon/'.$jobid.'/contents');
$zipcontentpath = make_temp_directory('tool_installaddon/'.$jobid.'/contents');
$zipcontentfiles = $installer->extract_installfromzip_file($zipfilepath, $zipcontentpath, $rootdir);

// Validate the contents of the plugin ZIP file.
$validator = tool_installaddon_validator::instance($zipcontentpath, $zipcontentfiles);
$validator->assert_plugin_type($plugintype);
$validator->assert_moodle_version($CFG->version);
$result = $validator->execute();

if ($result) {
    $validator->set_continue_url(new moodle_url('/admin/tool/installaddon/deploy.php', array(
        'sesskey' => sesskey(),
        'jobid' => $jobid,
        'type' => $plugintype,
        'name' => $validator->get_rootdir())));

} else {
    fulldelete($CFG->tempdir.'/tool_installaddon/'.$jobid);
}

// Display the validation results.
$output = $PAGE->get_renderer('tool_installaddon');
$output->set_installer_instance($installer);
$output->set_validator_instance($validator);
echo $output->validation_page();
