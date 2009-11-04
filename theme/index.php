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

$choose = optional_param('choose', '', PARAM_FILE);

admin_externalpage_setup('themeselector');

unset($SESSION->theme);

if ($choose and confirm_sesskey()) {
    // The user has chosen one theme from the list of all themes, show a
    // 'You have chosen a new theme' confirmation page.

    if (!is_dir($CFG->themedir .'/'. $choose)) {
        print_error('themenotinstall');
    }

    set_config('theme', $choose);

    admin_externalpage_print_header();
    echo $OUTPUT->heading(get_string('themesaved'));

    $readmehtml = $CFG->themedir . '/' . $choose . '/README.html';
    $readmetxt = $CFG->themedir . '/' . $choose . '/README.txt';
    if (is_readable($readmehtml)) {
        echo $OUTPUT->box_start();
        readfile($readmehtml);
        echo $OUTPUT->box_end();

    } else if (is_readable($readmetxt)) {
        echo $OUTPUT->box_start();
        $text = file_get_contents($readmetxt);
        echo format_text($text, FORMAT_MOODLE);
        echo $OUTPUT->box_end();
    }

    echo $OUTPUT->continue_button($CFG->wwwroot . '/' . $CFG->admin . '/index.php');

    echo $OUTPUT->footer();
    exit;
}

// Otherwise, show a list of themes.
admin_externalpage_print_header('themeselector');
echo $OUTPUT->heading(get_string('themes'));

$table = new html_table();
$table->id = 'adminthemeselector';
$table->head = array(get_string('theme'), get_string('info'));

$themes = get_plugin_list('theme');
$sesskey = sesskey();
foreach ($themes as $themename => $themedir) {

    // Load the theme config.
    try {
        $theme = theme_config::load($themename);
    } catch (coding_exception $e) {
        // Bad theme, just skip it for now.
        continue;
    }

    // Build the table row, and also a list of items to go in the second cell.
    $row = array();
    $infoitems = array();

    // Preview link.
    $infoitems['preview'] = '<a href="preview.php?preview=' . $themename . '">' . get_string('preview') . '</a>';

    // First cell (a preview) and also a link to the screenshot, if there is one.
    $screenshotpath = '';
    if (file_exists($theme->dir . '/screenshot.png')) {
        $screenshotpath = $themename . '/screenshot.png';
    } else if (file_exists($theme->dir . '/screenshot.jpg')) {
        $screenshotpath = $themename . '/screenshot.jpg';
    }
    if ($screenshotpath) {
        $infoitems['screenshot'] = '<a href="' . $CFG->themewww .'/'. $screenshotpath . '">' .
                get_string('screenshot') . '</a>';
    }

    // Link to the themes's readme.
    $readmeurl = '';
    if (file_exists($theme->dir . '/README.html')) {
        $readmeurl = $CFG->themewww .'/'. $themename .'/README.html';
    } else if (file_exists($theme->dir . '/README.txt')) {
        $readmeurl = $CFG->themewww .'/'. $themename .'/README.txt';
    }
    if ($readmeurl) {
        $link = html_link::make($readmeurl, get_string('info'));
        $link->add_action(new popup_action('click', $link->url, $themename));
        $infoitems['readme'] = $OUTPUT->link($link);
    }

    // Contents of the first screenshot/preview cell.
    if ($screenshotpath) {
        $row[] = '<object type="text/html" data="' . $CFG->themewww .'/' . $screenshotpath .
                '" height="200" width="400">' . $themename . '</object>';
    } else {
        $row[] = '<object type="text/html" data="preview.php?preview=' . $themename .
                '" height="200" width="400">' . $themename . '</object>';
    }

    // Contents of the second cell.
    $infocell = $OUTPUT->heading($themename, 3);
    if ($infoitems) {
        $infocell .= "<ul>\n<li>" . implode("</li>\n<li>", $infoitems) . "</li>\n</ul>\n";
    }
    if ($themename != $CFG->theme) {
        $infocell .= $OUTPUT->button(html_form::make_button('index.php', array('choose' => $themename, 'sesskey' => $sesskey),
                get_string('choose'), 'get'));

    }
    $row[] = $infocell;

    $table->data[$themename] = $row;
    if ($themename == $CFG->theme) {
        $table->rowclasses[$themename] = 'selectedtheme';
    }
}

echo $OUTPUT->table($table);

echo $OUTPUT->footer();
?>
