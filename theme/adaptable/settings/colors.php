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
 * @copyright  2015-2016 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2016 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

    // Colors section.
    $temp = new admin_settingpage('theme_adaptable_color', get_string('colorsettings', 'theme_adaptable'));
    $temp->add(new admin_setting_heading('theme_adaptable_color', get_string('colorsettingsheading', 'theme_adaptable'),
                   format_text(get_string('colordesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

    // Main colors heading.
    $name = 'theme_adaptable/settingsmaincolors';
    $heading = get_string('settingsmaincolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    // Site main color.
    $name = 'theme_adaptable/maincolor';
    $title = get_string('maincolor', 'theme_adaptable');
    $description = get_string('maincolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3A454b', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Site background color.
    $name = 'theme_adaptable/backcolor';
    $title = get_string('backcolor', 'theme_adaptable');
    $description = get_string('backcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFF', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Main region background color.
    $name = 'theme_adaptable/regionmaincolor';
    $title = get_string('regionmaincolor', 'theme_adaptable');
    $description = get_string('regionmaincolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFF', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Link color.
    $name = 'theme_adaptable/linkcolor';
    $title = get_string('linkcolor', 'theme_adaptable');
    $description = get_string('linkcolordesc', 'theme_adaptable');
    $default = '#51666C';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $temp->add($setting);

    // Link hover color.
    $name = 'theme_adaptable/linkhover';
    $title = get_string('linkhover', 'theme_adaptable');
    $description = get_string('linkhoverdesc', 'theme_adaptable');
    $default = '#009688';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Selection text color.
    $name = 'theme_adaptable/selectiontext';
    $title = get_string('selectiontext', 'theme_adaptable');
    $description = get_string('selectiontextdesc', 'theme_adaptable');
    $default = '#000000';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Selection background color.
    $name = 'theme_adaptable/selectionbackground';
    $title = get_string('selectionbackground', 'theme_adaptable');
    $description = get_string('selectionbackgrounddesc', 'theme_adaptable');
    $default = '#00B3A1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Header colors heading.
    $name = 'theme_adaptable/settingsheadercolors';
    $heading = get_string('settingsheadercolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    // Loading bar color.
    $name = 'theme_adaptable/loadingcolor';
    $title = get_string('loadingcolor', 'theme_adaptable');
    $description = get_string('loadingcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00B3A1', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Top header message badge background color.
    $name = 'theme_adaptable/msgbadgecolor';
    $title = get_string('msgbadgecolor', 'theme_adaptable');
    $description = get_string('msgbadgecolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#E53935', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Messages main chat window background colour.
    $name = 'theme_adaptable/messagingbackgroundcolor';
    $title = get_string('messagingbackgroundcolor', 'theme_adaptable');
    $description = get_string('messagingbackgroundcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Top header background color.
    $name = 'theme_adaptable/headerbkcolor';
    $title = get_string('headerbkcolor', 'theme_adaptable');
    $description = get_string('headerbkcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00796B', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Top header text color.
    $name = 'theme_adaptable/headertextcolor';
    $title = get_string('headertextcolor', 'theme_adaptable');
    $description = get_string('headertextcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Bottom header background color.
    $name = 'theme_adaptable/headerbkcolor2';
    $title = get_string('headerbkcolor2', 'theme_adaptable');
    $description = get_string('headerbkcolor2desc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#009688', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Bottom header text color.
    $name = 'theme_adaptable/headertextcolor2';
    $title = get_string('headertextcolor2', 'theme_adaptable');
    $description = get_string('headertextcolor2desc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Menu colors heading.
    $name = 'theme_adaptable/settingsmenucolors';
    $heading = get_string('settingsmenucolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    // Main menu background color.
    $name = 'theme_adaptable/menubkcolor';
    $title = get_string('menubkcolor', 'theme_adaptable');
    $description = get_string('menubkcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Main menu text color.
    $name = 'theme_adaptable/menufontcolor';
    $title = get_string('menufontcolor', 'theme_adaptable');
    $description = get_string('menufontcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#222222', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Main menu hover color.
    $name = 'theme_adaptable/menuhovercolor';
    $title = get_string('menuhovercolor', 'theme_adaptable');
    $description = get_string('menuhovercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00B3A1', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Main menu bottom border color.
    $name = 'theme_adaptable/menubordercolor';
    $title = get_string('menubordercolor', 'theme_adaptable');
    $description = get_string('menubordercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00B3A1', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Mobile Menu colors heading.
    $name = 'theme_adaptable/settingsmobilemenucolors';
    $heading = get_string('settingsmobilemenucolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    // Mobile menu background color.
    $name = 'theme_adaptable/mobilemenubkcolor';
    $title = get_string('mobilemenubkcolor', 'theme_adaptable');
    $description = get_string('mobilemenubkcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#F9F9F9', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Mobile menu text color.
    $name = 'theme_adaptable/mobilemenufontcolor';
    $title = get_string('mobilemenufontcolor', 'theme_adaptable');
    $description = get_string('mobilemenufontcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#000000', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);


    // Market blocks colors heading.
    $name = 'theme_adaptable/settingsmarketingcolors';
    $heading = get_string('settingsmarketingcolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    // Market blocks border color.
    $name = 'theme_adaptable/marketblockbordercolor';
    $title = get_string('marketblockbordercolor', 'theme_adaptable');
    $description = get_string('marketblockbordercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#e8eaeb', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Market blocks background color.
    $name = 'theme_adaptable/marketblocksbackgroundcolor';
    $title = get_string('marketblocksbackgroundcolor', 'theme_adaptable');
    $description = get_string('marketblocksbackgroundcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, 'transparent', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);


    // Overlay tiles colors heading.
    $name = 'theme_adaptable/settingsoverlaycolors';
    $heading = get_string('settingsoverlaycolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    $name = 'theme_adaptable/rendereroverlaycolor';
    $title = get_string('rendereroverlaycolor', 'theme_adaptable');
    $description = get_string('rendereroverlaycolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3A454b', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/rendereroverlayfontcolor';
    $title = get_string('rendereroverlayfontcolor', 'theme_adaptable');
    $description = get_string('rendereroverlayfontcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFF', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/tilesbordercolor';
    $title = get_string('tilesbordercolor', 'theme_adaptable');
    $description = get_string('tilesbordercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3A454b', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/covbkcolor';
    $title = get_string('covbkcolor', 'theme_adaptable');
    $description = get_string('covbkcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3A454b', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/covfontcolor';
    $title = get_string('covfontcolor', 'theme_adaptable');
    $description = get_string('covfontcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/dividingline';
    $title = get_string('dividingline', 'theme_adaptable');
    $description = get_string('dividinglinedesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/dividingline2';
    $title = get_string('dividingline2', 'theme_adaptable');
    $description = get_string('dividingline2desc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Breadcrumb colors heading.
    $name = 'theme_adaptable/settingsbreadcrumbcolors';
    $heading = get_string('settingsbreadcrumbcolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    // Breadcrumb background color.
    $name = 'theme_adaptable/breadcrumb';
    $title = get_string('breadcrumb', 'theme_adaptable');
    $description = get_string('breadcrumbdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#f5f5f5', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Breadcrumb text color.
    $name = 'theme_adaptable/breadcrumbtextcolor';
    $title = get_string('breadcrumbtextcolor', 'theme_adaptable');
    $description = get_string('breadcrumbtextcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#444444', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);


    // Messages pop-up colors heading.
    $name = 'theme_adaptable/settingsmessagescolors';
    $heading = get_string('settingsmessagescolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    // Messages pop-up background color.
    $name = 'theme_adaptable/messagepopupbackground';
    $title = get_string('messagepopupbackground', 'theme_adaptable');
    $description = get_string('messagepopupbackgrounddesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#fff000', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Messages pop-up text color.
    $name = 'theme_adaptable/messagepopupcolor';
    $title = get_string('messagepopupcolor', 'theme_adaptable');
    $description = get_string('messagepopupcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#333333', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Footer colors heading.
    $name = 'theme_adaptable/settingsfootercolors';
    $heading = get_string('settingsfootercolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    $name = 'theme_adaptable/footerbkcolor';
    $title = get_string('footerbkcolor', 'theme_adaptable');
    $description = get_string('footerbkcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#424242', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/footertextcolor';
    $title = get_string('footertextcolor', 'theme_adaptable');
    $description = get_string('footertextcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/footertextcolor2';
    $title = get_string('footertextcolor2', 'theme_adaptable');
    $description = get_string('footertextcolor2desc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/footerlinkcolor';
    $title = get_string('footerlinkcolor', 'theme_adaptable');
    $description = get_string('footerlinkcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Forum colors.
    $name = 'theme_adaptable/settingsforumheading';
    $heading = get_string('settingsforumheading', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $temp->add($setting);

    $name = 'theme_adaptable/forumheaderbackgroundcolor';
    $title = get_string('forumheaderbackgroundcolor', 'theme_adaptable');
    $description = get_string('forumheaderbackgroundcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_adaptable/forumbodybackgroundcolor';
    $title = get_string('forumbodybackgroundcolor', 'theme_adaptable');
    $description = get_string('forumbodybackgroundcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $ADMIN->add('theme_adaptable', $temp);
