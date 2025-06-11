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

defined('MOODLE_INTERNAL') || die;// Main settings.

$snapsettings = new admin_settingpage('themesnapfeaturespots', get_string('featurespots', 'theme_snap'));

// Feature spots settings.
// Feature spot instructions.
$name = 'theme_snap/fs_instructions';
$heading = new lang_string('featurespots', 'theme_snap');
$description = get_string('featurespotshelp', 'theme_snap');
$setting = new admin_setting_heading($name, $heading, $description);
$snapsettings->add($setting);

// Feature spots heading.
$name = 'theme_snap/fs_heading';
$title = new lang_string('featurespotsheading', 'theme_snap');
$description = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW, 50);
$snapsettings->add($setting);

$name = 'theme_snap/feature_spot_title_color';
$title = get_string('feature_spot_title_color', 'theme_snap');
$setting = new admin_setting_configcolourpicker($name, $title, '', '#ff7f41');
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);

$name = 'theme_snap/feature_spot_description_color';
$title = get_string('feature_spot_description_color', 'theme_snap');
$setting = new admin_setting_configcolourpicker($name, $title, '', '#565656');
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);

$name = 'theme_snap/feature_spot_background_color';
$title = get_string('feature_spot_background_color', 'theme_snap');
$setting = new \theme_snap\admin_setting_configcolorwithcontrast(
    \theme_snap\admin_setting_configcolorwithcontrast::FEATURESPOT_BACK, $name, $title, '', '#ffffff');
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);

// Feature spot images.
$name = 'theme_snap/fs_one_image';
$title = new lang_string('featureoneimage', 'theme_snap');
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'fs_one_image', 0, $opts);
$snapsettings->add($setting);

$name = 'theme_snap/fs_two_image';
$title = new lang_string('featuretwoimage', 'theme_snap');
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'fs_two_image', 0, $opts);
$snapsettings->add($setting);

$name = 'theme_snap/fs_three_image';
$title = new lang_string('featurethreeimage', 'theme_snap');
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'fs_three_image', 0, $opts);
$snapsettings->add($setting);

// Feature spot titles.
$name = 'theme_snap/fs_one_title';
$title = new lang_string('featureonetitle', 'theme_snap');
$description = '';
$setting = new admin_setting_configtext($name, $title, $description, '');
$snapsettings->add($setting);

$name = 'theme_snap/fs_two_title';
$title = new lang_string('featuretwotitle', 'theme_snap');
$setting = new admin_setting_configtext($name, $title, $description, '');
$snapsettings->add($setting);

$name = 'theme_snap/fs_three_title';
$title = new lang_string('featurethreetitle', 'theme_snap');
$setting = new admin_setting_configtext($name, $title, $description, '');
$snapsettings->add($setting);

// Feature spot title links.

$name = 'theme_snap/fs_one_title_link';
$title = new lang_string('featureonetitlelink', 'theme_snap');
$description = new lang_string('featuretitlelinkdesc', 'theme_snap');
$linkvalidation = '/(https|http)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?|^\/|^\s*$/';
$setting = new admin_setting_configtext($name, $title, $description, '', $linkvalidation);
$snapsettings->add($setting);

$name = 'theme_snap/fs_two_title_link';
$title = new lang_string('featuretwotitlelink', 'theme_snap');
$description = new lang_string('featuretitlelinkdesc', 'theme_snap');
$setting = new admin_setting_configtext($name, $title, $description, '', $linkvalidation);
$snapsettings->add($setting);

$name = 'theme_snap/fs_three_title_link';
$title = new lang_string('featurethreetitlelink', 'theme_snap');
$description = new lang_string('featuretitlelinkdesc', 'theme_snap');
$setting = new admin_setting_configtext($name, $title, $description, '', $linkvalidation);
$snapsettings->add($setting);

// Feature spot title checkbox new window links.

$name = 'theme_snap/fs_one_title_link_cb';
$title = new lang_string('featureonetitlecb', 'theme_snap');
$description = new lang_string('featuretitlecbdesc', 'theme_snap');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$snapsettings->add($setting);

$name = 'theme_snap/fs_two_title_link_cb';
$title = new lang_string('featuretwotitlecb', 'theme_snap');
$description = new lang_string('featuretitlecbdesc', 'theme_snap');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$snapsettings->add($setting);

