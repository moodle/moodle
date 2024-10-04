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

namespace core\task;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/db/upgradelib.php');

/**
 * Retroactively fixes file timestamps that are older than the containing folder record.
 *
 * @package     core
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT, 2021
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fix_file_timestamps_task extends adhoc_task {

    /**
     * Run the adhoc task and fix the file timestamps.
     */
    public function execute() {
        upgrade_fix_file_timestamps();
    }
}
