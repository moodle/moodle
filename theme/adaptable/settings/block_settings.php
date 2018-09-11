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
 * Version details
 *
 * @package    theme_adaptable
 * @copyright 2015 Jeremy Hopkins (Coventry University)
 * @copyright 2015 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

    $temp = new admin_settingpage('theme_adaptable_blocks', get_string('blocksettings', 'theme_adaptable'));

    // Colours.
    $name = 'theme_adaptable/settingscolors';
    $heading = get_string('settingscolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    $name = 'theme_adaptable/blockbackgroundcolor';
    $title = get_string('blockbackgroundcolor', 'theme_adaptable');
    $description = get_string('blockbackgroundcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockheaderbackgroundcolor';
    $title = get_string('blockheaderbackgroundcolor', 'theme_adaptable');
    $description = get_string('blockheaderbackgroundcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockbordercolor';
    $title = get_string('blockbordercolor', 'theme_adaptable');
    $description = get_string('blockbordercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#59585D', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockregionbackgroundcolor';
    $title = get_string('blockregionbackground', 'theme_adaptable');
    $description = get_string('blockregionbackgrounddesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, 'transparent', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Borders.
    $name = 'theme_adaptable/settingsborders';
    $heading = get_string('settingsborders', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    $name = 'theme_adaptable/blockheaderbordertopstyle';
    $title = get_string('blockheaderbordertopstyle', 'theme_adaptable');
    $description = get_string('blockheaderbordertopstyledesc', 'theme_adaptable');
    $radchoices = $borderstyles;
    $setting = new admin_setting_configselect($name, $title, $description, 'dashed', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockheadertopradius';
    $title = get_string('blockheadertopradius', 'theme_adaptable');
    $description = get_string('blockheadertopradiusdesc', 'theme_adaptable');
    $radchoices = $from0to20px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockheaderbottomradius';
    $title = get_string('blockheaderbottomradius', 'theme_adaptable');
    $description = get_string('blockheaderbottomradiusdesc', 'theme_adaptable');
    $radchoices = $from0to20px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockheaderbordertop';
    $title = get_string('blockheaderbordertop', 'theme_adaptable');
    $description = get_string('blockheaderbordertopdesc', 'theme_adaptable');
    $radchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '1px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockheaderborderleft';
    $title = get_string('blockheaderborderleft', 'theme_adaptable');
    $description = get_string('blockheaderborderleftdesc', 'theme_adaptable');
    $radchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockheaderborderright';
    $title = get_string('blockheaderborderright', 'theme_adaptable');
    $description = get_string('blockheaderborderrightdesc', 'theme_adaptable');
    $radchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockheaderborderbottom';
    $title = get_string('blockheaderborderbottom', 'theme_adaptable');
    $description = get_string('blockheaderborderbottomdesc', 'theme_adaptable');
    $radchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockmainbordertopstyle';
    $title = get_string('blockmainbordertopstyle', 'theme_adaptable');
    $description = get_string('blockmainbordertopstyledesc', 'theme_adaptable');
    $radchoices = $borderstyles;
    $setting = new admin_setting_configselect($name, $title, $description, 'none', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockmaintopradius';
    $title = get_string('blockmaintopradius', 'theme_adaptable');
    $description = get_string('blockmaintopradiusdesc', 'theme_adaptable');
    $radchoices = $from0to20px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockmainbottomradius';
    $title = get_string('blockmainbottomradius', 'theme_adaptable');
    $description = get_string('blockmainbottomradiusdesc', 'theme_adaptable');
    $radchoices = $from0to20px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockmainbordertop';
    $title = get_string('blockmainbordertop', 'theme_adaptable');
    $description = get_string('blockmainbordertopdesc', 'theme_adaptable');
    $radchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockmainborderleft';
    $title = get_string('blockmainborderleft', 'theme_adaptable');
    $description = get_string('blockmainborderleftdesc', 'theme_adaptable');
    $radchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockmainborderright';
    $title = get_string('blockmainborderright', 'theme_adaptable');
    $description = get_string('blockmainborderrightdesc', 'theme_adaptable');
    $radchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/blockmainborderbottom';
    $title = get_string('blockmainborderbottom', 'theme_adaptable');
    $description = get_string('blockmainborderbottomdesc', 'theme_adaptable');
    $radchoices = $from0to6px;
    $setting = new admin_setting_configselect($name, $title, $description, '0px', $radchoices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Fonts heading.
    $name = 'theme_adaptable/settingsfonts';
    $heading = get_string('settingsfonts', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    // Block Header Font size.
    $name = 'theme_adaptable/fontblockheadersize';
    $title = get_string('fontblockheadersize', 'theme_adaptable');
    $description = get_string('fontblockheadersizedesc', 'theme_adaptable');
    $default = '22px';
    $choices = $standardfontsize;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Block Header Font weight.
    $name = 'theme_adaptable/fontblockheaderweight';
    $title = get_string('fontblockheaderweight', 'theme_adaptable');
    $description = get_string('fontblockheaderweightdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, 400, $from100to900);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Block Header Font color.
    $name = 'theme_adaptable/fontblockheadercolor';
    $title = get_string('fontblockheadercolor', 'theme_adaptable');
    $description = get_string('fontblockheadercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3A454b', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Icons heading.
    $name = 'theme_adaptable/settingsblockicons';
    $heading = get_string('settingsblockicons', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    // Add icon to the title.
    $name = 'theme_adaptable/blockicons';
    $title = get_string('blockicons', 'theme_adaptable');
    $description = get_string('blockiconsdesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Block Header Icon size.
    $name = 'theme_adaptable/blockiconsheadersize';
    $title = get_string('blockiconsheadersize', 'theme_adaptable');
    $description = get_string('blockiconsheadersizedesc', 'theme_adaptable');
    $default = '20px';
    $choices = $standardfontsize;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $ADMIN->add('theme_adaptable', $temp);
