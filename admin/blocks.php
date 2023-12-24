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
 * Allows the admin to configure blocks (hide/show, uninstall and configure)
 *
 * @package   core_admin
 * @copyright 2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once("{$CFG->libdir}/adminlib.php");
require_once("{$CFG->libdir}/blocklib.php");
require_once("{$CFG->libdir}/tablelib.php");

admin_externalpage_setup('manageblocks');

$plugin = optional_param('plugin', '', PARAM_PLUGIN);
$action = optional_param('action', '', PARAM_ALPHA);
$unprotect = optional_param('unprotect', 0, PARAM_PLUGIN);
$protect = optional_param('protect', 0, PARAM_PLUGIN);

$strmanageblocks = get_string('manageblocks');

// If data submitted, then process and store.
if (!empty($plugin) && !empty($action) && confirm_sesskey()) {
    $manager = \core_plugin_manager::resolve_plugininfo_class('block');
    $pluginname = get_string('pluginname', "block_{$plugin}");

    if ($action === 'disable' && $manager::enable_plugin($plugin, 0)) {
        \core\notification::add(
            get_string('plugin_disabled', 'core_admin', $pluginname),
            \core\notification::SUCCESS
        );
        // Settings not required - only pages.
        admin_get_root(true, false);
    } else if ($action === 'enable' && $manager::enable_plugin($plugin, 1)) {
        \core\notification::add(
            get_string('plugin_enabled', 'core_admin', $pluginname),
            \core\notification::SUCCESS
        );

        // Settings not required - only pages.
        admin_get_root(true, false);
    }

    // Redirect back to the page with out any params.
    redirect(new moodle_url('/admin/blocks.php'));
}

if (!empty($protect) && confirm_sesskey()) {
    block_manager::protect_block($protect);
    $pluginname = get_string('pluginname', "block_{$protect}");
    \core\notification::add(
        get_string('blockprotected', 'core_admin', $pluginname),
        \core\notification::SUCCESS
    );
    // Settings not required - only pages.
    admin_get_root(true, false);
}

if (!empty($unprotect) && confirm_sesskey()) {
    block_manager::unprotect_block($unprotect);
    $pluginname = get_string('pluginname', "block_{$unprotect}");
    \core\notification::add(
        get_string('blockunprotected', 'core_admin', $pluginname),
        \core\notification::SUCCESS
    );
    // Settings not required - only pages.
    admin_get_root(true, false);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($strmanageblocks);
echo $OUTPUT->notification(get_string('noteunneededblocks', 'admin'), 'info', false);

// Print the table of all blocks.
$table = new \core_admin\table\block_management_table();
$table->out();
echo $OUTPUT->footer();
