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
 * This file is the admin frontend to execute all the checks available
 * in the environment.xml file. It includes database, php and
 * php_extensions. Also, it's possible to update the xml file
 * from moodle.org be able to check more and more versions.
 *
 * @package    core
 * @subpackage admin
 * @copyright  2006 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/environmentlib.php');
require_once($CFG->libdir.'/componentlib.class.php');

// Parameters
$action  = optional_param('action', '', PARAM_ALPHANUMEXT);
$version = optional_param('version', '', PARAM_FILE); //

$extraurlparams = array();
if ($version) {
    $extraurlparams['version'] = $version;
}
admin_externalpage_setup('environment', '', $extraurlparams);

// Handle the 'updatecomponent' action
if ($action == 'updatecomponent' && confirm_sesskey()) {
    // Create component installer and execute it
    if ($cd = new component_installer('https://download.moodle.org',
                                      'environment',
                                      'environment.zip')) {
        $status = $cd->install(); //returns COMPONENT_(ERROR | UPTODATE | INSTALLED)
        switch ($status) {
            case COMPONENT_ERROR:
                if ($cd->get_error() == 'remotedownloaderror') {
                    $a = new stdClass();
                    $a->url  = 'https://download.moodle.org/environment/environment.zip';
                    $a->dest = $CFG->dataroot . '/';
                    throw new \moodle_exception($cd->get_error(), 'error', $PAGE->url, $a);
                    die();

                } else {
                    throw new \moodle_exception($cd->get_error(), 'error', $PAGE->url);
                    die();
                }

            case COMPONENT_UPTODATE:
                redirect($PAGE->url, get_string($cd->get_error(), 'error'));
                die;

            case COMPONENT_INSTALLED:
                redirect($PAGE->url, get_string('componentinstalled', 'admin'));
                die;
        }
    }
}

// Get current Moodle version
$current_version = $CFG->release;

// Calculate list of versions
$versions = array();
if ($contents = load_environment_xml()) {
    if ($env_versions = get_list_of_environment_versions($contents)) {
        // Set the current version at the beginning
        $env_version = normalize_version($current_version); //We need this later (for the upwards)
        $versions[$env_version] = $current_version;
        // If no version has been previously selected, default to $current_version
        if (empty($version)) {
            $version =  $env_version;
        }
        //Iterate over each version, adding bigger than current
        foreach ($env_versions as $env_version) {
            if (version_compare(normalize_version($current_version), $env_version, '<')) {
                $versions[$env_version] = $env_version;
            }
        }
        // Add 'upwards' to the last element
        $versions[$env_version] = $env_version.' '.get_string('upwards', 'admin');
    } else {
        $versions = array('error' => get_string('error'));
    }
}

// Get the results of the environment check.
list($envstatus, $environment_results) = check_moodle_environment($version, ENV_SELECT_NEWER);

// Display the page.
$output = $PAGE->get_renderer('core', 'admin');
echo $output->environment_check_page($versions, $version, $envstatus, $environment_results);
