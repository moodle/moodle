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

namespace communication_matrix;

use communication_matrix\local\spec\features\matrix\{
    create_room_v3 as create_room_feature,
    get_room_members_v3 as get_room_members_feature,
    remove_member_from_room_v3 as remove_member_from_room_feature,
    update_room_avatar_v3 as update_room_avatar_feature,
    update_room_name_v3 as update_room_name_feature,
    update_room_topic_v3 as update_room_topic_feature,
    upload_content_v3 as upload_content_feature,
    media_create_v1 as media_create_feature,
};
use communication_matrix\local\spec\features\synapse\{
    create_user_v2 as create_user_feature,
    get_room_info_v1 as get_room_info_feature,
    get_user_info_v2 as get_user_info_feature,
    invite_member_to_room_v1 as invite_member_to_room_feature,
};
use core_communication\processor;
use stdClass;
use GuzzleHttp\Psr7\Response;

/**
 * class communication_feature to handle matrix specific actions.
 *
 * @package    communication_matrix
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class communication_feature implements
    \core_communication\communication_provider,
    \core_communication\form_provider,
    \core_communication\room_chat_provider,
    \core_communication\room_user_provider,
    \core_communication\synchronise_provider,
    \core_communication\user_provider {
    /** @var ?matrix_room $room The matrix room object to update room information */
    private ?matrix_room $room = null;

    /** @var string|null The URI of the home server */
    protected ?string $homeserverurl = null;

    /** @var string The URI of the Matrix web client */
    protected string $webclienturl;

    /** @var \communication_matrix\local\spec\v1p1|null The Matrix API processor */
    protected ?matrix_client $matrixapi;

    /**
     * Load the communication provider for the communication api.
     *
     * @param processor $communication The communication processor object
     * @return communication_feature The communication provider object
     */
    public static function load_for_instance(processor $communication): self {
        return new self($communication);
    }

    /**
     * Reload the room information.
     * This may be necessary after a room has been created or updated via the adhoc task.
     * This is primarily intended for use in unit testing, but may have real world cases too.
     */
    public function reload(): void {
        $this->room = null;
        $this->processor = processor::load_by_id($this->processor->get_id());
    }

    /**
     * Constructor for communication provider to initialize necessary objects for api cals etc..
     *
     * @param processor $processor The communication processor object
     */
    private function __construct(
        private \core_communication\processor $processor,
    ) {
        $this->homeserverurl = get_config('communication_matrix', 'matrixhomeserverurl');
        $this->webclienturl = get_config('communication_matrix', 'matrixelementurl');

        if ($processor::is_provider_available('communication_matrix')) {
            // Generate the API instance.
            $this->matrixapi = matrix_client::instance(
                serverurl: $this->homeserverurl,
                accesstoken: get_config('communication_matrix', 'matrixaccesstoken'),
            );
        }
    }

    /**
     * Check whether the room configuration has been created yet.
     *
     * @return bool
     */
    protected function room_exists(): bool {
        return (bool) $this->get_room_configuration();
    }

    /**
     * Whether the room exists on the remote server.
     * This does not involve a remote call, but checks whether Moodle is aware of the room id.
     * @return bool
     */
    protected function remote_room_exists(): bool {
        $room = $this->get_room_configuration();

        return $room && ($room->get_room_id() !== null);
    }

    /**
     * Get the stored room configuration.
     * @return null|matrix_room
     */
    public function get_room_configuration(): ?matrix_room {
        $this->room = matrix_room::load_by_processor_id($this->processor->get_id());
        return $this->room;
    }

    /**
     * Return the current room id.
     *
     * @return string|null
     */
    public function get_room_id(): ?string {
        return $this->get_room_configuration()?->get_room_id();
    }

    /**
     * Create members.
     *
     * @param array $userids The Moodle user ids to create
     */
    public function create_members(array $userids): void {
        $addedmembers = [];

        // This API requiures the create_user feature.
        $this->matrixapi->require_feature(create_user_feature::class);

        foreach ($userids as $userid) {
            $user = \core_user::get_user($userid);
            $userfullname = fullname($user);

            // Proceed if we have a user's full name and email to work with.
            if (!empty($user->email) && !empty($userfullname)) {
                $qualifiedmuid = matrix_user_manager::get_formatted_matrix_userid($user->username);

                // First create user in matrix.
                $response = $this->matrixapi->create_user(
                    userid: $qualifiedmuid,
                    displayname: $userfullname,
                    threepids: [(object) [
                        'medium' => 'email',
                        'address' => $user->email,
                    ], ],
                    externalids: [],
                );
                $body = json_decode($response->getBody());

                if (!empty($matrixuserid = $body->name)) {
                    // Then create matrix user id in moodle.
                    matrix_user_manager::set_matrix_userid_in_moodle($userid, $qualifiedmuid);
                    if ($this->add_registered_matrix_user_to_room($matrixuserid)) {
                        $addedmembers[] = $userid;
                    }
                }
            }
        }

        // Set the power level of the users.
        if (!empty($addedmembers) && $this->is_power_levels_update_required($addedmembers)) {
            $this->set_matrix_power_levels();
        }

        // Mark then users as synced for the added members.
        $this->processor->mark_users_as_synced($addedmembers);
    }

    public function update_room_membership(array $userids): void {

        // Filter out any users that are not room members yet.
        $response = $this->matrixapi->get_room_members(
            roomid: $this->get_room_id(),
        );
        $body = self::get_body($response);

        if (isset($body->joined)) {
            foreach ($userids as $key => $userid) {
                $matrixuserid = matrix_user_manager::get_matrixid_from_moodle(
                    userid: $userid,
                );
                if (!array_key_exists($matrixuserid, (array) $body->joined)) {
                    unset($userids[$key]);
                }
            }
        }

        $this->set_matrix_power_levels();
        // Mark the users as synced for the updated members.
        $this->processor->mark_users_as_synced($userids);
    }

    /**
     * Add members to a room.
     *
     * @param array $userids The user ids to add
     */
    public function add_members_to_room(array $userids): void {
        $unregisteredmembers = [];
        $addedmembers = [];

        foreach ($userids as $userid) {
            $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($userid);

            if ($matrixuserid && $this->check_user_exists($matrixuserid)) {
                if ($this->add_registered_matrix_user_to_room($matrixuserid)) {
                    $addedmembers[] = $userid;
                }
            } else {
                $unregisteredmembers[] = $userid;
            }
        }

        // Set the power level of the users.
        if (!empty($addedmembers) && $this->is_power_levels_update_required($addedmembers)) {
            $this->set_matrix_power_levels();
        }

        // Mark then users as synced for the added members.
        $this->processor->mark_users_as_synced($addedmembers);

        // Create Matrix users.
        if (count($unregisteredmembers) > 0) {
            $this->create_members($unregisteredmembers);
        }
    }

    /**
     * Adds the registered matrix user id to room.
     *
     * @param string $matrixuserid Registered matrix user id
     */
    private function add_registered_matrix_user_to_room(string $matrixuserid): bool {
        // Require the invite_member_to_room API feature.
        $this->matrixapi->require_feature(invite_member_to_room_feature::class);

        if (!$this->check_room_membership($matrixuserid)) {
            $response = $this->matrixapi->invite_member_to_room(
                roomid: $this->get_room_id(),
                userid: $matrixuserid,
            );

            $body = self::get_body($response);
            if (empty($body->room_id)) {
                return false;
            }

            if ($body->room_id !== $this->get_room_id()) {
                return false;
            }

            return true;
        }
        return false;
    }

    /**
     * Remove members from a room.
     *
     * @param array $userids The Moodle user ids to remove
     */
    public function remove_members_from_room(array $userids): void {
        // This API requiures the remove_members_from_room feature.
        $this->matrixapi->require_feature(remove_member_from_room_feature::class);

        if ($this->get_room_id() === null) {
            return;
        }

        // Remove the power level for the user first.
        $this->set_matrix_power_levels($userids);

        $membersremoved = [];

        $currentpowerlevels = $this->get_current_powerlevel_data();
        $currentuserpowerlevels = (array) $currentpowerlevels->users ?? [];

        foreach ($userids as $userid) {
            // Check user is member of room first.
            $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($userid);

            if (!$matrixuserid) {
                // Unable to find a matrix userid for this user.
                continue;
            }

            if (array_key_exists($matrixuserid, $currentuserpowerlevels)) {
                if ($currentuserpowerlevels[$matrixuserid] >= matrix_constants::POWER_LEVEL_MAXIMUM) {
                    // Skip removing the user if they are an admin.
                    continue;
                }
            }

            if (
                $this->check_user_exists($matrixuserid) &&
                $this->check_room_membership($matrixuserid)
            ) {
                $this->matrixapi->remove_member_from_room(
                    roomid: $this->get_room_id(),
                    userid: $matrixuserid,
                );

                $membersremoved[] = $userid;
            }
        }

        $this->processor->delete_instance_user_mapping($membersremoved);
    }

    /**
     * Check if a user exists in Matrix.
     * Use if user existence is needed before doing something else.
     *
     * @param string $matrixuserid The Matrix user id to check
     * @return bool
     */
    public function check_user_exists(string $matrixuserid): bool {
        // This API requires the get_user_info feature.
        $this->matrixapi->require_feature(get_user_info_feature::class);

        $response = $this->matrixapi->get_user_info(
            userid: $matrixuserid,
        );
        $body = self::get_body($response);

        return isset($body->name);
    }

    /**
     * Check if a user is a member of a room.
     * Use if membership confirmation is needed before doing something else.
     *
     * @param string $matrixuserid The Matrix user id to check
     * @return bool
     */
    public function check_room_membership(string $matrixuserid): bool {
        // This API requires the get_room_members feature.
        $this->matrixapi->require_feature(get_room_members_feature::class);

        $response = $this->matrixapi->get_room_members(
            roomid: $this->get_room_id(),
        );
        $body = self::get_body($response);

        // Check user id is in the returned room member ids.
        return isset($body->joined) && array_key_exists($matrixuserid, (array) $body->joined);
    }

    /**
     * Create a room based on the data in the communication instance.
     *
     * @return bool
     */
    public function create_chat_room(): bool {
        if ($this->remote_room_exists()) {
            // A room already exists. Update it instead.
            return $this->update_chat_room();
        }

        // This method requires the create_room API feature.
        $this->matrixapi->require_feature(create_room_feature::class);

        $room = $this->get_room_configuration();

        $response = $this->matrixapi->create_room(
            name: $this->processor->get_room_name(),
            visibility: 'private',
            preset: 'private_chat',
            initialstate: [],
            options: [
                'topic' => $room->get_topic(),
            ],
        );

        $response = self::get_body($response);

        if (empty($response->room_id)) {
            throw new \moodle_exception(
                'Unable to determine ID of matrix room',
            );
        }

        // Update our record of the matrix room_id.
        $room->update_room_record(
            roomid: $response->room_id,
        );

        // Update the room avatar.
        $this->update_room_avatar();
        return true;
    }

    public function update_chat_room(): bool {
        if (!$this->remote_room_exists()) {
            // No room exists. Create it instead.
            return $this->create_chat_room();
        }

        $this->matrixapi->require_features([
            get_room_info_feature::class,
            update_room_name_feature::class,
            update_room_topic_feature::class,
        ]);

        // Get room data.
        $response = $this->matrixapi->get_room_info(
            roomid: $this->get_room_id(),
        );
        $remoteroomdata = self::get_body($response);

        // Update the room name when it's updated from the form.
        if ($remoteroomdata->name !== $this->processor->get_room_name()) {
            $this->matrixapi->update_room_name(
                roomid: $this->get_room_id(),
                name: $this->processor->get_room_name(),
            );
        }

        // Update the room topic if set.
        $localroomdata = $this->get_room_configuration();
        if ($remoteroomdata->topic !== $localroomdata->get_topic()) {
            $this->matrixapi->update_room_topic(
                roomid: $localroomdata->get_room_id(),
                topic: $localroomdata->get_topic(),
            );
        }

        // Update room avatar.
        $this->update_room_avatar();

        return true;
    }

    public function delete_chat_room(): bool {
        $this->get_room_configuration()->delete_room_record();
        $this->room = null;

        return true;
    }

    /**
     * Update the room avatar when an instance image is added or updated.
     */
    public function update_room_avatar(): void {
        // Both of the following features of the remote API are required.
        $this->matrixapi->require_features([
            upload_content_feature::class,
            update_room_avatar_feature::class,
        ]);

        // Check if we have an avatar that needs to be synced.
        if ($this->processor->is_avatar_synced()) {
            return;
        }

        $instanceimage = $this->processor->get_avatar();
        $contenturi = null;

        if ($this->matrixapi->implements_feature(media_create_feature::class)) {
            // From version 1.7 we can fetch a mxc URI and use it before uploading the content.
            if ($instanceimage) {
                $response = $this->matrixapi->media_create();
                $contenturi = self::get_body($response)->content_uri;

                // Now update the room avatar.
                $response = $this->matrixapi->update_room_avatar(
                    roomid: $this->get_room_id(),
                    avatarurl: $contenturi,
                );

                // And finally upload the content.
                $this->matrixapi->upload_content($instanceimage);
            } else {
                $response = $this->matrixapi->update_room_avatar(
                    roomid: $this->get_room_id(),
                    avatarurl: null,
                );
            }
        } else {
            // Prior to v1.7 the only way to upload content was to upload the content, which returns a mxc URI to use.

            if ($instanceimage) {
                // First upload the content.
                $response = $this->matrixapi->upload_content($instanceimage);
                $body = self::get_body($response);
                $contenturi = $body->content_uri;
            }

            // Now update the room avatar.
            $response = $this->matrixapi->update_room_avatar(
                roomid: $this->get_room_id(),
                avatarurl: $contenturi,
            );
        }

        // Indicate the avatar has been synced if it was successfully set with Matrix.
        if ($response->getReasonPhrase() === 'OK') {
            $this->processor->set_avatar_synced_flag(true);
        }
    }

    public function get_chat_room_url(): ?string {
        if (!$this->get_room_id()) {
            // We don't have a room id for this record.
            return null;
        }

        return sprintf(
            "%s#/room/%s",
            $this->webclienturl,
            $this->get_room_id(),
        );
    }

    public function save_form_data(\stdClass $instance): void {
        $matrixroomtopic = $instance->matrixroomtopic ?? null;
        $room = $this->get_room_configuration();

        if ($room) {
            $room->update_room_record(
                topic: $matrixroomtopic,
            );
        } else {
            $this->room = matrix_room::create_room_record(
                processorid: $this->processor->get_id(),
                topic: $matrixroomtopic,
            );
        }
    }

    public function set_form_data(\stdClass $instance): void {
        if (!empty($instance->id) && !empty($this->processor->get_id())) {
            if ($this->room_exists()) {
                $instance->matrixroomtopic = $this->get_room_configuration()->get_topic();
            }
        }
    }

    public static function set_form_definition(\MoodleQuickForm $mform): void {
        // Room description for the communication provider.
        $mform->insertElementBefore($mform->createElement(
            'text',
            'matrixroomtopic',
            get_string('matrixroomtopic', 'communication_matrix'),
            'maxlength="255" size="20"'
        ), 'addcommunicationoptionshere');
        $mform->addHelpButton('matrixroomtopic', 'matrixroomtopic', 'communication_matrix');
        $mform->setType('matrixroomtopic', PARAM_TEXT);
    }

    /**
     * Get the body of a response as a stdClass.
     *
     * @param Response $response
     * @return stdClass
     */
    public static function get_body(Response $response): stdClass {
        $body = $response->getBody();

        return json_decode($body, false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Set the matrix power level with the room.
     *
     * Users with a non-moodle power level are not typically removed unless specified in the $forceremoval param.
     * Matrix Admin users are never removed.
     *
     * @param array $forceremoval The users to force removal from the room, even if they have a custom power level
     */
    private function set_matrix_power_levels(
        array $forceremoval = [],
    ): void {
        // Get the current power levels.
        $currentpowerlevels = $this->get_current_powerlevel_data();
        $currentuserpowerlevels = (array) $currentpowerlevels->users ?? [];

        // Get all the current users who need to be in the room.
        $userlist = $this->processor->get_all_userids_for_instance();

        // Translate the user ids to matrix user ids.
        $userlist = array_combine(
            array_map(
                fn ($userid) => matrix_user_manager::get_matrixid_from_moodle($userid),
                $userlist,
            ),
            $userlist,
        );

        // Determine the power levels, and filter out anyone with the default level.
        $newuserpowerlevels = array_filter(
            array_map(
                fn($userid) => $this->get_user_allowed_power_level($userid),
                $userlist,
            ),
            fn($level) => $level !== matrix_constants::POWER_LEVEL_DEFAULT,
        );

        // Keep current room admins, and users which don't use our MODERATOR power level without changing them.
        $staticusers = $this->get_users_with_custom_power_level($currentuserpowerlevels);
        foreach ($staticusers as $userid => $level) {
            $newuserpowerlevels[$userid] = $level;
        }

        if (!empty($forceremoval)) {
            // Remove the users from the power levels if they are not admins.
            foreach ($forceremoval as $userid) {
                $muid = matrix_user_manager::get_matrixid_from_moodle($userid);
                if (isset($newuserpowerlevels[$muid]) && $newuserpowerlevels[$muid] < matrix_constants::POWER_LEVEL_MAXIMUM) {
                    unset($newuserpowerlevels[$muid]);
                }
            }
        }

        if (!$this->power_levels_changed($currentuserpowerlevels, $newuserpowerlevels)) {
            // No changes to make.
            return;
        }

        // Update the power levels for the room.
        $this->matrixapi->update_room_power_levels(
            roomid: $this->get_room_id(),
            users: $newuserpowerlevels,
        );
    }

    /**
     * Filter the list of users provided to remove those with a moodle-related power level.
     *
     * @param array $users
     * @return array
     */
    private function get_users_with_custom_power_level(array $users): array {
        return array_filter(
            $users,
            function ($level): bool {
                switch ($level) {
                    case matrix_constants::POWER_LEVEL_DEFAULT:
                    case matrix_constants::POWER_LEVEL_MOODLE_SITE_ADMIN:
                    case matrix_constants::POWER_LEVEL_MOODLE_MODERATOR:
                        return false;
                    default:
                        return true;
                }
            },
        );
    }

    /**
     * Check whether power levels have changed compared with the proposed power levels.
     *
     * @param array $currentuserpowerlevels The current power levels
     * @param array $newuserpowerlevels The new power levels proposed
     * @return bool Whether there is any change to be made
     */
    private function power_levels_changed(
        array $currentuserpowerlevels,
        array $newuserpowerlevels,
    ): bool {
        if (count($newuserpowerlevels) !== count($currentuserpowerlevels)) {
            // Different number of keys - there must be a difference then.
            return true;
        }

        // Sort the power levels.
        ksort($newuserpowerlevels, SORT_NUMERIC);

        // Get the current power levels.
        ksort($currentuserpowerlevels);

        $diff = array_merge(
            array_diff_assoc(
                $newuserpowerlevels,
                $currentuserpowerlevels,
            ),
            array_diff_assoc(
                $currentuserpowerlevels,
                $newuserpowerlevels,
            ),
        );

        return count($diff) > 0;
    }

    /**
     * Get the current power level for the room.
     *
     * @return stdClass
     */
    private function get_current_powerlevel_data(): \stdClass {
        $roomid = $this->get_room_id();
        $response = $this->matrixapi->get_room_power_levels(
            roomid: $roomid,
        );
        if ($response->getStatusCode() !== 200) {
            throw new \moodle_exception(
                'Unable to get power levels for room',
            );
        }

        return $this->get_body($response);
    }

    /**
     * Determine if a power level update is required.
     * Matrix will always set a user to the default power level of 0 when a power level update is made.
     * That is, unless we specify another level. As long as one person's level is greater than the default,
     * we will need to set the power levels of all users greater than the default.
     *
     * @param array $userids The users to evaluate
     * @return boolean Returns true if an update is required
     */
    private function is_power_levels_update_required(array $userids): bool {
        // Is the user's power level greater than the default?
        foreach ($userids as $userid) {
            if ($this->get_user_allowed_power_level($userid) > matrix_constants::POWER_LEVEL_DEFAULT) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the allowed power level for the user id according to perms/site admin or default.
     *
     * @param int $userid
     * @return int
     */
    public function get_user_allowed_power_level(int $userid): int {
        $powerlevel = matrix_constants::POWER_LEVEL_DEFAULT;

        if (has_capability('communication/matrix:moderator', $this->processor->get_context(), $userid)) {
            $powerlevel = matrix_constants::POWER_LEVEL_MOODLE_MODERATOR;
        }

        // If site admin, override all caps.
        if (is_siteadmin($userid)) {
            $powerlevel = matrix_constants::POWER_LEVEL_MOODLE_SITE_ADMIN;
        }

        return $powerlevel;
    }

    /*
     * Check if matrix settings are configured
     *
     * @return boolean
     */
    public static function is_configured(): bool {
        // Matrix communication settings.
        $matrixhomeserverurl = get_config('communication_matrix', 'matrixhomeserverurl');
        $matrixaccesstoken = get_config('communication_matrix', 'matrixaccesstoken');
        $matrixelementurl = get_config('communication_matrix', 'matrixelementurl');

        if (
            !empty($matrixhomeserverurl) &&
            !empty($matrixaccesstoken) &&
            (PHPUNIT_TEST || defined('BEHAT_SITE_RUNNING') || !empty($matrixelementurl))
        ) {
            return true;
        }
        return false;
    }

    public function synchronise_room_members(): void {
        $this->set_matrix_power_levels();
    }
}
