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
 * Manage penalty plugins
 *
 * @package   core_grades
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_grades\table\gradepenalty_management_table;
use core\notification;
use core\url;

require_once('../../config.php');
require_once('../../course/lib.php');
require_once("$CFG->libdir/adminlib.php");
require_once("$CFG->libdir/tablelib.php");

admin_externalpage_setup('managepenaltyplugins');

$plugin = optional_param('plugin', '', PARAM_PLUGIN);
$action = optional_param('action', '', PARAM_ALPHA);

// If Javascript is disabled, we need to handle the form submission.
if (!empty($action) && !empty($plugin) && confirm_sesskey()) {
    $manager = core_plugin_manager::resolve_plugininfo_class('gradepenalty');
    $pluginname = get_string('pluginname', 'gradepenalty_' . $plugin);

    if ($action === 'disable' && $manager::enable_plugin($plugin, 0)) {
        notification::add(
            get_string('plugin_disabled', 'core_admin', $pluginname),
            notification::SUCCESS
        );
        admin_get_root(true, false);
    } else if ($action === 'enable' && $manager::enable_plugin($plugin, 1)) {
        notification::add(
            get_string('plugin_enabled', 'core_admin', $pluginname),
            notification::SUCCESS
        );

        admin_get_root(true, false);
    }

    // Redirect back to the settings page.
    redirect(new url('/grade/penalty/manage_penalty_plugins.php'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("gradepenalty", 'core_grades'));
$table = new gradepenalty_management_table();
$table->out();
echo $OUTPUT->footer();