$name = 'theme_snap/fs_three_title_link_cb';
$title = new lang_string('featurethreetitlecb', 'theme_snap');
$description = new lang_string('featuretitlecbdesc', 'theme_snap');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$snapsettings->add($setting);

// Feature spot text.
$name = 'theme_snap/fs_one_text';
$description = '';
$title = new lang_string('featureonetext', 'theme_snap');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$snapsettings->add($setting);

$name = 'theme_snap/fs_two_text';
$title = new lang_string('featuretwotext', 'theme_snap');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$snapsettings->add($setting);

$name = 'theme_snap/fs_three_text';
$title = new lang_string('featurethreetext', 'theme_snap');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$snapsettings->add($setting);

// Second group of 3 settings.

$name = 'theme_snap/fs_four_image';
$title = new lang_string('featurefourimage', 'theme_snap');
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'fs_four_image', 0, $opts);
$snapsettings->add($setting);

$name = 'theme_snap/fs_five_image';
$title = new lang_string('featurefiveimage', 'theme_snap');
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'fs_five_image', 0, $opts);
$snapsettings->add($setting);

$name = 'theme_snap/fs_six_image';
$title = new lang_string('featuresiximage', 'theme_snap');
$opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.svg'));
$setting = new admin_setting_configstoredfile($name, $title, $description, 'fs_six_image', 0, $opts);
$snapsettings->add($setting);

$name = 'theme_snap/fs_four_title';
$title = new lang_string('featurefourtitle', 'theme_snap');
$description = '';
$setting = new admin_setting_configtext($name, $title, $description, '');
$snapsettings->add($setting);

$name = 'theme_snap/fs_five_title';
$title = new lang_string('featurefivetitle', 'theme_snap');
$setting = new admin_setting_configtext($name, $title, $description, '');
$snapsettings->add($setting);

$name = 'theme_snap/fs_six_title';
$title = new lang_string('featuresixtitle', 'theme_snap');
$setting = new admin_setting_configtext($name, $title, $description, '');
$snapsettings->add($setting);

$name = 'theme_snap/fs_four_title_link';
$title = new lang_string('featurefourtitlelink', 'theme_snap');
$description = new lang_string('featuretitlelinkdesc', 'theme_snap');
$linkvalidation = '/(https|http)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?|^\/|^\s*$/';
$setting = new admin_setting_configtext($name, $title, $description, '', $linkvalidation);
$snapsettings->add($setting);

$name = 'theme_snap/fs_five_title_link';
$title = new lang_string('featurefivetitlelink', 'theme_snap');
$description = new lang_string('featuretitlelinkdesc', 'theme_snap');
$setting = new admin_setting_configtext($name, $title, $description, '', $linkvalidation);
$snapsettings->add($setting);

$name = 'theme_snap/fs_six_title_link';
$title = new lang_string('featuresixtitlelink', 'theme_snap');
$description = new lang_string('featuretitlelinkdesc', 'theme_snap');
$setting = new admin_setting_configtext($name, $title, $description, '', $linkvalidation);
$snapsettings->add($setting);

$name = 'theme_snap/fs_four_title_link_cb';
$title = new lang_string('featurefourtitlecb', 'theme_snap');
$description = new lang_string('featuretitlecbdesc', 'theme_snap');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$snapsettings->add($setting);

$name = 'theme_snap/fs_five_title_link_cb';
$title = new lang_string('featurefivetitlecb', 'theme_snap');
$description = new lang_string('featuretitlecbdesc', 'theme_snap');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$snapsettings->add($setting);

$name = 'theme_snap/fs_six_title_link_cb';
$title = new lang_string('featuresixtitlecb', 'theme_snap');
$description = new lang_string('featuretitlecbdesc', 'theme_snap');
$setting = new admin_setting_configcheckbox($name, $title, $description, 0);
$snapsettings->add($setting);

$name = 'theme_snap/fs_four_text';
$description = '';
$title = new lang_string('featurefourtext', 'theme_snap');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$snapsettings->add($setting);

$name = 'theme_snap/fs_five_text';
$title = new lang_string('featurefivetext', 'theme_snap');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$snapsettings->add($setting);

$name = 'theme_snap/fs_six_text';
$title = new lang_string('featuresixtext', 'theme_snap');
$setting = new admin_setting_configtextarea($name, $title, $description, '');
$snapsettings->add($setting);

$settings->add($snapsettings);
