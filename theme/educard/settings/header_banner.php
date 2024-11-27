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
 * Educard frontpage settings.
 *
 * @package   theme_educard
 * @copyright 2023 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$page = new admin_settingpage('theme_educard_banner', get_string('bannereducard', 'theme_educard'));

$page->add(new admin_setting_heading('theme_educard_bannerhead', get_string('bannerheading', 'theme_educard'),
format_text(get_string('bannerheadingdesc', 'theme_educard'), FORMAT_MARKDOWN)));

// Enable or disable banners settings.
$name = 'theme_educard/bannerheadingenabled';
$title = get_string('bannerheadingenabled', 'theme_educard');
$description = get_string('bannerheadingenableddesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Frontpage banners select display.
$name = 'theme_educard/bannerheadingchoice';
$title = get_string('bannerheadingchoice', 'theme_educard');
$description = get_string('bannerheadingchoicedesc', 'theme_educard');
$default = 3;
$options = [];
for ($i = 1; $i <= 6; $i++) {
    $options[$i] = $i;
}
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// FRONTPAGE BANNERS 1.
$page->add(new admin_setting_heading('theme_educard_bannerhead1', get_string('bannerheading1', 'theme_educard'),
format_text(get_string('bannerheading1desc', 'theme_educard'), FORMAT_MARKDOWN)));
// Front page banners img.
$name = 'theme_educard/imgbanner1';
$title = get_string('imgbanner1', 'theme_educard');
$description = get_string('imgbanner1desc', 'theme_educard');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'imgbanner1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Visible or hide shape image.
$name = 'theme_educard/banner1shape';
$title = get_string('banner1shape', 'theme_educard');
$description = get_string('banner1shapedesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 1.
$name = 'theme_educard/banner1caption1';
$title = get_string('banner1caption1', 'theme_educard');
$description = get_string('banner1caption1desc', 'theme_educard');
$default = 'ONLINE LEARNING';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1' );
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 2.
$name = 'theme_educard/banner1caption2';
$title = get_string('banner1caption2', 'theme_educard');
$description = get_string('banner1caption2desc', 'theme_educard');
$default = 'Learn The Skills You ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 3.
$name = 'theme_educard/banner1caption3';
$title = get_string('banner1caption3', 'theme_educard');
$description = get_string('banner1caption3desc', 'theme_educard');
$default = 'Need To Succeed ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 4.
$name = 'theme_educard/banner1caption4';
$title = get_string('banner1caption4', 'theme_educard');
$description = get_string('banner1caption4desc', 'theme_educard');
$default = 'Free online courses from the world’s Leading experts. Start today and get certified.';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Search placeholder.
$name = 'theme_educard/banner1placeholder';
$title = get_string('banner1placeholder', 'theme_educard');
$description = get_string('banner1placeholderdesc', 'theme_educard');
$default = 'Keywords of Your Course...';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Category text.
$name = 'theme_educard/banner1ctgtext';
$title = get_string('banner1ctgtext', 'theme_educard');
$description = get_string('banner1ctgtextdesc', 'theme_educard');
$default = 'Popular : ';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Category id.
$name = 'theme_educard/banner1ctgid';
$title = get_string('banner1ctgid', 'theme_educard');
$description = get_string('banner1ctgiddesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// FRONTPAGE BANNERS 2.
$page->add(new admin_setting_heading('theme_educard_bannerhead2', get_string('bannerheading2', 'theme_educard'),
format_text(get_string('bannerheading2desc', 'theme_educard'), FORMAT_MARKDOWN)));
// Front page banners img.
$name = 'theme_educard/imgbanner2';
$title = get_string('imgbanner2', 'theme_educard');
$description = get_string('imgbanner2desc', 'theme_educard');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'imgbanner2');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Visible or hide shape image.
$name = 'theme_educard/banner2shape';
$title = get_string('banner2shape', 'theme_educard');
$description = get_string('banner2shapedesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 1.
$name = 'theme_educard/banner2caption1';
$title = get_string('banner2caption1', 'theme_educard');
$description = get_string('banner2caption1desc', 'theme_educard');
$default = 'ONLINE LEARNING';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1' );
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 2.
$name = 'theme_educard/banner2caption2';
$title = get_string('banner2caption2', 'theme_educard');
$description = get_string('banner2caption2desc', 'theme_educard');
$default = 'Learn The Skills You ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 3.
$name = 'theme_educard/banner2caption3';
$title = get_string('banner2caption3', 'theme_educard');
$description = get_string('banner2caption3desc', 'theme_educard');
$default = 'Need To Succeed ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 4.
$name = 'theme_educard/banner2caption4';
$title = get_string('banner2caption4', 'theme_educard');
$description = get_string('banner2caption4desc', 'theme_educard');
$default = 'Free online courses from the world’s Leading experts. Start today and get certified.';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Search placeholder.
$name = 'theme_educard/banner2placeholder';
$title = get_string('banner2placeholder', 'theme_educard');
$description = get_string('banner2placeholderdesc', 'theme_educard');
$default = 'Keywords of Your Course...';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Category text.
$name = 'theme_educard/banner2ctgtext';
$title = get_string('banner2ctgtext', 'theme_educard');
$description = get_string('banner2ctgtextdesc', 'theme_educard');
$default = 'Popular : ';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Category id's.
$name = 'theme_educard/banner2ctgid';
$title = get_string('banner2ctgid', 'theme_educard');
$description = get_string('banner2ctgiddesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Course style options.
$name = 'theme_educard/banner2coursestyl';
$title = get_string('banner2coursestyl', 'theme_educard');
$description = get_string('banner2coursestyldesc', 'theme_educard');
$default = "1";
$options = [
    '1' => 'Block-7 COURSES-1',
    '2' => 'Block-7 COURSES-2',
    '3' => 'Block-7 COURSES-3',
    '4' => 'Block-7 COURSES-4',
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $options);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Course id.
$name = 'theme_educard/banner2courseid';
$title = get_string('banner2courseid', 'theme_educard');
$description = get_string('banner2courseiddesc', 'theme_educard');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// FRONTPAGE BANNERS 3.
$page->add(new admin_setting_heading('theme_educard_bannerhead3', get_string('bannerheading3', 'theme_educard'),
format_text(get_string('bannerheading3desc', 'theme_educard'), FORMAT_MARKDOWN)));
// Front page banners img.
$name = 'theme_educard/imgbanner3';
$title = get_string('imgbanner3', 'theme_educard');
$description = get_string('imgbanner3desc', 'theme_educard');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'imgbanner3');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Visible or hide shape image.
$name = 'theme_educard/banner3shape';
$title = get_string('banner3shape', 'theme_educard');
$description = get_string('banner3shapedesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 1.
$name = 'theme_educard/banner3caption1';
$title = get_string('banner3caption1', 'theme_educard');
$description = get_string('banner3caption1desc', 'theme_educard');
$default = 'Learn The Skills You ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1' );
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 2.
$name = 'theme_educard/banner3caption2';
$title = get_string('banner3caption2', 'theme_educard');
$description = get_string('banner3caption2desc', 'theme_educard');
$default = 'Need To Succeed ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 3.
$name = 'theme_educard/banner3caption3';
$title = get_string('banner3caption3', 'theme_educard');
$description = get_string('banner3caption3desc', 'theme_educard');
$default = 'Free online courses from the world’s Leading experts. Start today and get certified. ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners button text.
$name = 'theme_educard/banner3btn';
$title = get_string('banner3btn', 'theme_educard');
$description = get_string('banner3btndesc', 'theme_educard');
$default = 'Discover More Courses';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Banner 3 button link.
$name = 'theme_educard/banner3btnlnk';
$title = get_string('banner3btnlnk', 'theme_educard');
$description = get_string('banner3btnlnkdesc', 'theme_educard');
$default = '/course/';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Banners sub header text.
$name = 'theme_educard/banner3subhd1';
$title = get_string('banner3subhd1', 'theme_educard');
$description = get_string('banner3subhd1desc', 'theme_educard');
$default = '200+ Courses & Lifetime Access & 800k+ Enrolled';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Banners sub header text.
$name = 'theme_educard/banner3subhd2';
$title = get_string('banner3subhd2', 'theme_educard');
$description = get_string('banner3subhd2desc', 'theme_educard');
$default = '1- write a summary here & 2- write a summary here & 3- write a summary here';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '2');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Banners sub header icons.
$name = 'theme_educard/banner3icon';
$title = get_string('banner3icon', 'theme_educard');
$description = get_string('banner3icondesc', 'theme_educard');
$default = 'fa fa-book & fa fa-suitcase & fa fa-user';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// FRONTPAGE BANNERS 4.
$page->add(new admin_setting_heading('theme_educard_bannerhead4', get_string('bannerheading4', 'theme_educard'),
format_text(get_string('bannerheading4desc', 'theme_educard'), FORMAT_MARKDOWN)));
// Front page banners img.
$name = 'theme_educard/imgbanner4';
$title = get_string('imgbanner4', 'theme_educard');
$description = get_string('imgbanner4desc', 'theme_educard');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'imgbanner4');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Visible or hide shape image.
$name = 'theme_educard/banner4shape';
$title = get_string('banner4shape', 'theme_educard');
$description = get_string('banner4shapedesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 1.
$name = 'theme_educard/banner4caption1';
$title = get_string('banner4caption1', 'theme_educard');
$description = get_string('banner4caption1desc', 'theme_educard');
$default = 'Learn The Skills You ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1' );
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 2.
$name = 'theme_educard/banner4caption2';
$title = get_string('banner4caption2', 'theme_educard');
$description = get_string('banner4caption2desc', 'theme_educard');
$default = 'Need To Succeed ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 3.
$name = 'theme_educard/banner4caption3';
$title = get_string('banner4caption3', 'theme_educard');
$description = get_string('banner4caption3desc', 'theme_educard');
$default = 'Free online courses from the world’s Leading experts. Start today and get certified. ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners button text.
$name = 'theme_educard/banner4btn';
$title = get_string('banner4btn', 'theme_educard');
$description = get_string('banner4btndesc', 'theme_educard');
$default = 'Discover More Courses';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Banners button link.
$name = 'theme_educard/banner4btnlnk';
$title = get_string('banner4btnlnk', 'theme_educard');
$description = get_string('banner4btnlnkdesc', 'theme_educard');
$default = '/course/';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// FRONTPAGE BANNERS 5.
$page->add(new admin_setting_heading('theme_educard_bannerhead5', get_string('bannerheading5', 'theme_educard'),
format_text(get_string('bannerheading5desc', 'theme_educard'), FORMAT_MARKDOWN)));
// Front page banners img.
$name = 'theme_educard/imgbanner5';
$title = get_string('imgbanner5', 'theme_educard');
$description = get_string('imgbanner5desc', 'theme_educard');
$setting = new admin_setting_configstoredfile($name, $title, $description, 'imgbanner5');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Visible or hide shape image.
$name = 'theme_educard/banner5shape';
$title = get_string('banner5shape', 'theme_educard');
$description = get_string('banner5shapedesc', 'theme_educard');
$setting = new admin_setting_configcheckbox($name, $title, $description, 1);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 1.
$name = 'theme_educard/banner5caption1';
$title = get_string('banner5caption1', 'theme_educard');
$description = get_string('banner5caption1desc', 'theme_educard');
$default = 'Welcome To ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1' );
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 2.
$name = 'theme_educard/banner5caption2';
$title = get_string('banner5caption2', 'theme_educard');
$description = get_string('banner5caption2desc', 'theme_educard');
$default = 'Educard ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 3.
$name = 'theme_educard/banner5caption3';
$title = get_string('banner5caption3', 'theme_educard');
$description = get_string('banner5caption3desc', 'theme_educard');
$default = 'A new different way to improve your skill.
Free online courses from the world’s Leading experts. Start today and free trial.';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners button text.
$name = 'theme_educard/banner5btn';
$title = get_string('banner5btn', 'theme_educard');
$description = get_string('banner5btndesc', 'theme_educard');
$default = 'Start A Free Tria';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Banners button link.
$name = 'theme_educard/banner5btnlnk';
$title = get_string('banner5btnlnk', 'theme_educard');
$description = get_string('banner5btnlnkdesc', 'theme_educard');
$default = '/course/';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners button 1 text.
$name = 'theme_educard/banner5btn1';
$title = get_string('banner5btn1', 'theme_educard');
$description = get_string('banner5btn1desc', 'theme_educard');
$default = 'View Courses';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Banners button 1 link.
$name = 'theme_educard/banner5btn1lnk';
$title = get_string('banner5btn1lnk', 'theme_educard');
$description = get_string('banner5btn1lnkdesc', 'theme_educard');
$default = '/course/';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// FRONTPAGE BANNERS 6.
$page->add(new admin_setting_heading('theme_educard_bannerhead6', get_string('bannerheading6', 'theme_educard'),
format_text(get_string('bannerheading6desc', 'theme_educard'), FORMAT_MARKDOWN)));
// Front page banners caption 1.
$name = 'theme_educard/banner6caption1';
$title = get_string('banner6caption1', 'theme_educard');
$description = get_string('banner6caption1desc', 'theme_educard');
$default = 'Educard Video Icon Box Header ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1' );
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners caption 2.
$name = 'theme_educard/banner6caption2';
$title = get_string('banner6caption2', 'theme_educard');
$description = get_string('banner6caption2desc', 'theme_educard');
$default = 'Educard theme video block usage ';
$setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '1', '1');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners button text.
$name = 'theme_educard/banner6btn';
$title = get_string('banner6btn', 'theme_educard');
$description = get_string('banner6btndesc', 'theme_educard');
$default = 'Start A Free Tria';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Banners button link.
$name = 'theme_educard/banner6btnlnk';
$title = get_string('banner6btnlnk', 'theme_educard');
$description = get_string('banner6btnlnkdesc', 'theme_educard');
$default = '/course/';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Banners video link.
$name = 'theme_educard/banner6vdolnk';
$title = get_string('banner6vdolnk', 'theme_educard');
$description = get_string('banner6vdolnkdesc', 'theme_educard');
$default = '/image/video/course-video.mp4';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);
// Front page banners margin bottom.
$name = 'theme_educard/banner6mb';
$title = get_string('banner6mb', 'theme_educard');
$description = get_string('banner6mbdesc', 'theme_educard');
$default = '-120px';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$page->add(new admin_setting_heading('theme_educard_bannerend', get_string('bannerend', 'theme_educard'),
format_text(get_string('bannerdesc', 'theme_educard'), FORMAT_MARKDOWN)));
$settings->add($page);
