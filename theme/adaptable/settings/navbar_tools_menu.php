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
 * Navbar tools menu
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
defined('MOODLE_INTERNAL') || die;

// Settings for tools menus.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_navbar_tools_menu',
        get_string('toolsmenu', 'theme_adaptable'), true);

    $page->add(new admin_setting_heading(
        'theme_adaptable_toolsmenu',
        get_string('toolsmenu', 'theme_adaptable'),
        format_text(
            get_string('toolsmenuheadingdesc', 'theme_adaptable'),
            FORMAT_MARKDOWN
        )
    ));

    $name = 'theme_adaptable/enabletoolsmenus';
    $title = get_string('enabletoolsmenus', 'theme_adaptable');
    $description = get_string('enabletoolsmenusdesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // Number of tools menus.
    $name = 'theme_adaptable/toolsmenuscount';
    $title = get_string('toolsmenuscount', 'theme_adaptable');
    $description = get_string('toolsmenuscountdesc', 'theme_adaptable');
    $default = THEME_ADAPTABLE_DEFAULT_TOOLSMENUSCOUNT;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices0to12);
    $page->add($setting);

    // If we don't have a menuscount yet, default to the preset.
    $toolsmenuscount = get_config('theme_adaptable', 'toolsmenuscount');

    if (!$toolsmenuscount) {
        $toolsmenuscount = THEME_ADAPTABLE_DEFAULT_TOOLSMENUSCOUNT;
    }

    for ($toolsmenusindex = 1; $toolsmenusindex <= $toolsmenuscount; $toolsmenusindex++) {
        $page->add(new admin_setting_heading(
            'theme_adaptable_menus' . $toolsmenusindex,
            get_string('toolsmenuheadingindex', 'theme_adaptable', $toolsmenusindex), ''
        ));

        $name = 'theme_adaptable/toolsmenu' . $toolsmenusindex . 'title';
        $title = get_string('toolsmenutitle', 'theme_adaptable') . ' ' . $toolsmenusindex;
        $description = get_string('toolsmenutitledesc', 'theme_adaptable');
        $default = get_string('toolsmenutitledefault', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_RAW);
        $page->add($setting);

        $name = 'theme_adaptable/toolsmenu' . $toolsmenusindex;
        $title = get_string('toolsmenu', 'theme_adaptable') . ' ' . $toolsmenusindex;
        $description = get_string('toolsmenudesc', 'theme_adaptable');
        $setting = new admin_setting_configtextarea($name, $title, $description, '', PARAM_RAW, '50', '10');
        $page->add($setting);

        $name = 'theme_adaptable/toolsmenu' . $toolsmenusindex . 'field';
        $title = get_string('toolsmenufield', 'theme_adaptable');
        $description = get_string('toolsmenufielddesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW);
        $page->add($setting);
    }

    $asettings->add($page);
}
