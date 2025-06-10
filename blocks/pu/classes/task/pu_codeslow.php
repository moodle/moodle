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
 * The task for emailing out administrators if the number of coupon
 * codes droppes below the predetermined threshold.
 *
 * @package    block_pu
 * @copyright  2021 onwards LSUOnline & Continuing Education
 * @copyright  2021 onwards Robert Russo
 */
namespace block_pu\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Extend the Moodle scheduled task class with ours.
 */
class pu_codeslow extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {

        return get_string('pu_codeslow', 'block_pu');

    }

    /**
     * Do the job.
     *
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {

        global $CFG;
        require_once($CFG->dirroot . '/blocks/pu/importlib.php');
        $pu = new \pu();
        $pu->run_pu_codeslow();
    }
}
