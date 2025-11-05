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
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\task;

use core\task\scheduled_task;
use tool_ally\content_processor;
use tool_ally\local_content;
use tool_ally\models\component_content;
use tool_ally\push_config;
use tool_ally\event_handlers;
use tool_ally\push_content_updates;
use moodle_exception;

use stdClass;

/**
 * Content updates task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_updates_task extends scheduled_task {
    /**
     * @var push_config
     */
    public $config;

    /**
     * @var bool
     */
    private $clionly;

    /**
     * @var push_content_updates
     */
    public $updates;

    public function get_name() {
        return get_string('contentupdatestask', 'tool_ally');
    }

    public function execute() {
        $config = $this->config ?: new push_config();
        if (!$config->is_valid()) {
            return;
        }
        $time = clean_param(get_config('tool_ally', 'push_content_timestamp'), PARAM_INT);

        if (empty($time)) {
            // First time running or reset.  Since this pushes content updates and this is first time, then we have no update
            // window. So, set time and wait till next task execution.
            $this->set_push_content_timestamp(time());
            return;
        }

        $this->clionly = $config->is_cli_only();

        $this->updates = $this->updates ?: new push_content_updates($config);

        // Push deleted files.
        $this->push_deletes($config);

        // Push content updates.
        $this->push_content_updates($config);

        // Set push content timestamp - even if we've had failures we can still do this as for content
        // we don't use the push content timestamp to limit the rows.
        $this->set_push_content_timestamp(time());
    }

    /**
     * Push content deletions to Ally.
     *
     * @param push_config $config
     * @param push_content_updates $updates
     */
    private function push_deletes(push_config $config) {
        global $DB;

        // Delete previously processed deletions.
        $DB->delete_records_select('tool_ally_deleted_content', 'timeprocessed IS NOT NULL');

        $ids     = [];
        $payload = [];
        $deletes = $DB->get_recordset('tool_ally_deleted_content', ['timeprocessed' => null], 'id');

        while ($deletes->valid()) {
            $todelete = $deletes->current();
            $deletes->next();

            $ids[]     = $todelete->id;

            // Note - always use FORMAT_HMTL for deletes. Once something is deleted we have no idea what it's format
            // is, so just go with FORMAT_HTML.
            $content = new component_content(
                    $todelete->comprowid, $todelete->component, $todelete->comptable, $todelete->compfield,
                    $todelete->courseid, $todelete->timedeleted, FORMAT_HTML, '');
            $payload[] = $content;

            // Check to see if we have our batch size or if we are at the last file.
            if (count($payload) >= $config->get_batch_size() || !$deletes->valid()) {
                $sendsuccess = content_processor::push_update($this->updates, $payload, event_handlers::API_RICH_CNT_DELETED);
                if (!$sendsuccess) {
                    // Send failures occurred, might as well switch on cli only mode to avoid slowness on front end.
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

                // Successfully sent, update deleted content.
                list ($insql, $params) = $DB->get_in_or_equal($ids);
                $params = array_merge([time()], $params);
                $updatesql = "UPDATE {tool_ally_deleted_content}
                                 SET timeprocessed = ?
                                 WHERE id $insql";
                $DB->execute($updatesql, $params);

                // Reset arrays for next payload.
                $ids     = [];
                $payload = [];
            }
        }
        $deletes->close();
    }

    private function failed_attempt_string(stdClass $queuerow) {
        $str = $queuerow->component.' table '.
                $queuerow->comptable.' field '.$queuerow->compfield.' with id '.$queuerow->comprowid.
                ' attempt number '.$queuerow->attempts;
        return $str;
    }

    /**
     * Push content updates.
     *
     * @param push_config $config
     */
    private function push_content_updates(push_config $config) {
        global $DB;

        $ids     = [];
        $payload = [];
        $queue = $DB->get_recordset('tool_ally_content_queue', null, 'id');

        // 100,000 records = 47mb approx.
        // http://sandbox.onlinephpfunctions.com/code/7b2fd36221a01f59507cbf0a67867454a82bbc74 < link to test mb.
        if ($DB->count_records('tool_ally_deleted_content') < 100000) {
            $sql = "SELECT ".$DB->sql_concat_join("'_'", ['comptable', 'compfield', 'comprowid'])."
                  FROM {tool_ally_deleted_content}";
            $deleted = $DB->get_records_sql($sql);
        } else {
            // Will have to get 1 record at a time if there's any content issues later on.
            $deleted = [];
        }

        while ($queue->valid()) {
            $queuerow = $queue->current();
            $queue->next();

            try {
                $content = local_content::get_html_content(
                    $queuerow->comprowid, $queuerow->component, $queuerow->comptable, $queuerow->compfield,
                    $queuerow->courseid);
            } catch (moodle_exception $e) {
                $content = null;
            }
            if ($content === null) {
                if ($deleted === []) {
                    // There were too many deletion records to dump into an array so we need to get individual deletion
                    // records to check for deletion. This isn't great for performance but we should only need this when
                    // a) we fail to get content because it's been deleted and b) there are too many deletion records to
                    // use an array.
                    if ($DB->get_record('tool_ally_deleted_content', [
                        'comptable' => $queuerow->comptable,
                        'compfield' => $queuerow->compfield,
                        'comprowid' => $queuerow->comprowid
                    ])) {
                        // Content definitely deleted.
                        $DB->delete_records('tool_ally_content_queue', ['id' => $queuerow->id]);
                        continue;
                    }
                } else if (isset($deleted[$queuerow->comptable . '_' . $queuerow->compfield . '_' . $queuerow->comprowid])) {
                    // Content definitely deleted.
                    $DB->delete_records('tool_ally_content_queue', ['id' => $queuerow->id]);
                    continue;
                }
                // Content likely to be deleted and purged from deletion queue.
                $queuerow->attempts++;
                $msg = 'Failed to get content for component '.$this->failed_attempt_string($queuerow);
                mtrace($msg);
                if ($queuerow->attempts > 10) {
                    // Tried 10 times to process this, let's call it a day and delete the record.
                    $DB->delete_records('tool_ally_content_queue', ['id' => $queuerow->id]);
                    continue;
                }
                $DB->update_record('tool_ally_content_queue', $queuerow);
                continue;
            }

            $ids[]     = $queuerow->id;
            $payload[] = $content;

            // Check to see if we have our batch size or if we are at the last file.
            if (count($payload) >= $config->get_batch_size() || !$queue->valid()) {
                $sendsuccess = content_processor::push_update($this->updates, $payload, $queuerow->eventname);
                if (!$sendsuccess) {
                    // Send failures occurred, might as well switch on cli only mode to avoid slowness on front end.
                    set_config('push_cli_only', 1, 'tool_ally');
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
                $DB->delete_records_list('tool_ally_content_queue', 'id', $ids);

                // Reset arrays for next payload.
                $ids     = [];
                $payload = [];
            }
        }
        $queue->close();
    }

    /**
     * Save push timestamp.  This is just used to record the last time the cron pushed content.
     *
     * @param int $timestamp
     */
    private function set_push_content_timestamp($timestamp) {
        if (!empty($timestamp)) {
            set_config('push_content_timestamp', $timestamp, 'tool_ally');
        }
    }
}
