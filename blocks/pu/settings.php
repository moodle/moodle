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
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Robert Russo, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Set the string for use later.
$fn = new lang_string('foldername', 'block_pu');

// Create the folder / submenu.
$ADMIN->add('blocksettings', new admin_category('blockpufolder', $fn));

// Create the settings block.
$settings = new admin_settingpage($section, get_string('settings'));

// Make sure only admins see this one.
if ($ADMIN->fulltree) {
    // Default coupon codes per course
    $settings->add(
        new admin_setting_configtext(
            'block_pu_defaultcodes',
            get_string('default_numcodes', 'block_pu'),
            get_string('default_numcodes_help', 'block_pu'),
            2 // Default.
        )
    );

    // Copy File Settings.
    $settings->add(
        new admin_setting_configtext(
            'block_pu_copy_file',
            get_string('pu_copy_file', 'block_pu'),
            get_string('pu_copy_file_help', 'block_pu'),
            null // Default.
        )
    );

    // Coupon code filename.
    $settings->add(
        new admin_setting_configtext(
            'block_pu_ccfile',
            get_string('pu_ccfile', 'block_pu'),
            get_string('pu_ccfile_help', 'block_pu'),
            null // Default.
        )
    );

    // Guild mapping filename.
    $settings->add(
        new admin_setting_configtext(
            'block_pu_guildfile',
            get_string('pu_guildfile', 'block_pu'),
            get_string('pu_guildfile_help', 'block_pu'),
            null // Default.
        )
    );

    // ProctorU minimum number of lines in a GUILD file.
    $settings->add(
        new admin_setting_configtext(
            'block_pu_minlines',
            get_string('pu_minlines', 'block_pu'),
            get_string('pu_minlines_help', 'block_pu'),
            50 // Default.
        )
    );

    // Guild mapping sectionmap for UES.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_pu_sectionmap',
            get_string('pu_sectionmap', 'block_pu'),
            get_string('pu_sectionmap_help', 'block_pu'),
            0 // Default.
        )
    );

    // ProctorU coupon code admin.
    $settings->add(
        new admin_setting_configtext(
            'block_pu_code_admin',
            get_string('pu_code_admin', 'block_pu'),
            get_string('pu_code_admin_help', 'block_pu'),
            null // Default.
        )
    );

    // ProctorU minimum number of codes before an email.
    $settings->add(
        new admin_setting_configtext(
            'block_pu_mincodes',
            get_string('pu_mincodes', 'block_pu'),
            get_string('pu_mincodes_help', 'block_pu'),
            50 // Default.
        )
    );

    // Email profile fields.
    if (block_pu_helpers::get_user_profile_field_array()) {
        $settings->add(
            new admin_setting_configselect(
               'block_pu_profile_field',
                get_string('pu_profilefield', 'block_pu'),
                get_string('pu_profilefield_help', 'block_pu'),
                'pu_idnumber',
                block_pu_helpers::get_user_profile_field_array()
            )
        );
    }
}

// Add the folder.
$ADMIN->add('blockpufolder', $settings);

// Prevent Moodle from adding settings block in standard location.
$settings = null;

// Set the url for the ProctorU override tool.
$puoverride = new admin_externalpage(
    'manage_overrides',
    new lang_string('manage_overrides', 'block_pu'),
    "$CFG->wwwroot/blocks/pu/overrides.php"
);

// Set the url for the ProctorU validate tool.
$puinvalids = new admin_externalpage(
    'manage_invalids',
    new lang_string('manage_invalids', 'block_pu'),
    "$CFG->wwwroot/blocks/pu/validate.php"
);

// Set the url for the ProctorU file uploader.
$puuploader = new admin_externalpage(
    'manage_uploader',
    new lang_string('manage_uploader', 'block_pu'),
    "$CFG->wwwroot/blocks/pu/uploader.php"
);

// Set the url for the ProctorU file viewer.
$puviewer = new admin_externalpage(
    'manage_viewer',
    new lang_string('manage_viewer', 'block_pu'),
    "$CFG->wwwroot/blocks/pu/view.php"
);

// Add the ProctorU override tool url.
$context = \context_system::instance();

// Add the link for those who have access.
if (has_capability('block/pu:admin', $context)) {
    $ADMIN->add('blockpufolder', $puoverride);
    $ADMIN->add('blockpufolder', $puinvalids);
    $ADMIN->add('blockpufolder', $puuploader);
    $ADMIN->add('blockpufolder', $puviewer);
}
