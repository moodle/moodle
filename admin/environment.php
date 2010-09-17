<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

// This file is the admin frontend to execute all the checks available
// in the environment.xml file. It includes database, php and
// php_extensions. Also, it's possible to update the xml file
// from moodle.org be able to check more and more versions.

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/environmentlib.php');
    require_once($CFG->libdir.'/componentlib.class.php');

    admin_externalpage_setup('environment');

/// Parameters
    $action  = optional_param('action', '', PARAM_ACTION);
    $version = optional_param('version', '', PARAM_FILE); //


/// Get some strings
    $stradmin = get_string('administration');
    $stradminhelpenvironment = get_string("adminhelpenvironment");
    $strenvironment = get_string('environment', 'admin');
    $strerror = get_string('error');
    $strmoodleversion = get_string('moodleversion');
    $strupdate = get_string('updatecomponent', 'admin');
    $strupwards = get_string('upwards', 'admin');
    $strmisc = get_string('miscellaneous');

/// Print the header stuff
    echo $OUTPUT->header();

/// Print the component download link
    echo '<div class="reportlink"><a href="environment.php?action=updatecomponent&amp;sesskey='.sesskey().'">'.$strupdate.'</a></div>';

    echo $OUTPUT->heading($strenvironment);

/// Handle the 'updatecomponent' action
    if ($action == 'updatecomponent' && confirm_sesskey()) {
    /// Create component installer and execute it
        if ($cd = new component_installer('http://download.moodle.org',
                                          'environment',
                                          'environment.zip')) {
            $status = $cd->install(); //returns COMPONENT_(ERROR | UPTODATE | INSTALLED)
            switch ($status) {
                case COMPONENT_ERROR:
                    if ($cd->get_error() == 'remotedownloaderror') {
                        $a = new stdClass();
                        $a->url = 'http://download.moodle.org/environment/environment.zip';
                        $a->dest= $CFG->dataroot.'/';
                        echo $OUTPUT->box(get_string($cd->get_error(), 'error', $a), 'errorbox');
                    } else {
                        echo $OUTPUT->box(get_string($cd->get_error(), 'error'), 'errorbox');
                    }
                    break;
                case COMPONENT_UPTODATE:
                    echo $OUTPUT->box(get_string($cd->get_error(), 'error'));
                    break;
                case COMPONENT_INSTALLED:
                    echo $OUTPUT->box(get_string('componentinstalled', 'admin'));
                    break;
            }
        }
    }

/// Start of main box
    echo $OUTPUT->box_start();

    echo "<div style=\"text-align:center\">".$stradminhelpenvironment."</div><br />";

/// Get current Moodle version
    $current_version = $CFG->release;

/// Calculate list of versions
    $versions = array();
    if ($contents = load_environment_xml()) {
        if ($env_versions = get_list_of_environment_versions($contents)) {
        /// Set the current version at the beginning
            $env_version = normalize_version($current_version); //We need this later (for the upwards)
            $versions[$env_version] = $current_version;
        /// If no version has been previously selected, default to $current_version
            if (empty($version)) {
                $version =  $env_version;
            }
        ///Iterate over each version, adding bigger than current
            foreach ($env_versions as $env_version) {
                if (version_compare(normalize_version($current_version), $env_version, '<')) {
                    $versions[$env_version] = $env_version;
                }
            }
        /// Add 'upwards' to the last element
            $versions[$env_version] = $env_version.' '.$strupwards;
        } else {
            $versions = array('error' => $strerror);
        }
    }

/// Print form and popup menu
    echo '<div style="text-align:center"> ';
    $select = new single_select(new moodle_url('/admin/environment.php'), 'version', $versions, $version, null);
    $select->label = $strmoodleversion;
    echo $OUTPUT->render($select);
    echo '</div>';

/// End of main box
    echo $OUTPUT->box_end();

/// Gather and show results
    $status = check_moodle_environment($version, $environment_results);

/// Print footer
    echo $OUTPUT->footer();

