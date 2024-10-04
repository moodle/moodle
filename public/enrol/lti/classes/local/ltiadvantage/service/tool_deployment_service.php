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

namespace enrol_lti\local\ltiadvantage\service;

use enrol_lti\local\ltiadvantage\entity\deployment;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;

/**
 * Class tool_deployment_service.
 *
 * @package enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_deployment_service {

    /** @var application_registration_repository repository to work with application_registration instances. */
    private $appregistrationrepo;

    /** @var deployment_repository repository to work with deployment instances. */
    private $deploymentrepo;

    /** @var resource_link_repository repository to work with resource link instances. */
    private $resourcelinkrepo;

    /** @var context_repository repository to work with context instances. */
    private $contextrepo;

    /** @var user_repository repository to work with user instances. */
    private $userrepo;

    /**
     * The tool_deployment_service constructor.
     *
     * @param application_registration_repository $appregistrationrepo an application_registration_repository instance.
     * @param deployment_repository $deploymentrepo a deployment_repository instance.
     * @param resource_link_repository $resourcelinkrepo a resource_link_repository instance.
     * @param context_repository $contextrepo a context_repository instance.
     * @param user_repository $userrepo a user_repository instance.
     */
    public function __construct(application_registration_repository $appregistrationrepo,
            deployment_repository $deploymentrepo, resource_link_repository $resourcelinkrepo,
            context_repository $contextrepo, user_repository $userrepo) {

        $this->appregistrationrepo = $appregistrationrepo;
        $this->deploymentrepo = $deploymentrepo;
        $this->resourcelinkrepo = $resourcelinkrepo;
        $this->contextrepo = $contextrepo;
        $this->userrepo = $userrepo;
    }

    /**
     * Service handling the use case "As an admin I can add a tool deployment to a platform registration".
     *
     * @param \stdClass $requestdto the required service data.
     * @return deployment the deployment instance which has been created.
     * @throws \coding_exception if the registration doesn't exist.
     */
    public function add_tool_deployment(\stdClass $requestdto): deployment {
        // DTO contains: registration_id, deployment_id, deployment_name.
        [
            'registration_id' => $registrationid,
            'deployment_id' => $deploymentid,
            'deployment_name' => $deploymentname
        ] = (array) $requestdto;

        $registration = $this->appregistrationrepo->find($registrationid);
        if (is_null($registration)) {
            throw new \coding_exception("Cannot add deployment to non-existent application registration ".
                "'$registrationid'");
        }

        $deployment = $registration->add_tool_deployment($deploymentname, $deploymentid);

        return $this->deploymentrepo->save($deployment);
    }

    /**
     * Service handling the use case "As an admin I can delete a tool deployment from a platform registration".
     *
     * @param int $deploymentid the id of the deployment to remove.
     */
    public function delete_tool_deployment(int $deploymentid): void {
        // Delete any resource links attached to this deployment.
        $this->resourcelinkrepo->delete_by_deployment($deploymentid);

        // Delete any context entries for the deployment.
        $this->contextrepo->delete_by_deployment($deploymentid);

        // Delete all enrolments for any users tied to this deployment.
        global $DB, $CFG;
        $sql = "SELECT lu.userid as ltiuserid, e.*
                  FROM {enrol_lti_users} lu
                  JOIN {enrol_lti_tools} lt
                    ON (lt.id = lu.toolid)
                  JOIN {enrol} e
                    ON (e.id = lt.enrolid)
                 WHERE lu.ltideploymentid = :deploymentid";
        $instancesrs = $DB->get_recordset_sql($sql, ['deploymentid' => $deploymentid]);
        require_once($CFG->dirroot . '/enrol/lti/lib.php');
        $enrollti = new \enrol_lti_plugin();
        foreach ($instancesrs as $instance) {
            $userid = $instance->ltiuserid;
            $enrollti->unenrol_user($instance, $userid);
        }
        $instancesrs->close();

        // Delete any lti user enrolments.
        $this->userrepo->delete_by_deployment($deploymentid);

        // Delete the deployment itself.
        $this->deploymentrepo->delete($deploymentid);
    }
}
