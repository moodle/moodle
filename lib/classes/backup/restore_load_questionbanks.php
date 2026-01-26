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

namespace core\backup;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/backup/util/plan/restore_execution_step.class.php');

/**
 * Load question bank context IDs.
 *
 * Execution step that, *conditionally* (if there isn't preloaded information)
 * will load the context IDs of activities in the backup containing questions
 * to backup_temp_ids. They will be stored with "questionbank" itemname and their
 * original context ID as itemid.
 *
 * @package   core
 * @copyright 2026 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_load_questionbanks extends \restore_execution_step {
    /**
     * If restore data has not already been loaded in this request, load the question bank context IDs.
     */
    protected function define_execution(): void {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/dbops/restore_dbops.class.php');
        if ($this->task->get_preloaded_information()) { // If info is already preloaded, nothing to do.
            return;
        }
        $path = $this->get_basepath() . '/activities';
        \restore_dbops::load_questionbanks_to_tempids($this->get_restoreid(), $path);
    }
}
