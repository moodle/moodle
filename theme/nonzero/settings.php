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
 * This file contains the settings for the Nonzero theme.
 *
 * Currently you can set the following settings:
 *    - Region pre width
 *    - Region post width
 *    - Some custom CSS
 *
 * @package  theme_nonzero
 * @copyright 2010 Dietmar Wagner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    // Block region-pre width
    $name = 'theme_nonzero/regionprewidth';
    $title = get_string('regionprewidth','theme_nonzero');
    $description = get_string('regionprewidthdesc', 'theme_nonzero');
    $default = 200;
    $choices = array(180=>'180px', 190=>'190px', 200=>'200px', 210=>'210px', 220=>'220px', 230=>'230px', 240=>'240px', 250=>'250px', 260=>'260px');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Block region-post width
    $name = 'theme_nonzero/regionpostwidth';
    $title = get_string('regionpostwidth','theme_nonzero');
    $description = get_string('regionpostwidthdesc', 'theme_nonzero');
    $default = 200;
    $choices = array(180=>'180px', 190=>'190px', 200=>'200px', 210=>'210px', 220=>'220px', 230=>'230px', 240=>'240px', 250=>'250px', 260=>'260px');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Custom CSS file
    $name = 'theme_nonzero/customcss';
    $title = get_string('customcss','theme_nonzero');
    $description = get_string('customcssdesc', 'theme_nonzero');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}