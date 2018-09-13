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
 * @package    atto_fontfamily
 * @copyright  2015 Pau Ferrer OCaña
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Set params for this plugin
 */
function atto_fontfamily_params_for_js() {

    $fonts = get_config('atto_fontfamily', 'fontselectlist');
    $fonts = str_replace("\r", ';', $fonts);
    $fonts = str_replace("\n", ';', $fonts);
    $possiblefonts = explode(";", $fonts);
    if (empty($possiblefonts)) {
        $avalaiblefonts = array('Arial=Arial, Helvetica, sans-serif',
            'Times=Times New Roman, Times, serif',
            'Courier=Courier New, Courier, mono',
            'Georgia=Georgia, Times New Roman, Times, serif',
            'Verdana=Verdana, Geneva, sans-serif',
            'Trebuchet=Trebuchet MS, Helvetica, sans-serif');
    } else {
        $avalaiblefonts = array();
        foreach ($possiblefonts as $font) {
            if (!empty($font)) {
                $fonttype = explode('=', $font, 2);
                if (isset($fonttype[1])) {
                    $avalaiblefonts[] = $fonttype[0].'='.$fonttype[1];
                } else {
                    $avalaiblefonts[] = $fonttype[0].'='.$fonttype[0];
                }
            }
        }
    }
    return array('avalaiblefonts' => $avalaiblefonts);
}