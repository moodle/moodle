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
 * File updates task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\task;

use core\task\scheduled_task;
use tool_ally\local_course;
use tool_ally\push_config;
use tool_ally\push_course_updates;

/**
 * File updates task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_updates_task extends scheduled_task {
    /**
     * @var push_config
     */
    public $config;

    /**
     * @var push_course_updates
     */
    public $updates;

    /**
     * @var bool
     */
    private $clionly;

    /**
     * {@inheritdoc}
     */
    public function get_name() {
        return get_string('courseupdatestask', 'tool_ally');
    }

    /**
     * {@inheritdoc}
     */
    public function execute() {
        $config = $this->config ?: new push_config();
        if (!$config->is_valid()) {
            return;
        }

        if (empty(get_config('tool_ally', 'deferredcourseevents'))) {
            return;
        }

        $updates = $this->updates ?: new push_course_updates($config);

        $this->clionly = $config->is_cli_only();

        // Push file updates.
        $pushupdatesok = $this->push_updates($config, $updates);
        if (!$pushupdatesok) {
            return;
        }
    }

    /**
     * Push course updates to Ally.
     *
     * @param push_config $config
     * @param push_course_updates $updates
     * @throws \Exception
     * @return bool
     */
    private function push_updates(push_config $config, push_course_updates $updates) {
        global $DB;

        $ids     = [];
        $payload = [];
        $events = $DB->get_recordset('tool_ally_course_event', null, 'id');

        while ($events->valid()) {
            $event = $events->current();
            $events->next();

            $ids[]     = $event->id;
            $payload[] = local_course::to_crud($event);

            // Check to see if we have our batch size or if we are at the last file.
            if (count($payload) >= $config->get_batch_size() || !$events->valid()) {
                $sendsuccess = $updates->send($payload);
                if (!$sendsuccess) {
                    // Failed to send, might as well switch on cli only mode to avoid slowness on front end.
                    set_config('push_cli_only', 1, 'tool_ally');
                    set_config('push_cli_only_on', time(), 'tool_ally');
                    $this->clionly = true;
                    // Reset arrays for next payload.
                    $ids     = [];
                    $payload = [];
                    continue; // This send wasn't successful, try with next payload batch.
                }

                if ($this->clionly) {
                    // Successful send, enable live push updates.
                    set_config('push_cli_only', 0, 'tool_ally');
                    set_config('push_cli_only_off', time(), 'tool_ally');
                    $this->clionly = false;
                }

                // Successfully sent, remove.
                $DB->delete_records_list('tool_ally_course_event', 'id', $ids);

                // Reset arrays for next payload.
                $ids     = [];
                $payload = [];
            }
        }
        $events->close();
    }
}
