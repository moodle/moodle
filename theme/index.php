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
 * This page prvides the Administration -> ... -> Theme selector UI.
 */

require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');

$choose = optional_param('choose', '', PARAM_PLUGIN);
$reset  = optional_param('reset', 0, PARAM_BOOL);
$device = optional_param('device', '', PARAM_TEXT);
$unsettheme = optional_param('unsettheme', 0, PARAM_BOOL);

admin_externalpage_setup('themeselector');

if (!empty($device)) {
    // Make sure the device requested is valid
    $devices = get_device_type_list();
    if (!in_array($device, $devices)) {
        // The provided device isn't a valid device throw an error
        print_error('invaliddevicetype');
    }
}

unset($SESSION->theme);

if ($reset and confirm_sesskey()) {
    theme_reset_all_caches();

} else if ($choose && $device && !$unsettheme && confirm_sesskey()) {
    // Load the theme to make sure it is valid.
    $theme = theme_config::load($choose);
    // Get the config argument for the chosen device.
    $themename = get_device_cfg_var_name($device);
    set_config($themename, $theme->name);

    // Create a new page for the display of the themes readme.
    // This ensures that the readme page is shown using the new theme.
    $confirmpage = new moodle_page();
    $confirmpage->set_context($PAGE->context);
    $confirmpage->set_url($PAGE->url);
    $confirmpage->set_pagelayout($PAGE->pagelayout);
    $confirmpage->set_pagetype($PAGE->pagetype);
    $confirmpage->set_title($PAGE->title);
    $confirmpage->set_heading($PAGE->heading);

    // Get the core renderer for the new theme.
    $output = $confirmpage->get_renderer('core');

    echo $output->header();
    echo $output->heading(get_string('themesaved'));
    echo $output->box_start();
    echo format_text(get_string('choosereadme', 'theme_'.$theme->name), FORMAT_MOODLE);
    echo $output->box_end();
    echo $output->continue_button($CFG->wwwroot . '/theme/index.php');
    echo $output->footer();
    exit;
} else if ($device && $unsettheme && confirm_sesskey() && ($device != 'default')) {
    //Unset the theme and continue.
    unset_config(get_device_cfg_var_name($device));
    $device = '';
}

// Otherwise, show either a list of devices, or is enabledevicedetection set to no or a
// device is specified show a list of themes.

