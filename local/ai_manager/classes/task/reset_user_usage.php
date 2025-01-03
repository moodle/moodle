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

namespace local_ai_manager\task;

use local_ai_manager\local\config_manager;
use local_ai_manager\local\tenant;

/**
 * Cleanup task for cleaning up broken tasks which left locks and entries behind in redis and the database.
 *
 * Care: If all scheduled task locks already have been burned, this task will not run, so you will have to fix this by
 * running cli/cleanup_broken_task_entries.php to unlock the tasks again.
 *
 * @package   local_ai_manager
 * @copyright 2024 ISB Bayern
 * @author    Philipp Memmel
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reset_user_usage extends \core\task\scheduled_task {

    /**
     * Clock object injected via \core\di.
     *
     * @var \core\clock the clock object
     */
    private \core\clock $clock;

    /**
     * Create the task object.
     */
    public function __construct() {
        $this->clock = \core\di::get(\core\clock::class);
    }

    /**
     * Returns the name of the task.
     *
     * @return string the name of the task
     */
    public function get_name(): string {
        return get_string('resetuserusagetask', 'local_ai_manager');
    }

    /**
     * Execute the cleanup.
     */
    public function execute(): void {
        global $DB;
        $tenantfield = get_config('local_ai_manager', 'tenantcolumn');
        $tenants = $DB->get_fieldset_sql("SELECT DISTINCT " . $tenantfield
                . " FROM {local_ai_manager_userusage} uu LEFT JOIN {user} u ON uu.userid = u.id");
        if (empty($tenants)) {
            // Just in the rare case of an empty table.
            mtrace('No entries found. Exiting.');
            return;
        }

        foreach ($tenants as $tenantidentifier) {
            // We intentionally do not use \core\di here, because we need to reset the objects for each tenant.
            $tenant = new tenant($tenantidentifier);
            $configmanager = new config_manager($tenant);
            $sql = "SELECT uu.* FROM {local_ai_manager_userusage} uu "
                    . "JOIN {user} u ON uu.userid = u.id WHERE " . $tenantfield . " = :tenantidentifier";
            $rs = $DB->get_recordset_sql($sql, ['tenantidentifier' => $tenantidentifier]);
            foreach ($rs as $record) {
                $lastreset = !empty($record->lastreset) ? $record->lastreset : 0;
                if ($this->clock->time() - $lastreset > $configmanager->get_max_requests_period()) {
                    $record->lastreset = $this->clock->time();
                    $record->currentusage = 0;
                    $DB->update_record('local_ai_manager_userusage', $record);
                }
                mtrace('Successfully reset user usage of tenant ' . $tenantidentifier);
            }
            $rs->close();
        }
    }
}
