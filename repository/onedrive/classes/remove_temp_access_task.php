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
 * A scheduled task.
 *
 * @package    repository_onedrive
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_onedrive;

use \core\task\scheduled_task;
use DateTime;
use DateInterval;
use repository_exception;
use \core\oauth2\rest_exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/repository/lib.php');

/**
 * Simple task to delete temporary permission records.
 * @package    repository_onedrive
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remove_temp_access_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('removetempaccesstask', 'repository_onedrive');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        $accessrecords = access::get_records();
        $expires = new DateTime();
        $expires->sub(new DateInterval("P7D"));
        $timestamp = $expires->getTimestamp();

        $issuerid = get_config('onedrive', 'issuerid');
        $issuer = \core\oauth2\api::get_issuer($issuerid);

        // Add the current user as an OAuth writer.
        $systemauth = \core\oauth2\api::get_system_oauth_client($issuer);

        if ($systemauth === false) {
            $details = 'Cannot connect as system user';
            throw new repository_exception('errorwhilecommunicatingwith', 'repository', '', $details);
        }
        $systemservice = new \repository_onedrive\rest($systemauth);

        foreach ($accessrecords as $access) {
            if ($access->get('timemodified') < $timestamp) {
                $params = ['permissionid' => $access->get('permissionid'), 'itemid' => $access->get('itemid')];
                try {
                    $systemservice->call('delete_permission', $params);
                } catch (rest_exception $re) {
                    // We log and give up here or we will always fail for eternity.
                    mtrace('Failed to remove access from file: ' . $access->get('itemid'));
                }
                $access->delete();
            }
        }
    }

}
