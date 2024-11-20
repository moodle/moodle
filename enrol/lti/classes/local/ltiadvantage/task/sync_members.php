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
use core\task\scheduled_task;
use enrol_lti\helper;
use enrol_lti\local\ltiadvantage\entity\application_registration;
use enrol_lti\local\ltiadvantage\entity\nrps_info;
use enrol_lti\local\ltiadvantage\entity\resource_link;
use enrol_lti\local\ltiadvantage\entity\user;
use enrol_lti\local\ltiadvantage\lib\issuer_database;
use enrol_lti\local\ltiadvantage\lib\launch_cache_session;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;
use Packback\Lti1p3\LtiNamesRolesProvisioningService;
use Packback\Lti1p3\LtiRegistration;
use Packback\Lti1p3\LtiServiceConnector;
use stdClass;

/**
 * LTI Advantage-specific task responsible for syncing memberships from tool platforms with the tool.
 *
 * This task may gather members from a context-level service call, depending on whether a resource-level service call
 * (which is made first) was successful. Because of the context-wide memberships, and because each published resource
 * has per-resource access control (role assignments), this task only enrols user into the course, and does not assign
 * roles to resource/course contexts. Role assignment only takes place during a launch, via the tool_launch_service.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_members extends scheduled_task {

    /** @var array Array of user photos. */
    protected $userphotos = [];

    /** @var resource_link_repository $resourcelinkrepo for fetching resource_link instances.*/
    protected $resourcelinkrepo;

    /** @var application_registration_repository $appregistrationrepo for fetching application_registration instances.*/
    protected $appregistrationrepo;

    /** @var deployment_repository $deploymentrepo for fetching deployment instances. */
    protected $deploymentrepo;

    /** @var user_repository $userrepo for fetching and saving lti user information.*/
    protected $userrepo;

    /** @var issuer_database $issuerdb library specific registration DB required to create service connectors.*/
    protected $issuerdb;

    /**
     * Get the name for this task.
     *
     * @return string the name of the task.
     */
    public function get_name(): string {
        return get_string('tasksyncmembers', 'enrol_lti');
    }

    /**
     * Make a resource-link-level memberships call.
     *
     * @param nrps_info $nrps information about names and roles service endpoints and scopes.
     * @param LtiServiceConnector $sc a service connector object.
     * @param LtiRegistration $registration the registration
     * @param resource_link $resourcelink the resource link
     * @return array an array of members if found.
     */
    protected function get_resource_link_level_members(nrps_info $nrps, LtiServiceConnector $sc, LtiRegistration $registration,
            resource_link $resourcelink) {

        // Try a resource-link-level memberships call first, falling back to context-level if no members are found.
        $reslinkmembershipsurl = $nrps->get_context_memberships_url();
        $reslinkmembershipsurl->param('rlid', $resourcelink->get_resourcelinkid());
        $servicedata = [
            'context_memberships_url' => $reslinkmembershipsurl->out(false)
        ];
        $reslinklevelnrps = new LtiNamesRolesProvisioningService($sc, $registration, $servicedata);

        mtrace('Making resource-link-level memberships request');
        return $reslinklevelnrps->getMembers();
    }

    /**
     * Make a context-level memberships call.
     *
     * @param nrps_info $nrps information about names and roles service endpoints and scopes.
     * @param LtiServiceConnector $sc a service connector object.
     * @param LtiRegistration $registration the registration
     * @return array an array of members.
     */
    protected function get_context_level_members(nrps_info $nrps, LtiServiceConnector $sc, LtiRegistration $registration) {
        $clservicedata = [
            'context_memberships_url' => $nrps->get_context_memberships_url()->out(false)
        ];
        $contextlevelnrps = new LtiNamesRolesProvisioningService($sc, $registration, $clservicedata);

        return $contextlevelnrps->getMembers();
    }

    /**
     * Make the NRPS service call and fetch members based on the given resource link.
     *
     * Memberships will be retrieved by first trying the link-level memberships service first, falling back to calling
     * the context-level memberships service only if the link-level call fails.
     *
     * @param application_registration $appregistration an application registration instance.
     * @param resource_link $resourcelink a resourcelink instance.
     * @return array an array of members.
     */
    protected function get_members_from_resource_link(application_registration $appregistration,
            resource_link $resourcelink) {

        // Get a service worker for the corresponding application registration.
        $registration = $this->issuerdb->findRegistrationByIssuer(
            $appregistration->get_platformid()->out(false),
            $appregistration->get_clientid()
        );
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        $sc = new LtiServiceConnector(new launch_cache_session(), new http_client());

        $nrps = $resourcelink->get_names_and_roles_service();
        try {
            $members = $this->get_resource_link_level_members($nrps, $sc, $registration, $resourcelink);
        } catch (\Exception $e) {
            mtrace('Link-level memberships request failed. Making context-level memberships request');
            $members = $this->get_context_level_members($nrps, $sc, $registration);
        }

        return $members;
    }

    /**
     * Performs the synchronisation of members.
     */
    public function execute() {
        if (!is_enabled_auth('lti')) {
            mtrace('Skipping task - ' . get_string('pluginnotenabled', 'auth', get_string('pluginname', 'auth_lti')));
            return;
        }
        if (!enrol_is_enabled('lti')) {
            mtrace('Skipping task - ' . get_string('enrolisdisabled', 'enrol_lti'));
            return;
        }
        $this->resourcelinkrepo = new resource_link_repository();
        $this->appregistrationrepo = new application_registration_repository();
        $this->deploymentrepo = new deployment_repository();
        $this->userrepo = new user_repository();
        $this->issuerdb = new issuer_database($this->appregistrationrepo, $this->deploymentrepo);

        $resources = helper::get_lti_tools(['status' => ENROL_INSTANCE_ENABLED, 'membersync' => 1,
            'ltiversion' => 'LTI-1p3']);

        foreach ($resources as $resource) {
            mtrace("Starting - Member sync for published resource '$resource->id' for course '$resource->courseid'.");
            $usercount = 0;
            $enrolcount = 0;
            $unenrolcount = 0;
            $syncedusers = [];

            // Get all resource_links for this shared resource.
            // This is how context/resource_link memberships calls will be made.
            $resourcelinks = $this->resourcelinkrepo->find_by_resource((int)$resource->id);
            foreach ($resourcelinks as $resourcelink) {
                mtrace("Requesting names and roles for the resource link '{$resourcelink->get_id()}' for the resource" .
                    " '{$resource->id}'");

                if (!$resourcelink->get_names_and_roles_service()) {
                    mtrace("Skipping - No names and roles service found.");
                    continue;
                }

                $appregistration = $this->appregistrationrepo->find_by_deployment(
                    $resourcelink->get_deploymentid()
                );
                if (!$appregistration) {
                    mtrace("Skipping - no corresponding application registration found.");
                    continue;
                }

                try {
                    $members = $this->get_members_from_resource_link($appregistration, $resourcelink);
                } catch (\Exception $e) {
                    mtrace("Skipping - Names and Roles service request failed: {$e->getMessage()}.");
                    continue;
                }

                // Fetched members count.
                $membercount = count($members);
                $usercount += $membercount;
                mtrace("$membercount members received.");

                // Process member information.
                [$rlenrolcount, $userids] = $this->sync_member_information($appregistration, $resource,
                    $resourcelink, $members);
                $enrolcount += $rlenrolcount;

                // Update the list of users synced for this shared resource or its context.
                $syncedusers = array_unique(array_merge($syncedusers, $userids));

                mtrace("Completed - Synced $membercount members for the resource link '{$resourcelink->get_id()}' ".
                    "for the resource '{$resource->id}'.\n");

                // Sync unenrolments on a per-resource-link basis so we have fine grained control over unenrolments.
                // If a resource link doesn't support NRPS, it will already have been skipped.
                $unenrolcount += $this->sync_unenrol_resourcelink($resourcelink, $resource, $syncedusers);
            }

            mtrace("Completed - Synced members for tool '$resource->id' in the course '$resource->courseid'. " .
                "Processed $usercount users; enrolled $enrolcount members; unenrolled $unenrolcount members.\n");
        }

        if (!empty($resources) && !empty($this->userphotos)) {
            // Sync the user profile photos.
            mtrace("Started - Syncing user profile images.");
            $countsyncedimages = $this->sync_profile_images();
            mtrace("Completed - Synced $countsyncedimages profile images.");
        }
    }

    /**
     * Process unenrolment of users for a given resource link and based on the list of recently synced users.
     *
     * @param resource_link $resourcelink the resource_link instance to which the $synced users pertains
     * @param stdClass $resource the resource object instance
     * @param array $syncedusers the array of recently synced users, who are not to be unenrolled.
     * @return int the number of unenrolled users.
     */
    protected function sync_unenrol_resourcelink(resource_link $resourcelink, stdClass $resource,
            array $syncedusers): int {

        if (!$this->should_sync_unenrol($resource->membersyncmode)) {
            return 0;
        }
        $ltiplugin = enrol_get_plugin('lti');
        $unenrolcount = 0;

        // Get all users for the resource_link instance.
        $linkusers = $this->userrepo->find_by_resource_link($resourcelink->get_id());

        foreach ($linkusers as $ltiuser) {
            if (!in_array($ltiuser->get_localid(), $syncedusers)) {
                $instance = new stdClass();
                $instance->id = $resource->enrolid;
                $instance->courseid = $resource->courseid;
                $instance->enrol = 'lti';
                $ltiplugin->unenrol_user($instance, $ltiuser->get_localid());
                $unenrolcount++;
            }
        }
        return $unenrolcount;
    }

    /**
     * Check whether the member has an instructor role or not.
     *
     * @param array $member
     * @return bool
     */
    protected function member_is_instructor(array $member): bool {
        // See: http://www.imsglobal.org/spec/lti/v1p3/#role-vocabularies.
        $memberroles = $member['roles'];
        if ($memberroles) {
            $adminroles = [
                'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Administrator',
                'http://purl.imsglobal.org/vocab/lis/v2/system/person#Administrator'
            ];
            $staffroles = [
                'http://purl.imsglobal.org/vocab/lis/v2/membership#ContentDeveloper',
                'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                'http://purl.imsglobal.org/vocab/lis/v2/membership/Instructor#TeachingAssistant',
                'ContentDeveloper',
                'Instructor',
                'Instructor#TeachingAssistant'
            ];
            $instructorroles = array_merge($adminroles, $staffroles);

            foreach ($instructorroles as $validrole) {
                if (in_array($validrole, $memberroles)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Method to determine whether to sync unenrolments or not.
     *
     * @param int $syncmode The shared resource's membersyncmode.
     * @return bool true if unenrolment should be synced, false if not.
     */
    protected function should_sync_unenrol($syncmode): bool {
        return $syncmode == helper::MEMBER_SYNC_ENROL_AND_UNENROL || $syncmode == helper::MEMBER_SYNC_UNENROL_MISSING;
    }

    /**
     * Method to determine whether to sync enrolments or not.
     *
     * @param int $syncmode The shared resource's membersyncmode.
     * @return bool true if enrolment should be synced, false if not.
     */
    protected function should_sync_enrol($syncmode): bool {
        return $syncmode == helper::MEMBER_SYNC_ENROL_AND_UNENROL || $syncmode == helper::MEMBER_SYNC_ENROL_NEW;
    }

    /**
     * Creates an lti user object from a member entry.
     *
     * @param stdClass $user the Moodle user record representing this member.
     * @param stdClass $resource the locally published resource record, used for setting user defaults.
     * @param resource_link $resourcelink the resource_link instance.
     * @param array $member the member information from the NRPS service call.
     * @return user the lti user instance.
     */
    protected function ltiuser_from_member(stdClass $user, stdClass $resource,
            resource_link $resourcelink, array $member): user {

        if (!$ltiuser = $this->userrepo->find_single_user_by_resource($user->id, $resource->id)) {
            // New user, so create them.
            $ltiuser = user::create(
                $resourcelink->get_resourceid(),
                $user->id,
                $resourcelink->get_deploymentid(),
                $member['user_id'],
                $resource->lang,
                $resource->timezone,
                $resource->city ?? '',
                $resource->country ?? '',
                $resource->institution ?? '',
                $resource->maildisplay
            );
        }
        $ltiuser->set_lastaccess(time());
        return $ltiuser;
    }

    /**
     * Performs synchronisation of member information and enrolments.
     *
     * @param application_registration $appregistration the application_registration instance.
     * @param stdClass $resource the enrol_lti_tools resource information.
     * @param resource_link $resourcelink the resource_link instance.
     * @param user[] $members an array of members to sync.
     * @return array An array containing the counts of enrolled users and a list of userids.
     */
    protected function sync_member_information(application_registration $appregistration, stdClass $resource,
            resource_link $resourcelink, array $members): array {

        $enrolcount = 0;
        $userids = [];

        // Get the verified legacy consumer key, if mapped, from the resource link's tool deployment.
        // This will be used to locate legacy user accounts and link them to LTI 1.3 users.
        // A launch must have been made in order to get the legacy consumer key from the lti1p1 migration claim.
        $deployment = $this->deploymentrepo->find($resourcelink->get_deploymentid());
        $legacyconsumerkey = $deployment->get_legacy_consumer_key() ?? '';

        foreach ($members as $member) {
            $auth = get_auth_plugin('lti');
            if ($auth->get_user_binding($appregistration->get_platformid()->out(false), $member['user_id'])) {
                // Use is bound already, so we can update them.
                $user = $auth->find_or_create_user_from_membership($member, $appregistration->get_platformid()->out(false));
                if ($user->auth != 'lti') {
                    mtrace("Skipped profile sync for user '$user->id'. The user does not belong to the LTI auth method.");
                }
            } else {
                // Not bound, so defer to the role-based provisioning mode for the resource.
                $provisioningmode = $this->member_is_instructor($member) ? $resource->provisioningmodeinstructor :
                    $resource->provisioningmodelearner;
                switch ($provisioningmode) {
                    case \auth_plugin_lti::PROVISIONING_MODE_AUTO_ONLY:
                        // Automatic provisioning - this will create a user account and log the user in.
                        $user = $auth->find_or_create_user_from_membership($member, $appregistration->get_platformid()->out(false),
                            $legacyconsumerkey);
                        break;
                    case \auth_plugin_lti::PROVISIONING_MODE_PROMPT_NEW_EXISTING:
                    case \auth_plugin_lti::PROVISIONING_MODE_PROMPT_EXISTING_ONLY:
                    default:
                        mtrace("Skipping account creation for member '{$member['user_id']}'. This member is not eligible for ".
                            "automatic creation due to the current account provisioning mode.");
                        continue 2;
                }
            }

            $ltiuser = $this->ltiuser_from_member($user, $resource, $resourcelink, $member);

            if ($this->should_sync_enrol($resource->membersyncmode)) {

                $ltiuser->set_resourcelinkid($resourcelink->get_id());
                $ltiuser = $this->userrepo->save($ltiuser);
                if ($user->auth != 'lti') {
                    mtrace("Skipped picture sync for user '$user->id'. The user does not belong to the LTI auth method.");
                } else {
                    if (isset($member['picture'])) {
                        $this->userphotos[$ltiuser->get_localid()] = $member['picture'];
                    }
                }

                // Enrol the user in the course.
                if (helper::enrol_user($resource, $ltiuser->get_localid()) === helper::ENROLMENT_SUCCESSFUL) {
                    $enrolcount++;
                }
            }

            // If the member has been created, or exists locally already, mark them as valid so as to not unenrol them
            // when syncing memberships for shared resources configured as either MEMBER_SYNC_ENROL_AND_UNENROL or
            // MEMBER_SYNC_UNENROL_MISSING.
            $userids[] = $user->id;
        }

        return [$enrolcount, $userids];
    }

    /**
     * Performs synchronisation of user profile images.
     *
     * @return int the count of synced photos.
     */
    protected function sync_profile_images(): int {
        $counter = 0;
        foreach ($this->userphotos as $userid => $url) {
            if ($url) {
                $result = helper::update_user_profile_image($userid, $url);
                if ($result === helper::PROFILE_IMAGE_UPDATE_SUCCESSFUL) {
                    $counter++;
                    mtrace("Profile image successfully downloaded and created for user '$userid' from $url.");
                } else {
                    mtrace($result);
                }
            }
        }
        return $counter;
    }
}
