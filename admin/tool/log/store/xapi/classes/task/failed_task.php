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

namespace logstore_xapi\task;

use logstore_xapi\log\store;

/**
 * Emit failed records to LRS.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class failed_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskfailed', 'logstore_xapi');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        $manager = get_log_manager();
        $store = new store($manager);
        $batchsize = $store->get_max_batch_size_for_failed();

        $extractedevents = logstore_xapi_extract_events($batchsize, XAPI_REPORT_SOURCE_LOG, XAPI_IMPORT_TYPE_FAILED);
        $loadedevents = $store->process_events($extractedevents, XAPI_IMPORT_TYPE_FAILED);

        logstore_xapi_store_failed_events($loadedevents);
        logstore_xapi_record_successful_events($loadedevents);
        logstore_xapi_save_sent_events($loadedevents);
        logstore_xapi_delete_processed_events($loadedevents);
    }
}
