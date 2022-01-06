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
 * Legacy Cron Quiz Reports Task
 *
 * @package    quiz_statistics
 * @copyright  2017 Michael Hughes, University of Strathclyde
 * @author Michael Hughes <michaelhughes@strath.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
namespace quiz_statistics\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Legacy Cron Quiz Reports Task
 *
 * @package    quiz_statistics
 * @copyright  2017 Michael Hughes
 * @author Michael Hughes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class quiz_statistics_cleanup extends \core\task\scheduled_task {
    public function get_name() {
        return get_string('quizstatisticscleanuptask', 'quiz_statistics');
    }

    /**
     * Run the clean up task.
     */
    public function execute() {
        global $DB;

        $expiretime = time() - 4 * HOURSECS;
        $DB->delete_records_select('quiz_statistics', 'timemodified < ?', array($expiretime));

        return true;
    }
}
