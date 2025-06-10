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
 * Scheduled task to refresh the system API user's refresh token.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\task;

use local_o365\httpclient;
use local_o365\oauth2\clientdata;
use local_o365\rest\unified;
use local_o365\utils;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task to refresh the system API user's refresh token.
 */
class refreshsystemrefreshtoken extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_refreshsystemrefreshtoken', 'local_o365');
    }

    /**
     * Attempt token refresh.
     */
    public function execute() {
        if (utils::is_connected() !== true) {
            return false;
        }

        $httpclient = new httpclient();
        $clientdata = clientdata::instance_from_oidc();
        $graphresource = unified::get_tokenresource();
        $systemtoken = utils::get_app_or_system_token($graphresource, $clientdata, $httpclient, false, false);
        if (!empty($systemtoken)) {
            mtrace('... Success!');
        } else {
            mtrace('... !!! Could not refresh token. !!!');
        }
        return true;
    }
}
