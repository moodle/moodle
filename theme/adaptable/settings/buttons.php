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
 * Buttons
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Buttons Section.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_buttons', get_string('buttonsettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_header',
        get_string('buttonsettingsheading', 'theme_adaptable'),
        format_text(get_string('buttondesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $name = 'theme_adaptable/buttonradius';
    $title = get_string('buttonradius', 'theme_adaptable');
    $description = get_string('buttonradiusdesc', 'theme_adaptable');
    $radchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '5px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Buttons background color.
    $name = 'theme_adaptable/buttoncolor';
    $title = get_string('buttoncolor', 'theme_adaptable');
    $description = get_string('buttoncolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#51666C', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Buttons text color.
    $name = 'theme_adaptable/buttontextcolor';
    $title = get_string('buttontextcolor', 'theme_adaptable');
    $description = get_string('buttontextcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Buttons background hover color.
    $name = 'theme_adaptable/buttonhovercolor';
    $title = get_string('buttonhovercolor', 'theme_adaptable');
    $description = get_string('buttonhovercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#009688', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Buttons text hover color.
    $name = 'theme_adaptable/buttontexthovercolor';
    $title = get_string('buttontexthovercolor', 'theme_adaptable');
    $description = get_string('buttontexthovercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#eeeeee', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Buttons background focus color.
    $name = 'theme_adaptable/buttonfocuscolour';
    $title = get_string('buttonfocuscolour', 'theme_adaptable');
    $description = get_string('buttonfocuscolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0f6cc0', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Buttons text focus color.
    $name = 'theme_adaptable/buttontextfocuscolour';
    $title = get_string('buttontextfocuscolour', 'theme_adaptable');
    $description = get_string('buttontextfocuscolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#eeeeee', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Input buttons focus color.
    $name = 'theme_adaptable/inputbuttonfocuscolour';
    $title = get_string('inputbuttonfocuscolour', 'theme_adaptable');
    $description = get_string('inputbuttonfocuscolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0f6cc0', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Input buttons focus color opacity.
    $ibfcoopactitychoices = [
        '0.0' => '0.0',
        '0.05' => '0.05',
        '0.1' => '0.1',
        '0.15' => '0.15',
        '0.2' => '0.2',
        '0.25' => '0.25',
        '0.3' => '0.3',
        '0.35' => '0.35',
        '0.4' => '0.4',
        '0.45' => '0.45',
        '0.5' => '0.5',
        '0.55' => '0.55',
        '0.6' => '0.6',
        '0.65' => '0.65',
        '0.7' => '0.7',
        '0.75' => '0.75',
        '0.8' => '0.8',
        '0.85' => '0.85',
        '0.9' => '0.9',
        '0.95' => '0.95',
        '1.0' => '1.0',
    ];

    $name = 'theme_adaptable/inputbuttonfocuscolouropacity';
    $title = get_string('inputbuttonfocuscolouropacity', 'theme_adaptable');
    $description = get_string('inputbuttonfocuscolouropacitydesc', 'theme_adaptable');
    $default = '0.75';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $ibfcoopactitychoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Secondary Buttons background color.
    $name = 'theme_adaptable/buttoncolorscnd';
    $title = get_string('buttoncolorscnd', 'theme_adaptable');
    $description = get_string('buttoncolordescscnd', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#51666C', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Secondary Buttons background hover color.
    $name = 'theme_adaptable/buttonhovercolorscnd';
    $title = get_string('buttonhovercolorscnd', 'theme_adaptable');
    $description = get_string('buttonhovercolordescscnd', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#009688', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Secondary Buttons text color.
    $name = 'theme_adaptable/buttontextcolorscnd';
    $title = get_string('buttontextcolorscnd', 'theme_adaptable');
    $description = get_string('buttontextcolordescscnd', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Cancel Buttons background color.
    $name = 'theme_adaptable/buttoncolorcancel';
    $title = get_string('buttoncolorcancel', 'theme_adaptable');
    $description = get_string('buttoncolordesccancel', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#c64543', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Cancel Buttons background hover color.
    $name = 'theme_adaptable/buttonhovercolorcancel';
    $title = get_string('buttonhovercolorcancel', 'theme_adaptable');
    $description = get_string('buttonhovercolordesccancel', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#e53935', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Cancel Buttons text color.
    $name = 'theme_adaptable/buttontextcolorcancel';
    $title = get_string('buttontextcolorcancel', 'theme_adaptable');
    $description = get_string('buttontextcolordesccancel', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/editonbk';
    $title = get_string('editonbk', 'theme_adaptable');
    $description = get_string('editonbkdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#4caf50', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/editoffbk';
    $title = get_string('editoffbk', 'theme_adaptable');
    $description = get_string('editoffbkdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#f44336', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/editfont';
    $title = get_string('editfont', 'theme_adaptable');
    $description = get_string('editfontdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/edithorizontalpadding';
    $title = get_string('edithorizontalpadding', 'theme_adaptable');
    $description = get_string('edithorizontalpadding', 'theme_adaptable');
    $radchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '4px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/buttonlogincolor';
    $title = get_string('buttonlogincolor', 'theme_adaptable');
    $description = get_string('buttonlogincolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#c64543', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/buttonloginhovercolor';
    $title = get_string('buttonloginhovercolor', 'theme_adaptable');
    $description = get_string('buttonloginhovercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#e53935', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/buttonlogintextcolor';
    $title = get_string('buttonlogintextcolor', 'theme_adaptable');
    $description = get_string('buttonlogintextcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/buttonloginpadding';
    $title = get_string('buttonloginpadding', 'theme_adaptable');
    $description = get_string('buttonloginpaddingdesc', 'theme_adaptable');
    $radchoices = $from0to8px;
    $setting = new admin_setting_configselect($name, $title, $description, '0', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/buttonloginheight';
    $title = get_string('buttonloginheight', 'theme_adaptable');
    $description = get_string('buttonloginheightdesc', 'theme_adaptable');
    $radchoices = [
        '16px' => "16px",
        '18px' => "18px",
        '20px' => "20px",
        '22px' => "22px",
        '24px' => "24px",
        '26px' => "26px",
        '28px' => "28px",
        '30px' => "30px",
        '32px' => "32px",
        '34px' => "34px",
    ];
    $setting = new admin_setting_configselect($name, $title, $description, '24px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/buttonloginmargintop';
    $title = get_string('buttonloginmargintop', 'theme_adaptable');
    $description = get_string('buttonloginmargintopdesc', 'theme_adaptable');
    $radchoices = $from0to12px;
    $setting = new admin_setting_configselect($name, $title, $description, '2px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Enable drop shadow on bottom of button.
    $name = 'theme_adaptable/buttondropshadow';
    $title = get_string('buttondropshadow', 'theme_adaptable');
    $description = get_string('buttondropshadowdesc', 'theme_adaptable');
    $shadowchoices = [
        '0' => get_string('none', 'theme_adaptable'),
        '-1px' => get_string('slight', 'theme_adaptable'),
        '-2px' => get_string('standard', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, '0', $shadowchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add($page);
}
