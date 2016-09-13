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
 * @package    contrib
 * @subpackage block_iomad_progress
 * @copyright  2012 Enovation Solutions, Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Backup task for the Progress Bar block
 *
 * @package    contrib
 * @subpackage block_iomad_progress
 * @copyright  2012 Enovation Solutions, Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_iomad_progress_block_task extends restore_block_task {

    /**
     * Translates the backed up configuration data for the target course modules.
     *
     * @global type $DB
     */
    public function after_restore() {
        global $DB;
        $prefixes = array('monitor_', 'date_time_', 'action_', 'locked_');

        // Get the blockid.
        $id = $this->get_blockid();

        // Get restored course id.
        $courseid = $this->get_courseid();

        if ($configdata = $DB->get_field('block_instances', 'configdata', array('id' => $id))) {
            $config = (array)unserialize(base64_decode($configdata));
            $newconfig = $config;

            // Filter module related config information.
            foreach ($newconfig as $key => $value) {
                foreach ($prefixes as $prefix) {
                    if (substr($key, 0, strlen($prefix)) === $prefix) {
                        unset($newconfig[$key]);
                    }
                }
            }

            // Translate the old config information to the target course values.
            foreach ($config as $key => $value) {
                $matches = array();
                preg_match('/monitor_(\D+)(\d+)/', $key, $matches);
                if (!empty($matches)) {
                    $module = $matches[1];
                    $instance = $matches[2];

                    // Find the mapped instance ID.
                    if ($newinstance = restore_dbops::get_backup_ids_record($this->get_restoreid(), $module, $instance)) {
                        $newinstanceid = $newinstance->newitemid;

                        // Translate new instance values from old IDs.
                        foreach ($prefixes as $prefix) {
                            if (isset($config["$prefix$module$instance"])) {
                                $newconfig["$prefix$module$newinstanceid"] = $config["$prefix$module$instance"];
                            }
                        }
                    }
                }
            }

            // Save everything back to DB.
            $configdata = base64_encode(serialize((object)$newconfig));
            $DB->set_field('block_instances', 'configdata', $configdata, array('id' => $id));
        }
    }

    /**
     * There are no unusual settings for this restore
     */
    protected function define_my_settings() {
    }

    /**
     * There are no unusual steps for this restore
     */
    protected function define_my_steps() {
    }

    /**
     * There are no files associated with this block
     *
     * @return array An empty array
     */
    public function get_fileareas() {
        return array();
    }

    /**
     * There are no specially encoded attributes
     *
     * @return array An empty array
     */
    public function get_configdata_encoded_attributes() {
        return array();
    }

    /**
     * There is no coded content in the backup
     *
     * @return array An empty array
     */
    static public function define_decode_contents() {
        return array();
    }

    /**
     * There are no coded links in the backup
     *
     * @return array An empty array
     */
    static public function define_decode_rules() {
        return array();
    }

}
