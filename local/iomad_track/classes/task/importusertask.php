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
 * An adhoc task for local Iomad
 *
 * @package    local_iomad
 * @copyright  2024 E-Learn Design https://www.e-learndesign.co.uk
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_iomad_track\task;

require_once($CFG->dirroot.'/local/iomad_track/db/install.php');
require_once($CFG->dirroot.'/admin/tool/redocerts/lib.php');

defined('MOODLE_INTERNAL') || die();

use core\task\adhoc_task;

class importusertask extends adhoc_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('importuser', 'local_iomad_track');
    }

    /**
     * Run importusertask
     */
    public function execute() {
        global $DB;

        $runtime = time();
        $customdata = $this->get_custom_data();

        $sql = "SELECT DISTINCT courseid,
                                coursename,
                                userid,
                                timecompleted,
                                timeenrolled,
                                timestarted,
                                timeexpires,
                                finalscore,
                                expirysent,
                                notstartedstop,
                                completedstop,
                                expiredstop,
                                coursecleared,
                                licenseallocated
                FROM {local_iomad_track}
                WHERE userid = :userid
                AND companyid != :companyid";

        $params = ['companyid' => $customdata->companyid,
                   'userid' => $customdata->userid];

        $comprecords = $DB->get_records_sql($sql, $params);
        foreach ($comprecords as $comprecord) {
            if (!empty($comprecord->licenseallocated)) {
                $comprecord->licensename = "HISTORIC";
            }
            $comprecord->modifiedtime = $runtime;
            $comprecord->companyid = $customdata->companyid;
            $DB->insert_record('local_iomad_track', $comprecord);
        }
        do_redocerts($this->importuser, 0, $customdata->companyid);
    }

    /**
     * Queues the task.
     *
     */
    public static function queue_task() {

        // Let's set up the adhoc task.
        $task = new \local_iomad\task\resetrolestask();
        \core\task\manager::queue_adhoc_task($task, true);
    }
}
