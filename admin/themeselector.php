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
 * Theme selector page for admin use.
 *
 * @package core_admin
 * @copyright 2023 David Woloszyn <david.woloszyn@moodle.com>
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
$PAGE->set_pagelayout('standard');

// Clear theme cache.
if ($reset && confirm_sesskey()) {
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
    redirect(new moodle_url('/admin/themeselector.php'), $notifymessage, null, $notifytype);
}

// Insert header.
echo $OUTPUT->header();

// Prepare data for rendering.
$data = [];
$index = 0;
$currentthemeindex = 0;
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

    // All params for modal use are set here, except for 'choosereadme' (description).
    // That string can be long. We will fetch it with JS as opposed to passing it as an attribute.
    $themedata = [];

    // The 'name' param is formatted and should not to be confused with 'choose'.
    $themedata['name'] = get_string('pluginname', 'theme_'.$themename);;
    $themedata['choose'] = $themename;

    // Image to display for previewing.
    $image = new moodle_url('/theme/image.php', ['theme' => $themename, 'image' => 'screenshot', 'component' => 'theme']);
    $themedata['image'] = $image;

    // Is this the current theme?
    if ($themename === $CFG->theme) {
        $themedata['current'] = true;
        $currentthemeindex = $index;
    } else if (!$definedinconfig) {
        // Form params.
        $actionurl = new moodle_url('/admin/themeselector.php');
        $themedata['actionurl'] = $actionurl;
        $themedata['sesskey'] = sesskey();
    }

    // Settings url.
    $settingspath = "$themedir/settings.php";
    if (file_exists($settingspath)) {
        $section = "themesetting{$themename}";
        $settingsurl = new moodle_url('/admin/settings.php', ['section' => $section]);
        $themedata['settingsurl'] = $settingsurl;
    }

    // Link to the theme usage report if override enabled and it is being used in at least one context.
    if (\core\output\theme_usage::is_theme_used_in_any_context($themename) === \core\output\theme_usage::THEME_IS_USED) {
        $reporturl = new moodle_url($CFG->wwwroot . '/report/themeusage/index.php');
        $reporturl->params(['themechoice' => $themename]);
        $themedata['reporturl'] = $reporturl->out(false);
    }

    $data[$index] = $themedata;
    $index++;
}

// Reorder the array to always have the current theme first.
if (isset($data[$currentthemeindex])) {
    $currenttheme = $data[$currentthemeindex];
    unset($data[$currentthemeindex]);
    array_unshift($data, $currenttheme);
}

// Show theme selector.
$renderable = new \core_admin\output\theme_selector($data, $definedinconfig);
$renderer = $PAGE->get_renderer('core', 'admin');
echo $renderer->theme_selector_list($renderable);

// Show footer.
echo $OUTPUT->footer();
