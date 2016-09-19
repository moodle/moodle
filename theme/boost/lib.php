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
 * @package    theme_boost
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Post process the CSS tree.
 *
 * @param string $tree The CSS tree.
 * @param theme_config $theme The theme config object.
 */
function theme_boost_css_tree_post_processor($tree, $this) {
    $prefixer = new theme_boost\autoprefixer($tree);
    $prefixer->prefix();
}

/**
 * Get the SCSS file to include.
 *
 * @param theme_config $theme The theme config object.
 * @return string The name of the file without 'scss'.
 */
function theme_boost_get_scss_file($theme) {
    $preset = !empty($theme->settings->preset) ? $theme->settings->preset : 'default';
    return 'preset-' . $preset;
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_boost_get_extra_scss($theme) {
    return !empty($theme->settings->scss) ? $theme->settings->scss : '';
}

/**
 * Get additional SCSS variables.
 *
 * @param theme_config $theme The theme config object.
 * @return array
 */
function theme_boost_get_scss_variables($theme) {
    $variables = [];
    $configurable = [
        // Config key => [variableName, ...].
        'brandcolor' => ['brand-primary'],
    ];

    foreach ($configurable as $configkey => $targets) {
        $value = $theme->settings->{$configkey};
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$variables, $value) {
            $variables[$target] = $value;
        }, (array) $targets);
    }

    if (!empty($theme->settings->scss_variables)) {
        $variables = array_merge($variables, theme_boost_parse_scss_variables($theme->settings->scss_variables));
    }

    return $variables;
}

/**
 * Parse a string into SCSS variables.
 *
 * - One variable definition per line,
 * - The variable name is separated from the value by a colon,
 * - The dollar sign is optional,
 * - The trailing semi-colon is optional,
 * - CSS comments (starting with //) are accepted
 * - Variables names can only contain letters, numbers, hyphens and underscores.
 *
 * @param string $data The string to parse from.
 * @param bool $lenient When non lenient, an exception will be thrown when a line cannot be parsed.
 * @return array
 */
function theme_boost_parse_scss_variables($data, $lenient = true) {
    $variables = [];
    $lines = explode("\n", $data);
    $i = 0;

    foreach ($lines as $line) {
        $i++;
        if (preg_match('@^\s*//@', $line)) {
            continue;
        }

        $parts = explode(':', trim($line));
        $variable = ltrim($parts[0], '$ ');
        $value = rtrim(ltrim(isset($parts[1]) ? $parts[1] : ''), "; ");

        if (empty($variable) || !preg_match('/^[a-z0-9_-]+$/i', $variable) || (empty($value) && !is_numeric($value))) {
            if ($lenient) {
                continue;
            }
            throw new moodle_exception('errorparsingscssvariables', 'theme_boost', null, $i);
        }

        $variables[$variable] = $value;
    }

    return $variables;
}
