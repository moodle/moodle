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
 * Theme functions.
 *
 * @package    theme_moove
 * @copyright 2017 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_moove_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = \core\context\system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Moove scss.
    $moovevariables = file_get_contents($CFG->dirroot . '/theme/moove/scss/moove/_variables.scss');
    $moove = file_get_contents($CFG->dirroot . '/theme/moove/scss/default.scss');
    $security = file_get_contents($CFG->dirroot . '/theme/moove/scss/moove/_security.scss');

    $lastpreset = '';
    if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_moove', 'preset', 0, '/', $filename))) {
        $lastpreset = $presetfile->get_content();
    }

    // Combine them together.
    $allscss = $moovevariables . "\n" . $scss . "\n" . $moove . "\n" . $lastpreset .    "\n" . $security;

    return $allscss;
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_moove_get_extra_scss($theme) {
    $content = '';

    // Sets the login background image.
    $loginbgimgurl = $theme->setting_file_url('loginbgimg', 'loginbgimg');

    if (empty($loginbgimgurl)) {
        return '';
    }

    $content .= 'body.pagelayout-login #page { ';
    $content .= "background-color: initial; background-image: url('$loginbgimgurl'); background-size: cover;";
    $content .= ' }';

    // Always return the background image with the scss when we have it.
    return !empty($theme->settings->scss) ? $theme->settings->scss . ' ' . $content : $content;
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_moove_get_pre_scss($theme) {
    $scss = '';
    $configurable = [
        // Config key => [variableName, ...].
        'brandcolor' => ['brand-primary'],
        'secondarymenucolor' => 'secondary-menu-color',
        'fontsite' => 'font-family-sans-serif',
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }

        if ($configkey == 'fontsite' && $value == 'Moodle') {
            continue;
        }

        array_map(function ($target) use (&$scss, $value) {
            if ($target == 'fontsite') {
                $scss .= '$' . $target . ': "' . $value . '", sans-serif !default' . ";\n";
            } else {
                $scss .= '$' . $target . ': ' . $value . ";\n";
            }
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 * Get compiled css.
 *
 * @return string compiled css
 */
function theme_moove_get_precompiled_css() {
    global $CFG;

    return file_get_contents($CFG->dirroot . '/theme/moove/style/moodle.css');
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return mixed
 */
function theme_moove_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    $theme = theme_config::load('moove');

    if (
        $context->contextlevel == CONTEXT_SYSTEM &&
        ($filearea === 'logo' || $filearea === 'loginbgimg' || $filearea == 'favicon')
    ) {
        $theme = theme_config::load('moove');
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }

    if ($filearea === 'hvp') {
        return theme_moove_serve_hvp_css($args[1], $theme);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && preg_match("/^sliderimage[1-9][0-9]?$/", $filearea) !== false) {
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'marketing1icon') {
        return $theme->setting_file_serve('marketing1icon', $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'marketing2icon') {
        return $theme->setting_file_serve('marketing2icon', $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'marketing3icon') {
        return $theme->setting_file_serve('marketing3icon', $args, $forcedownload, $options);
    }

    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'marketing4icon') {
        return $theme->setting_file_serve('marketing4icon', $args, $forcedownload, $options);
    }

    send_file_not_found();
}

/**
 * Serves the H5P Custom CSS.
 *
 * @param string $filename The filename.
 * @param theme_config $theme The theme config object.
 *
 * @throws dml_exception
 */
function theme_moove_serve_hvp_css($filename, $theme) {
    global $CFG, $PAGE;

    require_once($CFG->dirroot . '/lib/configonlylib.php'); // For minenable_zlib_compression function.

    $PAGE->set_context(\core\context\system::instance());
    $themename = $theme->name;

    $settings = new \theme_moove\util\settings();
    $content = $settings->hvpcss;

    $md5content = md5($content);
    $md5stored = get_config('theme_moove', 'hvpccssmd5');
    if ((empty($md5stored)) || ($md5stored != $md5content)) {
        // Content changed, so the last modified time needs to change.
        set_config('hvpccssmd5', $md5content, $themename);
        $lastmodified = time();
        set_config('hvpccsslm', $lastmodified, $themename);
    } else {
        $lastmodified = get_config($themename, 'hvpccsslm');
        if (empty($lastmodified)) {
            $lastmodified = time();
        }
    }

    // Sixty days only - the revision may get incremented quite often.
    $lifetime = 60 * 60 * 24 * 60;

    header('HTTP/1.1 200 OK');

    header('Etag: "' . $md5content . '"');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: ' . strlen($content));
    }

    echo $content;

    die;
}
