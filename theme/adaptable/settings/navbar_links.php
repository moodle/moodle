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
 * Navbar links
 *
 * @package    theme_adaptable
 * @copyright  2019 Jeremy Hopkins (Coventry University)
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Navbar links.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_navbar_links',
        get_string('navbarlinkssettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_navbar',
        get_string('navbarlinksettingsheading', 'theme_adaptable'),
        format_text(get_string('navbarlinksettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Help section.
    $page->add(new admin_setting_heading(
        'theme_adaptable_help_heading',
        get_string('headernavbarhelpheading', 'theme_adaptable'),
        format_text(get_string('headernavbarhelpheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $name = 'theme_adaptable/helptarget';
    $title = get_string('helptarget', 'theme_adaptable');
    $description = get_string('helptargetdesc', 'theme_adaptable');
    $choices = [
        '_blank' => get_string('targetnewwindow', 'theme_adaptable'),
        '_self' => get_string('targetsamewindow', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, '_blank', $choices);
    $page->add($setting);

    // Number of help links.
    $name = 'theme_adaptable/helplinkscount';
    $title = get_string('helplinkscount', 'theme_adaptable');
    $description = get_string('helplinkscountdesc', 'theme_adaptable');
    $default = 2;
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices0to12);
    $page->add($setting);

    $helplinkscount = get_config('theme_adaptable', 'helplinkscount');

    for ($helpcount = 1; $helpcount <= $helplinkscount; $helpcount++) {
        // Enable help link.
        $name = 'theme_adaptable/enablehelp' . $helpcount;
        $title = get_string('enablehelp', 'theme_adaptable', ['number' => $helpcount]);
        $description = get_string('enablehelpdesc', 'theme_adaptable', ['number' => $helpcount]);
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
        $page->add($setting);

        // Help link title.
        $name = 'theme_adaptable/helplinktitle' . $helpcount;
        $title = get_string('helplinktitle', 'theme_adaptable', ['number' => $helpcount]);
        $description = get_string('helplinktitledesc', 'theme_adaptable', ['number' => $helpcount]);
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
        $page->add($setting);

        $name = 'theme_adaptable/helpprofilefield' . $helpcount;
        $title = get_string('helpprofilefield', 'theme_adaptable', ['number' => $helpcount]);
        $description = get_string('helpprofilefielddesc', 'theme_adaptable', ['number' => $helpcount]);
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW);
        $page->add($setting);
    }

    $asettings->add($page);
}
