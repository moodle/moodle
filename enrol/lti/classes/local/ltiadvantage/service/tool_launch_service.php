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

use enrol_lti\helper;
use enrol_lti\local\ltiadvantage\entity\context;
use enrol_lti\local\ltiadvantage\entity\deployment;
use enrol_lti\local\ltiadvantage\entity\migration_claim;
use enrol_lti\local\ltiadvantage\entity\resource_link;
use enrol_lti\local\ltiadvantage\entity\user;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\legacy_consumer_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;
use Packback\Lti1p3\LtiMessageLaunch;

/**
 * Class tool_launch_service.
 *
 * This class handles the launch of a resource by a user, using the LTI Advantage Resource Link Launch.
 *
 * See http://www.imsglobal.org/spec/lti/v1p3/#launch-from-a-resource-link
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_launch_service {

    /** @var deployment_repository $deploymentrepo instance of a deployment repository. */
    private $deploymentrepo;

    /** @var application_registration_repository instance of a application_registration repository */
    private $registrationrepo;

    /** @var resource_link_repository instance of a resource_link repository */
    private $resourcelinkrepo;

    /** @var user_repository instance of a user repository*/
    private $userrepo;

    /** @var context_repository instance of a context repository  */
    private $contextrepo;

    /**
     * The tool_launch_service constructor.
     *
     * @param deployment_repository $deploymentrepo instance of a deployment_repository.
     * @param application_registration_repository $registrationrepo instance of an application_registration_repository.
     * @param resource_link_repository $resourcelinkrepo instance of a resource_link_repository.
     * @param user_repository $userrepo instance of a user_repository.
     * @param context_repository $contextrepo instance of a context_repository.
     */
    public function __construct(deployment_repository $deploymentrepo,
            application_registration_repository $registrationrepo, resource_link_repository $resourcelinkrepo,
            user_repository $userrepo, context_repository $contextrepo) {

        $this->deploymentrepo = $deploymentrepo;
        $this->registrationrepo = $registrationrepo;
        $this->resourcelinkrepo = $resourcelinkrepo;
        $this->userrepo = $userrepo;
        $this->contextrepo = $contextrepo;
    }

    /** Get the launch data from the launch.
     *
     * @param LtiMessageLaunch $launch the launch instance.
     * @return \stdClass the launch data.
     */
    private function get_launch_data(LtiMessageLaunch $launch): \stdClass {
        $launchdata = $launch->getLaunchData();
        $data = [
            'platform' => $launchdata['iss'],
            'clientid' => $launchdata['aud'], // See LTI_Message_Launch::validate_registration for details about aud.
            'exp' => $launchdata['exp'],
            'nonce' => $launchdata['nonce'],
            'sub' => $launchdata['sub'],
            'roles' => $launchdata['https://purl.imsglobal.org/spec/lti/claim/roles'],
            'deploymentid' => $launchdata['https://purl.imsglobal.org/spec/lti/claim/deployment_id'],
            'context' => !empty($launchdata['https://purl.imsglobal.org/spec/lti/claim/context']) ?
                $launchdata['https://purl.imsglobal.org/spec/lti/claim/context'] : null,
            'resourcelink' => $launchdata['https://purl.imsglobal.org/spec/lti/claim/resource_link'],
            'targetlinkuri' => $launchdata['https://purl.imsglobal.org/spec/lti/claim/target_link_uri'],
            'custom' => $launchdata['https://purl.imsglobal.org/spec/lti/claim/custom'] ?? null,
            'launchid' => $launch->getLaunchId(),
            'user' => [
                'givenname' => !empty($launchdata['given_name']) ? $launchdata['given_name'] : null,
                'familyname' => !empty($launchdata['family_name']) ? $launchdata['family_name'] : null,
                'name' => !empty($launchdata['name']) ? $launchdata['name'] : null,
                'email' => !empty($launchdata['email']) ? $launchdata['email'] : null,
                'picture' => !empty($launchdata['picture']) ? $launchdata['picture'] : null,
            ],
            'ags' => $launchdata['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint'] ?? null,
            'nrps' => $launchdata['https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice'] ?? null,
            'lti1p1' => $launchdata['https://purl.imsglobal.org/spec/lti/claim/lti1p1'] ?? null
        ];

        return (object) $data;
    }

    /**
     * Get a context instance from the launch data.
     *
     * @param \stdClass $launchdata launch data.
     * @param deployment $deployment the deployment to which the context belongs.
     * @return context the context instance.
     */
    private function context_from_launchdata(\stdClass $launchdata, deployment $deployment): context {
        if ($context = $this->contextrepo->find_by_contextid($launchdata->context['id'], $deployment->get_id())) {
            // The context has been mapped, just update it.
            $context->set_types($launchdata->context['type']);
        } else {
            // Map a new context.
            $context = $deployment->add_context($launchdata->context['id'], $launchdata->context['type']);
        }
        return $context;
    }

    /**
     * Get a resource_link from the launch data.
     *
     * @param \stdClass $launchdata the launch data.
     * @param \stdClass $resource the resource to which the resource link refers.
     * @param deployment $deployment the deployment to which the resource_link belongs.
     * @param context|null $context optional context in which the resource_link lives, null if not needed.
     * @return resource_link the resource_link instance.
     */
    private function resource_link_from_launchdata(\stdClass $launchdata, \stdClass $resource, deployment $deployment,
            ?context $context): resource_link {

        if ($resourcelink = $this->resourcelinkrepo->find_by_deployment($deployment, $launchdata->resourcelink['id'])) {
            // Resource link exists, so update it.
            if (isset($context)) {
                $resourcelink->set_contextid($context->get_id());
            }
            // A resource link may have been updated, via content item selection, to refer to a different resource.
            if ($resourcelink->get_resourceid() != $resource->id) {
                $resourcelink->set_resourceid($resource->id);
            }
        } else {
            // Create a new resource link.
            $resourcelink = $deployment->add_resource_link(
                $launchdata->resourcelink['id'],
                $resource->id,
                $context ? $context->get_id() : null
            );
        }
        // AGS. If the lineitemsurl is missing, it means the tool has no access to the endpoint.
        // See: http://www.imsglobal.org/spec/lti-ags/v2p0#assignment-and-grade-service-claim.
        if ($launchdata->ags && $launchdata->ags['lineitems']) {
            $resourcelink->add_grade_service(
                new \moodle_url($launchdata->ags['lineitems']),
                isset($launchdata->ags['lineitem']) ? new \moodle_url($launchdata->ags['lineitem']) : null,
                $launchdata->ags['scope']
            );
        }

        // NRPS.
        if ($launchdata->nrps) {
            $resourcelink->add_names_and_roles_service(
                new \moodle_url($launchdata->nrps['context_memberships_url']),
                $launchdata->nrps['service_versions']
            );
        }
        return $resourcelink;
    }

    /**
     * Get an lti user instance from the launch data.
     *
     * @param \stdClass $user the moodle user object.
     * @param \stdClass $launchdata the launch data.
     * @param \stdClass $resource the resource to which the user belongs.
     * @param resource_link $resourcelink the resource_link from which the user originated.
     * @return user the user instance.
     */
    private function lti_user_from_launchdata(\stdClass $user, \stdClass $launchdata, \stdClass $resource,
            resource_link $resourcelink): user {

        // Find the user based on the unique-to-the-issuer 'sub' value.
        if ($ltiuser = $this->userrepo->find_single_user_by_resource($user->id, $resource->id)) {
            // User exists, so update existing based on resource data which may have changed.
            $ltiuser->set_resourcelinkid($resourcelink->get_id());
            $ltiuser->set_lang($resource->lang);
            $ltiuser->set_city($resource->city);
            $ltiuser->set_country($resource->country);
            $ltiuser->set_institution($resource->institution);
            $ltiuser->set_timezone($resource->timezone);
            $ltiuser->set_maildisplay($resource->maildisplay);
        } else {
            // Create the lti user.
            $ltiuser = $resourcelink->add_user(
                $user->id,
                $launchdata->sub,
                $resource->lang,
                $resource->city ?? '',
                $resource->country ?? '',
                $resource->institution ?? '',
                $resource->timezone ?? '',
                $resource->maildisplay ?? null,
            );
        }
        $ltiuser->set_lastaccess(time());
        return $ltiuser;
    }

    /**
     * Get the migration claim from the launch data, or null if not found.
     *
     * @param \stdClass $launchdata the launch data.
     * @return migration_claim|null the claim instance if present in the launch data, else null.
     */
    private function migration_claim_from_launchdata(\stdClass $launchdata): ?migration_claim {
        if (!isset($launchdata->lti1p1)) {
            return null;
        }

        // Despite the spec requiring the oauth_consumer_key field be present in the migration claim:
        // (see https://www.imsglobal.org/spec/lti/v1p3/migr#oauth_consumer_key),
        // Platforms may omit this field making migration impossible.
        // E.g. for Canvas launches taking place after an assignment_selection placement.
        if (empty($launchdata->lti1p1['oauth_consumer_key'])) {
            return null;
        }

        return new migration_claim($launchdata->lti1p1, $launchdata->deploymentid,
            $launchdata->platform, $launchdata->clientid, $launchdata->exp, $launchdata->nonce,
            new legacy_consumer_repository());
    }

    /**
     * Check whether the launch user has an admin role.
     *
     * @param \stdClass $launchdata the launch data.
     * @return bool true if the user is admin, false otherwise.
     */
    private function user_is_admin(\stdClass $launchdata): bool {
        // See: http://www.imsglobal.org/spec/lti/v1p3/#role-vocabularies.
        if ($launchdata->roles) {
            $adminroles = [
                'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Administrator',
                'http://purl.imsglobal.org/vocab/lis/v2/system/person#Administrator'
            ];

            foreach ($adminroles as $validrole) {
                if (in_array($validrole, $launchdata->roles)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check whether the launch user is an instructor.
     *
     * @param \stdClass $launchdata the launch data.
     * @param bool $includelegacy whether to also consider legacy simple names as valid roles.
     * @return bool true if the user is an instructor, false otherwise.
     */
    private function user_is_staff(\stdClass $launchdata, bool $includelegacy = false): bool {
        // See: http://www.imsglobal.org/spec/lti/v1p3/#role-vocabularies.
        // This method also provides support for (legacy, deprecated) simple names for context roles.
        // I.e. 'ContentDeveloper' may be supported.
        if ($launchdata->roles) {
            $staffroles = [
                'http://purl.imsglobal.org/vocab/lis/v2/membership#ContentDeveloper',
                'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistant'
            ];

            if ($includelegacy) {
                $staffroles[] = 'ContentDeveloper';
                $staffroles[] = 'Instructor';
                $staffroles[] = 'Instructor#TeachingAssistant';
            }

            foreach ($staffroles as $validrole) {
                if (in_array($validrole, $launchdata->roles)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Handles the use case "A user launches the tool so they can view an external resource".
     *
     * @param \stdClass $user the Moodle user record, obtained via the auth_lti authentication process.
     * @param LtiMessageLaunch $launch the launch data.
     * @return array array containing [int $userid, \stdClass $resource]
     * @throws \moodle_exception if launch problems are encountered.
     */
    public function user_launches_tool(\stdClass $user, LtiMessageLaunch $launch): array {

        $launchdata = $this->get_launch_data($launch);

        if (!$registration = $this->registrationrepo->find_by_platform($launchdata->platform, $launchdata->clientid)) {
            throw new \moodle_exception('ltiadvlauncherror:invalidregistration', 'enrol_lti', '',
                [$launchdata->platform, $launchdata->clientid]);
        }

        if (!$deployment = $this->deploymentrepo->find_by_registration($registration->get_id(),
            $launchdata->deploymentid)) {
            throw new \moodle_exception('ltiadvlauncherror:invaliddeployment', 'enrol_lti', '',
                [$launchdata->deploymentid]);
        }

        $resourceuuid = $launchdata->custom['id'] ?? null;
        if (empty($resourceuuid)) {
            throw new \moodle_exception('ltiadvlauncherror:missingid', 'enrol_lti');
        }

        $resource = array_values(helper::get_lti_tools(['uuid' => $resourceuuid]));
        $resource = $resource[0] ?? null;
        if (empty($resource) || $resource->status != ENROL_INSTANCE_ENABLED) {
            throw new \moodle_exception('ltiadvlauncherror:invalidid', 'enrol_lti', '', $resourceuuid);
        }

        // Update the deployment with the legacy consumer_key information, allowing migration of users to take place in future
        // names and roles syncs.
        if ($migrationclaim = $this->migration_claim_from_launchdata($launchdata)) {
            $deployment->set_legacy_consumer_key($migrationclaim->get_consumer_key());
            $this->deploymentrepo->save($deployment);
        }

        // Save the context, if that claim is present.
        $context = null;
        if ($launchdata->context) {
            $context = $this->context_from_launchdata($launchdata, $deployment);
            $context = $this->contextrepo->save($context);
        }

        // Save the resource link for the tool deployment.
        $resourcelink = $this->resource_link_from_launchdata($launchdata, $resource, $deployment, $context);
        $resourcelink = $this->resourcelinkrepo->save($resourcelink);

        // Save the user launching the resource link.
        $ltiuser = $this->lti_user_from_launchdata($user, $launchdata, $resource, $resourcelink);
        $ltiuser = $this->userrepo->save($ltiuser);

        // Set the frame embedding mode, which controls the display of blocks and nav when launching.
        global $SESSION;
        $context = \context::instance_by_id($resource->contextid);
        $isforceembed = $launchdata->custom['force_embed'] ?? false;
        $isinstructor = $this->user_is_staff($launchdata, true) || $this->user_is_admin($launchdata);
        $isforceembed = $isforceembed || ($context->contextlevel == CONTEXT_MODULE && !$isinstructor);
        if ($isforceembed) {
            $SESSION->forcepagelayout = 'embedded';
        } else {
            unset($SESSION->forcepagelayout);
        }

        // Enrol the user in the course with no role.
        $result = helper::enrol_user($resource, $ltiuser->get_localid());
        if ($result !== helper::ENROLMENT_SUCCESSFUL) {
            throw new \moodle_exception($result, 'enrol_lti');
        }

        // Give the user the role in the given context.
        $roleid = $isinstructor ? $resource->roleinstructor : $resource->rolelearner;
        role_assign($roleid, $ltiuser->get_localid(), $resource->contextid);

        return [$ltiuser->get_localid(), $resource];
    }

}
