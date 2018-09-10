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
 * Essential is a clean and customizable theme.
 *
 * Common methods for the core and core maintenance renderers.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_essential\output;

defined('MOODLE_INTERNAL') || die;

trait core_renderer_toolbox {
    public function get_setting($setting) {
        $tcr = array_reverse($this->themeconfig, true);

        $settingvalue = false;
        foreach ($tcr as $tconfig) {
            if (property_exists($tconfig->settings, $setting)) {
                $settingvalue = $tconfig->settings->$setting;
                break;
            }
        }
        return $settingvalue;
    }

    public function setting_file_url($setting, $filearea) {
        $tcr = array_reverse($this->themeconfig, true);
        $settingconfig = null;
        foreach ($tcr as $tconfig) {
            if (property_exists($tconfig->settings, $setting)) {
                $settingconfig = $tconfig;
                break;
            }
        }

        if ($settingconfig) {
            return $settingconfig->setting_file_url($setting, $filearea);
        }
        return null;
    }


    public function pix_url($imagename, $component = 'moodle') {
        return end($this->themeconfig)->image_url($imagename, $component);
    }

    public function get_tile_file($filename) {
        global $CFG;
        $filename .= '.php';

        if (file_exists("$CFG->dirroot/theme/essential/layout/tiles/$filename")) {
            return "$CFG->dirroot/theme/essential/layout/tiles/$filename";
        } else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/essential/layout/tiles/$filename")) {
            return "$CFG->themedir/essential/layout/tiles/$filename";
        } else {
            return dirname(__FILE__) . "/$filename";
        }
    }
}
