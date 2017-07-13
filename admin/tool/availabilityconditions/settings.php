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
 * Adds settings links to admin tree.
 *
 * @package tool_availabilityconditions
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig && !empty($CFG->enableavailability)) {
    $ADMIN->add('modules', new admin_category('availabilitysettings',
            new lang_string('type_availability_plural', 'plugin')));
    $ADMIN->add('availabilitysettings', new admin_externalpage('manageavailability',
            new lang_string('manageplugins', 'tool_availabilityconditions'),
            $CFG->wwwroot . '/' . $CFG->admin . '/tool/availabilityconditions/'));
    foreach (core_plugin_manager::instance()->get_plugins_of_type('availability') as $plugin) {
        /** @var \core\plugininfo\format $plugin */
        $plugin->load_settings($ADMIN, 'availabilitysettings', $hassiteconfig);
    }
}
