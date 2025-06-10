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
 * This file contains the mhaairs block admin settings definition.
 *
 * @package     block_mhaairs
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @copyright   2013 Moodlerooms inc.
 * @author      Teresa Hardy <thardy@moodlerooms.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Backwards compatibility.
$blockisenabled = method_exists($block, 'is_enabled') ? $block->is_enabled() : !empty($block->visible);
$section = !empty($section) ? $section : 'blocks';

$admincatstr = new lang_string('pluginname', 'block_mhaairs');
$ADMIN->add('blocksettings', new admin_category('blockmhaairsfolder', $admincatstr, $blockisenabled === false));

$settings = new admin_settingpage($section, get_string('settings'), 'moodle/site:config', $blockisenabled === false);

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/blocks/mhaairs/settingslib.php');

    // Allow connection only via SSL.
    $settings->add(new admin_setting_configcheckbox(
        'block_mhaairs_sslonly',
        new lang_string('sslonlylabel', 'block_mhaairs'),
        '',
        0
    ));

    // Customer number.
    $settings->add(new admin_setting_configtext(
        'block_mhaairs_customer_number',
        new lang_string('customernumberlabel', 'block_mhaairs'),
        '',
        '',
        PARAM_ALPHANUMEXT
    ));

    // Customer shared secret.
    $settings->add(new admin_setting_configtext(
        'block_mhaairs_shared_secret',
        new lang_string('secretlabel', 'block_mhaairs'),
        '',
        '',
        PARAM_ALPHANUMEXT
    ));

    // End point url.
    $settings->add(new admin_setting_configtext(
        'block_mhaairs_endpoint_url',
        new lang_string('endpointurllabel', 'block_mhaairs'),
        new lang_string('endpointurldesc', 'block_mhaairs'),
        '',
        PARAM_URL
    ));

    // Instructor roles.
    $settings->add(new admin_setting_configtext(
        'block_mhaairs_instructor_roles',
        new lang_string('instructorroleslabel', 'block_mhaairs'),
        new lang_string('instructorrolesdesc', 'block_mhaairs'),
        '',
        PARAM_TAGLIST
    ));

    // Student roles.
    $settings->add(new admin_setting_configtext(
        'block_mhaairs_student_roles',
        new lang_string('studentroleslabel', 'block_mhaairs'),
        new lang_string('studentrolesdesc', 'block_mhaairs'),
        '',
        PARAM_TAGLIST
    ));

    // Available services.
    $settings->add(new admin_setting_configmulticheckbox_mhaairs(
        'block_mhaairs_display_services',
        new lang_string('services_displaylabel', 'block_mhaairs'),
        new lang_string('services_desc', 'block_mhaairs')
    ));

    // Display help links.
    //$settings->add(new admin_setting_configcheckbox(
    //    'block_mhaairs_display_helplinks',
    //    new lang_string('mhaairs_displayhelp', 'block_mhaairs'),
    //    new lang_string('mhaairs_displayhelpdesc', 'block_mhaairs'),
    //    1
    //));

    // Sync gradebook.
    $settings->add(new admin_setting_configcheckbox(
        'block_mhaairs_sync_gradebook',
        new lang_string('mhaairs_syncgradebook', 'block_mhaairs'),
        new lang_string('mhaairs_syncgradebookdesc', 'block_mhaairs'),
        1
    ));

    // Grade exchange log.
    $settings->add(new admin_setting_configcheckbox(
            'block_mhaairs_gradelog',
            new lang_string('gradelog', 'block_mhaairs'),
            new lang_string('gradelogdesc', 'block_mhaairs'),
            '0'
    ));
}

$ADMIN->add('blockmhaairsfolder', $settings);
// Tell core we already added the settings structure.
$settings = null;

// Test client.
$externalpage = new admin_externalpage(
    'blockmhaairs_testclient',
    new lang_string('testclient', 'webservice'),
    "$CFG->wwwroot/blocks/mhaairs/admin/testclient.php"
);
$ADMIN->add('blockmhaairsfolder', $externalpage);

// Reset debugging logs.
$externalpage = new admin_externalpage(
    'blockmhaairs_gradelogs',
    new lang_string('gradelogs', 'block_mhaairs'),
    "$CFG->wwwroot/blocks/mhaairs/admin/gradelogs.php"
);
$ADMIN->add('blockmhaairsfolder', $externalpage);

// Reset caches.
$externalpage = new admin_externalpage(
    'blockmhaairs_resetcaches',
    new lang_string('resetcaches', 'block_mhaairs'),
    "$CFG->wwwroot/blocks/mhaairs/admin/resetcaches.php"
);
$ADMIN->add('blockmhaairsfolder', $externalpage);
