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
 * Admin settings configuration for home page slider section.
 *
 * @package    theme_academi
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

// Home page slider.
$temp = new admin_settingpage('theme_academi_slideshow', get_string('slideshowheading', 'theme_academi'));
$temp->add(new admin_setting_heading('theme_academi_slideshow', get_string('slideshowheadingsub', 'theme_academi'),
format_text(get_string('slideshowdesc', 'theme_academi'), FORMAT_MARKDOWN)));

// Enable or disable option for slider show / hide in the home page.
$name = 'theme_academi/toggleslideshow';
$title = get_string('toggleslideshow', 'theme_academi');
$description = get_string('toggleslideshowdesc', 'theme_academi');
$default = YES;
$choices = [
    YES => get_string('yes'),
    NO => get_string('no'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$temp->add($setting);

// Enable or diable option for home page slider auto scroll.
$name = 'theme_academi/autoslideshow';
$title = get_string('autoslideshow', 'theme_academi');
$description = get_string('autoslideshowdesc', 'theme_academi');
$default = YES;
$choices = [
    YES => get_string('yes'),
    NO => get_string('no'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
$temp->add($setting);

// Give interval time for home page slider.
$name = 'theme_academi/slideinterval';
$title = get_string('slideinterval', 'theme_academi');
$description = get_string('slideintervaldesc', 'theme_academi');
$default = 3500;
$setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_INT);
$temp->add($setting);

// Select the overlay opacity value for the home page slider.
$name = 'theme_academi/slideOverlay';
$title = get_string('slideOverlay', 'theme_academi');
$description = get_string('slideOverlay_desc', 'theme_academi');
$opacity = [];
$opacity = array_combine(range(0, 1, 0.1 ), range(0, 1, 0.1 ));
$setting = new admin_setting_configselect($name, $title, $description, '0.4', $opacity);
$setting->set_updatedcallback('theme_reset_all_caches');
$temp->add($setting);

// Select the number of slides show in the homepage.
$name = 'theme_academi/numberofslides';
$title = get_string('numberofslides', 'theme_academi');
$description = get_string('numberofslides_desc', 'theme_academi');
$default = 3;
$choices = [
    1 => '1',
    2 => '2',
    3 => '3',
    4 => '4',
    5 => '5',
    6 => '6',
    7 => '7',
    8 => '8',
    9 => '9',
    10 => '10',
    11 => '11',
    12 => '12',
];
$temp->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

// Slideshow settings.
$numberofslides = get_config('theme_academi', 'numberofslides');
for ($i = 1; $i <= $numberofslides; $i++) {

    // This is the descriptor for Slide.
    $name = 'theme_academi/slide' . $i . 'info';
    $heading = get_string('slideno', 'theme_academi', ['slide' => $i]);
    $information = get_string('slidenodesc', 'theme_academi', ['slide' => $i]);
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    // Enable or disable option for slide show.
    $name = 'theme_academi/slide' . $i .'status';
    $title = get_string('slideStatus', 'theme_academi', ['slide' => $i]);
    $description = get_string('slideStatus_desc', 'theme_academi', ['slide' => $i]);
    $default = YES;
    $choices = [
        YES => get_string('enable', 'theme_academi'),
        NO => get_string('disable', 'theme_academi'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $temp->add($setting);

    // Slider image uploaded option.
    $name = 'theme_academi/slide' . $i . 'image';
    $title = get_string('slideimage', 'theme_academi');
    $description = get_string('slideimagedesc', 'theme_academi');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'slide' . $i . 'image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Enable or disable option for SlideShow content.
    $name = 'theme_academi/slide' . $i .'contentstatus';
    $title = get_string('slidecontentstatus', 'theme_academi', ['slide' => $i]);
    $description = get_string('slidecontentstatus_desc', 'theme_academi', ['slide' => $i]);
    $default = YES;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $temp->add($setting);

    // Give a caption for the home page slider.
    $name = 'theme_academi/slide' . $i . 'caption';
    $title = get_string('slidecaption', 'theme_academi');
    $description = get_string('slidecaptiondesc', 'theme_academi');
    $default = get_string('slidecaptiondefault', 'theme_academi', ['slideno' => sprintf('%02d', $i)]);
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $temp->add($setting);

    // Give a description for the home page slider.
    $name = 'theme_academi/slide' . $i . 'desc';
    $title = get_string('slidedesc', 'theme_academi');
    $description = get_string('slidedesctext', 'theme_academi');
    $default = get_string('slidedescdefault', 'theme_academi');
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $temp->add($setting);

    // Give a text for the home page slider button.
    $name = 'theme_academi/slide' . $i . 'btntext';
    $title = get_string('slidebtntext', 'theme_academi');
    $description = get_string('slidebtntext_desc', 'theme_academi');
    $default = 'lang:knowmore';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $temp->add($setting);

    // Give a url for the home page slider button.
    $name = 'theme_academi/slide' . $i . 'btnurl';
    $title = get_string('slidebtnlink', 'theme_academi');
    $description = get_string('slidebtnlink_desc', 'theme_academi');
    $default = 'http://www.example.com/';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Select the target of the button for the home page slider.
    $name = 'theme_academi/slide' . $i . 'btntarget';
    $title = get_string('slidebtntarget', 'theme_academi');
    $description = get_string('slidebtntarget_desc', 'theme_academi', ['slide' => $i]);
    $default = NEWWINDOW;
    $choices = [
        SAMEWINDOW => get_string('sameWindow', 'theme_academi'),
        NEWWINDOW => get_string('newWindow', 'theme_academi'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $temp->add($setting);

    // Give a content width for the home page slider.
    $name = 'theme_academi/slide' . $i . 'contFullwidth';
    $title = get_string('slideCont_full', 'theme_academi');
    $description = get_string('slideCont_fulldesc', 'theme_academi');
    $default = "50";
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Select the content position option for the home page slider.
    $name = 'theme_academi/slide' . $i . 'contentPosition';
    $title = get_string('slidecontent', 'theme_academi', ['slide' => $i]);
    $description = get_string('slidecontentdesc', 'theme_academi');
    $default = 'centerRight';
    $choices = [
        "topLeft" => get_string("topLeft", "theme_academi"),
        "topCenter" => get_string("topCenter", "theme_academi"),
        "topRight" => get_string("topRight", "theme_academi"),
        "centerLeft" => get_string("centerLeft", "theme_academi"),
        "center" => get_string("center", "theme_academi"),
        "centerRight" => get_string("centerRight", "theme_academi"),
        "bottomLeft" => get_string("bottomLeft", "theme_academi"),
        "bottomCenter" => get_string("bottomCenter", "theme_academi"),
        "bottomRight" => get_string("bottomRight", "theme_academi"),
        ];
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $temp->add($setting);
}
/* Slideshow Settings End*/
$settings->add($temp);
