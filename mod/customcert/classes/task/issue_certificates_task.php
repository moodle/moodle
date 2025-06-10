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
 * A scheduled task for issuing certificates that have requested someone get emailed.
 *
 * @package    mod_customcert
 * @copyright  2024 Oscar Nadjar <oscar.nadjar@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_customcert\task;

/**
 * A scheduled task for issuing certificates that have requested someone get emailed.
 *
 * @package    mod_customcert
 * @copyright  2024 Oscar Nadjar <oscar.nadjar@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class issue_certificates_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('taskissuecertificate', 'customcert');
    }

    /**
     * Execute.
     */
    public function execute() {
        global $DB;

        // Get the certificatesperrun, includeinnotvisiblecourses, and certificateexecutionperiod configurations.
        $certificatesperrun = (int)get_config('customcert', 'certificatesperrun');
        $includeinnotvisiblecourses = (bool)get_config('customcert', 'includeinnotvisiblecourses');
        $certificateexecutionperiod = (int)get_config('customcert', 'certificateexecutionperiod');
        $offset = (int)get_config('customcert', 'certificate_offset');

        // We are going to issue certificates that have requested someone get emailed.
        $emailotherslengthsql = $DB->sql_length('c.emailothers');
        $sql = "SELECT c.id, c.templateid, c.course, c.requiredtime, c.emailstudents, c.emailteachers, c.emailothers,
                       ct.id AS templateid, ct.name AS templatename, ct.contextid, co.id AS courseid,
                       co.fullname AS coursefullname, co.shortname AS courseshortname
                  FROM {customcert} c
                  JOIN {customcert_templates} ct
                    ON c.templateid = ct.id
                  JOIN {course} co
                    ON c.course = co.id
                  JOIN {course_categories} cat
                    ON co.category = cat.id
             LEFT JOIN {customcert_issues} ci
                    ON c.id = ci.customcertid";

        $sql .= " WHERE (c.emailstudents = :emailstudents
                     OR c.emailteachers = :emailteachers
                     OR $emailotherslengthsql >= 3)";

        $params = ['emailstudents' => 1, 'emailteachers' => 1];

        // Check the includeinnotvisiblecourses configuration.
        if (!$includeinnotvisiblecourses) {
            // Exclude certificates from hidden courses.
            $sql .= " AND co.visible = 1 AND cat.visible = 1";
        }

        // Add condition based on certificate execution period.
        if ($certificateexecutionperiod > 0) {
            // Include courses with no end date or end date greater than the specified period.
            $sql .= " AND (co.enddate > :enddate OR (co.enddate = 0 AND ci.timecreated > :enddate2))";
            $params['enddate'] = time() - $certificateexecutionperiod;
            $params['enddate2'] = $params['enddate'];
        }

        $sql .= " GROUP BY c.id, ct.id, ct.name, ct.contextid, co.id, co.fullname, co.shortname";

        // Execute the SQL query.
        $customcerts = $DB->get_records_sql($sql, $params, $offset, $certificatesperrun);

        // When we get to the end of the list, reset the offset.
        set_config('certificate_offset', !empty($customcerts) ? $offset + $certificatesperrun : 0, 'customcert');

        if (empty($customcerts)) {
            return;
        }

        foreach ($customcerts as $customcert) {
            // Check if the certificate is hidden, quit early.
            $cm = get_course_and_cm_from_instance($customcert->id, 'customcert', $customcert->course)[1];
            if (!$cm->visible) {
                continue;
            }

            // Do not process an empty certificate.
            $sql = "SELECT ce.*
                      FROM {customcert_elements} ce
                      JOIN {customcert_pages} cp
                        ON cp.id = ce.pageid
                      JOIN {customcert_templates} ct
                        ON ct.id = cp.templateid
                     WHERE ct.contextid = :contextid";
            if (!$DB->record_exists_sql($sql, ['contextid' => $customcert->contextid])) {
                continue;
            }

            // Get the context.
            $context = \context::instance_by_id($customcert->contextid);

            // Get a list of all the issues.
            $sql = "SELECT u.id
                      FROM {customcert_issues} ci
                      JOIN {user} u
                        ON ci.userid = u.id
                     WHERE ci.customcertid = :customcertid
                           AND ci.emailed = 1";
            $issuedusers = $DB->get_records_sql($sql, ['customcertid' => $customcert->id]);

            // Now, get a list of users who can Manage the certificate.
            $userswithmanage = get_users_by_capability($context, 'mod/customcert:manage', 'u.id');

            // Get the context of the Custom Certificate module.
            $cmcontext = \context_module::instance($cm->id);

            // Now, get a list of users who can view and issue the certificate but have not yet.
            // Get users with the mod/customcert:receiveissue capability in the Custom Certificate module context.
            $userswithissue = get_users_by_capability($cmcontext, 'mod/customcert:receiveissue');
            // Get users with mod/customcert:view capability.
            $userswithview = get_users_by_capability($cmcontext, 'mod/customcert:view');
            // Users with both mod/customcert:view and mod/customcert:receiveissue cabapilities.
            $userswithissueview = array_intersect_key($userswithissue, $userswithview);

            // Filter the remaining users by determining whether they can actually see the CM or not
            // (Note: filter_user_list only takes into account those availability condition which actually implement
            // this function, so the second check with get_fast_modinfo must be still performed - but we can reduce the
            // size of the users list here already).
            $infomodule = new \core_availability\info_module($cm);
            $filteredusers = $infomodule->filter_user_list($userswithissueview);

            foreach ($filteredusers as $filtereduser) {
                // Check if the user has already been issued and emailed.
                if (in_array($filtereduser->id, array_keys((array)$issuedusers))) {
                    continue;
                }

                // Don't want to issue to teachers.
                if (in_array($filtereduser->id, array_keys((array)$userswithmanage))) {
                    continue;
                }

                // Now check if the certificate is not visible to the current user.
                $cm = get_fast_modinfo($customcert->courseid, $filtereduser->id)->instances['customcert'][$customcert->id];
                if (!$cm->uservisible) {
                    continue;
                }

                // Check that they have passed the required time.
                if (!empty($customcert->requiredtime)) {
                    if (\mod_customcert\certificate::get_course_time($customcert->courseid,
                            $filtereduser->id) < ($customcert->requiredtime * 60)) {
                        continue;
                    }
                }

                // Ensure the cert hasn't already been issued, e.g via the UI (view.php) - a race condition.
                $issue = $DB->get_record('customcert_issues',
                    ['userid' => $filtereduser->id, 'customcertid' => $customcert->id], 'id, emailed');

                // Ok, issue them the certificate.
                $issueid = empty($issue) ?
                    \mod_customcert\certificate::issue_certificate($customcert->id, $filtereduser->id) : $issue->id;

                // Validate issueid and one last check for emailed.
                if (!empty($issueid) && empty($issue->emailed)) {
                    // We create a new adhoc task to send the email.
                    $task = new \mod_customcert\task\email_certificate_task();
                    $task->set_custom_data(['issueid' => $issueid, 'customcertid' => $customcert->id]);
                    $useadhoc = get_config('customcert', 'useadhoc');
                    if ($useadhoc) {
                        \core\task\manager::queue_adhoc_task($task);
                    } else {
                        $task->execute();
                    }
                }
            }
        }
    }
}
