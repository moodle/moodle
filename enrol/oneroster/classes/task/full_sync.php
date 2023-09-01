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
 * One Roster Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\task;

use enrol_oneroster\client_helper;
use text_progress_trace;

/**
 * One Roster Client - Full synchronisation task.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class full_sync extends \core\task\scheduled_task {

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('fullsync', 'enrol_oneroster');
    }

    /**
     * Run task for synchronising users.
     */
    public function execute() {
        $trace = new text_progress_trace();
        if (!enrol_is_enabled('oneroster')) {
            $trace->output('One Roster not enabled');
            return;
        }

        $config = get_config('enrol_oneroster');

        $client = client_helper::get_client(
            $config->oauth_version,
            $config->oneroster_version,
            $config->token_url,
            $config->root_url,
            $config->clientid,
            $config->secret
        );

        $client->set_trace($trace);
        $client->authenticate();
        $client->synchronise();
    }
}
