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
     * Get or create course if it does not exist
     *
     * @param string $roomname The room name for the communication api
     * @param string $provider The selected provider
     * @return \stdClass
     */
    protected function get_course(
        string $roomname = 'Sampleroom',
        string $provider = 'communication_matrix'
    ): \stdClass {

        $this->setup_communication_configs();
        $records = [
            'selectedcommunication' => $provider,
            'communicationroomname' => $roomname,
        ];

        return $this->getDataGenerator()->create_course($records);
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
            'username' => $username
        ];

        return $this->getDataGenerator()->create_user($records);
    }
}
