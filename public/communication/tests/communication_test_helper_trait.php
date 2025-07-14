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

namespace core_communication;

/**
 * Trait communication_test_helper_trait to generate initial setup for communication providers.
 *
 * @package    core_communication
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait communication_test_helper_trait {
    /**
     * Setup necessary configs for communication subsystem.
     *
     * @return void
     */
    protected function setup_communication_configs(): void {
        set_config('enablecommunicationsubsystem', 1);
    }

    /**
     * Disable configs for communication subsystem.
     *
     * @return void
     */
    protected function disable_communication_configs(): void {
        set_config('enablecommunicationsubsystem', 0);
    }

    /**
     * Get or create course if it does not exist
     *
     * @param string $roomname The room name for the communication api
     * @param string $provider The selected provider
     * @return \stdClass
     */
    protected function get_course(
        string $roomname = 'Sampleroom',
        string $provider = 'communication_matrix',
        array $extrafields = [],
    ): \stdClass {

        $this->setup_communication_configs();
        $records = [
            'selectedcommunication' => $provider,
            'communicationroomname' => $roomname,
        ];

        return $this->getDataGenerator()->create_course(array_merge($records, $extrafields));
    }

    /**
     * Get or create user if it does not exist.
     *
     * @param string $firstname The user's firstname for the communication api
     * @param string $lastname The user's lastname for the communication api
     * @param string $username The user's username for the communication api
     * @return \stdClass
     */
    protected function get_user(
        string $firstname = 'Samplefn',
        string $lastname = 'Sampleln',
        string $username = 'sampleun'
    ): \stdClass {

        $this->setup_communication_configs();
        $records = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'username' => $username,
        ];

        return $this->getDataGenerator()->create_user($records);
    }


    /**
     * Create a stored_file in a draft file area from a fixture file.
     *
     * @param string $filename The file name within the communication/tests/fixtures folder.
     * @param string $storedname The name to use in the database.
     * @return \stored_file
     */
    protected function create_communication_file(
        string $filename,
        string $storedname,
    ): \stored_file {
        global $CFG;

        $fs = get_file_storage();

        $itemid = file_get_unused_draft_itemid();
        return $fs->create_file_from_pathname((object) [
            'contextid' => \context_system::instance()->id,
            'component' => 'user',
            'filearea' => 'draftfile',
            'itemid' => $itemid,
            'filepath' => '/',
            'filename' => $storedname,
        ], "{$CFG->dirroot}/communication/tests/fixtures/{$filename}");
    }

    /**
     * Helper to execute a particular task.
     *
     * @param string $task The task.
     */
    private function execute_task(string $task): void {
        // Run the scheduled task.
        ob_start();
        $task = \core\task\manager::get_scheduled_task($task);
        $task->execute();
        ob_end_clean();
    }
}
