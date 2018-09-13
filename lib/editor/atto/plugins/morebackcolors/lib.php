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
 * @package    atto_morebackcolors
 * @copyright  2014-2015 Université de Lausanne
 * @author     Nicolas Dunand <nicolas.dunand@unil.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Set params for this plugin
 * @param string $elementid
 */
function atto_morebackcolors_params_for_js($elementid, $options, $fpoptions) {
    // Pass the number of visible groups as a param.
    $availablecolors = get_config('atto_morebackcolors', 'availablecolors');
    $possiblecolors = explode("\n", (str_replace("\r", '', $availablecolors)));
    $colors = array();
    foreach($possiblecolors as $color) {
        if (preg_match('/^#?([0-9a-fA-F]{3})|([0-9a-fA-F]{6})$/', $color, $matches)) {
            $colors[] = $color;
        }
    }
    $allowcustom = get_config ('atto_morebackcolors', 'allowcustom');
    return array(
        'colors' => $colors,
        'allowcustom' => $allowcustom
    );
}

/**
 * Initialise the js strings required for this module.
 */
function atto_morebackcolors_strings_for_js() {
    global $PAGE;

    $PAGE->requires->strings_for_js(array('custom',
                                          'customcolor',
                                          'submit',
                                          'rgb',
                                          'hsl',
                                          'hexadecimal',
                                          'saturation',
                                          'luminance'),
                                    'atto_morebackcolors');
}

