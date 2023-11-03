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
 * Controller
 *
 * @package    block_reporttiles
 * @copyright  2017 eAbyas info solutions
 * @license    http://www.gnu.org/copyleft/gpl.reporttiles GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * [block_learnerscript_pluginfile description]
 * @param  [type] $course        [description]
 * @param  [type] $cm            [description]
 * @param  [type] $context       [description]
 * @param  [type] $filearea      [description]
 * @param  [type] $args          [description]
 * @param  [type] $forcedownload [description]
 * @param  array  $options       [description]
 * @return [type]                [description]
 */
function block_reporttiles_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG;

    if ($filearea == 'reporttiles') {
        $itemid = (int) array_shift($args);

        $fs = get_file_storage();
        $filename = array_pop($args);
        if (empty($args)) {
            $filepath = '/';
        } else {
            $filepath = '/' . implode('/', $args) . '/';
        }

        $file = $fs->get_file($context->id, 'block_reporttiles', $filearea, $itemid, $filepath, $filename);

        if (!$file) {
            return false;
        }
        $filedata = $file->resize_image(200, 200);
        \core\session\manager::write_close();
        send_stored_file($file, null, 0, 1);
    }

    send_file_not_found();
}
/**
 * Parses CSS before it is cached.
 *
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param string $css The CSS
 * @param theme_config $theme The theme config object.
 * @return string The parsed CSS The parsed CSS.
 */
function block_reporttiles_process_css($css, $theme) {

    // Set custom CSS.
    $css = block_reporttiles_set_customcss($css, $customcss);

    // Define the default settings for the theme incase they've not been set.
    $defaults = array(
        '[[setting:bordercolor]]' => '#009688',
    );

    // Get all the defined settings for the theme and replace defaults.
    foreach ($theme->settings as $key => $val) {
        if (array_key_exists('[[setting:'.$key.']]', $defaults) && !empty($val)) {
            $defaults['[[setting:'.$key.']]'] = $val;
        }
    }

    // Replace the CSS with values from the $defaults array.
    $css = strtr($css, $defaults);
    return $css;
}

/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function block_reporttiles_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}
