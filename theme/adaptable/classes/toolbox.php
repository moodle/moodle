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
 * @package    theme
 * @subpackage adaptable
 * @copyright  &copy; 2018 G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_adaptable;

defined('MOODLE_INTERNAL') || die;

class toolbox {
    static public function compile_properties($themename, $array = true) {
        global $CFG, $DB;

        $props = array();
        $themeprops = $DB->get_records('config_plugins', array('plugin' => 'theme_'.$themename));

        if ($array) {
            $props['moodle_version'] = $CFG->version;
            // Put the theme version next so that it will be at the top of the table.
            foreach ($themeprops as $themeprop) {
                if ($themeprop->name == 'version') {
                    $props['theme_version'] = $themeprop->value;
                    unset($themeprops[$themeprop->id]);
                    break;
                }
            }

            foreach ($themeprops as $themeprop) {
                $props[$themeprop->name] = $themeprop->value;
            }
        } else {
            $data = new \stdClass();
            $data->id = 0;
            $data->value = $CFG->version;
            $props['moodle_version'] = $data;
            // Convert 'version' to 'theme_version'.
            foreach ($themeprops as $themeprop) {
                if ($themeprop->name == 'version') {
                    $data = new \stdClass();
                    $data->id = $themeprop->id;
                    $data->name = 'theme_version';
                    $data->value = $themeprop->value;
                    $props['theme_version'] = $data;
                    unset($themeprops[$themeprop->id]);
                    break;
                }
            }
            foreach ($themeprops as $themeprop) {
                $data = new \stdClass();
                $data->id = $themeprop->id;
                $data->value = $themeprop->value;
                $props[$themeprop->name] = $data;
            }
        }

        return $props;
    }

    static public function put_properties($themename, $props) {
        global $DB;

        // Get the current properties as a reference and for theme version information.
        $currentprops = self::compile_properties($themename, false);

        // Build the report.
        $report = get_string('putpropertyreport', 'theme_adaptable').PHP_EOL;
        $report .= get_string('putpropertyproperties', 'theme_adaptable').' \'Moodle\' '.
            get_string('putpropertyversion', 'theme_adaptable').' '.$props['moodle_version'].'.'.PHP_EOL;
        unset($props['moodle_version']);
        $report .= get_string('putpropertyour', 'theme_adaptable').' \'Moodle\' '.
            get_string('putpropertyversion', 'theme_adaptable').' '.$currentprops['moodle_version']->value.'.'.PHP_EOL;
        unset($currentprops['moodle_version']);
        $report .= get_string('putpropertyproperties', 'theme_adaptable').' \''.ucfirst($themename).'\' '.
            get_string('putpropertyversion', 'theme_adaptable').' '.$props['theme_version'].'.'.PHP_EOL;
        unset($props['theme_version']);
        $report .= get_string('putpropertyour', 'theme_adaptable').' \''.ucfirst($themename).'\' '.
            get_string('putpropertyversion', 'theme_adaptable').' '.$currentprops['theme_version']->value.'.'.PHP_EOL.PHP_EOL;
        unset($currentprops['theme_version']);

        // Pre-process files - using 'theme_adaptable_pluginfile' in lib.php as a reference.
        $filestoreport = '';
        $preprocessfilesettings = array('logo', 'homebk', 'pagebackground', 'iphoneicon', 'iphoneretinaicon',
            'ipadicon', 'ipadretinaicon', 'fontfilettfheading', 'fontfilettfbody', 'adaptablemarkettingimages');

        // Slide show.
        for ($propslide = 1; $propslide <= $props['slidercount']; $propslide++) {
            $preprocessfilesettings[] = 'p'.$propslide;
        }

        // Process the file properties.
        foreach ($preprocessfilesettings as $preprocessfilesetting) {
            self::put_prop_file_preprocess($preprocessfilesetting, $props, $filestoreport);
            unset($currentprops[$preprocessfilesetting]);
        }

        if ($filestoreport) {
            $report .= get_string('putpropertiesreportfiles', 'theme_adaptable').PHP_EOL.$filestoreport.PHP_EOL;
        }

        // Need to ignore and report on any unknown settings.
        $report .= get_string('putpropertiessettingsreport', 'theme_adaptable').PHP_EOL;
        $changed = '';
        $unchanged = '';
        $added = '';
        $ignored = '';
        $settinglog = '';
        foreach ($props as $propkey => $propvalue) {
            $settinglog = '\''.$propkey.'\' '.get_string('putpropertiesvalue', 'theme_adaptable').' \''.$propvalue.'\'';
            if (array_key_exists($propkey, $currentprops)) {
                if ($propvalue != $currentprops[$propkey]->value) {
                    $settinglog .= ' '.get_string('putpropertiesfrom', 'theme_adaptable').' \''.$currentprops[$propkey]->value.'\'';
                    $changed .= $settinglog.'.'.PHP_EOL;
                    $DB->update_record('config_plugins', array('id' => $currentprops[$propkey]->id, 'value' => $propvalue), true);
                } else {
                    $unchanged .= $settinglog.'.'.PHP_EOL;
                }
            } else if (self::to_add_property($propkey)) {
                // Properties that have an index and don't already exist.
                $DB->insert_record('config_plugins', array(
                    'plugin' => 'theme_'.$themename, 'name' => $propkey, 'value' => $propvalue), true);
                $added .= $settinglog.'.'.PHP_EOL;
            } else {
                $ignored .= $settinglog.'.'.PHP_EOL;
            }
        }

        if (!empty($changed)) {
            $report .= get_string('putpropertieschanged', 'theme_adaptable').PHP_EOL.$changed.PHP_EOL;
        }
        if (!empty($added)) {
            $report .= get_string('putpropertiesadded', 'theme_adaptable').PHP_EOL.$added.PHP_EOL;
        }
        if (!empty($unchanged)) {
            $report .= get_string('putpropertiesunchanged', 'theme_adaptable').PHP_EOL.$unchanged.PHP_EOL;
        }
        if (!empty($ignored)) {
            $report .= get_string('putpropertiesignored', 'theme_adaptable').PHP_EOL.$ignored.PHP_EOL;
        }

        return $report;
    }

