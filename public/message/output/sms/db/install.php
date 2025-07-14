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
 * Installation code for the SMS message processor.
 *
 * @package    message_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Add the records for SMS message processor.
 *
 * @return bool
 */
function xmldb_message_sms_install(): bool {
    // Insert the processor record for sms.
    global $DB;
    $provider = new stdClass();
    $provider->name  = 'sms';
    $DB->insert_record('message_processors', $provider);

    // Keep the plugin disabled by default.
    $class = \core_plugin_manager::resolve_plugininfo_class('message');
    $class::enable_plugin($provider->name, 0);

    return true;
}
