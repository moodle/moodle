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
 * Educard page contact settings.
 *
 * @package   theme_educard
 * @copyright 2023 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Contact page info.
$name = 'theme_educard/page01info';
$heading = get_string('page01info', 'theme_educard');
$information = get_string('page01infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable page 01 settings.
$name = 'theme_educard/page01enabled';
$title = get_string('page01enabled', 'theme_educard');
$description = get_string('page01enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page 1 design select.
$name = 'theme_educard/page01desing';
$title = get_string('page01desing', 'theme_educard');
$description = get_string('page01desingdesc', 'theme_educard');
$default = 1;
$options = [];
for ($i = 1; $i <= 2; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Page 1 banner img.
$name = 'theme_educard/imgpage01';
$title = get_string('imgpage01', 'theme_educard');
$description = get_string('imgpage01desc', 'theme_educard');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'imgpage01');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page 1 background img.
$name = 'theme_educard/imgpage02';
$title = get_string('imgpage02', 'theme_educard');
$description = get_string('imgpage02desc', 'theme_educard');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'imgpage02');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page 1 header.
$name = 'theme_educard/page01header';
$title = get_string('page01header', 'theme_educard');
$description = get_string('page01headerdesc', 'theme_educard');
$default = get_string('page01headerdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page 01 caption.
$name = 'theme_educard/page01caption';
$title = get_string('page01caption', 'theme_educard');
$description = get_string('page01captiondesc', 'theme_educard');
$default = get_string('page01captiondefault', 'theme_educard');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page 01 address.
$name = 'theme_educard/page01address';
$title = get_string('page01address', 'theme_educard');
$description = get_string('page01addressdesc', 'theme_educard');
$default = "Address";
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page 01 geolocation.
$name = 'theme_educard/page01geolocation';
$title = get_string('page01geolocation', 'theme_educard');
$description = get_string('page01geolocationdesc', 'theme_educard');
$default = get_string('page01geolocationdefault', 'theme_educard');
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page 1 phone.
$name = 'theme_educard/page01phone';
$title = get_string('page01phone', 'theme_educard');
$description = get_string('page01phonedesc', 'theme_educard');
$default = "+1 555 77 963";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page 1 email.
$name = 'theme_educard/page01email';
$title = get_string('page01email', 'theme_educard');
$description = get_string('page01emaildesc', 'theme_educard');
$default = "contact@yoursite.com";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Page 1 opening hours.
$name = 'theme_educard/page01opening';
$title = get_string('page01opening', 'theme_educard');
$description = get_string('page01openingdesc', 'theme_educard');
$default = "Mon - Fri | 9am - 5pm | Sat - Sun |9am - 2pm";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
