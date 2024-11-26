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
 * Information blocks
 *
 * @package    theme_adaptable
 * @copyright  2024 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
defined('MOODLE_INTERNAL') || die;

// Information blocks section.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_information_blocks',
        get_string('informationblocksettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_information',
        get_string('informationsettingsheading', 'theme_adaptable'),
        format_text(get_string('informationsettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $name = 'theme_adaptable/informationblocksenabled';
    $title = get_string('informationblocksenabled', 'theme_adaptable');
    $description = get_string('informationblocksenableddesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/informationblocksvisible';
    $title = get_string('informationblocksvisible', 'theme_adaptable');
    $description = get_string('informationblocksvisibledesc', 'theme_adaptable');
    $options = [
        1 => get_string('informationblocksvisibleloggedout', 'theme_adaptable'),
        2 => get_string('informationblocksvisibleloggedin', 'theme_adaptable'),
        3 => get_string('informationblocksvisibleloggedinout', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 3, $options);
    $page->add($setting);

    $page->add(new admin_setting_heading(
        'theme_adaptable_informationblocksbuilder',
        get_string('informationblocksbuilderheading', 'theme_adaptable'),
        format_text(get_string('informationblocksbuilderdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Information block region builder.
    ['imgblder' => $imgblder, 'totalblocks' => $totalblocks] = \theme_adaptable\toolbox::admin_settings_layout_builder(
        $page,
        'informationblockslayoutrow',
        5,
        $informationblocksbuilderdefaults,
        $bootstrap12
    );

    if ($totalblocks > 0) {
        $page->add(new admin_setting_heading(
            'theme_adaptable_informationblocklayoutcheck',
            get_string('layoutcheck', 'theme_adaptable'),
            format_text(get_string('layoutcheckdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $page->add(new admin_setting_heading('theme_adaptable_informationlayoutbuilder', '', $imgblder));
    }

    $asettings->add($page);
}
