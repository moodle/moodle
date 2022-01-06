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
* Social networking settings page file.
*
* @package    theme_schoollege
* @copyright  2020 Chris Kenniburg
* 
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

// Icon Navigation);
$page = new admin_settingpage('theme_schoollege_iconnavheading', get_string('iconnavheading', 'theme_schoollege'));

// This is the descriptor for the page.
$name = 'theme_schoollege/iconnavinfo';
$heading = get_string('iconnavinfo', 'theme_schoollege');
$information = get_string('iconnavinfo_desc', 'theme_schoollege');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// This is the descriptor for icon One
$name = 'theme_schoollege/iconwidthinfo';
$heading = get_string('iconwidthinfo', 'theme_schoollege');
$information = get_string('iconwidthinfodesc', 'theme_schoollege');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Icon width setting.
$name = 'theme_schoollege/iconwidth';
$title = get_string('iconwidth', 'theme_schoollege');
$description = get_string('iconwidth_desc', 'theme_schoollege');;
$default = '100px';
$choices = array(
    '75px' => '75px',
    '85px' => '85px',
    '95px' => '95px',
    '100px' => '100px',
    '105px' => '105px',
    '110px' => '110px',
    '115px' => '115px',
    '120px' => '120px',
    '125px' => '125px',
    '130px' => '130px',
    '135px' => '135px',
    '140px' => '140px',
    '145px' => '145px',
    '150px' => '150px',
);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);


