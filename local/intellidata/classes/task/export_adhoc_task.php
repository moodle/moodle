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
 * Task to process datafiles export for specific datatype.
 *
 * @package    local_intellidata
 * @category   task
 * @author     IntelliBoard Inc.
 * @copyright  2022 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\task;

use local_intellidata\helpers\ParamsHelper;
use local_intellidata\services\encryption_service;
use local_intellidata\services\export_service;
use local_intellidata\services\database_service;
use local_intellidata\helpers\TrackingHelper;
use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\repositories\export_log_repository;

/**
 * Task to process datafiles export for specific datatype.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2022 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export_adhoc_task extends \core\task\adhoc_task {

    /** @var bool */
    private $divideexportbydatatype = false;

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {

        if (TrackingHelper::enabled()) {
            raise_memory_limit(MEMORY_HUGE);

            // Divide adhoc task to smaller tasks if needed.
            if ((int)SettingsHelper::get_setting('divideexportbydatatype')) {
                $this->execute_chunks_export();
            } else {
                $this->execute_full_export();
            }
        }
    }

    /**
     * Export data by once.
     * Throw exceptions on errors (the job will be retried).
     */
    private function execute_full_export() {

        DebugHelper::enable_moodle_debug();

        mtrace("IntelliData Data Files Export CRON started!");

        $data = $this->get_custom_data();

        $exportservice = new export_service(ParamsHelper::MIGRATION_MODE_ENABLED);
        $exportlogrepository = new export_log_repository();
        $encryptionservice = new encryption_service();

        $services = [
            'encryptionservice' => $encryptionservice,
            'exportservice' => $exportservice,
            'exportlogrepository' => new $exportlogrepository,
        ];

        $databaseservice = new database_service(true, $services);
        $databaseservice->set_all_tables(true);
        $databaseservice->set_adhoctask(true);

        foreach ($data->datatypes as $datatype) {

            // Delete old files.
            $exportservice->delete_files([
                'datatype' => $datatype,
                'timemodified' => time(),
            ]);

            // Export table.
            $databaseservice->export_tables([
                'table' => $datatype,
            ]);

            // Export files to storage.
            $exportservice->save_files([
                'datatype' => $datatype,
            ]);

            // Set datatype migrated.
            $exportlogrepository->save_migrated($datatype);
        }

        // Send callback when files ready.
        if (!empty($data->callbackurl)) {
            $client = new \curl();
            $client->post($data->callbackurl, [
                'data' => $encryptionservice->encrypt(json_encode(['datatypes' => $data->datatypes])),
            ]);
        }

        mtrace("IntelliData Data Files Export CRON ended!");
    }

    /**
     * Export data by chunks.
     * Throw exceptions on errors (the job will be retried).
     */
    private function execute_chunks_export() {

        DebugHelper::enable_moodle_debug();

        $data = $this->get_custom_data();

        // Divide one large task to multiple smaller tasks.
        if (count($data->datatypes) > 1) {
            return $this->divide_adhoc_by_datatypes($data);
        }

        mtrace("IntelliData Data Files Export CRON started!");

        $exportservice = new export_service(ParamsHelper::MIGRATION_MODE_ENABLED);
        $exportlogrepository = new export_log_repository();
        $encryptionservice = new encryption_service();

        $services = [
            'encryptionservice' => $encryptionservice,
            'exportservice' => $exportservice,
            'exportlogrepository' => new $exportlogrepository,
        ];

        $databaseservice = new database_service(true, $services);
        $databaseservice->set_all_tables(true);
        $databaseservice->set_adhoctask(true);

        foreach ($data->datatypes as $datatype) {

            // Delete old files.
            if (empty($data->limit)) {
                $exportservice->delete_files([
                    'datatype' => $datatype,
                    'timemodified' => time(),
                ]);
            }

            // Export table.
            $databaseservice->export_tables([
                'table' => $datatype,
                'cronprocessing' => true,
                'adhoctask' => true,
                'limit' => (!empty($data->limit)) ? $data->limit : 0,
                'callback' => !empty($data->callbackurl) ? $data->callbackurl : null,
            ]);
        }

        mtrace("IntelliData Data Files Export CRON ended!");
    }

    /**
     * Divide datatypes to multiple adhoc tasks.
     *
     * @param $data
     * @return void
     */
    private function divide_adhoc_by_datatypes($data) {

        foreach ($data->datatypes as $datatype) {

            $customdata = [
                'datatypes' => [$datatype],
            ];
            if (!empty($data->callbackurl)) {
                $customdata['callbackurl'] = $data->callbackurl;
            }

            $exporttask = new export_adhoc_task();
            $exporttask->set_custom_data($customdata);
            \core\task\manager::queue_adhoc_task($exporttask);
        }

        mtrace("IntelliData Data Adhoc task divided to datatypes.");
    }
}
