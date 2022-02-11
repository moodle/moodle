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
use enrol_lti\local\ltiadvantage\entity\registration_url;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\registration_url_repository;
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
        return application_registration::create(
            $dto->name,
            new \moodle_url($dto->platformid),
            $dto->clientid,
            new \moodle_url($dto->authenticationrequesturl),
            new \moodle_url($dto->jwksurl),
            new \moodle_url($dto->accesstokenurl),
            $dto->id ?? null
        );
    }

    /**
     * Application service handling the use case "As an admin I can register an application as an LTI platform".
     *
     * @param \stdClass $appregdto details of the application to register.
     * @return application_registration the application_registration domain object.
     */
    public function create_application_registration(\stdClass $appregdto): application_registration {
        if ($this->appregistrationrepo->find_by_platform($appregdto->platformid, $appregdto->clientid)) {
            throw new \moodle_exception("A registration for issuer '$appregdto->platformid', "
                ."clientid '$appregdto->clientid' already exists.");
        }
        return $this->appregistrationrepo->save($this->registration_from_dto($appregdto));
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

    /**
     * Get a one-time-use dynamic registration URL which is valid only for the specified duration.
     *
     * @param int $durationsecs how long, in seconds, the registration URL will be valid for.
     * @return registration_url the registration_url instnace.
     */
    public function create_registration_url(int $durationsecs = 86400): registration_url {
        if ($durationsecs <= 0) {
            throw new \coding_exception('Invalid registration URL duration. Must be greater than 0.');
        }

        $regurl = new registration_url(time() + $durationsecs);
        $regurlrepo = new registration_url_repository();
        return $regurlrepo->save($regurl);
    }

    /**
     * Get the current dynamic registration URL.
     *
     * Will return null if the URL doesn't exist, or if the URL has expired.
     *
     * @param string $token Used to get the registration url by token.
     * @return registration_url|null the registration_url instance if valid, otherwise null.
     */
    public function get_registration_url(string $token = ''): ?registration_url {
        $regurlrepo = new registration_url_repository();
        return $token ? $regurlrepo->find_by_token($token) : $regurlrepo->find();
    }

    /**
     * Delete the current dynamic registration URL.
     */
    public function delete_registration_url(): void {
        $regurlrepo = new registration_url_repository();
        $regurlrepo->delete();
    }
}
