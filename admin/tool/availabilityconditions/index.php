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
 * Provides an overview of installed availability conditions.
 *
 * You can also enable/disable them from this screen.
 *
 * @package tool_availabilityconditions
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

admin_externalpage_setup('manageavailability');

// Get sorted list of all availability condition plugins.
$plugins = [];
foreach (core_component::get_plugin_list('availability') as $plugin => $plugindir) {
    if (get_string_manager()->string_exists('pluginname', 'availability_' . $plugin)) {
        $strpluginname = get_string('pluginname', 'availability_' . $plugin);
    } else {
        $strpluginname = $plugin;
    }
    $plugins[$plugin] = $strpluginname;
}
core_collator::asort($plugins);

// Do plugin actions.
$pageurl = new moodle_url('/' . $CFG->admin . '/tool/availabilityconditions/');
$classavailability = \core_plugin_manager::resolve_plugininfo_class('availability');
if (($plugin = optional_param('plugin', '', PARAM_PLUGIN))) {
    require_sesskey();
    if (!array_key_exists($plugin, $plugins)) {
        throw new \moodle_exception('invalidcomponent', 'error', $pageurl);
    }
    $action = optional_param('action', '', PARAM_ALPHA);
    if ($action === 'hide' && $classavailability::enable_plugin($plugin, 0)) {
        \core\notification::add(
            \core\notification::SUCCESS
        );
    } else if ($action === 'show' && $classavailability::enable_plugin($plugin, 1)) {
        \core\notification::add(
            \core\notification::SUCCESS
        );
    }

    $displaymode = optional_param('displaymode', '', PARAM_ALPHA);
    switch ($displaymode) {
        case 'hide' :
            $classavailability::update_display_mode($plugin, false);
            break;
        case 'show' :
            $classavailability::update_display_mode($plugin, true);
            break;
    }

    // Always redirect back after an action.
    redirect($pageurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manageplugins', 'availability'));

$table = new \core_admin\table\availability_management_table();
$table->out();
echo $OUTPUT->footer();
