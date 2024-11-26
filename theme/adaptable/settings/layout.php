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
 * Layout
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_layout', get_string('layoutsettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_layout',
        get_string('layoutsettingsheading', 'theme_adaptable'),
        format_text(get_string('layoutdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Background Image.
    $name = 'theme_adaptable/homebk';
    $title = get_string('homebk', 'theme_adaptable');
    $description = get_string('homebkdesc', 'theme_adaptable');
    $setting = new \theme_adaptable\admin_setting_configstoredfiles(
        $name, $title, $description, 'homebk',
        ['accepted_types' => '*.jpg,*.jpeg,*.png', 'maxfiles' => 1]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Display block in the Left/Right side.
    $name = 'theme_adaptable/blockside';
    $title = get_string('blockside', 'theme_adaptable');
    $description = get_string('blocksidedesc', 'theme_adaptable');
    $setting = new admin_setting_configselect(
        $name,
        $title,
        $description,
        0,
        [
            0 => get_string('rightblocks', 'theme_adaptable'),
            1 => get_string('leftblocks', 'theme_adaptable'),
        ]
    );
    $page->add($setting);

    // Fullscreen width.
    $name = 'theme_adaptable/fullscreenwidth';
    $title = get_string('fullscreenwidth', 'theme_adaptable');
    $description = get_string('fullscreenwidthdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, '98%', $from95to100percent);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Standard screen width.
    $name = 'theme_adaptable/standardscreenwidth';
    $title = get_string('standardscreenwidth', 'theme_adaptable');
    $description = get_string('standardscreenwidthdesc', 'theme_adaptable');
    $choices = [
        'standard' => '1170px',
        'narrow' => '1000px',
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'standard', $choices);
    $page->add($setting);

    // Emoticons size.
    $name = 'theme_adaptable/emoticonsize';
    $title = get_string('emoticonsize', 'theme_adaptable');
    $description = get_string('emoticonsizedesc', 'theme_adaptable');
    $default = '16px';
    $choices = $standardfontsize;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Info icon colour.
    $name = 'theme_adaptable/infoiconcolor';
    $title = get_string('infoiconcolor', 'theme_adaptable');
    $description = get_string('infoiconcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#5bc0de', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Danger icon colour.
    $name = 'theme_adaptable/dangericoncolor';
    $title = get_string('dangericoncolor', 'theme_adaptable');
    $description = get_string('dangericoncolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#d9534f', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Adaptable Tabbed layout changes.
    $name = 'theme_adaptable/tabbedlayoutheading';
    $heading = get_string('tabbedlayoutheading', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $page->add($setting);

    // Course page tabbed layout.
    $name = 'theme_adaptable/tabbedlayoutcoursepage';
    $title = get_string('tabbedlayoutcoursepage', 'theme_adaptable');
    $description = get_string('tabbedlayoutcoursepagedesc', 'theme_adaptable');
    $default = 0;
    $choices = $tabbedlayoutdefaultscourse;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Have a link back to the course page in the course tabs.
    $name = 'theme_adaptable/tabbedlayoutcoursepagelink';
    $title = get_string('tabbedlayoutcoursepagelink', 'theme_adaptable');
    $description = get_string('tabbedlayoutcoursepagelinkdesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // Course page tab colour selected.
    $name = 'theme_adaptable/tabbedlayoutcoursepagetabcolorselected';
    $title = get_string('tabbedlayoutcoursepagetabcolorselected', 'theme_adaptable');
    $description = get_string('tabbedlayoutcoursepagetabcolorselecteddesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#06c', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course page tab colour unselected.
    $name = 'theme_adaptable/tabbedlayoutcoursepagetabcolorunselected';
    $title = get_string('tabbedlayoutcoursepagetabcolorunselected', 'theme_adaptable');
    $description = get_string('tabbedlayoutcoursepagetabcolorunselecteddesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#eee', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course home page tab persistence time.
    $name = 'theme_adaptable/tabbedlayoutcoursepagetabpersistencetime';
    $title = get_string('tabbedlayoutcoursepagetabpersistencetime', 'theme_adaptable');
    $description = get_string('tabbedlayoutcoursepagetabpersistencetimedesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '30', PARAM_INT);
    $page->add($setting);

    // Dashboard page tabbed layout.
    $name = 'theme_adaptable/tabbedlayoutdashboard';
    $title = get_string('tabbedlayoutdashboard', 'theme_adaptable');
    $description = get_string('tabbedlayoutdashboarddesc', 'theme_adaptable');
    $default = 0;
    $choices = $tabbedlayoutdefaultsdashboard;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Dashboard page tab colour selected.
    $name = 'theme_adaptable/tabbedlayoutdashboardcolorselected';
    $title = get_string('tabbedlayoutdashboardtabcolorselected', 'theme_adaptable');
    $description = get_string('tabbedlayoutdashboardtabcolorselecteddesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#06c', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Dashboard page tab colour unselected.
    $name = 'theme_adaptable/tabbedlayoutdashboardcolorunselected';
    $title = get_string('tabbedlayoutdashboardtabcolorunselected', 'theme_adaptable');
    $description = get_string('tabbedlayoutdashboardtabcolorunselecteddesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#eee', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/tabbedlayoutdashboardtab1condition';
    $title = get_string('tabbedlayoutdashboardtab1condition', 'theme_adaptable');
    $description = get_string('tabbedlayoutdashboardtab1conditiondesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW, '');
    $page->add($setting);

    $name = 'theme_adaptable/tabbedlayoutdashboardtab2condition';
    $title = get_string('tabbedlayoutdashboardtab2condition', 'theme_adaptable');
    $description = get_string('tabbedlayoutdashboardtab2conditiondesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW, '');
    $page->add($setting);

    $asettings->add($page);
}
