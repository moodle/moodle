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
 * Import / Export settings.
 *
 * @package    theme_adaptable
 * @copyright  2018 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

$page = new \theme_adaptable\admin_settingspage('theme_adaptable_importexport', get_string('properties', 'theme_adaptable'));
if ($ADMIN->fulltree) {

    $page->add(new admin_setting_heading(
        'theme_adaptable_importexport',
        get_string('propertiessub', 'theme_adaptable'),
        format_text(get_string('propertiesdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $page->add(new \theme_adaptable\admin_setting_getprops(
        'theme_adaptable/getprops',
        get_string('propertiesproperty', 'theme_adaptable'),
        get_string('propertiesvalue', 'theme_adaptable'),
        'theme_adaptable',
        'theme_adaptable_importexport',
        get_string('propertiesreturn', 'theme_adaptable'),
        get_string('propertiesexport', 'theme_adaptable'),
        get_string('propertiesexportfilestoo', 'theme_adaptable'),
        get_string('propertiesexportfilestoofile', 'theme_adaptable')
    ));

    $name = 'theme_adaptable/propertyfiles';
    $title = get_string('propertyfiles', 'theme_adaptable');
    $description = get_string('propertyfilesdesc', 'theme_adaptable');
    $setting = new \theme_adaptable\admin_setting_configstoredfiles(
        $name, $title, $description, 'propertyfiles',
        ['accepted_types' => '*.json', 'maxfiles' => 8]
    );
    $page->add($setting);

    // Import theme settings section (put properties).
    $name = 'theme_adaptable/theme_adaptable_putprops_import_heading';
    $heading = get_string('putpropertiesheading', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $page->add($setting);

    $fileputpropssetting = new \theme_adaptable\admin_setting_configstoredfile_putprops(
        'theme_adaptable/fileputprops',
        get_string('putpropertiesfilename', 'theme_adaptable'),
        get_string('putpropertiesfiledesc', 'theme_adaptable'),
        'fileputprops',
        'Adaptable',
        'theme_adaptable',
        '\theme_adaptable\toolbox::put_properties',
        'putprops',
        ['accepted_types' => '*.json', 'maxfiles' => 1]
    );
    $fileputpropssetting->set_updatedcallback('purge_all_caches');
    $page->add($fileputpropssetting);

    $setting = new \theme_adaptable\admin_setting_putprops(
        'theme_adaptable/putprops',
        get_string('putpropertiesname', 'theme_adaptable'),
        get_string('putpropertiesdesc', 'theme_adaptable'),
        'Adaptable',
        'theme_adaptable',
        '\theme_adaptable\toolbox::put_properties'
    );
    $setting->set_updatedcallback('purge_all_caches');
    $fileputpropssetting->set_admin_setting_putprops($setting);
    $page->add($setting);
}
$ADMIN->add('theme_adaptable', $page);