$table = new html_table();
$table->data = array();
$heading = '';
if (!empty($CFG->enabledevicedetection) && empty($device)) {
    $heading = get_string('selectdevice', 'admin');
    // Display a list of devices that a user can select a theme for.

    $strthemenotselected = get_string('themenoselected', 'admin');
    $strthemeselect = get_string('themeselect', 'admin');

    // Display the device selection screen
    $table->id = 'admindeviceselector';
    $table->head = array(get_string('devicetype', 'admin'), get_string('currenttheme', 'admin'), get_string('info'));

    $devices = get_device_type_list();
    foreach ($devices as $thedevice) {

        $headingthemename = ''; // To output the picked theme name when needed
        $themename = get_selected_theme_for_device_type($thedevice);
        if (!$themename && $thedevice == 'default') {
            $themename = theme_config::DEFAULT_THEME;
        }

        $screenshotcell = $strthemenotselected;
        $unsetthemebutton = '';
        if ($themename) {
            // Check the theme exists
            $themename = clean_param($themename, PARAM_THEME);
            if (empty($themename)) {
                // Likely the theme has been deleted
                unset_config(get_device_cfg_var_name($thedevice));
            } else {
                $strthemename = get_string('pluginname', 'theme_'.$themename);
                // link to the screenshot, now mandatory - the image path is hardcoded because we need image from other themes, not the current one
                $screenshoturl = new moodle_url('/theme/image.php', array('theme' => $themename, 'image' => 'screenshot', 'component' => 'theme'));
                // Contents of the screenshot/preview cell.
                $screenshotcell = html_writer::empty_tag('img', array('src' => $screenshoturl, 'alt' => $strthemename));
                // Show the name of the picked theme
                $headingthemename = $OUTPUT->heading($strthemename, 3);
            }
            // If not default device then show option to unset theme.
            if ($thedevice != 'default') {
                $unsetthemestr = get_string('unsettheme', 'admin');
                $unsetthemeurl = new moodle_url('/theme/index.php', array('device' => $thedevice, 'sesskey' => sesskey(), 'unsettheme' => true));
                $unsetthemebutton = new single_button($unsetthemeurl, $unsetthemestr, 'get');
                $unsetthemebutton = $OUTPUT->render($unsetthemebutton);
            }
        }

        $deviceurl = new moodle_url('/theme/index.php', array('device' => $thedevice, 'sesskey' => sesskey()));
        $select = new single_button($deviceurl, $strthemeselect, 'get');

        $table->data[] = array(
            $OUTPUT->heading(ucfirst($thedevice), 3),
            $screenshotcell,
            $headingthemename . $OUTPUT->render($select) . $unsetthemebutton
        );
    }
} else {
    // Either a device has been selected of $CFG->enabledevicedetection is off so display a list
    // of themes to select.
    $heading = get_string('selecttheme', 'admin', $device);
    if (empty($device)) {
        // if $CFG->enabledevicedetection is off this will return 'default'
        $device = get_device_type();
    }

    $table->id = 'adminthemeselector';
    $table->head = array(get_string('theme'), get_string('info'));

    $themes = get_plugin_list('theme');

    foreach ($themes as $themename => $themedir) {

        // Load the theme config.
        try {
            $theme = theme_config::load($themename);
        } catch (Exception $e) {
            // Bad theme, just skip it for now.
            continue;
        }
        if ($themename !== $theme->name) {
            //obsoleted or broken theme, just skip for now
            continue;
        }
        if (empty($CFG->themedesignermode) && $theme->hidefromselector) {
            // The theme doesn't want to be shown in the theme selector and as theme
            // designer mode is switched off we will respect that decision.
            continue;
        }
        $strthemename = get_string('pluginname', 'theme_'.$themename);

        // Build the table row, and also a list of items to go in the second cell.
        $row = array();
        $infoitems = array();
        $rowclasses = array();

        // Set up bools whether this theme is chosen either main or legacy
        $ischosentheme = ($themename == get_selected_theme_for_device_type($device));

        if ($ischosentheme) {
            // Is the chosen main theme
            $rowclasses[] = 'selectedtheme';
        }

        // link to the screenshot, now mandatory - the image path is hardcoded because we need image from other themes, not the current one
        $screenshotpath = new moodle_url('/theme/image.php', array('theme'=>$themename, 'image'=>'screenshot', 'component'=>'theme'));
        // Contents of the first screenshot/preview cell.
        $row[] = html_writer::empty_tag('img', array('src'=>$screenshotpath, 'alt'=>$strthemename));
        // Contents of the second cell.
        $infocell = $OUTPUT->heading($strthemename, 3);

        // Button to choose this as the main theme or unset this theme for
        // devices other then default
        if (($ischosentheme) && ($device != 'default')) {
            $unsetthemestr = get_string('unsettheme', 'admin');
            $unsetthemeurl = new moodle_url('/theme/index.php', array('device' => $device, 'unsettheme' => true, 'sesskey' => sesskey()));
            $unsetbutton = new single_button($unsetthemeurl, $unsetthemestr, 'get');
            $infocell .= $OUTPUT->render($unsetbutton);
        } else if ((!$ischosentheme)) {
            $setthemestr = get_string('usetheme');
            $setthemeurl = new moodle_url('/theme/index.php', array('device' => $device, 'choose' => $themename, 'sesskey' => sesskey()));
            $setthemebutton = new single_button($setthemeurl, $setthemestr, 'get');
            $infocell .= $OUTPUT->render($setthemebutton);
        }

        $row[] = $infocell;

        $table->data[$themename] = $row;
        $table->rowclasses[$themename] = join(' ', $rowclasses);
    }
}
echo $OUTPUT->header('themeselector');
echo $OUTPUT->heading($heading);

$params = array('sesskey' => sesskey(), 'reset' => 1);
if (!empty($device)) {
    $params['device'] = $device;
}
echo $OUTPUT->single_button(new moodle_url('index.php', $params), get_string('themeresetcaches', 'admin'));

echo html_writer::table($table);

echo $OUTPUT->footer();
