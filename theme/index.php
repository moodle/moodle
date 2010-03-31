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
$reset  = optional_param('reset', 0, PARAM_BOOL);

admin_externalpage_setup('themeselector');

unset($SESSION->theme);

if ($reset and confirm_sesskey()) {
    theme_reset_all_caches();

} else if ($choose and confirm_sesskey()) {
    // The user has chosen one theme from the list of all themes, show a
    // 'You have chosen a new theme' confirmation page.

    $theme = theme_config::load($choose);
    $choose = $theme->name;

    set_config('theme', $choose);

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('themesaved'));

    echo $OUTPUT->box_start();
    $text = get_string('choosereadme', 'theme_'.$CFG->theme);
    echo format_text($text, FORMAT_MOODLE);
    echo $OUTPUT->box_end();

    echo $OUTPUT->continue_button($CFG->wwwroot . '/' . $CFG->admin . '/index.php');

    echo $OUTPUT->footer();
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

    // Build the table row, and also a list of items to go in the second cell.
    $row = array();
    $infoitems = array();


    // link to the screenshot, now mandatory - the image path is hardcoded because we need image from other themes, not the current one
    $screenshotpath = "$CFG->wwwroot/theme/image.php?theme=$themename&amp;image=screenshot&amp;component=theme";

    // Contents of the first screenshot/preview cell.
    $row[] = "<img src=\"$screenshotpath\" alt=\"$themename\" />";

    // Contents of the second cell.
    $infocell = $OUTPUT->heading($themename, 3);
    if ($themename != $CFG->theme) {
        $infocell .= $OUTPUT->single_button(new moodle_url('index.php', array('choose' => $themename, 'sesskey' => sesskey())), get_string('choose'), 'get');

    }
    $row[] = $infocell;

    $table->data[$themename] = $row;
    if ($themename == $CFG->theme) {
        $table->rowclasses[$themename] = 'selectedtheme';
    }
}

echo html_writer::table($table);

echo $OUTPUT->footer();

