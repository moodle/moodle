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
 * The main screen of the tool.
 *
 * @package     tool_installaddon
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('tool_installaddon_index');

if (!empty($CFG->disableonclickaddoninstall)) {
    notice(get_string('featuredisabled', 'tool_installaddon'));
}

$installer = tool_installaddon_installer::instance();

$output = $PAGE->get_renderer('tool_installaddon');
$output->set_installer_instance($installer);

// Handle the eventual request for installing from remote repository.
$remoterequest = optional_param('installaddonrequest', null, PARAM_RAW);
$confirmed = optional_param('confirm', false, PARAM_BOOL);
$installer->handle_remote_request($output, $remoterequest, $confirmed);

$form = $installer->get_installfromzip_form();

if ($form->is_cancelled()) {
    redirect($PAGE->url);

} else if ($data = $form->get_data()) {
    // Save the ZIP file into a temporary location.
    $jobid = md5(rand().uniqid('', true));
    $sourcedir = make_temp_directory('tool_installaddon/'.$jobid.'/source');
    $zipfilename = $installer->save_installfromzip_file($form, $sourcedir);
    if (empty($data->plugintype)) {
        $versiondir = make_temp_directory('tool_installaddon/'.$jobid.'/version');
        $detected = $installer->detect_plugin_component($sourcedir.'/'.$zipfilename, $versiondir);
        if (empty($detected)) {
            $form->require_explicit_plugintype();
        } else {
            list($detectedtype, $detectedname) = core_component::normalize_component($detected);
            if ($detectedtype and $detectedname and $detectedtype !== 'core') {
                $data->plugintype = $detectedtype;
            } else {
                $form->require_explicit_plugintype();
            }
        }
    }
    // Redirect to the validation page.
    if (!empty($data->plugintype)) {
        $nexturl = new moodle_url('/admin/tool/installaddon/validate.php', array(
            'sesskey' => sesskey(),
            'jobid' => $jobid,
            'zip' => $zipfilename,
            'type' => $data->plugintype,
            'rootdir' => $data->rootdir));
        redirect($nexturl);
    }
}

// Output starts here.
echo $output->index_page();
