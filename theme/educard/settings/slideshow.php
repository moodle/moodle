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
 * Educard main slide settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$page = new admin_settingpage('theme_educard_slide', get_string('slideshow', 'theme_educard'));
$page->add(new admin_setting_heading('theme_educard_slideshow', get_string('slideshowheading', 'theme_educard'),
format_text(get_string('slideshowheadingdesc', 'theme_educard'), FORMAT_MARKDOWN)));
// Enable or disable Slideshow settings.
$name = 'theme_educard/sliderenabled';
$title = get_string('sliderenabled', 'theme_educard');
$description = get_string('sliderenableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Slideshow design select.
$name = 'theme_educard/sliderdesing';
$title = get_string('sliderdesing', 'theme_educard');
$description = get_string('sliderdesingdesc', 'theme_educard');
$default = 4;
$options = [];
for ($i = 1; $i <= 4; $i++) {
    $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Count Slideshow settings.
$name = 'theme_educard/slidercount';
$title = get_string('slidercount', 'theme_educard');
$description = get_string('slidercountdesc', 'theme_educard');
$default = 4;
$options = [];
for ($i = 1; $i <= 10; $i++) {
    $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// If we don't have an slide yet, default to the preset.
$slidercount = get_config('theme_educard', 'slidercount');

if (!$slidercount) {
    $slidercount = 1;
}
// Header size setting.
$name = 'theme_educard/slidershowheight';
$title = get_string('slidershowheight', 'theme_educard');
$description = get_string('slidershowheight_desc', 'theme_educard');
$default = '550';
$options = [
    '350' => '350',
    '375' => '375',
    '400' => '400',
    '425' => '425',
    '450' => '450',
    '475' => '475',
    '500' => '500',
    '525' => '525',
    '550' => '550',
    '575' => '575',
    '600' => '600',
    '650' => '650',
    '0' => 'Full Page',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Img kenburns setting.
$name = 'theme_educard/sliderimgkenburns';
$title = get_string('sliderimgkenburns', 'theme_educard');
$description = get_string('sliderimgkenburns_desc', 'theme_educard');
$default = 'None';
$options = [
    'none' => 'None',
    'kenburns-top' => 'kenburns-top',
    'kenburns-top-left' => 'kenburns-top-left',
    'kenburns-top-right' => 'kenburns-top-right',
    'kenburns-bottom' => 'kenburns-bottom',
    'kenburns-bottom-left' => 'kenburns-bottom-left',
    'kenburns-bottom-right' => 'kenburns-bottom-right',
    'kenburns-left' => 'kenburns-left',
    'kenburns-right' => 'kenburns-right',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Text position setting.
$name = 'theme_educard/textposition';
$title = get_string('textposition', 'theme_educard');
$description = get_string('textposition_desc', 'theme_educard');
$default = 'Center';
$options = [
    'text-center' => 'Center',
    'text-left' => 'Left',
    'text-right' => 'Right',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Slider text animation.
$name = 'theme_educard/textanimation';
$title = get_string('textanimation', 'theme_educard');
$description = get_string('textanimation_desc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Slider autoplay.
$name = 'theme_educard/autoplay';
$title = get_string('autoplay', 'theme_educard');
$description = get_string('autoplay_desc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Slider next-prev button hide.
$name = 'theme_educard/nextprev';
$title = get_string('nextprev', 'theme_educard');
$description = get_string('nextprev_desc', 'theme_educard');
$default = 'None';
$options = [
    'arrow-none' => 'None',
    'arrow-top' => 'Top',
    'arrow-center' => 'Center',
    'arrow-bottom' => 'Bottom',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Slider pagination.
$name = 'theme_educard/pagination';
$title = get_string('pagination', 'theme_educard');
$description = get_string('pagination_desc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Frontpage color opacity to slider.
$name = 'theme_educard/slideropacity';
$title = get_string('slideropacity', 'theme_educard');
$description = get_string('slideropacitydesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Slider shapes enable.
$name = 'theme_educard/slidershapes';
$title = get_string('slidershapes', 'theme_educard');
$description = get_string('slidershapesdesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
for ($count = 1; $count <= $slidercount; $count++) {
    $name = 'theme_educard/slide' . $count . 'info';
    $heading = get_string('slideno', 'theme_educard', ['slide' => $count]);
    $information = get_string('slidenodesc', 'theme_educard', ['slide' => $count]);
    $setting = new admin_setting_heading($name, $heading, $information);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Slider image.
    $fileid = 'sliderimage'.$count;
    $name = 'theme_educard/sliderimage'.$count;
    $title = get_string('sliderimage', 'theme_educard');
    $description = get_string('sliderimagedesc', 'theme_educard');
    $opts = ['accepted_types' => ['.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'], 'maxfiles' => 1];
    $setting = new admin_setting_configstoredfile($name, $title, $description, $fileid,  0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Slide image enable/disable.
    $name = 'theme_educard/sliderimageenable'. $count;
    $title = get_string('sliderimageenable', 'theme_educard');
    $description = get_string('sliderimageenabledesc', 'theme_educard');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Slider title.
    $name = 'theme_educard/slidertitle' . $count;
    $title = get_string('slidertitle', 'theme_educard');
    $description = get_string('slidertitledesc', 'theme_educard');
    $default = $count.' - Write slider title here';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Slider caption.
    $name = 'theme_educard/slidercap' . $count;
    $title = get_string('slidercaption', 'theme_educard');
    $description = get_string('slidercaptiondesc', 'theme_educard');
    $default = $count.' - Write description for slider here';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Slider button.
    $name = 'theme_educard/sliderbutton' . $count;
    $title = get_string('sliderbutton', 'theme_educard');
    $description = get_string('sliderbuttondesc', 'theme_educard');
    $default = $count.' - Link button';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Slide button link.
    $name = 'theme_educard/sliderurl'. $count;
    $title = get_string('sliderbuttonurl', 'theme_educard');
    $description = get_string('sliderbuttonurldesc', 'theme_educard');
    $default = 'http://www.themesalmond.com/';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
    // Slide button link new window.
    $name = 'theme_educard/sliderurlblank'. $count;
    $title = get_string('sliderbuttonurlblank', 'theme_educard');
    $description = get_string('sliderbuttonurlblankdesc', 'theme_educard');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);
}
$page->add(new admin_setting_heading('theme_educard_slideend', get_string('slideshowend', 'theme_educard'),
format_text(get_string('slideshowenddesc', 'theme_educard'), FORMAT_MARKDOWN)));
$settings->add($page);
