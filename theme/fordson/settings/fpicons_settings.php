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
* @package    theme_fordson
* @copyright  2016 Chris Kenniburg
* 
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

// Icon Navigation);
$page = new admin_settingpage('theme_fordson_iconnavheading', get_string('iconnavheading', 'theme_fordson'));

// This is the descriptor for icon One
$name = 'theme_fordson/iconwidthinfo';
$heading = get_string('iconwidthinfo', 'theme_fordson');
$information = get_string('iconwidthinfodesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Icon width setting.
$name = 'theme_fordson/iconwidth';
$title = get_string('iconwidth', 'theme_fordson');
$description = get_string('iconwidth_desc', 'theme_fordson');;
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
$name = 'theme_fordson/createinfo';
$heading = get_string('createinfo', 'theme_fordson');
$information = get_string('createinfodesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Creator Icon
$name = 'theme_fordson/createicon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = 'edit';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/createbuttontext';
$title = get_string('naviconbuttontext', 'theme_fordson');
$description = get_string('naviconbuttontextdesc', 'theme_fordson');
$default = get_string('naviconbuttoncreatetextdefault', 'theme_fordson');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/createbuttonurl';
$title = get_string('naviconbuttonurl', 'theme_fordson');
$description = get_string('naviconbuttonurldesc', 'theme_fordson');
$default =  $CFG->wwwroot.'/course/edit.php?category=1';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for teacher create a course
$name = 'theme_fordson/sliderinfo';
$heading = get_string('sliderinfo', 'theme_fordson');
$information = get_string('sliderinfodesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// Creator Icon
$name = 'theme_fordson/slideicon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('naviconslidedesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/slideiconbuttontext';
$title = get_string('naviconbuttontext', 'theme_fordson');
$description = get_string('naviconbuttontextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Slide Textbox.
$name = 'theme_fordson/slidetextbox';
$title = get_string('slidetextbox', 'theme_fordson');
$description = get_string('slidetextbox_desc', 'theme_fordson');
$default = '';
$setting = new admin_setting_confightmleditor($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon One
$name = 'theme_fordson/navicon1info';
$heading = get_string('navicon1', 'theme_fordson');
$information = get_string('navicondesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

// icon One
$name = 'theme_fordson/nav1icon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = 'home';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav1buttontext';
$title = get_string('naviconbuttontext', 'theme_fordson');
$description = get_string('naviconbuttontextdesc', 'theme_fordson');
$default = get_string('naviconbutton1textdefault', 'theme_fordson');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav1buttonurl';
$title = get_string('naviconbuttonurl', 'theme_fordson');
$description = get_string('naviconbuttonurldesc', 'theme_fordson');
$default =  $CFG->wwwroot.'/my/';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav1target';
$title = get_string('marketingurltarget' , 'theme_fordson');
$description = get_string('marketingurltargetdesc', 'theme_fordson');
$target1 = get_string('marketingurltargetself', 'theme_fordson');
$target2 = get_string('marketingurltargetnew', 'theme_fordson');
$target3 = get_string('marketingurltargetparent', 'theme_fordson');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon One
$name = 'theme_fordson/navicon2info';
$heading = get_string('navicon2', 'theme_fordson');
$information = get_string('navicondesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_fordson/nav2icon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = 'calendar';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav2buttontext';
$title = get_string('naviconbuttontext', 'theme_fordson');
$description = get_string('naviconbuttontextdesc', 'theme_fordson');
$default = get_string('naviconbutton2textdefault', 'theme_fordson');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav2buttonurl';
$title = get_string('naviconbuttonurl', 'theme_fordson');
$description = get_string('naviconbuttonurldesc', 'theme_fordson');
$default =  $CFG->wwwroot.'/calendar/view.php?view=month';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav2target';
$title = get_string('marketingurltarget' , 'theme_fordson');
$description = get_string('marketingurltargetdesc', 'theme_fordson');
$target1 = get_string('marketingurltargetself', 'theme_fordson');
$target2 = get_string('marketingurltargetnew', 'theme_fordson');
$target3 = get_string('marketingurltargetparent', 'theme_fordson');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon three
$name = 'theme_fordson/navicon3info';
$heading = get_string('navicon3', 'theme_fordson');
$information = get_string('navicondesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_fordson/nav3icon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = 'bookmark';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav3buttontext';
$title = get_string('naviconbuttontext', 'theme_fordson');
$description = get_string('naviconbuttontextdesc', 'theme_fordson');
$default = get_string('naviconbutton3textdefault', 'theme_fordson');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav3buttonurl';
$title = get_string('naviconbuttonurl', 'theme_fordson');
$description = get_string('naviconbuttonurldesc', 'theme_fordson');
$default =  $CFG->wwwroot.'/badges/mybadges.php';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav3target';
$title = get_string('marketingurltarget' , 'theme_fordson');
$description = get_string('marketingurltargetdesc', 'theme_fordson');
$target1 = get_string('marketingurltargetself', 'theme_fordson');
$target2 = get_string('marketingurltargetnew', 'theme_fordson');
$target3 = get_string('marketingurltargetparent', 'theme_fordson');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon four
$name = 'theme_fordson/navicon4info';
$heading = get_string('navicon4', 'theme_fordson');
$information = get_string('navicondesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_fordson/nav4icon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = 'book';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav4buttontext';
$title = get_string('naviconbuttontext', 'theme_fordson');
$description = get_string('naviconbuttontextdesc', 'theme_fordson');
$default = get_string('naviconbutton4textdefault', 'theme_fordson');
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav4buttonurl';
$title = get_string('naviconbuttonurl', 'theme_fordson');
$description = get_string('naviconbuttonurldesc', 'theme_fordson');
$default =  $CFG->wwwroot.'/course/';
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav4target';
$title = get_string('marketingurltarget' , 'theme_fordson');
$description = get_string('marketingurltargetdesc', 'theme_fordson');
$target1 = get_string('marketingurltargetself', 'theme_fordson');
$target2 = get_string('marketingurltargetnew', 'theme_fordson');
$target3 = get_string('marketingurltargetparent', 'theme_fordson');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon four
$name = 'theme_fordson/navicon5info';
$heading = get_string('navicon5', 'theme_fordson');
$information = get_string('navicondesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_fordson/nav5icon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav5buttontext';
$title = get_string('naviconbuttontext', 'theme_fordson');
$description = get_string('naviconbuttontextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav5buttonurl';
$title = get_string('naviconbuttonurl', 'theme_fordson');
$description = get_string('naviconbuttonurldesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav5target';
$title = get_string('marketingurltarget' , 'theme_fordson');
$description = get_string('marketingurltargetdesc', 'theme_fordson');
$target1 = get_string('marketingurltargetself', 'theme_fordson');
$target2 = get_string('marketingurltargetnew', 'theme_fordson');
$target3 = get_string('marketingurltargetparent', 'theme_fordson');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon six
$name = 'theme_fordson/navicon6info';
$heading = get_string('navicon6', 'theme_fordson');
$information = get_string('navicondesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_fordson/nav6icon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav6buttontext';
$title = get_string('naviconbuttontext', 'theme_fordson');
$description = get_string('naviconbuttontextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav6buttonurl';
$title = get_string('naviconbuttonurl', 'theme_fordson');
$description = get_string('naviconbuttonurldesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav6target';
$title = get_string('marketingurltarget' , 'theme_fordson');
$description = get_string('marketingurltargetdesc', 'theme_fordson');
$target1 = get_string('marketingurltargetself', 'theme_fordson');
$target2 = get_string('marketingurltargetnew', 'theme_fordson');
$target3 = get_string('marketingurltargetparent', 'theme_fordson');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon seven
$name = 'theme_fordson/navicon7info';
$heading = get_string('navicon7', 'theme_fordson');
$information = get_string('navicondesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_fordson/nav7icon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav7buttontext';
$title = get_string('naviconbuttontext', 'theme_fordson');
$description = get_string('naviconbuttontextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav7buttonurl';
$title = get_string('naviconbuttonurl', 'theme_fordson');
$description = get_string('naviconbuttonurldesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav7target';
$title = get_string('marketingurltarget' , 'theme_fordson');
$description = get_string('marketingurltargetdesc', 'theme_fordson');
$target1 = get_string('marketingurltargetself', 'theme_fordson');
$target2 = get_string('marketingurltargetnew', 'theme_fordson');
$target3 = get_string('marketingurltargetparent', 'theme_fordson');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// This is the descriptor for icon eight
$name = 'theme_fordson/navicon8info';
$heading = get_string('navicon8', 'theme_fordson');
$information = get_string('navicondesc', 'theme_fordson');
$setting = new admin_setting_heading($name, $heading, $information);
$page->add($setting);

$name = 'theme_fordson/nav8icon';
$title = get_string('navicon', 'theme_fordson');
$description = get_string('navicondesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav8buttontext';
$title = get_string('naviconbuttontext', 'theme_fordson');
$description = get_string('naviconbuttontextdesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav8buttonurl';
$title = get_string('naviconbuttonurl', 'theme_fordson');
$description = get_string('naviconbuttonurldesc', 'theme_fordson');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$name = 'theme_fordson/nav8target';
$title = get_string('marketingurltarget' , 'theme_fordson');
$description = get_string('marketingurltargetdesc', 'theme_fordson');
$target1 = get_string('marketingurltargetself', 'theme_fordson');
$target2 = get_string('marketingurltargetnew', 'theme_fordson');
$target3 = get_string('marketingurltargetparent', 'theme_fordson');
$default = 'target1';
$choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Must add the page after definiting all the settings!
$settings->add($page);
