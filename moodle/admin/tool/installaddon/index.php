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

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('tool_installaddon_index');

if (!empty($CFG->disableupdateautodeploy)) {
    notice(get_string('featuredisabled', 'tool_installaddon'));
}

$pluginman = core_plugin_manager::instance();
$installer = tool_installaddon_installer::instance();

$output = $PAGE->get_renderer('tool_installaddon');
$output->set_installer_instance($installer);

// Handle the eventual request for installing from remote repository.
$remoterequest = optional_param('installaddonrequest', null, PARAM_RAW);
$installer->handle_remote_request($output, $remoterequest);

// Handle the confirmed installation request.
$installremote = optional_param('installremote', null, PARAM_COMPONENT);
$installremoteversion = optional_param('installremoteversion', null, PARAM_INT);
$installremoteconfirm = optional_param('installremoteconfirm', false, PARAM_BOOL);

if ($installremote and $installremoteversion) {
    require_sesskey();
    require_once($CFG->libdir.'/upgradelib.php');

    $PAGE->set_pagelayout('maintenance');
    $PAGE->set_popup_notification_allowed(false);

    if ($pluginman->is_remote_plugin_installable($installremote, $installremoteversion)) {
        $installable = array($pluginman->get_remote_plugin_info($installremote, $installremoteversion, true));
        upgrade_install_plugins($installable, $installremoteconfirm,
            get_string('installfromrepo', 'tool_installaddon'),
            new moodle_url($PAGE->url, array('installremote' => $installremote,
                'installremoteversion' => $installremoteversion, 'installremoteconfirm' => 1)
            )
        );
    }
    // We should never get here.
    throw new moodle_exception('installing_non_installable_component', 'tool_installaddon');
}

// Handle installation of a plugin from the ZIP file.
$installzipcomponent = optional_param('installzipcomponent', null, PARAM_COMPONENT);
$installzipstorage = optional_param('installzipstorage', null, PARAM_FILE);
$installzipconfirm = optional_param('installzipconfirm', false, PARAM_BOOL);

if ($installzipcomponent and $installzipstorage) {
    require_sesskey();
    require_once($CFG->libdir.'/upgradelib.php');

    $PAGE->set_pagelayout('maintenance');
    $PAGE->set_popup_notification_allowed(false);

    $installable = array((object)array(
        'component' => $installzipcomponent,
        'zipfilepath' => make_temp_directory('tool_installaddon').'/'.$installzipstorage.'/plugin.zip',
    ));
    upgrade_install_plugins($installable, $installzipconfirm, get_string('installfromzip', 'tool_installaddon'),
        new moodle_url($installer->index_url(), array('installzipcomponent' => $installzipcomponent,
            'installzipstorage' => $installzipstorage, 'installzipconfirm' => 1)
        )
    );
}

$form = $installer->get_installfromzip_form();

if ($form->is_cancelled()) {
    redirect($PAGE->url);

} else if ($data = $form->get_data()) {
    $storage = $installer->make_installfromzip_storage();
    $form->save_file('zipfile', $storage.'/plugin.zip');

    $ziprootdir = $pluginman->get_plugin_zip_root_dir($storage.'/plugin.zip');
    if (empty($ziprootdir)) {
        echo $output->zip_not_valid_plugin_package_page($installer->index_url());
        die();
    }

    $component = $installer->detect_plugin_component($storage.'/plugin.zip');
    if (!empty($component) and !empty($data->plugintype)) {
        // If the plugin type was explicitly set, make sure it matches the detected one.
        list($detectedtype, $detectedname) = core_component::normalize_component($component);
        if ($detectedtype !== $data->plugintype) {
            $form->selected_plugintype_mismatch($detectedtype);
            echo $output->index_page();
            die();
        }
    }
    if (empty($component)) {
        // This should not happen as all plugins are supposed to declare their
        // component. Still, let admins upload legacy packages if they want/need.
        if (empty($data->plugintype)) {
            $form->require_explicit_plugintype();
            echo $output->index_page();
            die();
        }
        if (!empty($data->rootdir)) {
            $usepluginname = $data->rootdir;
        } else {
            $usepluginname = $ziprootdir;
        }
        $component = $data->plugintype.'_'.$usepluginname;
    }

    redirect($installer->index_url(array(
        'installzipcomponent' => $component,
        'installzipstorage' => basename($storage),
        'sesskey' => sesskey(),
    )));
}

// Display the tool main page.
echo $output->index_page();