// This is the descriptor for teacher create a course
$name = 'theme_schoollege/sliderinfo';
$heading = get_string('sliderinfo', 'theme_schoollege');
$information = get_string('sliderinfodesc', 'theme_schoollege');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Creator Icon
$name = 'theme_schoollege/slideicon';
$title = get_string('navicon', 'theme_schoollege');
$description = get_string('naviconslidedesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/slideiconbuttontext';
$title = get_string('naviconbuttontext', 'theme_schoollege');
$description = get_string('naviconbuttontextdesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Slide Textbox.
$name = 'theme_schoollege/slidetextbox';
$title = get_string('slidetextbox', 'theme_schoollege');
$description = get_string('slidetextbox_desc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon One
$name = 'theme_schoollege/navicon1info';
$heading = get_string('navicon1', 'theme_schoollege');
$information = get_string('navicondesc', 'theme_schoollege');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// icon One
$name = 'theme_schoollege/nav1icon';
$title = get_string('navicon', 'theme_schoollege');
$description = get_string('navicondesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav1buttontext';
$title = get_string('naviconbuttontext', 'theme_schoollege');
$description = get_string('naviconbuttontextdesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav1buttonurl';
$title = get_string('naviconbuttonurl', 'theme_schoollege');
$description = get_string('naviconbuttonurldesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav1target';
$title = get_string('marketingurltarget' , 'theme_schoollege');
$description = get_string('marketingurltargetdesc', 'theme_schoollege');
$target1 = get_string('marketingurltargetself', 'theme_schoollege');
$target2 = get_string('marketingurltargetnew', 'theme_schoollege');
$target3 = get_string('marketingurltargetparent', 'theme_schoollege');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon One
$name = 'theme_schoollege/navicon2info';
$heading = get_string('navicon2', 'theme_schoollege');
$information = get_string('navicondesc', 'theme_schoollege');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_schoollege/nav2icon';
$title = get_string('navicon', 'theme_schoollege');
$description = get_string('navicondesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav2buttontext';
$title = get_string('naviconbuttontext', 'theme_schoollege');
$description = get_string('naviconbuttontextdesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav2buttonurl';
$title = get_string('naviconbuttonurl', 'theme_schoollege');
$description = get_string('naviconbuttonurldesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav2target';
$title = get_string('marketingurltarget' , 'theme_schoollege');
$description = get_string('marketingurltargetdesc', 'theme_schoollege');
$target1 = get_string('marketingurltargetself', 'theme_schoollege');
$target2 = get_string('marketingurltargetnew', 'theme_schoollege');
$target3 = get_string('marketingurltargetparent', 'theme_schoollege');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon three
$name = 'theme_schoollege/navicon3info';
$heading = get_string('navicon3', 'theme_schoollege');
$information = get_string('navicondesc', 'theme_schoollege');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_schoollege/nav3icon';
$title = get_string('navicon', 'theme_schoollege');
$description = get_string('navicondesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav3buttontext';
$title = get_string('naviconbuttontext', 'theme_schoollege');
$description = get_string('naviconbuttontextdesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav3buttonurl';
$title = get_string('naviconbuttonurl', 'theme_schoollege');
$description = get_string('naviconbuttonurldesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav3target';
$title = get_string('marketingurltarget' , 'theme_schoollege');
$description = get_string('marketingurltargetdesc', 'theme_schoollege');
$target1 = get_string('marketingurltargetself', 'theme_schoollege');
$target2 = get_string('marketingurltargetnew', 'theme_schoollege');
$target3 = get_string('marketingurltargetparent', 'theme_schoollege');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon four
$name = 'theme_schoollege/navicon4info';
$heading = get_string('navicon4', 'theme_schoollege');
$information = get_string('navicondesc', 'theme_schoollege');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_schoollege/nav4icon';
$title = get_string('navicon', 'theme_schoollege');
$description = get_string('navicondesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav4buttontext';
$title = get_string('naviconbuttontext', 'theme_schoollege');
$description = get_string('naviconbuttontextdesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav4buttonurl';
$title = get_string('naviconbuttonurl', 'theme_schoollege');
$description = get_string('naviconbuttonurldesc', 'theme_schoollege');
$default =  '';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav4target';
$title = get_string('marketingurltarget' , 'theme_schoollege');
$description = get_string('marketingurltargetdesc', 'theme_schoollege');
$target1 = get_string('marketingurltargetself', 'theme_schoollege');
$target2 = get_string('marketingurltargetnew', 'theme_schoollege');
$target3 = get_string('marketingurltargetparent', 'theme_schoollege');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon four
$name = 'theme_schoollege/navicon5info';
$heading = get_string('navicon5', 'theme_schoollege');
$information = get_string('navicondesc', 'theme_schoollege');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_schoollege/nav5icon';
$title = get_string('navicon', 'theme_schoollege');
$description = get_string('navicondesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav5buttontext';
$title = get_string('naviconbuttontext', 'theme_schoollege');
$description = get_string('naviconbuttontextdesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav5buttonurl';
$title = get_string('naviconbuttonurl', 'theme_schoollege');
$description = get_string('naviconbuttonurldesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav5target';
$title = get_string('marketingurltarget' , 'theme_schoollege');
$description = get_string('marketingurltargetdesc', 'theme_schoollege');
$target1 = get_string('marketingurltargetself', 'theme_schoollege');
$target2 = get_string('marketingurltargetnew', 'theme_schoollege');
$target3 = get_string('marketingurltargetparent', 'theme_schoollege');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon six
$name = 'theme_schoollege/navicon6info';
$heading = get_string('navicon6', 'theme_schoollege');
$information = get_string('navicondesc', 'theme_schoollege');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_schoollege/nav6icon';
$title = get_string('navicon', 'theme_schoollege');
$description = get_string('navicondesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav6buttontext';
$title = get_string('naviconbuttontext', 'theme_schoollege');
$description = get_string('naviconbuttontextdesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav6buttonurl';
$title = get_string('naviconbuttonurl', 'theme_schoollege');
$description = get_string('naviconbuttonurldesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav6target';
$title = get_string('marketingurltarget' , 'theme_schoollege');
$description = get_string('marketingurltargetdesc', 'theme_schoollege');
$target1 = get_string('marketingurltargetself', 'theme_schoollege');
$target2 = get_string('marketingurltargetnew', 'theme_schoollege');
$target3 = get_string('marketingurltargetparent', 'theme_schoollege');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon seven
$name = 'theme_schoollege/navicon7info';
$heading = get_string('navicon7', 'theme_schoollege');
$information = get_string('navicondesc', 'theme_schoollege');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_schoollege/nav7icon';
$title = get_string('navicon', 'theme_schoollege');
$description = get_string('navicondesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav7buttontext';
$title = get_string('naviconbuttontext', 'theme_schoollege');
$description = get_string('naviconbuttontextdesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav7buttonurl';
$title = get_string('naviconbuttonurl', 'theme_schoollege');
$description = get_string('naviconbuttonurldesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav7target';
$title = get_string('marketingurltarget' , 'theme_schoollege');
$description = get_string('marketingurltargetdesc', 'theme_schoollege');
$target1 = get_string('marketingurltargetself', 'theme_schoollege');
$target2 = get_string('marketingurltargetnew', 'theme_schoollege');
$target3 = get_string('marketingurltargetparent', 'theme_schoollege');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon eight
$name = 'theme_schoollege/navicon8info';
$heading = get_string('navicon8', 'theme_schoollege');
$information = get_string('navicondesc', 'theme_schoollege');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_schoollege/nav8icon';
$title = get_string('navicon', 'theme_schoollege');
$description = get_string('navicondesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav8buttontext';
$title = get_string('naviconbuttontext', 'theme_schoollege');
$description = get_string('naviconbuttontextdesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav8buttonurl';
$title = get_string('naviconbuttonurl', 'theme_schoollege');
$description = get_string('naviconbuttonurldesc', 'theme_schoollege');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_schoollege/nav8target';
$title = get_string('marketingurltarget' , 'theme_schoollege');
$description = get_string('marketingurltargetdesc', 'theme_schoollege');
$target1 = get_string('marketingurltargetself', 'theme_schoollege');
$target2 = get_string('marketingurltargetnew', 'theme_schoollege');
$target3 = get_string('marketingurltargetparent', 'theme_schoollege');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);
