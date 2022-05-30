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
 * @package    local_email
 * @copyright  2022 Derick Turner
 * @author    Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_email_reports\task;

use \EmailTemplate;
use \company;
use \context_course;

//require_once($CFG->dirroot . '/local/iomad/lib/company.php');

class trainingevent_not_selected_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('trainingevent_not_selected_task', 'local_email_reports');
    }

    /**
     * Run email trainingevent_not_selected_task.
     */
    public function execute() {
        global $DB, $CFG;

        // Set some defaults.
        $runtime = time();
        $courses = array();
        $dayofweek = date('w', $runtime) + 1;

        // We only want the student role.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        mtrace("Running email report training event not selected task at ".date('D M Y h:m:s', $runtime));

        // Get all of the upcoming training event courses.
        $courses = $DB->get_records_sql("SELECT DISTINCT c.*,ic.warnnotstarted, ic.notifyperiod FROM {trainingevent} t
                                         JOIN {course} c ON (t.course = c.id)
                                         JOIN {iomad_courses} ic ON (t.course = ic.courseid AND c.id = ic.courseid)
                                         WHERE ic.warnnotstarted > 0
                                         AND c.visible = 1
                                         AND t.startdatetime > :time",
                                         ['time' => $runtime]);
        foreach ($courses as $course) {       
            // Get all of the users on the course who are not already signed up for an event or waiting list.
            $users = $DB->get_records_sql("SELECT u.* FROM {user} u
                                           JOIN {user_enrolments} ue ON (ue.userid = u.id)
                                           JOIN {enrol} e ON (ue.enrolid = e.id)
                                           WHERE e.courseid = :courseid1
                                           AND ue.timestart < :warntime
                                           AND u.id NOT IN (
                                               SELECT tu.userid FROM {trainingevent_users} tu
                                               JOIN {trainingevent} t ON (tu.trainingeventid = t.id)
                                               WHERE t.course = :courseid2
                                           )",
                                           ['courseid1' => $course->id,
                                            'courseid2' => $course->id,
                                            'warntime' => $runtime - $course->warnnotstarted * 24 * 60 * 60]);
            foreach ($users as $user) {
                // Get the user's company.
                if ($company = company::by_userid($user->id, true)) {
                    
                    // Get the company template info.
                    // Check against per company template repeat instead.
                    if ($templateinfo = $DB->get_record('email_template', array('companyid' => $company->id, 'lang' => $user->lang, 'name' => 'trainingevent_not_selected'))) {
                        // Check if its the correct day, if not continue.
                        if (!empty($templateinfo->repeatday) && $templateinfo->repeatday != 99 && $templateinfo->repeatday != $dayofweek - 1) {
                            continue;
                        }

                        // otherwise set the notifyperiod
                        if ($templateinfo->repeatperiod == 0) {
                            $notifyperiod = "";
                        } else if ($templateinfo->repeatperiod == 99) {
                            $notifyperiod = "";
                        } else {
                            $notifytime = strtotime("- 1" . $periods[$templateinfo->repeatperiod], $runtime);
                            $notifyperiod = " AND sent <  $notifytime";
                        }
                    } else {
                        // use the default notify period.
                        $notifytime = $runtime - $course->notifyperiod * 86400;
                        $notifyperiod = " AND sent < $notifytime";
                    }

                    // Check if we have sent any emails and if they are within the period.
                    if ($DB->count_records('email', array('userid' => $user->id,
                                                          'courseid' => $course->id,
                                                          'templatename' => 'trainingevent_not_selected')) > 0) {
                        if (!empty($notifyperiod)) {
                            if (!$DB->get_records_sql("SELECT id FROM {email}
                                                      WHERE userid = :userid
                                                      AND courseid = :courseid
                                                      AND templatename = :templatename
                                                      $notifyperiod
                                                      AND id IN (
                                                         SELECT MAX(id) FROM {emai}l
                                                         WHERE userid = :userid2
                                                         AND courseid = :courseid2
                                                         AND templatename = :templatename2)",
                                                      array('userid' => $user->id,
                                                            'courseid' => $course->id,
                                                            'templatename' => 'trainingevent_not_selected',
                                                            'userid2' => $user->id,
                                                            'courseid2' => $course->id,
                                                            'templatename2' => 'trainingevent_not_selected'))) {
                                continue;
                            }
                        }
                    }

                    // Passed all checks, send the email.
                    mtrace("Sending trainingevent not selected email to $user->email");
                    EmailTemplate::send('trainingevent_not_selected', array('user' => $user, 'course' => $course, 'company' => $company));
                    
                }
            }

        }
        mtrace("email reporting training event not selected completed at " . date('D M Y h:m:s', time()));
    }

}