    static protected function to_add_property($propkey) {
        static $matches = '('.
             // Slider ....
            '^p[1-9][0-9]?url$|'.
            '^p[1-9][0-9]?cap$|'.
            '^sliderh3color$|'.
            '^sliderh4color$|'.
            '^slidersubmitcolor$|'.
            '^slidersubmitbgcolor$|'.
            '^slider2h3color$|'.
            '^slider2h3bgcolor$|'.
            '^slider2h4color$|'.
            '^slider2h4bgcolor$|'.
            '^slideroption2submitcolor$|'.
            '^slideroption2color$|'.
            '^slideroption2a$|'.
            // Alerts....
            '^enablealert[1-9][0-9]?$|'.
            '^alertkey[1-9][0-9]?$|'.
            '^alerttext[1-9][0-9]?$|'.
            '^alerttype[1-9][0-9]?$|'.
            '^alertaccess[1-9][0-9]?$|'.
            '^alertprofilefield[1-9][0-9]?$|'.
            // Analytics....
            '^analyticstext[1-9][0-9]?$|'.
            '^analyticsprofilefield[1-9][0-9]?$|'.
            // Header menu....
            '^newmenu[1-9][0-9]?title$|'.
            '^newmenu[1-9][0-9]?$|'.
            '^newmenu[1-9][0-9]?requirelogin$|'.
            '^newmenu[1-9][0-9]?field$|'.
            // Marketing blocks....
            '^market[1-9][0-9]?$|'.
            '^marketlayoutrow[1-9][0-9]?$|'.
            // Navbar menu....
            '^toolsmenu[1-9][0-9]?title$|'.
            '^toolsmenu[1-9][0-9]?$|'.
            // Ticker text....
            '^tickertext[1-9][0-9]?$|'.
            '^tickertext[1-9][0-9]?profilefield$'.
            ')';

        return (preg_match($matches, $propkey) === 1);
    }

    static private function put_prop_file_preprocess($key, &$props, &$filestoreport) {
        if (!empty($props[$key])) {
            $filestoreport .= '\''.$key.'\' '.get_string('putpropertiesvalue', 'theme_adaptable').' \''.
                \core_text::substr($props[$key], 1).'\'.'.PHP_EOL;
        }
        unset($props[$key]);
    }
}
