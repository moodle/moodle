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
 * @copyright  2023 IntelliBoard
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
 * Task to run Full Migration daily.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2023 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class daily_snapshot_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('daily_snapshot_task', 'local_intellidata');
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

        // Validate if 'enablescheduledsnapshot' setting enabled.
        if (!SettingsHelper::get_setting('enablescheduledsnapshot')) {
            mtrace(get_string('scheduledsnapshotdisabled', 'local_intellidata'));
            return true;
        }

        DebugHelper::enable_moodle_debug();

        mtrace("IntelliData: Begin setup for the migration process");

        // Reset migration.
        SettingsHelper::set_setting('resetmigrationprogress', 1);

        // Enabled divide migration by data type.
        SettingsHelper::set_setting('dividemigrationtbydatatype', 0);

        // Enable cron task.
        MigrationHelper::enabled_migration_task();

        mtrace("IntelliData: Completed setup for the migration process");

        return true;
    }

}
