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
 * This page lets users manage default purposes and categories.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/dataprivacy/lib.php');

require_login(null, false);

$url = new \moodle_url('/admin/tool/dataprivacy/defaults.php');
$title = get_string('setdefaults', 'tool_dataprivacy');

\tool_dataprivacy\page_helper::setup($url, $title, 'dataregistry');

$mode = optional_param('mode', CONTEXT_COURSECAT, PARAM_INT);
$classname = context_helper::get_class_for_level($mode);
list($purposevar, $categoryvar) = \tool_dataprivacy\data_registry::var_names_from_context($classname);
$purpose = get_config('tool_dataprivacy', $purposevar);
$category = get_config('tool_dataprivacy', $categoryvar);

$otherdefaults = [];
if ($mode == CONTEXT_MODULE) {
    // Get activity module plugin info.
    $pluginmanager = core_plugin_manager::instance();
    $modplugins = $pluginmanager->get_enabled_plugins('mod');

    foreach ($modplugins as $name) {
        list($purposevar, $categoryvar) = \tool_dataprivacy\data_registry::var_names_from_context($classname, $name);
        $plugincategory = get_config('tool_dataprivacy', $categoryvar);
        $pluginpurpose = get_config('tool_dataprivacy', $purposevar);
        if ($plugincategory === false && $pluginpurpose === false) {
            // If no purpose and category has been set for this plugin, then there's no need to show this on the list.
            continue;
        }

        $displayname = $pluginmanager->plugin_name('mod_' . $name);
        $otherdefaults[$name] = (object)[
            'name' => $displayname,
            'category' => $plugincategory,
            'purpose' => $pluginpurpose,
        ];
    }
}

$defaultspage = new \tool_dataprivacy\output\defaults_page($mode, $category, $purpose, $otherdefaults, true);

$output = $PAGE->get_renderer('tool_dataprivacy');
echo $output->header();
echo $output->heading($title);
echo $output->render_from_template('tool_dataprivacy/defaults_page', $defaultspage->export_for_template($output));
echo $output->footer();
