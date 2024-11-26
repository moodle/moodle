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
 * Course index settings
 *
 * @package    theme_adaptable
 * @copyright  2022 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Course index.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage(
        'theme_adaptable_course_index',
        get_string('courseindexsettings', 'theme_adaptable')
    );

    $page->add(new admin_setting_heading(
        'theme_adaptable_courseindex',
        get_string('courseindexsettingsheading', 'theme_adaptable'),
        format_text(get_string('courseindexsettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Enabled.
    $name = 'theme_adaptable/courseindexenabled';
    $title = get_string('courseindexenabled', 'theme_adaptable');
    $description = get_string('courseindexenableddesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, true);
    $page->add($setting);

    // Item.
    $name = 'theme_adaptable/courseindexitemcolor';
    $title = get_string('courseindexitemcolor', 'theme_adaptable');
    $description = get_string('courseindexitemcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#495057', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Item hover.
    $name = 'theme_adaptable/courseindexitemhovercolor';
    $title = get_string('courseindexitemhovercolor', 'theme_adaptable');
    $description = get_string('courseindexitemhovercolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#000000', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Page item.
    $name = 'theme_adaptable/courseindexpageitemcolor';
    $title = get_string('courseindexpageitemcolor', 'theme_adaptable');
    $description = get_string('courseindexpageitemcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Page item background.
    $name = 'theme_adaptable/courseindexpageitembgcolor';
    $title = get_string('courseindexpageitembgcolor', 'theme_adaptable');
    $description = get_string('courseindexpageitembgcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0f6cbf', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add($page);
}
