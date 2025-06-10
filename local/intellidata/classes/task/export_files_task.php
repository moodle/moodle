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


use local_intellidata\persistent\datatypeconfig;
use local_intellidata\services\export_service;
use local_intellidata\helpers\TrackingHelper;
use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\ExportHelper;


/**
 * Task to process datafiles export.
 *
 * @package    local_intellidata
 * @author     IntelliBoard Inc.
 * @copyright  2020 IntelliBoard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export_files_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('export_files_task', 'local_intellidata');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {

        if (TrackingHelper::enabled()) {

            DebugHelper::enable_moodle_debug();

            $starttime = microtime();
            mtrace("Export Files process started at " . date('r') . "...");

            $exportservice = new export_service();
            ExportHelper::process_files_export($exportservice);

            mtrace("Export Files process completed at " . date('r') . ".");
            $difftime = microtime_diff($starttime, microtime());
            mtrace("Export Files Execution took " . $difftime . " seconds.");
            mtrace("-------------------------------------------");
        }
    }
}
