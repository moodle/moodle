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
 * schoollege backgrounds callbacks.
 *
 * @package    theme_schoollege
 * @copyright  2020 Chris Kenniburg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_schoollege_get_main_scss_content($theme) {
    global $CFG ;

    $scss = '';
    $pre = '';
    $post = '';

    $headerimage = !empty($theme->settings->defaultbackgroundimage) ? $theme->settings->defaultbackgroundimage : null;
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();
    $context = context_system::instance();

    $headeroverlay = !empty($theme->settings->headeroverlay) ? $theme->settings->headeroverlay : null;
    if (isset($headeroverlay)) {
        $headeroverlaylink = $theme->settings->headeroverlay;
        $pre .= '.headeroverlay {background-image: url("' . $headeroverlaylink . '");}';
    }

    $footeroverlay = !empty($theme->settings->footeroverlay) ? $theme->settings->footeroverlay : null;
    if (isset($footeroverlay)) {
        $footeroverlaylinkfooter = $theme->settings->footeroverlay;
        $pre .= '#page-footer {background-image: url("' . $footeroverlaylinkfooter . '");}';
    } else {
        $pre .= '#page-footer {background-image: none;}';
    } 


    // Set default preset.
    if ($filename == 'schoollege.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/schoollege/scss/preset/schoollege.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_schoollege', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_schoollege and not theme_boost (see the line above).
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/schoollege/scss/preset/schoollege.scss');
    }

    // Section Style
    if ($theme->settings->sectionlayout == 1) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/schoollege/scss/sectionstyles/section1.scss');
    }
    if ($theme->settings->sectionlayout == 2) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/schoollege/scss/sectionstyles/section2.scss');
    }
    if ($theme->settings->sectionlayout == 3) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/schoollege/scss/sectionstyles/section3.scss');
    }
    if ($theme->settings->sectionlayout == 4) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/schoollege/scss/sectionstyles/section4.scss');
    }

    // Set the background image for the login page.
    $loginbg = $theme->setting_file_url('loginbkgimage', 'loginbkgimage');
    if (isset($loginbg)) {
        $pre .= 'body.pagelayout-login #page {background-image: url("' . $loginbg . '") !important; background-size:cover; background-position:center;}';
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre .= file_get_contents($CFG->dirroot . '/theme/schoollege/scss/pre.scss');

    // Get schoollege default styling for schoollege only features like sidebar and other page elements.
    $theme = file_get_contents($CFG->dirroot . '/theme/schoollege/scss/theme.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post .= file_get_contents($CFG->dirroot . '/theme/schoollege/scss/post.scss');

    // Combine them together.
    return $pre . "\n" . $scss . "\n" . $theme . "\n" . $post;
}



function theme_schoollege_get_pre_scss($theme) {
    global $CFG;

    $scss = '';
    $configurable = [
        // Config key => [variableName, ...].
        'brandcolor' => ['primary'],
        'headerbg' => ['header-bg'],
        'footerbg' => ['footer-bg'],
        'navbarbg' => ['navbar-bg'],
        'sidebarbg' => ['sidebar-bg'],
        'sidebarahoverbg' => ['sidebar-ahover-bg'],
        'iconwidth' => ['fpicon-width'],
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

function theme_schoollege_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('schoollege');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === '')) {
        $theme = theme_config::load('schoollege');
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else if ($filearea === 'logintopimage') {
        return $theme->setting_file_serve('logintopimage', $args, $forcedownload, $options);
    } else if ($filearea === 'defaultbackgroundimage') {
        return $theme->setting_file_serve('defaultbackgroundimage', $args, $forcedownload, $options);
    } else if ($filearea === 'coursetilebg') {
        return $theme->setting_file_serve('coursetilebg', $args, $forcedownload, $options);
    } else if ($filearea === 'brandlogo') {
        return $theme->setting_file_serve('brandlogo', $args, $forcedownload, $options);
    } else if ($filearea === 'loginbkgimage') {
        return $theme->setting_file_serve('loginbkgimage', $args, $forcedownload, $options);
    } else if ($filearea === 'headeroverlay') {
        return $theme->setting_file_serve('headeroverlay', $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
    theme_reset_all_caches();
}

