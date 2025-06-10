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
 * The sync user class for Panopto, syncs user permissions with Panopto server, called on unenrollment event.
 *
 * @package block_panopto
 * @copyright Panopto 2009 - 2016
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_panopto\task;
defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '/../../lib/panopto_data.php');

/**
 * Panopto "sync user login" task. This task syncs a user with all targeted Panopto servers.
 *
 * @copyright Panopto 2009 - 2016 /With contributions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_user_login extends \core\task\adhoc_task {
    /**
     * The the parent component for this class
     */
    public function get_component() {
        return 'block_panopto';
    }

    /**
     * The main execution function of the class
     */
    public function execute() {
        global $DB;

        try {
            $eventdata = (array) $this->get_custom_data();

            if (empty($eventdata['targetserver'])) {
                $targetserver = panopto_get_target_panopto_server();
            } else {
                $targetserver = $eventdata['targetserver'];
            }

            $serverpanopto = new \panopto_data(null);
            $serverpanopto->applicationkey = $targetserver->appkey;
            $serverpanopto->servername = $targetserver->name;

            // Sync the user to all courses mapped to the server.
            $serverpanopto->sync_external_user($eventdata['userid']);
        } catch (Exception $e) {
            \panopto_data::print_log($e->getMessage());
        }
    }
}
