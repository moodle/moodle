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
 * Ad hoc task to be executed the next time cron runs for component 'format_tiles', to register plugin.
 *
 * @package   format_tiles
 * @copyright 2019 David Watson {@link http://evolutioncode.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_tiles\task;

use format_tiles\registration_manager;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Class deferred_register
 * @package format_tiles
 * @copyright 2018 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class deferred_register extends \core\task\adhoc_task {

    /**
     * Run the register task to attempt to register the tiles plugin with the developer's website.
     * The task is initiated if the site admin clicks "Register" from Site Admin > Course Formats > Tiles format.
     * Ad hoc tasks are tried on cron and if they fail are retried with exponential fall off up to 24 hours.
     * i.e. after 1 min, 2, 4, 8, 16 mins etc.
     */
    public function execute() {
        try {
            $data = $this->get_custom_data();
            $result = registration_manager::attempt_deferred_registration($data);
            if (!$result) {
                // Do not throw exception as don't want to try again later - just trace.
                mtrace("Failed to complete deferred registration");
            } else {
                mtrace("Tiles plugin registration success.");
                return true;
            }
        } catch (\Exception $ex) {
            mtrace("Failed to complete deferred registration");
            mtrace($ex->getMessage());
        }
        return false;
    }
}
