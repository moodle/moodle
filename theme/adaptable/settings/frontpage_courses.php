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
 * Frontpage courses
 *
 * @package    theme_adaptable
 * @copyright  2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2019 Fernando Acedo (3-bits.com)
 * @copyright  2017-2019 Manoj Solanki (Coventry University)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Frontpage courses section.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_frontpage_courses',
        get_string('frontpagecoursesettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_frontpage_courses',
        get_string('frontpagesettingsheading', 'theme_adaptable'),
        format_text(get_string('frontpagedesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $name = 'theme_adaptable/frontpagerenderer';
    $title = get_string('frontpagerenderer', 'theme_adaptable');
    $description = get_string('frontpagerendererdesc', 'theme_adaptable');
    $choices = [
        1 => get_string('frontpagerendereroption1', 'theme_adaptable'),
        2 => get_string('frontpagerendereroption2', 'theme_adaptable'),
        3 => get_string('frontpagerendereroption3', 'theme_adaptable'),
        4 => get_string('frontpagerendereroption4', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 2, $choices);
    $page->add($setting);

    // Number of tiles per row.
    // Number of tiles per row: 12=1 tile / 6=2 tiles / 4 (default)=3 tiles / 3=4 tiles / 2=6 tiles.
    $name = 'theme_adaptable/frontpagenumbertiles';
    $title = get_string('frontpagenumbertiles', 'theme_adaptable');
    $description = get_string('frontpagenumbertilesdesc', 'theme_adaptable');
    $choices = [
        12 => get_string('frontpagetiles1', 'theme_adaptable'),
        6 => get_string('frontpagetiles2', 'theme_adaptable'),
        4 => get_string('frontpagetiles3', 'theme_adaptable'),
        3 => get_string('frontpagetiles4', 'theme_adaptable'),
        2 => get_string('frontpagetiles6', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 4, $choices);
    $page->add($setting);

    // Default image for 'Tiles with overlay' on 'frontpagerenderer' setting.
    $name = 'theme_adaptable/frontpagerendererdefaultimage';
    $title = get_string('frontpagerendererdefaultimage', 'theme_adaptable');
    $description = get_string('frontpagerendererdefaultimagedesc', 'theme_adaptable');
    $setting = new \theme_adaptable\admin_setting_configstoredfiles(
        $name, $title, $description, 'frontpagerendererdefaultimage',
        ['accepted_types' => '*.jpg,*.jpeg,*.png', 'maxfiles' => 1]
    );
    $page->add($setting);

    // Show course contacts.
    $name = 'theme_adaptable/tilesshowcontacts';
    $title = get_string('tilesshowcontacts', 'theme_adaptable');
    $description = get_string('tilesshowcontactsdesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $page->add($setting);

    $name = 'theme_adaptable/tilesshowallcontacts';
    $title = get_string('tilesshowallcontacts', 'theme_adaptable');
    $description = get_string('tilesshowallcontactsdesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/tilescontactstitle';
    $title = get_string('tilescontactstitle', 'theme_adaptable');
    $description = get_string('tilescontactstitledesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $page->add($setting);

    $name = 'theme_adaptable/covhidebutton';
    $title = get_string('covhidebutton', 'theme_adaptable');
    $description = get_string('covhidebuttondesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $page->add($setting);

    // Show 'Available Courses' label.
    $name = 'theme_adaptable/enableavailablecourses';
    $title = get_string('enableavailablecourses', 'theme_adaptable');
    $description = get_string('enableavailablecoursesdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect(
        $name,
        $title,
        $description,
        0,
        [
            'inherit' => get_string('show'),
            'none' => get_string('hide'),
        ]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add($page);
}
