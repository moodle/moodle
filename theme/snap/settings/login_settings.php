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

$snapsettings = new admin_settingpage('themesnaplogin', get_string('loginsetting', 'theme_snap'));

// Select login background image.
$name = 'theme_snap/loginbgimgheading';
$title = new lang_string('loginbgimgheading', 'theme_snap');
$description = new lang_string('loginbgimgheadingdesc', 'theme_snap');
$setting = new admin_setting_heading($name, $title, $description);
$snapsettings->add($setting);

// Select login template.
$templates = array (
    'classic_template' => $OUTPUT->image_url('classic_template', 'theme_snap'),
    'stylish_template' => $OUTPUT->image_url('stylish_template', 'theme_snap'),
);
$snaptemplatetitle = get_string('classic_template', 'theme_snap');
$snapstylishtemplatetitle = get_string('stylish_template', 'theme_snap');
$templatedescription =
        '<div id="snap_login_templates" class="row">
            <div id="snap_classic_template_img" class="col=4">
                <a target="_blank" href='.$templates['classic_template'].'>
                    <img class="img-responsive" src="'.$templates['classic_template'].'" alt="'.$snaptemplatetitle.'">
                </a>
                <div class="text-center">' . $snaptemplatetitle . '</div>
            </div>
            <div id="snap_stylish_template_img" class="col=4">
                <a target="_blank" href='.$templates['stylish_template'].'>
                    <img class="img-responsive" src="'.$templates['stylish_template'].'" alt="'.$snapstylishtemplatetitle.'">
                </a>
                <div class="text-center">' . $snapstylishtemplatetitle . '</div>
            </div>
        </div>';

$name = 'theme_snap/loginpagetemplate';
$title = new lang_string('loginpagetemplate', 'theme_snap');
$setting = new admin_setting_configselect($name, $title, $templatedescription, 'classic',
    array('classic' => get_string('classic_template', 'theme_snap'), 'stylish' => get_string('stylish_template', 'theme_snap')));
$setting->set_updatedcallback('theme_reset_all_caches');
$snapsettings->add($setting);

$name = 'theme_snap/loginbgimg';
$title = get_string('loginbgimg', 'theme_snap');
$description = get_string('loginbgimgdesc', 'theme_snap');
$opts = array('accepted_types' => array('.png', '.jpg'), 'maxfiles' => 3);
$setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbgimg', 0, $opts);
$setting->set_updatedcallback('theme_snap_resize_bgimage_after_save');
$snapsettings->add($setting);

// Alternative login Settings.
$name = 'theme_snap/alternativeloginoptionsheading';
$title = new lang_string('alternativeloginoptions', 'theme_snap');
$description = '';
$setting = new admin_setting_heading($name, $title, $description);
$snapsettings->add($setting);

// Enable login options display.
$name = 'theme_snap/enabledlogin';
$title = new lang_string('enabledlogin', 'theme_snap');
$description = new lang_string('enabledlogindesc', 'theme_snap');
$default = '0';
$enabledloginchoices = [
    \theme_snap\output\core_renderer::ENABLED_LOGIN_BOTH        => new lang_string('bothlogin', 'theme_snap'),
    \theme_snap\output\core_renderer::ENABLED_LOGIN_MOODLE      => new lang_string('moodlelogin', 'theme_snap'),
    \theme_snap\output\core_renderer::ENABLED_LOGIN_ALTERNATIVE => new lang_string('alternativelogin', 'theme_snap'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $enabledloginchoices);
$snapsettings->add($setting);

// Enabled login options order.
$name = 'theme_snap/enabledloginorder';
$title = new lang_string('enabledloginorder', 'theme_snap');
$description = new lang_string('enabledloginorderdesc', 'theme_snap');
$default = '0';
$enabledloginchoices = [
    \theme_snap\output\core_renderer::ORDER_LOGIN_MOODLE_FIRST      => new lang_string('moodleloginfirst', 'theme_snap'),
    \theme_snap\output\core_renderer::ORDER_LOGIN_ALTERNATIVE_FIRST => new lang_string('alternativeloginfirst', 'theme_snap'),
];
$setting = new admin_setting_configselect($name, $title, $description, $default, $enabledloginchoices);
$snapsettings->add($setting);

$settings->add($snapsettings);
