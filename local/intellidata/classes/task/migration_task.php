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
 *
 * @package    local_intellidata
 * @category   task
 * @author     IntelliBoard Inc.
 * @copyright  2020 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\task;


use local_intellidata\helpers\ExportHelper;
use local_intellidata\helpers\MigrationHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\migration_service;
use local_intellidata\services\export_service;

/**
 * Task to process datafiles export.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2020 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migration_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('migration_task', 'local_intellidata');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     * @return bool
     * @throws \dml_exception
     * @throws \moodle_exception
     * @throws \Exception
     */
    public function execute() {

        // Validate if plugin is enabled and configured.
        if (!TrackingHelper::enabled()) {
            mtrace(get_string('pluginnotconfigured', 'local_intellidata'));
            return true;
        }

        // Validate if forcedisablemigration setting enabled.
        if (MigrationHelper::migration_disabled()) {
            mtrace(get_string('migrationdisabled', 'local_intellidata'));
            return true;
        }

        raise_memory_limit(MEMORY_HUGE);

        DebugHelper::enable_moodle_debug();

        if (SettingsHelper::get_setting('dividemigrationtbydatatype')) {
            // Starting the migration in parts, by datatype.
            $this->partial_migration();
        } else {
            // Running a full migration, all datatypes in one run.
            $this->full_migration();
        }

        return true;
    }

    /**
     * Starting the migration in parts, by datatype.
     * @return void
     */
    private function partial_migration() {
        $params = [];
        $exportservice = new export_service();

        $this->reset_migration();

        $migrationdatatype = SettingsHelper::get_setting('migrationdatatype');
        if ($migrationdatatype) {

            // Ignore if migration completed.
            if ($migrationdatatype == MigrationHelper::MIGRATIONS_COMPLETED_STATUS) {

                $this->complete_migration();

                return;
            }

            $params['datatype'] = $migrationdatatype;
        }

        $migrationstart = (int) SettingsHelper::get_setting('migrationstart');
        $params['migrationstart'] = $migrationstart;
        $params['rewritable'] = false;

        mtrace("IntelliData Migration CRON started!");

        // Set migration time.
        SettingsHelper::set_lastmigrationdate();

        // Export tables.
        $exportservice->set_migration_mode();
        $migrationservice = new migration_service(null, $exportservice);
        $migrationservice->process($params, true);

        mtrace("IntelliData Migration CRON ended!");
    }

    /**
     * Running a full migration, all datatypes.
     *
     * @return void
     */
    private function full_migration() {
        $params = [];
        $exportservice = new export_service();

        $this->reset_migration();

        $migrationstart = (int) SettingsHelper::get_setting('migrationstart');
        $params['migrationstart'] = $migrationstart;
        $params['rewritable'] = false;

        mtrace("IntelliData Migration CRON started!");

        // Set migration time.
        SettingsHelper::set_lastmigrationdate();

        // Export tables.
        $exportservice->set_migration_mode();
        $migrationservice = new migration_service(null, $exportservice);
        $migrationservice->process($params);

        $this->complete_migration();

        mtrace("IntelliData Migration CRON ended!");
    }

    /**
     * The final step of completing the migration process.
     *
     * @return void
     */
    private function complete_migration() {
        if (TrackingHelper::new_tracking_enabled()) {
            SettingsHelper::set_setting('divideexportbydatatype', 0);

            $exportservice = new export_service();
            ExportHelper::process_data_export($exportservice, ['cronprocessing' => true, 'forceexport' => true]);
        }

        // Export files to Moodledata.
        ExportHelper::process_files_export(new export_service());

        // Send callback to IBN.
        MigrationHelper::send_callback();

        // Change callback to IBN.
        MigrationHelper::change_migration_files();

        // Disable scheduled migration task.
        MigrationHelper::disable_sheduled_tasks();
        MigrationHelper::enable_sheduled_tasks(['\local_intellidata\task\migration_task']);
    }

    /**
     * Delete migration files if the corresponding setting is enabled.
     *
     * @return void
     */
    private function reset_migration() {
        // Reset migration process if enabled.
        if (SettingsHelper::get_setting('resetmigrationprogress')) {
            MigrationHelper::reset_migration_details();

            mtrace("IntelliData Cleaner CRON started!");

            // Delete all IntelliData files.
            $filesrecords = (new export_service())->delete_all_files(['timemodified' => time()]);

            mtrace("IntelliData Cleaner: $filesrecords deleted.");
        }
    }
}
