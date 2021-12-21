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
 * Plugin administration pages are defined here.
 *
 * @package     tool_brickfield
 * @category    admin
 * @copyright   2020 Brickfield Education Labs, https://www.brickfield.ie - Author: Karen Holland
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_brickfield\accessibility;
use tool_brickfield\manager;
use tool_brickfield\analysis;
use tool_brickfield\output\renderer;
use tool_brickfield\registration;

defined('MOODLE_INTERNAL') || die();

$accessibilitydisabled = !accessibility::is_accessibility_enabled();

if ($hassiteconfig) {
    // Add an enable subsystem setting to the "Advanced features" settings page.
    $optionalsubsystems = $ADMIN->locate('optionalsubsystems');
    $optionalsubsystems->add(new admin_setting_configcheckbox(
        'enableaccessibilitytools',
        new lang_string('enableaccessibilitytools', manager::PLUGINNAME),
        new lang_string('enableaccessibilitytools_desc', manager::PLUGINNAME),
        1,
        1,
        0
    ));
}

$moodleurl = accessibility::get_plugin_url();
if ($hassiteconfig) {
    $ADMIN->add(
        'tools',
        new admin_category('brickfieldfolder', get_string('accessibility', manager::PLUGINNAME), $accessibilitydisabled)
    );

    $ADMIN->add(
        'brickfieldfolder',
        new admin_externalpage(
            'tool_brickfield_activation',
            get_string('activationform', manager::PLUGINNAME),
            manager::registration_url(),
            'moodle/site:config'
        )
    );

    $settings = new admin_settingpage(manager::PLUGINNAME, get_string('settings', manager::PLUGINNAME));

    $settings->add(new admin_setting_configcheckbox(
        manager::PLUGINNAME . '/analysistype',
        get_string('analysistype', manager::PLUGINNAME),
        get_string('analysistype_desc', manager::PLUGINNAME),
        analysis::ANALYSISDISABLED,
        analysis::ANALYSISBYREQUEST,
        analysis::ANALYSISDISABLED
    ));

    $settings->add(new admin_setting_configcheckbox(
        manager::PLUGINNAME . '/deletehistoricaldata',
        get_string('deletehistoricaldata', manager::PLUGINNAME),
        '',
        1
    ));

    $settings->add(new admin_setting_configtext(
        manager::PLUGINNAME . '/batch',
        get_string('batch', manager::PLUGINNAME),
        '',
        1000,
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtext(
        manager::PLUGINNAME . '/perpage',
        get_string('perpage', manager::PLUGINNAME),
        '',
        50,
        PARAM_INT));

    $ADMIN->add('brickfieldfolder', $settings);

    $ADMIN->add('brickfieldfolder', new admin_externalpage('tool_brickfield_tool',
        get_string('tools', manager::PLUGINNAME),
        $moodleurl,
        accessibility::get_capability_name('viewsystemtools')
    ));
}

// Add the reports link if the toolkit is enabled, and is either registered, or the user has the ability to register it.
$showreports = has_capability('moodle/site:config', \context_system::instance());
$showreports = $showreports || (new registration())->toolkit_is_active();

// Create a link to the main page in the reports menu.
$ADMIN->add(
    'reports',
    new admin_externalpage(
        'tool_brickfield_reports',
        get_string('pluginname', manager::PLUGINNAME),
        $moodleurl,
        accessibility::get_capability_name('viewsystemtools'),
        $accessibilitydisabled || !$showreports
    )
);
