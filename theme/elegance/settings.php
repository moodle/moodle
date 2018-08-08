<?php
// This file is part of the custom Moodle elegance theme
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
 * Renderers to align Moodle's HTML with that expected by elegance
 *
 * @package    theme_elegance
 * @copyright  2014 Julian Ridden http://moodleman.net
 * @copyright  2015 Bas Brands http://basbrands.nl
 * @authors    Bas Brands, Abstracting Theme settings: David Scotson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$settings = null;


require_once(__DIR__ . "/simple_theme_settings.class.php");

defined('MOODLE_INTERNAL') || die;

global $PAGE;

$ss = new elegance_simple_theme_settings($settings, 'theme_elegance');

$ADMIN->add('themes', new admin_category('theme_elegance', 'Elegance'));

// "geneicsettings" settingpage
$temp = new admin_settingpage('theme_elegance_generic',  get_string('geneicsettings', 'theme_elegance'));

$temp->add($ss->add_checkbox('invert'));

$temp->add($ss->add_checkbox('fixednavbar'));

$temp->add($ss->add_text('maxwidth', '1100'));

$choices = array();
$choices[1] = get_string('blocksleftandright', 'theme_elegance');
$choices[2] = get_string('blocksleft', 'theme_elegance');
$choices[3] = get_string('blocksright', 'theme_elegance');

$temp->add($ss->add_select('blocksconfig', '3', $choices));

$temp->add($ss->add_htmleditor('frontpagecontent'));

$temp->add($ss->add_htmleditor('footnote'));

$temp->add($ss->add_text('videowidth'));

$temp->add($ss->add_checkbox('showoldmessages'));

$temp->add($ss->add_textarea('customcss'));

$temp->add($ss->add_textarea('moodlemobilecss'));

$url = new moodle_url($CFG->wwwroot . '/theme/styles_debug.php', array('theme' => 'elegance',
    'type' => 'theme', 'sheet' => 'mobile'));

$temp->add($ss->add_empty('moodlemobilecsssettings', '', $url->out()));

$ADMIN->add('theme_elegance', $temp);

/* Color and Logo Settings */
$temp = new admin_settingpage('theme_elegance_colors', get_string('colorsettings', 'theme_elegance'));
$temp->add(new admin_setting_heading('theme_elegance_colors', get_string('colorsettingssub', 'theme_elegance'),
        format_text(get_string('colorsettingsdesc' , 'theme_elegance'), FORMAT_MARKDOWN)));

$temp->add($ss->add_colourpicker('themecolor', '#0098e0'));

$temp->add($ss->add_colourpicker('fontcolor', '#666'));

$temp->add($ss->add_colourpicker('headingcolor', '#27282a'));

$temp->add($ss->add_file('logo'));

$temp->add($ss->add_file('headerbg'));

$temp->add($ss->add_file('bodybg'));

$choices = array();
$choices[1] = get_string('bodybgrepeat', 'theme_elegance');
$choices[2] = get_string('bodybgfixed', 'theme_elegance');
$choices[3] = get_string('bodybgscroll', 'theme_elegance');

$temp->add($ss->add_select('bodybgconfig', '2', $choices));

$temp->add($ss->add_colourpicker('bodycolor', '#edecec'));

// Set Transparency.
$choices = array(
    '.10'=>'10%',
    '.15'=>'15%',
    '.20'=>'20%',
    '.25'=>'25%',
    '.30'=>'30%',
    '.35'=>'35%',
    '.40'=>'40%',
    '.45'=>'45%',
    '.50'=>'50%',
    '.55'=>'55%',
    '.60'=>'60%',
    '.65'=>'65%',
    '.70'=>'70%',
    '.75'=>'75%',
    '.80'=>'80%',
    '.85'=>'85%',
    '.90'=>'90%',
    '.95'=>'95%',
    '1'=>'100%');

$temp->add($ss->add_select('transparency', '1', $choices));

$ADMIN->add('theme_elegance', $temp);

/* Banner Settings */
$temp = new admin_settingpage('theme_elegance_banner', get_string('bannersettings', 'theme_elegance'));
$temp->add(new admin_setting_heading('theme_elegance_banner', get_string('bannersettingssub', 'theme_elegance'),
        format_text(get_string('bannersettingsdesc' , 'theme_elegance'), FORMAT_MARKDOWN)));


