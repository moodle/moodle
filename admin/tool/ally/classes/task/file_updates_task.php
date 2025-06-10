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
use tool_ally\local_file;
use tool_ally\push_config;
use tool_ally\push_file_updates;

/**
 * File updates task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_updates_task extends scheduled_task {
    /**
     * @var push_config
     */
    public $config;

    /**
     * @var push_file_updates
     */
    public $updates;

    /**
     * @var bool
     */
    private $clionly;

    public function get_name() {
        return get_string('fileupdatestask', 'tool_ally');
    }

    public function execute() {
        $config = $this->config ?: new push_config();
        if (!$config->is_valid()) {
            return;
        }
        $time = clean_param(get_config('tool_ally', 'push_timestamp'), PARAM_INT);

        if (empty($time)) {
            // First time running or reset.  Since this pushes file updates and this is first time, then we have no update
            // window. So, set time and wait till next task execution.
            $this->set_push_timestamp(time());
            return;
        }

        $updates = $this->updates ?: new push_file_updates($config);

        $this->clionly = $config->is_cli_only();

        // Push file updates.
        $pushupdatesok = $this->push_updates($config, $updates, $time);
        if (!$pushupdatesok) {
            return;
        }

        // Process any file in use changes that need to be checked.
        $this->mark_block_files_needing_updating($time);
        $this->process_file_in_use_updates();

        // Push deleted files.
        $this->push_deletes($config, $updates);
    }

    /**
     * Push file updates to Ally.
     *
     * @param push_config $config
     * @param push_file_updates $updates
     * @param int $time Push updates for files that have been modified after this time
     * @throws \Exception
     * @return bool
     */
    private function push_updates(push_config $config, push_file_updates $updates, $time) {
        global $CFG;

        $files      = local_file::iterator()->since($time)->sort_by('timemodified');
        $payload    = [];
        $timetosave = 0;

        try {
            $files->rewind();
            while ($files->valid()) {
                $file = $files->current();
                $files->next();

                $payload[] = local_file::to_crud($file);

                // Check to see if we have our batch size or if we are at the last file.
                if (count($payload) >= $config->get_batch_size() || !$files->valid()) {
                    $sendsuccess = $updates->send($payload);
                    if (!$sendsuccess) {
                        // Failed to send, might as well switch on cli only mode to avoid slowness on front end.
                        set_config('push_cli_only', 1, 'tool_ally');
                        set_config('push_cli_only_on', time(), 'tool_ally');
                        $this->clionly = true;
                        // Give up at this point.
                        // Time stamp is set to last successful batches final file time modified.
                        $this->set_push_timestamp($timetosave);
                        return false;
                    }

                    if ($this->clionly) {
                        // Successful send, enable live push updates.
                        set_config('push_cli_only', 0, 'tool_ally');
                        set_config('push_cli_only_off', time(), 'tool_ally');
                        $this->clionly = false;
                    }

                    // Reset payload and track last successful and latest time modified.
                    $payload    = [];
                    $timetosave = $file->get_timemodified();
                }
            }
        } catch (\Exception $e) {
            // Save current progress so we don't resend files that were successfully sent.
            $this->set_push_timestamp($timetosave);
            throw $e;
        }

        // Everything went according to plan, update our timestamp.
        $this->set_push_timestamp($timetosave);

        return true;
    }

    /**
     * Push file deletions to Ally.
     *
     * @param push_config $config
     * @param push_file_updates $updates
     */
    private function push_deletes(push_config $config, push_file_updates $updates) {
        global $DB, $CFG;

        $ids     = [];
        $payload = [];
        $deletes = $DB->get_recordset('tool_ally_deleted_files', null, 'id');

        while ($deletes->valid()) {
            $file = $deletes->current();
            $deletes->next();

            $ids[]     = $file->id;
            $payload[] = local_file::to_crud($file);

            // Check to see if we have our batch size or if we are at the last file.
            if (count($payload) >= $config->get_batch_size() || !$deletes->valid()) {
                $sendsuccess = $updates->send($payload);
                if (!$sendsuccess) {
                    // Failed to send, might as well switch on cli only mode to avoid slowness on front end.
                    set_config('push_cli_only', 1, 'tool_ally');
                    set_config('push_cli_only_on', time(), 'tool_ally');
                    $this->clionly = true;
                    // Give up at this point.
                    return false;
                }

                if ($this->clionly) {
                    // Successful send, enable live push updates.
                    set_config('push_cli_only', 0, 'tool_ally');
                    set_config('push_cli_only_off', time(), 'tool_ally');
                    $this->clionly = false;
                }

                // Successfully sent, remove.
                $DB->delete_records_list('tool_ally_deleted_files', 'id', $ids);

                // Reset arrays for next payload.
                $ids     = [];
                $payload = [];
            }
        }
        $deletes->close();
    }

    /**
     * Mark the files associated with HTML blocks as needing updating if the block has been updated.
     * This is needed because blocks don't have events, meaning we have to just look at the time modified.
     *
     * @param $time
     */
    private function mark_block_files_needing_updating($time) {
        global $DB;

        $select = 'contextid IN (SELECT ctx.id
                                   FROM {block_instances} bi
                                   JOIN {context} ctx ON ctx.instanceid = bi.id AND ctx.contextlevel = ? AND bi.blockname = ?
                                   WHERE bi.timemodified >= ?)';

        $DB->set_field_select('tool_ally_file_in_use', 'needsupdate', 1, $select, [CONTEXT_BLOCK, 'html', $time]);
    }

    /**
     * Process any files that need updating from the files_in_use table.
     */
    private function process_file_in_use_updates() {
        global $DB;

        $fs = get_file_storage();
        $validator = local_file::file_validator();
        $records = $DB->get_recordset('tool_ally_file_in_use', ['needsupdate' => 1]);

        foreach ($records as $record) {
            $file = $fs->get_file_by_id($record->fileid);
            if (!$file) {
                // This means the file no longer exists. Just remove the record.
                $DB->delete_records('tool_ally_file_in_use', ['id' => $record->id]);
                continue;
            }

            // The validator will check and update the file, sending whatever messages may be needed.
            $validator->validate_stored_file($file);
        }

        $records->close();
    }

    /**
     * Save push timestamp.  This is our file last modified window.
     *
     * @param int $timestamp
     */
    private function set_push_timestamp($timestamp) {
        if (!empty($timestamp)) {
            set_config('push_timestamp', $timestamp, 'tool_ally');
        }
    }
}
