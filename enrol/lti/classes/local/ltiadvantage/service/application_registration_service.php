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

use enrol_lti\local\ltiadvantage\entity\application_registration;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;

/**
 * Class application_registration_service.
 *
 * @package enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class application_registration_service {
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
     * The application_registration_service constructor.
     *
     * @param application_registration_repository $appregistrationrepo an application registration repository instance.
     * @param deployment_repository $deploymentrepo a deployment repository instance.
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
     * Convert a DTO into a new application_registration domain object.
     *
     * @param \stdClass $dto the object containing information needed to register an application.
     * @return application_registration the application_registration object
     */
    private function registration_from_dto(\stdClass $dto): application_registration {
        $registration = $this->appregistrationrepo->find($dto->id);
        $registration->set_name($dto->name);
        $registration->set_platformid(new \moodle_url($dto->platformid));
        $registration->set_clientid($dto->clientid);
        $registration->set_accesstokenurl(new \moodle_url($dto->accesstokenurl));
        $registration->set_jwksurl(new \moodle_url($dto->jwksurl));
        $registration->set_authenticationrequesturl(new \moodle_url($dto->authenticationrequesturl));
        $registration->complete_registration();
        return $registration;
    }

    /**
     * Gets a unique id for the registration, with uniqueness guaranteed with a lookup.
     *
     * @return string the unique id.
     */
    private function get_unique_id(): string {
        global $DB;
        do {
            $bytes = random_bytes(30);
            $uniqueid = bin2hex($bytes);
        } while ($DB->record_exists('enrol_lti_app_registration', ['uniqueid' => $uniqueid]));

        return $uniqueid;
    }

    /**
     * Convert a DTO into a new DRAFT application_registration domain object.
     *
     * @param \stdClass $dto the object containing information needed to create the draft registration.
     * @return application_registration the draft application_registration object
     */
    private function draft_registration_from_dto(\stdClass $dto): application_registration {
        return application_registration::create_draft(
            $dto->name,
            $this->get_unique_id()
        );
    }

    /**
     * Application service handling the use case "As an admin I can create a draft platform registration".
     *
     * @param \stdClass $appregdto details of the draft application to create.
     * @return application_registration the application_registration domain object.
     * @throws \coding_exception if the DTO doesn't contain required fields.
     */
    public function create_draft_application_registration(\stdClass $appregdto): application_registration {
        if (empty($appregdto->name)) {
            throw new \coding_exception('Cannot create draft registration. Name is missing.');
        }
        $draftregistration = $this->draft_registration_from_dto($appregdto);
        return $this->appregistrationrepo->save($draftregistration);
    }

    /**
     * Application service handling the use case "As an admin I can update the registration of an LTI platform".
     *
     * @param \stdClass $appregdto details of the registration to update.
     * @return application_registration the application_registration domain object.
     */
    public function update_application_registration(\stdClass $appregdto): application_registration {
        if (empty($appregdto->id)) {
            throw new \coding_exception('Cannot update registration. Id is missing.');
        }
        return $this->appregistrationrepo->save($this->registration_from_dto($appregdto));
    }

    /**
     * Application service handling the use case "As an admin I can delete a registration of an LTI platform".
     *
     * @param int $registrationid id of the registration to delete.
     */
    public function delete_application_registration(int $registrationid): void {

        $deployments = $this->deploymentrepo->find_all_by_registration($registrationid);
        if ($deployments) {
            $deploymentservice = new tool_deployment_service(
                $this->appregistrationrepo,
                $this->deploymentrepo,
                $this->resourcelinkrepo,
                $this->contextrepo,
                $this->userrepo
            );
            foreach ($deployments as $deployment) {
                $deploymentservice->delete_tool_deployment($deployment->get_id());
            }
        }

        $this->appregistrationrepo->delete($registrationid);
    }
}
