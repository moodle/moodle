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
 * Restore implementation for the (tool_log) logstore_database subplugin.
 *
 * @package    logstore_database
 * @category   backup
 * @copyright  2015 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class restore_logstore_database_subplugin extends restore_tool_log_logstore_subplugin {

    /**
     * @var moodle_database the external database.
     */
    private static $extdb = null;

    /**
     * @var string the external database table name.
     */
    private static $extdbtablename = null;

    /**
     * The constructor for this logstore.
     *
     * @param string $subplugintype the subplugin type.
     * @param string $subpluginname the subplugin name.
     * @param restore_structure_step $step.
     */
    public function __construct($subplugintype, $subpluginname, $step) {
        // Check that the logstore is enabled before setting variables.
        $enabledlogstores = explode(',', get_config('tool_log', 'enabled_stores'));
        if (in_array('logstore_database', $enabledlogstores)) {
            $manager = new \tool_log\log\manager();
            $store = new \logstore_database\log\store($manager);
            self::$extdb = $store->get_extdb();
            self::$extdbtablename = $store->get_config_value('dbtable');
        }

        parent::__construct($subplugintype, $subpluginname, $step);
    }

    /**
     * Returns the subplugin structure to attach to the 'logstore' XML element.
     *
     * @return restore_path_element[] array of elements to be processed on restore.
     */
    protected function define_logstore_subplugin_structure() {
        // If the logstore is not enabled we don't add structures for it.
        $enabledlogstores = explode(',', get_config('tool_log', 'enabled_stores'));
        if (!in_array('logstore_database', $enabledlogstores)) {
            return array(); // The logstore is not enabled, nothing to restore.
        }

        $paths = array();

        $elename = $this->get_namefor('log');
        $elepath = $this->get_pathfor('/logstore_database_log');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths;
    }

    /**
     * Process logstore_database_log entries.
     *
     * This method proceeds to read, complete, remap and, finally,
     * discard or save every log entry.
     *
     * @param array() $data log entry.
     * @return null if we are not restoring the log.
     */
    public function process_logstore_database_log($data) {
        // Do not bother processing if we can not add it to a database.
        if (!self::$extdb || !self::$extdbtablename) {
            return;
        }

        $data = $this->process_log($data, get_config('logstore_database', 'jsonformat'));

        if ($data) {
            self::$extdb->insert_record(self::$extdbtablename, $data);
        }
    }
}
