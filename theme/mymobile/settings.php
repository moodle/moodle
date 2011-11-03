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
 * settings file for mymobile theme
 *
 * @package    theme
 * @subpackage mymobile
 * @copyright  John Stabinger
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

$name = 'theme_mymobile/mswatch';
    $title = get_string('mswatch','theme_mymobile');
    $description = get_string('mswatch_desc', 'theme_mymobile');
    $default = 'light';
    $choices = array('light'=>'light', 'grey'=>'grey');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);
    
    $name = 'theme_mymobile/mtext';
    $title = get_string('mtext','theme_mymobile');
    $description = get_string('mtext_desc', 'theme_mymobile');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $settings->add($setting);

$name = 'theme_mymobile/mtopic';
    $title = get_string('mtopic','theme_mymobile');
    $description = get_string('mtopic_desc', 'theme_mymobile');
    $default = 'topicshow';
    $choices = array('topicshow'=>'Yes', 'topicnoshow'=>'No');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);
    
$name = 'theme_mymobile/mimgs';
    $title = get_string('mimgs','theme_mymobile');
    $description = get_string('mimgs_desc', 'theme_mymobile');
    $default = 'ithumb';
    $choices = array('ithumb'=>'No', 'ithumbno'=>'Yes');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);
 
$name = 'theme_mymobile/mtab';
    $title = get_string('mtab','theme_mymobile');
    $description = get_string('mtab_desc', 'theme_mymobile');
    $default = 'tabshow';
    $choices = array('tabshow'=>'Yes', 'tabnoshow'=>'No');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);    
  
   
}
