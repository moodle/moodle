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
 * Educard block 20 settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// Block 20 info.
$name = 'theme_educard/block20info';
$heading = get_string('block20info', 'theme_educard');
$information = get_string('block20infodesc', 'theme_educard');
$setting = new admin_setting_heading($name, $heading, $information);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable block 20 settings.
$name = 'theme_educard/block20enabled';
$title = get_string('block20enabled', 'theme_educard');
$description = get_string('block20enableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 design select.
$name = 'theme_educard/block20design';
$title = get_string('block20design', 'theme_educard');
$description = get_string('block20designdesc', 'theme_educard');
$default = 1;
$options = [];
for ($i = 1; $i <= 2; $i++) {
     $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 background color palet.
$name = 'theme_educard/footerbackcolor';
$title = get_string('footerbackcolor', 'theme_educard');
$description = get_string('footerbackcolordesc', 'theme_educard');
$setting = new admin_setting_configcolourpicker($name, $title, $description, '');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 logo image.
$name = 'theme_educard/block20logo';
$title = get_string('block20logo', 'theme_educard');
$description = get_string('block20logodesc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$opts = ['accepted_types' => ['.png', '.jpg', '.jpeg', '.gif', '.webp', '.tiff', '.svg'], 'maxfiles' => 1];
$setting = new admin_setting_configstoredfile($name, $title, $description, 'block20logo',  0, $opts);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 col 1 header.
$name = 'theme_educard/block20col1header';
$title = get_string('block20col1header', 'theme_educard');
$description = get_string('block20col1headerdesc', 'theme_educard');
$default = "Site Name";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 col 1 caption.
$name = 'theme_educard/block20col1caption';
$title = get_string('block20col1caption', 'theme_educard');
$description = get_string('block20col1captiondesc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$default = get_string('block20col1captiondefault', 'theme_educard');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '3');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 col 2 header.
$name = 'theme_educard/block20col2header';
$title = get_string('block20col2header', 'theme_educard');
$description = get_string('block20col2headerdesc', 'theme_educard');
$default = "Company";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 col 2 link area.
$name = 'theme_educard/block20col2link';
$title = get_string('block20col2link', 'theme_educard');
$description = get_string('block20col2linkdesc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$default = get_string('block20col2linkdefault', 'theme_educard');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '6');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 col 3 header.
$name = 'theme_educard/block20col3header';
$title = get_string('block20col3header', 'theme_educard');
$description = get_string('block20col3headerdesc', 'theme_educard');
$default = "Help";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 col 3 link area.
$name = 'theme_educard/block20col3link';
$title = get_string('block20col3link', 'theme_educard');
$description = get_string('block20col3linkdesc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$default = get_string('block20col3linkdefault', 'theme_educard');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '6');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 col 4 header.
$name = 'theme_educard/block20col4header';
$title = get_string('block20col4header', 'theme_educard');
$description = get_string('block20col4headerdesc', 'theme_educard');
$default = "Company";
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 col 3 caption.
$name = 'theme_educard/block20col4caption';
$title = get_string('block20col4caption', 'theme_educard');
$description = get_string('block20col4captiondesc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$default = get_string('block20col4captiondefault', 'theme_educard');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '6');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 social links.
$name = 'theme_educard/block20social';
$title = get_string('block20social', 'theme_educard');
$description = get_string('block20socialdesc', 'theme_educard');
$description = $description.get_string('underline', 'theme_educard');
$default = get_string('block20socialdefault', 'theme_educard');
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '6');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Block 20 Copyright.
$name = 'theme_educard/block20copyright';
$title = get_string('block20copyright', 'theme_educard');
$description = get_string('block20copyrightdesc', 'theme_educard');
$default = 'Copyright © ' .date('Y'). ' Designed by <a href="https://wwwthemesalmond.com">themesalmond.com</a>.
 All rights reserved.';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Enable or disable moodle frontpage orjinal button.
$name = 'theme_educard/block20moodle';
$title = get_string('block20moodle', 'theme_educard');
$description = get_string('block20moodledesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
