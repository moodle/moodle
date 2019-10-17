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
 * Adhoc task that updates all of the existing forum_post records with no wordcount or no charcount.
 *
 * @package    mod_forum
 * @copyright  2019 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Adhoc task that updates all of the existing forum_post records with no wordcount or no charcount.
 *
 * @package     mod_forum
 * @copyright   2019 David Monllao
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class refresh_forum_post_counts extends \core\task\adhoc_task {

    /**
     * Run the task to refresh calendar events.
     */
    public function execute() {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Default to chunks of 5000 records per run, unless overridden in config.php
        $chunksize = $CFG->forumpostcountchunksize ?? 5000;

        $numrecordsupdated = mod_forum_update_null_forum_post_counts($chunksize);

        // Re-queue this adhoc task if the maximum number of records were found during
        // the current run, since there may be more records to update.
        if ($numrecordsupdated == $chunksize) {
            \core\task\manager::queue_adhoc_task(new refresh_forum_post_counts());
        }
    }
}
