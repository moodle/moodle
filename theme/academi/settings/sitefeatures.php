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
 * Admin settings configuration for site features section.
 *
 * @package    theme_academi
 * @copyright  2023 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

// Site features.
$temp = new admin_settingpage('theme_academi_sitefeatures', get_string('sitefeatures', 'theme_academi'));

// Enable or disable option for the site features block.
$name = 'theme_academi/sitefblockstatus';
$title = get_string('status', 'theme_academi');
$description = get_string('statusdesc', 'theme_academi');
$default = NO;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$temp->add($setting);

// Site feature block Title.
$name = 'theme_academi/sitefeaturetitle';
$title = get_string('title', 'theme_academi');
$description = get_string('titledesc', 'theme_academi');
$default = 'lang:sitefeaturesdefault';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$temp->add($setting);

// Site feature block description.
$name = 'theme_academi/sitefeaturedesc';
$title = get_string('description', 'theme_academi');
$description = get_string('description_desc', 'theme_academi');
$default = 'lang:description_default';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$temp->add($setting);

// Select the number of site features show in the front page.
$name = 'theme_academi/numberofsitefeature';
$title = get_string('numberofsitef', 'theme_academi');
$description = get_string('numberofsitef_desc', 'theme_academi');
$default = 4;
$choices = array_combine( range(1, 4), range(1, 4) );
$temp->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

$sitefeatures = get_config('theme_academi', 'numberofsitefeature');
for ($i = 1; $i <= $sitefeatures; $i++) {

    // Site feature heading.
    $name = 'theme_academi_sitefblock'.$i.'heading';
    $heading = get_string('sitefblock', 'theme_academi', ['block' => $i]);
    $information = '';
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    // Site feature enable/disable option.
    $name = 'theme_academi/sitefblock'.$i.'status';
    $title = get_string('status', 'theme_academi');
    $description = get_string('statusdesc', 'theme_academi');
    $default = YES;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $temp->add($setting);

    // Site feature title.
    $name = 'theme_academi/sitefblock'.$i.'title';
    $title = get_string('title', 'theme_academi');
    $description = get_string('titledesc', 'theme_academi');
    $default = 'lang:sb'.$i.'_default_title';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Site feature content.
    $name = 'theme_academi/sitefblock'.$i.'content';
    $title = get_string('content', 'theme_academi');
    $description = get_string('content_desc', 'theme_academi');
    $default = 'lang:sb_default_content';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $temp->add($setting);

    // Site feature icon.
    $name = 'theme_academi/sitefblock'.$i.'icon';
    $title = get_string('icon', 'theme_academi');
    $description = get_string('icondesc', 'theme_academi');
    $default = 'lang:sitefblockicon'.$i.'_default';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Site feature url.
    $name = 'theme_academi/sitefblock'.$i.'url';
    $title = get_string('url', 'theme_academi');
    $description = get_string('urldesc', 'theme_academi', ['block' => $i]);
    $default = 'http://www.example.com/';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);
}
$settings->add($temp);
