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
 * @package    theme
 * @subpackage adaptable
 * @copyright  &copy; 2018 G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$adaptablesettingsprops = new admin_settingpage('theme_adaptable_importexport', get_string('properties', 'theme_adaptable'));
if ($ADMIN->fulltree) {
    if (file_exists("{$CFG->dirroot}/theme/adaptable/settings/adaptable_admin_setting_getprops.php")) {
        require_once($CFG->dirroot . '/theme/adaptable/settings/adaptable_admin_setting_getprops.php');
        require_once($CFG->dirroot . '/theme/adaptable/settings/adaptable_admin_setting_putprops.php');
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/adaptable/settings/adaptable_admin_setting_getprops.php")) {
        require_once($CFG->themedir . '/adaptable/settings/adaptable_admin_setting_getprops.php');
        require_once($CFG->themedir . '/adaptable/settings/adaptable_admin_setting_putprops.php');
    }

    $adaptablesettingsprops->add(new admin_setting_heading('theme_adaptable_importexport',
        get_string('propertiessub', 'theme_adaptable'),
        format_text(get_string('propertiesdesc', 'theme_adaptable'), FORMAT_MARKDOWN)));

    $adaptableexportprops = optional_param('theme_adaptable_getprops_saveprops', 0, PARAM_INT);
    $adaptableprops = \theme_adaptable\toolbox::compile_properties('adaptable');
    $adaptablesettingsprops->add(new adaptable_admin_setting_getprops('theme_adaptable_getprops',
        get_string('propertiesproperty', 'theme_adaptable'),
        get_string('propertiesvalue', 'theme_adaptable'),
        $adaptableprops,
        'theme_adaptable_importexport',
        get_string('propertiesreturn', 'theme_adaptable'),
        get_string('propertiesexport', 'theme_adaptable'),
        $adaptableexportprops
    ));

    // Import theme settings section (put properties).
    $name = 'theme_adaptable/theme_adaptable_putprops_import_heading';
    $heading = get_string('putpropertiesheading', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $adaptablesettingsprops->add($setting);

    $setting = new adaptable_admin_setting_putprops('theme_adaptable_putprops',
        get_string('putpropertiesname', 'theme_adaptable'),
        get_string('putpropertiesdesc', 'theme_adaptable'),
        'adaptable',
        '\theme_adaptable\toolbox::put_properties'
    );
    $setting->set_updatedcallback('purge_all_caches');
    $adaptablesettingsprops->add($setting);
}
$ADMIN->add('theme_adaptable', $adaptablesettingsprops);