$choices = array();
$choices[1] = get_string('alwaysdisplay', 'theme_elegance');
$choices[2] = get_string('displaybeforelogin', 'theme_elegance');
$choices[3] = get_string('displayafterlogin', 'theme_elegance');
$choices[4] = get_string('dontdisplay', 'theme_elegance');
$temp->add($ss->add_select('togglebanner', '1', $choices));

$choices = range(0, 10);

$temp->add($ss->add_select('slidenumber', '0', $choices));

$temp->add($ss->add_text('slidespeed', '3600'));

$hasslidenum = (!empty($PAGE->theme->settings->slidenumber));
if ($hasslidenum) {
        $slidenum = $PAGE->theme->settings->slidenumber;
} else {
    $slidenum = '0';
}

$bannertitle = array('Slide One', 'Slide Two', 'Slide Three','Slide Four','Slide Five','Slide Six','Slide Seven', 'Slide Eight', 'Slide Nine', 'Slide Ten');

foreach (range(1, $slidenum) as $bannernumber) {

    $temp->add($ss->add_headings('bannerindicator', $bannernumber));

    $temp->add($ss->add_checkboxes('enablebanner', $bannernumber));

    $temp->add($ss->add_texts('bannertitle', $bannernumber));

    $temp->add($ss->add_texts('bannertext', $bannernumber));

    $temp->add($ss->add_texts('bannerlinktext', $bannernumber));

    $temp->add($ss->add_texts('bannerlinkurl', $bannernumber));

    $temp->add($ss->add_files('bannerimage', $bannernumber));

    $temp->add($ss->add_colourpickers('bannercolor', '#000', $bannernumber));

}

$ADMIN->add('theme_elegance', $temp);

/* Marketing Spot Settings */
$temp = new admin_settingpage('theme_elegance_marketing', get_string('marketingspots', 'theme_elegance'));
$temp->add(new admin_setting_heading('theme_elegance_marketing', get_string('marketingheadingsub', 'theme_elegance'),
        format_text(get_string('marketingdesc' , 'theme_elegance'), FORMAT_MARKDOWN)));

$choices = array();
$choices[1] = get_string('alwaysdisplay', 'theme_elegance');
$choices[2] = get_string('displaybeforelogin', 'theme_elegance');
$choices[3] = get_string('displayafterlogin', 'theme_elegance');
$choices[4] = get_string('dontdisplay', 'theme_elegance');
$temp->add($ss->add_select('togglemarketing', '1', $choices));

$temp->add($ss->add_text('marketingtitle'));

$temp->add($ss->add_text('marketingtitleicon'));

$choices = array();
$choices[1] = 2;
$choices[2] = 4;
$choices[3] = 6;
$choices[4] = 8;
$temp->add($ss->add_select('marketingspotsinrow', '1', $choices));

$choices = (range(0, 24));

$temp->add($ss->add_select('marketingspotsnr', '0', $choices));

$hasspotsnr = (!empty($PAGE->theme->settings->marketingspotsnr));
if ($hasspotsnr) {
    $spotsnr = $PAGE->theme->settings->marketingspotsnr;
} else {
    $spotsnr = '4';
}


foreach (range(1, $spotsnr) as $spot) {
    $temp->add($ss->add_headings('marketingheading', $spot));

    $temp->add($ss->add_texts('marketingtitle', $spot));

    $temp->add($ss->add_texts('marketingicon', $spot));

    $temp->add($ss->add_texts('marketingurl', $spot));

    $temp->add($ss->add_htmleditors('marketingcontent', '', $spot));
}


$ADMIN->add('theme_elegance', $temp);

/* Quick Link Settings */
$temp = new admin_settingpage('theme_elegance_quicklinks', get_string('quicklinksheading', 'theme_elegance'));
$temp->add(new admin_setting_heading('theme_elegance_quicklinks', get_string('quicklinksheadingsub', 'theme_elegance'),
        format_text(get_string('quicklinksdesc' , 'theme_elegance'), FORMAT_MARKDOWN)));

