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

$choose = optional_param('choose', '', PARAM_SAFEDIR);
$chooselegacy = optional_param('chooselegacy', '', PARAM_SAFEDIR);
$reset  = optional_param('reset', 0, PARAM_BOOL);

admin_externalpage_setup('themeselector');

unset($SESSION->theme);

if ($reset and confirm_sesskey()) {
    theme_reset_all_caches();

} else if (($choose || $chooselegacy) && confirm_sesskey()) {

    if ($choose) {
        $chosentheme = $choose;
        $heading = get_string('themesaved');
        $config = 'theme';
    } else {
        $chosentheme = $chooselegacy;
        $heading = get_string('legacythemesaved');
        $config = 'themelegacy';
    }
    $theme = theme_config::load($chosentheme);
    set_config($config, $theme->name);

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
    echo $output->heading($heading);
    echo $output->box_start();
    echo format_text(get_string('choosereadme', 'theme_'.$CFG->theme), FORMAT_MOODLE);
    echo $output->box_end();
    echo $output->continue_button($CFG->wwwroot . '/' . $CFG->admin . '/index.php');
    echo $output->footer();
    exit;
}

// Otherwise, show a list of themes.
echo $OUTPUT->header('themeselector');
echo $OUTPUT->heading(get_string('themes'));

echo $OUTPUT->single_button(new moodle_url('index.php', array('sesskey'=>sesskey(),'reset'=>1)), get_string('themeresetcaches', 'admin'));

$table = new html_table();
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
    $ischosentheme = ($themename == $CFG->theme);
    $ischosenlegacytheme = (!empty($CFG->themelegacy) && $themename == $CFG->themelegacy);

    if ($ischosentheme) {
        // Is the chosen main theme
        $rowclasses[] = 'selectedtheme';
    }
    if ($ischosenlegacytheme) {
        // Is the chosen legacy theme
        $rowclasses[] = 'selectedlegacytheme';
    }

    // link to the screenshot, now mandatory - the image path is hardcoded because we need image from other themes, not the current one
    $screenshotpath = new moodle_url('/theme/image.php', array('theme'=>$themename, 'image'=>'screenshot','component'=>'theme'));
    // Contents of the first screenshot/preview cell.
    $row[] = html_writer::empty_tag('img', array('src'=>$screenshotpath, 'alt'=>$strthemename));

    // Contents of the second cell.
    $infocell = $OUTPUT->heading($strthemename, 3);

    // Button to choose this as the main theme
    $maintheme = new single_button(new moodle_url('/theme/index.php', array('choose' => $themename, 'sesskey' => sesskey())), get_string('useformaintheme'), 'get');
    $maintheme->disabled = $ischosentheme;
    $infocell .= $OUTPUT->render($maintheme);

    // Button to choose this as the legacy theme
    $legacytheme = new single_button(new moodle_url('/theme/index.php', array('chooselegacy' => $themename, 'sesskey' => sesskey())), get_string('useforlegacytheme'), 'get');
    $legacytheme->disabled = $ischosenlegacytheme;
    $infocell .= $OUTPUT->render($legacytheme);

    $row[] = $infocell;

    $table->data[$themename] = $row;
    $table->rowclasses[$themename] = join(' ', $rowclasses);
}

echo html_writer::table($table);

echo $OUTPUT->footer();
