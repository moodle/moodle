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
 * This tool can upgrade old assignment activities to the new assignment activity type
 *
 * The upgrade can be done on any old assignment instance providing it is using one of the core
 * assignment subtypes (online text, single upload, etc).
 * The new assignment module was introduced in Moodle 2.3 and although it completely reproduces
 * the features of the existing assignment type it wasn't designed to replace it entirely as there
 * are many custom assignment types people use and it wouldn't be practical to try to convert them.
 * Instead the existing assignment type will be left in core and people will be encouraged to
 * use the new assignment type.
 *
 * This screen is the main entry-point to the plugin, it gives the admin a list
 * of options available to them.
 *
 * @package    tool_assignmentupgrade
 * @copyright  2012 NetSpot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/'.$CFG->admin.'/tool/assignmentupgrade/locallib.php');

// This calls require_login and checks moodle/site:config.
admin_externalpage_setup('assignmentupgrade');

$renderer = $PAGE->get_renderer('tool_assignmentupgrade');

$actions = array();

$header = get_string('pluginname', 'tool_assignmentupgrade');
$actions[] = tool_assignmentupgrade_action::make('listnotupgraded');

echo $renderer->index_page($header, $actions);
