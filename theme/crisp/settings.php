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
 * Moodle's crisp theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package   theme_crisp
 * @copyright 2014 dualcube {@link http://dualcube.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    // For slider.
    
     
    $names6 = 'theme_crisp/textinformation';
    $titles6 = get_string('textinformation', 'theme_crisp');
    $descriptions6 = get_string('textinformationdesc', 'theme_crisp');
    $defaults6 = '';
    $setting = new admin_setting_confightmleditor($names6, $titles6, $descriptions6, $defaults6);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);


    // Number of slides.
    $name = 'theme_crisp/numberofslides';
    $title = get_string('numberofslides', 'theme_crisp');
    $description = get_string('numberofslides_desc', 'theme_crisp');
    $default = 3;
    $choices = array(
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
        6 => '6',
        7 => '7',
        8 => '8',
        9 => '9',
        10 => '10',
        11 => '11',
        12 => '12',
    );
    $setting = new admin_setting_configselect($name,$title,$description,$default,$choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
    
    $numberofslides = get_config('theme_crisp', 'numberofslides');
     
    for ($i = 1; $i <= $numberofslides; $i++) {

        // Slide Image.
        $name = 'theme_crisp/slide' . $i . 'image';
        $title = get_string( 'slideimage', 'theme_crisp');
        $description = get_string('slidedesc', 'theme_crisp');
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'slide' . $i . 'image');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $settings->add($setting);

        // Slide Caption.
        $name = 'theme_crisp/slide' . $i . 'caption';
        $title = get_string('slidecaption', 'theme_crisp');
        $description = get_string('slidecaptiondesc', 'theme_crisp');
        $default = get_string('slidecaptiondefault', 'theme_crisp', array('slideno' => sprintf('%02d', $i) ));
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $settings->add($setting);



    }

 
}

