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

    $allowedmethods = 'none';
    if ($addembed) {
        $allowedmethods = 'embed';
    }

    $params = ['allowedmethods' => $allowedmethods];
    return $params;
}

/**
 * Initialise the strings required for js
 */
function atto_h5p_strings_for_js() {
    global $PAGE;

    $strings = array(
        'saveh5p',
        'h5pproperties',
        'enterurl',
        'invalidh5purl'
    );

    $PAGE->requires->strings_for_js($strings, 'atto_h5p');
    $PAGE->requires->js(new moodle_url('/lib/editor/atto/plugins/h5p/js/h5p-resizer.js'));
}


