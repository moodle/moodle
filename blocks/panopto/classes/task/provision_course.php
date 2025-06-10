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
 * The provision course class for Panopto.
 *
 * @package block_panopto
 * @copyright Panopto 2009 - 2016 /With contributions from Spenser Jones (sjones@ambrose.edu),
 * Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_panopto\task;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../lib/panopto_data.php');

/**
 * Panopto "provision course" task.
 *
 * @copyright Panopto 2009 - 2016 /With contributions from Spenser Jones (sjones@ambrose.edu),
 * Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provision_course extends \core\task\adhoc_task {
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
        try {
            $eventdata = (array) $this->get_custom_data();

            $panopto = new \panopto_data($eventdata['courseid']);

            $targetserver = panopto_get_target_panopto_server();
            $panopto->servername = $targetserver->name;
            $panopto->applicationkey = $targetserver->appkey;
            $provisioninginfo = $panopto->get_provisioning_info();
            $provisioneddata = $panopto->provision_course($provisioninginfo, false);
        } catch (Exception $e) {
            \panopto_data::print_log($e->getMessage());
        }
    }
}
