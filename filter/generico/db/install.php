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
 * filter generico installation tasks
 *
 * @package    filter_generico
 * @copyright  2016 Justin Hunt {@link http://poodll.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Install the plugin.
 */
function xmldb_filter_generico_install() {
    $admin_presets = new \filter_generico\presets_control('filter_generico/templatepresets_0',
            'presets', '', 0);
    $presets = $admin_presets->fetch_presets();
    $forinstall = array('welcomeuser');
    $templateindex = 0;
    foreach ($presets as $preset) {
        if (in_array($preset['key'], $forinstall)) {
            $templateindex++;
            //set the config
            $admin_presets->set_preset_to_config($preset, $templateindex);
        }
    }//end of for each presets	
}