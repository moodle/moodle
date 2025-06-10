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
 * Push file updates.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

defined('MOODLE_INTERNAL') || die();

use tool_ally\logging\logger;

global $CFG;

require_once($CFG->libdir.'/filelib.php');

/**
 * Push file updates.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class push_content_updates extends push_updates {

    public function handle_send_error(\Exception $e) {
        $climode = get_config('tool_ally', 'push_cli_only');
        // Too many errors, ensure it only runs on cli.
        set_config('push_cli_only', 1, 'tool_ally');
        set_config('push_cli_only_on', time(), 'tool_ally');

        if ($climode) {
            $msg = 'logger:pushcontentliveskip';
        } else {
            $msg = 'logger:pushcontenterror';
        }

        $context['_explanation'] = $msg.'_exp';
        $context['_exception'] = $e;
        logger::get()->error($msg, $context);
    }

    public function on_send_success(array $context) {
        logger::get()->info('logger:pushcontentsuccess', $context);
    }
}
