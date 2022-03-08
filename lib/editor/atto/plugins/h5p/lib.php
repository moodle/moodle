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
 * Atto text editor integration version file.
 *
 * @package    atto_h5p
 * @copyright  2019 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_h5p\local\library\autoloader;

/**
 * Set params for this button.
 *
 * @param string $elementid
 * @param stdClass $options - the options for the editor, including the context.
 * @param stdClass $fpoptions - unused.
 */
function atto_h5p_params_for_js($elementid, $options, $fpoptions) {
    $context = $options['context'];
    if (!$context) {
        $context = context_system::instance();
    }

    $addembed = has_capability('atto/h5p:addembed', $context);
    $upload = has_capability('moodle/h5p:deploy', $context);

    $allowedmethods = 'none';
    if ($addembed && $upload) {
        $allowedmethods = 'both';
    } else if ($addembed) {
        $allowedmethods = 'embed';
    } else if ($upload) {
        $allowedmethods = 'upload';
    }

    $params = [
        'allowedmethods' => $allowedmethods,
        'storeinrepo' => true
    ];
    return $params;
}

/**
 * Initialise the strings required for js
 */
function atto_h5p_strings_for_js() {
    global $PAGE;

    $strings = array(
        'browserepositories',
        'copyrightbutton',
        'downloadbutton',
        'instructions',
        'embedbutton',
        'h5pfile',
        'h5poptions',
        'h5purl',
        'h5pfileorurl',
        'invalidh5purl',
        'noh5pcontent',
        'pluginname'
    );

    $PAGE->requires->strings_for_js($strings, 'atto_h5p');
    $PAGE->requires->strings_for_js(['expand', 'collapse'], 'moodle');
    $PAGE->requires->js(autoloader::get_h5p_core_library_url('js/h5p-resizer.js'));
}
