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
 * A scheduled task.
 *
 * @package    core
 * @copyright  2013 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Simple task to run the portfolio cron.
 */
class portfolio_cron_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskportfoliocron', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG, $DB;

        if ($CFG->enableportfolios) {
            require_once($CFG->libdir . '/portfoliolib.php');
            require_once($CFG->libdir . '/portfolio/exporter.php');
            if ($expired = $DB->get_records_select('portfolio_tempdata', 'expirytime < ?', [time()], '', 'id')) {
                foreach ($expired as $tempdata) {
                    try {
                        $exporter = \portfolio_exporter::rewaken_object($tempdata->id);
                        $exporter->process_stage_cleanup(true);
                    } catch (\Exception $exception) {
                        mtrace('Exception thrown in portfolio cron while cleaning up ' . $tempdata->id . ': ' .
                                $exception->getMessage());
                    }
                }
            }

            $process = $DB->get_records('portfolio_tempdata', ['queued' => 1], 'id ASC', 'id');
            foreach ($process as $tempdata) {
                try {
                    $exporter = \portfolio_exporter::rewaken_object($tempdata->id);
                    $exporter->process_stage_package();
                    $exporter->process_stage_send();
                    $exporter->save();
                    $exporter->process_stage_cleanup();
                } catch (\Exception $exception) {
                    // This will get probably retried in the next cron until it is discarded by the code above.
                    mtrace('Exception thrown in portfolio cron while processing ' . $tempdata->id . ': ' .
                            $exception->getMessage());
                }
            }
        }
    }
}
