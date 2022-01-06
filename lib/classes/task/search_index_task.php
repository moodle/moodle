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
 * A scheduled task for global search.
 *
 * @package    core
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Runs global search indexing.
 *
 * @package    core
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_index_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskglobalsearchindex', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        if (!\core_search\manager::is_indexing_enabled()) {
            return;
        }
        $globalsearch = \core_search\manager::instance();

        // Get total indexing time limit.
        $timelimit = get_config('core', 'searchindextime');
        $start = time();

        // Do normal indexing.
        $globalsearch->index(false, $timelimit, new \text_progress_trace());

        // Do requested indexing (if any) for the rest of the time.
        if ($timelimit != 0) {
            $now = time();
            $timelimit -= ($now - $start);
            if ($timelimit <= 1) {
                // There is hardly any time left, so don't try to do requests.
                return;
            }
        }
        $globalsearch->process_index_requests($timelimit, new \text_progress_trace());
    }
}
