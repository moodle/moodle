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
 * This page provides the Administration -> ... -> Theme selector UI.
 *
 * @package core
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');

$choose = optional_param('choose', '', PARAM_PLUGIN);
$reset  = optional_param('reset', 0, PARAM_BOOL);
$confirmation = optional_param('confirmation', 0, PARAM_BOOL);

admin_externalpage_setup('themeselector');

unset($SESSION->theme);

$PAGE->set_primary_active_tab('siteadminnode');
$PAGE->navbar->add(get_string('themeselector', 'admin'), $PAGE->url);

// Clear theme cache.
if ($reset and confirm_sesskey()) {
    theme_reset_all_caches();
}

$definedinconfig = array_key_exists('theme', $CFG->config_php_settings);
if ($definedinconfig) {
    $forcedthemename = get_string('pluginname', 'theme_'.$CFG->theme);
    // Show a notification that the theme is defined in config.php.
    \core\notification::info(get_string('themedefinedinconfigphp', 'admin', $forcedthemename));
}

// Change theme.
if (!$definedinconfig && !empty($choose) && confirm_sesskey()) {

    // Load the theme to make sure it is valid.
    $theme = theme_config::load($choose);

    if ($theme instanceof \theme_config) {
        set_config('theme', $theme->name);
        $notifytype = 'success';
        $notifymessage = get_string('themesaved');
    } else {
        $notifytype = 'error';
        $notifymessage = get_string('error');
    }

    // Redirect with notification.
    redirect(new moodle_url('/theme/index.php'), $notifymessage, null, $notifytype);
}

$table = new html_table();
$table->data = [];
$table->id = 'adminthemeselector';
$table->head = [get_string('theme'), get_string('info')];
$table->align = ['left', 'left'];

$themes = core_component::get_plugin_list('theme');

// Loop through available themes.
foreach ($themes as $themename => $themedir) {

    try {
        $theme = theme_config::load($themename);
    } catch (Exception $e) {
        // Bad theme, just skip it for now.
        continue;
    }
    if ($themename !== $theme->name) {
        // Obsoleted or broken theme, just skip for now.
        continue;
    }
    if (empty($CFG->themedesignermode) && $theme->hidefromselector) {
        // The theme doesn't want to be shown in the theme selector and as theme
        // designer mode is switched off we will respect that decision.
        continue;
    }

    // Build the table rows.
    $row = [];
    $rowclasses = [];
    $strthemename = get_string('pluginname', 'theme_'.$themename);

    // Screenshot/preview cell.
    $screenshotpath = new moodle_url('/theme/image.php', ['theme' => $themename, 'image' => 'screenshot', 'component' => 'theme']);
    $row[] = html_writer::empty_tag('img', ['class' => 'img-fluid', 'src' => $screenshotpath, 'alt' => $strthemename]);

    // Info cell.
    $infocell = $OUTPUT->heading($strthemename, 3);

    // Is this the current theme?
    if ($themename == $CFG->theme) {
        $rowclasses[] = 'selectedtheme';
        if ($definedinconfig) {
            $infocell .= html_writer::div(get_string('configoverride', 'admin'), 'alert alert-info');
        }
    } else if (!$definedinconfig) {
        // Button to choose this as the main theme.
        $setthemestr = get_string('usetheme');
        $setthemeurl = new moodle_url('/theme/index.php', ['choose' => $themename, 'sesskey' => sesskey()]);
        $setthemebutton = new single_button($setthemeurl, $setthemestr, 'post');
        $infocell .= html_writer::div($OUTPUT->render($setthemebutton), 'mt-2');
    }
    $row[] = $infocell;

    $table->data[$themename] = $row;
    $table->rowclasses[$themename] = join(' ', $rowclasses);
    $table->responsive = false;
}

// Show heading.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('themeselector', 'admin'));

// Reset theme caches button.
$reseturl = new moodle_url('index.php', ['sesskey' => sesskey(), 'reset' => 1]);
echo $OUTPUT->single_button($reseturl, get_string('themeresetcaches', 'admin'), 'post');

// Render main table.
echo html_writer::table($table);
echo $OUTPUT->footer();
