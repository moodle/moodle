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
 * Backup implementation for the (tool_log) logstore_database subplugin.
 *
 * @package    logstore_database
 * @category   backup
 * @copyright  2015 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once('backup_logstore_database_nested_element.php');

class backup_logstore_database_subplugin extends backup_tool_log_logstore_subplugin {

    /**
     * Returns the subplugin structure to attach to the 'logstore' XML element.
     *
     * @return backup_subplugin_element the subplugin structure to be attached.
     */
    protected function define_logstore_subplugin_structure() {
        $subplugin = $this->get_subplugin_element();
        $subpluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Create the custom (base64 encoded, xml safe) 'other' final element.
        $otherelement = new base64_encode_final_element('other');

        $subpluginlog = new backup_logstore_database_nested_element('logstore_database_log', array('id'), array(
            'eventname', 'component', 'action', 'target', 'objecttable',
            'objectid', 'crud', 'edulevel', 'contextid', 'userid', 'relateduserid',
            'anonymous', $otherelement, 'timecreated', 'ip', 'realuserid'));

        $subplugin->add_child($subpluginwrapper);
        $subpluginwrapper->add_child($subpluginlog);

        // Get the details for the external database.
        $manager = new \tool_log\log\manager();
        $store = new \logstore_database\log\store($manager);
        $extdb = $store->get_extdb();

        if (!$extdb) {
            return false;
        }

        $subpluginlog->set_source_db($extdb);
        $subpluginlog->set_source_table($store->get_config_value('dbtable'), array('contextid' => backup::VAR_CONTEXTID));

        return $subplugin;
    }
}