$choices = array('1' => get_string('alwaysdisplay', 'theme_elegance'),
    '2' => get_string('displaybeforelogin', 'theme_elegance'),
    '3'=> get_string('displayafterlogin', 'theme_elegance'),
    '4'=> get_string('dontdisplay', 'theme_elegance'));

$temp->add($ss->add_select('togglequicklinks', '1', $choices));

$choices = range(0, 12);
$temp->add($ss->add_select('quicklinksnumber', '0', $choices));

$temp->add($ss->add_text('quicklinkstitle'));

$temp->add($ss->add_text('quicklinksicon'));

$hasquicklinksnum = (!empty($PAGE->theme->settings->quicklinksnumber));
if ($hasquicklinksnum) {
    $quicklinksnum = $PAGE->theme->settings->quicklinksnumber;
} else {
    $quicklinksnum = '4';
}


foreach (range(1, $quicklinksnum) as $qln) {

    $temp->add($ss->add_headings('quicklinks', $qln));

    $temp->add($ss->add_texts('quicklinkicon', $qln));

    $temp->add($ss->add_colourpickers('quicklinkiconcolor', '', $qln));

    $temp->add($ss->add_texts('quicklinkbuttontext', $qln));

    $temp->add($ss->add_colourpickers('quicklinkbuttoncolor', '', $qln));

    $temp->add($ss->add_texts('quicklinkbuttonurl', $qln));
}


$ADMIN->add('theme_elegance', $temp);

/* Login Page Settings */
$temp = new admin_settingpage('theme_elegance_loginsettings', get_string('loginsettings', 'theme_elegance'));
$temp->add(new admin_setting_heading('theme_elegance_loginsettings', get_string('loginsettingssub', 'theme_elegance'),
        format_text(get_string('loginsettingsdesc' , 'theme_elegance'), FORMAT_MARKDOWN)));

// Set Number of Slides.
$choices = range(0, 5);
$temp->add($ss->add_select('loginbgnumber', '1', $choices));

$hasloginbgnum = (!empty($PAGE->theme->settings->loginbgnumber));
if ($hasloginbgnum) {
    $loginbgnum = $PAGE->theme->settings->loginbgnumber;
} else {
    $loginbgnum = '3';
}

foreach (range(1, $loginbgnum) as $i) {
    $temp->add($ss->add_files('loginimage', $i));
}

$ADMIN->add('theme_elegance', $temp);

/* Social Network Settings */
$temp = new admin_settingpage('theme_elegance_social', get_string('socialheading', 'theme_elegance'));
$temp->add(new admin_setting_heading('theme_elegance_social', get_string('socialheadingsub', 'theme_elegance'),
        format_text(get_string('socialdesc' , 'theme_elegance'), FORMAT_MARKDOWN)));

$temp->add($ss->add_text('website'));

$temp->add($ss->add_text('blog'));

$temp->add($ss->add_text('facebook'));

$temp->add($ss->add_text('flickr'));

$temp->add($ss->add_text('twitter'));

$temp->add($ss->add_text('googleplus'));

$temp->add($ss->add_text('linkedin'));

$temp->add($ss->add_text('tumblr'));

$temp->add($ss->add_text('pinterest'));

$temp->add($ss->add_text('instagram'));

$temp->add($ss->add_text('youtube'));

$temp->add($ss->add_text('vimeo'));

$temp->add($ss->add_text('skype'));

$temp->add($ss->add_text('vk'));

$ADMIN->add('theme_elegance', $temp);

/* Moodle Mobile Application Settings */

$temp = new admin_settingpage('theme_elegance_mobile', get_string('mobileappsheading', 'theme_elegance'));
$temp->add(new admin_setting_heading('theme_elegance_mobileapps', get_string('mobileappsheadingsub', 'theme_elegance'),
    format_text(get_string('mobileappsdesc', 'theme_elegance'), FORMAT_MARKDOWN)));

// Android App url setting.
$temp->add($ss->add_text('android'));

// iOS App url setting.
$temp->add($ss->add_text('ios'));

// Windows App url setting.
$temp->add($ss->add_text('windows'));

// Windows PhoneApp url setting.
$temp->add($ss->add_text('winphone'));

$ADMIN->add('theme_elegance', $temp);
