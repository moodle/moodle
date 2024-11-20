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

namespace enrol_lti\local\ltiadvantage\task;

use core\http_client;
use core\task\adhoc_task;
use enrol_lti\local\ltiadvantage\lib\issuer_database;
use enrol_lti\local\ltiadvantage\lib\launch_cache_session;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;
use Packback\Lti1p3\LtiAssignmentsGradesService;
use Packback\Lti1p3\LtiGrade;
use Packback\Lti1p3\LtiLineitem;
use Packback\Lti1p3\LtiRegistration;
use Packback\Lti1p3\LtiServiceConnector;

/**
 * LTI Advantage task responsible for pushing grades to tool platforms.
 *
 * @package    enrol_lti
 * @copyright  2023 David Pesce <david.pesce@exputo.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_tool_grades extends adhoc_task {

    /**
     * Sync grades to the platform using the Assignment and Grade Services (AGS).
     *
     * @param \stdClass $resource the enrol_lti_tools data record for the shared resource.
     * @return array an array containing the
     */
    protected function sync_grades_for_resource($resource): array {
        $usercount = 0;
        $sendcount = 0;
        $userrepo = new user_repository();
        $resourcelinkrepo = new resource_link_repository();
        $appregistrationrepo = new application_registration_repository();
        $issuerdb = new issuer_database($appregistrationrepo, new deployment_repository());

        if ($users = $userrepo->find_by_resource($resource->id)) {
            $completion = new \completion_info(get_course($resource->courseid));
            $syncedusergrades = []; // Keep track of those users who have had their grade synced during this run.
            foreach ($users as $user) {
                $mtracecontent = "for the user '{$user->get_localid()}', for the resource '$resource->id' and the course " .
                    "'$resource->courseid'";
                $usercount++;

                // Check if we do not have a grade service endpoint in either of the resource links.
                // Remember, not all launches need to support grade services.
                $userresourcelinks = $resourcelinkrepo->find_by_resource_and_user($resource->id, $user->get_id());
                $userlastgrade = $user->get_lastgrade();
                mtrace("Found ".count($userresourcelinks)." resource link(s) $mtracecontent. Attempting to sync grades for all.");

                foreach ($userresourcelinks as $userresourcelink) {
                    mtrace("Processing resource link '{$userresourcelink->get_resourcelinkid()}'.");
                    if (!$gradeservice = $userresourcelink->get_grade_service()) {
                        mtrace("Skipping - No grade service found $mtracecontent.");
                        continue;
                    }

                    if (!$context = \context::instance_by_id($resource->contextid, IGNORE_MISSING)) {
                        mtrace("Failed - Invalid contextid '$resource->contextid' for the resource '$resource->id'.");
                        continue;
                    }

                    $grade = false;
                    $dategraded = false;
                    if ($context->contextlevel == CONTEXT_COURSE) {
                        if ($resource->gradesynccompletion && !$completion->is_course_complete($user->get_localid())) {
                            mtrace("Skipping - Course not completed $mtracecontent.");
                            continue;
                        }

                        // Get the grade.
                        if ($grade = grade_get_course_grade($user->get_localid(), $resource->courseid)) {
                            $grademax = floatval($grade->item->grademax);
                            $dategraded = $grade->dategraded;
                            $grade = $grade->grade;
                        }
                    } else if ($context->contextlevel == CONTEXT_MODULE) {
                        $cm = get_coursemodule_from_id(false, $context->instanceid, 0, false, MUST_EXIST);

                        if ($resource->gradesynccompletion) {
                            $data = $completion->get_data($cm, false, $user->get_localid());
                            if (!in_array($data->completionstate, [COMPLETION_COMPLETE_PASS, COMPLETION_COMPLETE])) {
                                mtrace("Skipping - Activity not completed $mtracecontent.");
                                continue;
                            }
                        }

                        $grades = grade_get_grades($cm->course, 'mod', $cm->modname, $cm->instance,
                            $user->get_localid());
                        if (!empty($grades->items[0]->grades)) {
                            $grade = reset($grades->items[0]->grades);
                            if (!empty($grade->item)) {
                                $grademax = floatval($grade->item->grademax);
                            } else {
                                $grademax = floatval($grades->items[0]->grademax);
                            }
                            $dategraded = $grade->dategraded;
                            $grade = $grade->grade;
                        }
                    }

                    if ($grade === false || $grade === null || strlen($grade) < 1) {
                        mtrace("Skipping - Invalid grade $mtracecontent.");
                        continue;
                    }

                    if (empty($grademax)) {
                        mtrace("Skipping - Invalid grademax $mtracecontent.");
                        continue;
                    }

                    if (!grade_floats_different($grade, $userlastgrade)) {
                        mtrace("Not sent - The grade $mtracecontent was not sent as the grades are the same.");
                        continue;
                    }
                    $floatgrade = $grade / $grademax;

                    try {
                        // Get an AGS instance for the corresponding application registration and service data.
                        $appregistration = $appregistrationrepo->find_by_deployment(
                            $userresourcelink->get_deploymentid()
                        );
                        $registration = $issuerdb->findRegistrationByIssuer(
                            $appregistration->get_platformid()->out(false),
                            $appregistration->get_clientid()
                        );
                        global $CFG;
                        require_once($CFG->libdir . '/filelib.php');
                        $sc = new LtiServiceConnector(new launch_cache_session(), new http_client());

                        $lineitemurl = $gradeservice->get_lineitemurl();
                        $lineitemsurl = $gradeservice->get_lineitemsurl();
                        $servicedata = [
                            'lineitems' => $lineitemsurl ? $lineitemsurl->out(false) : null,
                            'lineitem' => $lineitemurl ? $lineitemurl->out(false) : null,
                            'scope' => $gradeservice->get_scopes(),
                        ];

                        $ags = $this->get_ags($sc, $registration, $servicedata);
                        $ltigrade = LtiGrade::new()
                            ->setScoreGiven($grade)
                            ->setScoreMaximum($grademax)
                            ->setUserId($user->get_sourceid())
                            ->setTimestamp(date(\DateTimeInterface::ISO8601, $dategraded))
                            ->setActivityProgress('Completed')
                            ->setGradingProgress('FullyGraded');

                        if (empty($servicedata['lineitem'])) {
                            // The launch did not include a couple lineitem, so find or create the line item for grading.
                            $lineitem = $ags->findOrCreateLineitem(new LtiLineitem([
                                'label' => $this->get_line_item_label($resource, $context),
                                'scoreMaximum' => $grademax,
                                'tag' => 'grade',
                                'resourceId' => $userresourcelink->get_resourceid(),
                                'resourceLinkId' => $userresourcelink->get_resourcelinkid()
                            ]));
                            $response = $ags->putGrade($ltigrade, $lineitem);
                        } else {
                            // Let AGS find the coupled line item.
                            $response = $ags->putGrade($ltigrade);
                        }

                    } catch (\Exception $e) {
                        mtrace("Failed - The grade '$floatgrade' $mtracecontent failed to send.");
                        mtrace($e->getMessage());
                        continue;
                    }

                    $successresponses = [200, 201, 202, 204];
                    if (in_array($response['status'], $successresponses)) {
                        $user->set_lastgrade(grade_floatval($grade));
                        $syncedusergrades[$user->get_id()] = $user;
                        mtrace("Success - The grade '$floatgrade' $mtracecontent was sent.");
                    } else {
                        mtrace("Failed - The grade '$floatgrade' $mtracecontent failed to send.");
                        mtrace("Header: {$response['headers']['httpstatus']}");
                    }
                }
            }
            // Update the lastgrade value for any users who had a grade synced. Allows skipping on future runs if not changed.
            // Update the count of total users having their grades synced, not the total number of grade sync calls made.
            foreach ($syncedusergrades as $ltiuser) {
                $userrepo->save($ltiuser);
                $sendcount = $sendcount + 1;
            }
        }
        return [$usercount, $sendcount];
    }

    /**
     * Get the string label for the line item associated with the resource, based on the course or module name.
     *
     * @param \stdClass $resource the enrol_lti_tools record.
     * @param \context $context the context of the resource - either course or module.
     * @return string the label to use in the line item.
     */
    protected function get_line_item_label(\stdClass $resource, \context $context): string {
        $resourcename = 'default';
        if ($context->contextlevel == CONTEXT_COURSE) {
            global $DB;
            $coursenamesql = "SELECT c.fullname
                                FROM {enrol_lti_tools} t
                                JOIN {enrol} e
                                  ON (e.id = t.enrolid)
                                JOIN {course} c
                                  ON (c.id = e.courseid)
                               WHERE t.id = :resourceid";
            $coursename = $DB->get_field_sql($coursenamesql, ['resourceid' => $resource->id]);
            $resourcename = format_string($coursename, true, ['context' => $context->id]);
        } else if ($context->contextlevel == CONTEXT_MODULE) {
            foreach (get_fast_modinfo($resource->courseid)->get_cms() as $mod) {
                if ($mod->context->id == $context->id) {
                    $resourcename = $mod->name;
                }
            }
        }
        return $resourcename;
    }

    /**
     * Get an Assignment and Grade Services (AGS) instance to make the call to the platform.
     *
     * @param LtiServiceConnector $sc a service connector instance.
     * @param LtiRegistration $registration the registration instance.
     * @param array $sd the service data.
     * @return LtiAssignmentsGradesService
     */
    protected function get_ags(LtiServiceConnector $sc, LtiRegistration $registration, array $sd): LtiAssignmentsGradesService {
        return new LtiAssignmentsGradesService($sc, $registration, $sd);
    }

    /**
     * Performs the synchronisation of grades from the tool to any registered platforms.
     *
     * @return bool|void
     */
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/lib/completionlib.php');
        require_once($CFG->libdir . '/gradelib.php');
        require_once($CFG->dirroot . '/grade/querylib.php');

        $resource = $this->get_custom_data();

        mtrace("Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$resource->courseid'.");

        [$usercount, $sendcount] = $this->sync_grades_for_resource($resource);

        mtrace("Completed - Synced grades for tool '$resource->id' in the course '$resource->courseid'. " .
            "Processed $usercount users; sent $sendcount grades.");
        mtrace("");

    }
}
