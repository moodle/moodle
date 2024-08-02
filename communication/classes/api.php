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

namespace core_communication;

use core\context;
use core_communication\task\add_members_to_room_task;
use core_communication\task\create_and_configure_room_task;
use core_communication\task\delete_room_task;
use core_communication\task\remove_members_from_room;
use core_communication\task\synchronise_provider_task;
use core_communication\task\update_room_task;
use core_communication\task\update_room_membership_task;
use stdClass;

/**
 * Class api is the public endpoint of the communication api. This class is the point of contact for api usage.
 *
 * Communication api allows to add ad-hoc tasks to the queue to perform actions on the communication providers. This api will
 * not allow any immediate actions to be performed on the communication providers. It will only add the tasks to the queue. The
 * exception has been made for deletion of members in case of deleting the user. This is because the user will not be available.
 * The member management api part allows run actions immediately if required.
 *
 * Communication api does allow to have form elements related to communication api in the required forms. This is done by using
 * the form_definition method. This method will add the form elements to the form.
 *
 * @package    core_communication
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {
    /**
     * @var null|processor $communication The communication settings object
     */
    private ?processor $communication;

    /**
     * Communication handler constructor to manage and handle all communication related actions.
     *
     * This class is the entrypoint for all kinda usages.
     * It will be used by the other api to manage the communication providers.
     *
     * @param context $context The context of the item for the instance
     * @param string $component The component of the item for the instance
     * @param string $instancetype The type of the item for the instance
     * @param int $instanceid The id of the instance
     * @param string|null $provider The provider type - if null will load for this context's active provider.
     *
     */
    private function __construct(
        private context $context,
        private string $component,
        private string $instancetype,
        private int $instanceid,
        private ?string $provider = null,
    ) {
        $this->communication = processor::load_by_instance(
            context: $context,
            component: $component,
            instancetype: $instancetype,
            instanceid: $instanceid,
            provider: $provider,
        );
    }

    /**
     * Get the communication processor object.
     *
     * @param context $context The context of the item for the instance
     * @param string $component The component of the item for the instance
     * @param string $instancetype The type of the item for the instance
     * @param int $instanceid The id of the instance
     * @param string|null $provider The provider type - if null will load for this context's active provider.
     * @return api
     */
    public static function load_by_instance(
        context $context,
        string $component,
        string $instancetype,
        int $instanceid,
        ?string $provider = null,
    ): self {
        return new self(
            context: $context,
            component: $component,
            instancetype: $instancetype,
            instanceid: $instanceid,
            provider: $provider,
        );
    }

    /**
     * Reload in the internal instance data.
     */
    public function reload(): void {
        $this->communication = processor::load_by_instance(
            context: $this->context,
            component: $this->component,
            instancetype: $this->instancetype,
            instanceid: $this->instanceid,
            provider: $this->provider,
        );
    }

    /**
     * Return the underlying communication processor object.
     *
     * @return ?processor
     */
    public function get_processor(): ?processor {
        return $this->communication;
    }

    /**
     * Return the room provider.
     *
     * @return \core_communication\room_chat_provider
     */
    public function get_room_provider(): \core_communication\room_chat_provider {
        return $this->communication->get_room_provider();
    }

    /**
     * Return the user provider.
     *
     * @return \core_communication\user_provider
     */
    public function get_user_provider(): \core_communication\user_provider {
        return $this->communication->get_user_provider();
    }

    /**
     * Return the room user provider.
     *
     * @return \core_communication\room_user_provider
     */
    public function get_room_user_provider(): \core_communication\room_user_provider {
        return $this->communication->get_room_user_provider();
    }

    /**
     * Return the form provider.
     *
     * @return \core_communication\form_provider
     */
    public function get_form_provider(): \core_communication\form_provider {
        return $this->communication->get_form_provider();
    }

    /**
     * Check if the communication api is enabled.
     */
    public static function is_available(): bool {
        return (bool) get_config('core', 'enablecommunicationsubsystem');
    }

    /**
     * Get the communication room url.
     *
     * @return string|null
     */
    public function get_communication_room_url(): ?string {
        return $this->communication?->get_room_url();
    }

    /**
     * Get the list of plugins for form selection.
     *
     * @return array
     */
    public static function get_communication_plugin_list_for_form(): array {
        // Add the option to have communication disabled.
        $selection[processor::PROVIDER_NONE] = get_string('nocommunicationselected', 'communication');
        $communicationplugins = \core\plugininfo\communication::get_enabled_plugins();
        foreach ($communicationplugins as $pluginname => $notusing) {
            $provider = 'communication_' . $pluginname;
            if (processor::is_provider_available($provider)) {
                $selection[$provider] = get_string('pluginname', 'communication_' . $pluginname);
            }
        }
        return $selection;
    }

    /**
     * Get the enabled communication providers and default provider according to the selected provider.
     *
     * @param string|null $selecteddefaulprovider
     * @return array
     */
    public static function get_enabled_providers_and_default(?string $selecteddefaulprovider = null): array {
        $communicationproviders = self::get_communication_plugin_list_for_form();
        $defaulprovider = processor::PROVIDER_NONE;
        if (!empty($selecteddefaulprovider) && array_key_exists($selecteddefaulprovider, $communicationproviders)) {
            $defaulprovider = $selecteddefaulprovider;
        }
        return [$communicationproviders, $defaulprovider];
    }

    /**
     * Define the form elements for the communication api.
     * This method will be called from the form definition method of the instance.
     *
     * @param \MoodleQuickForm $mform The form element
     * @param string $selectdefaultcommunication The default selected communication provider in the form field
     */
    public function form_definition(
        \MoodleQuickForm $mform,
        string $selectdefaultcommunication = processor::PROVIDER_NONE
    ): void {
        global $PAGE;

        [$communicationproviders, $defaulprovider] = self::get_enabled_providers_and_default($selectdefaultcommunication);

        $PAGE->requires->js_call_amd('core_communication/providerchooser', 'init');

        // List the communication providers.
        $mform->addElement(
            'select',
            'selectedcommunication',
            get_string('selectcommunicationprovider', 'communication'),
            $communicationproviders,
            ['data-communicationchooser-field' => 'selector'],
        );
        $mform->addHelpButton('selectedcommunication', 'selectcommunicationprovider', 'communication');
        $mform->setDefault('selectedcommunication', $defaulprovider);

        $mform->registerNoSubmitButton('updatecommunicationprovider');
        $mform->addElement(
            'submit',
            'updatecommunicationprovider',
            'update communication',
            ['data-communicationchooser-field' => 'updateButton', 'class' => 'd-none']
        );

        // Just a placeholder for the communication options.
        $mform->addElement('hidden', 'addcommunicationoptionshere');
        $mform->setType('addcommunicationoptionshere', PARAM_BOOL);
    }

    /**
     * Set the form definitions for the plugins.
     *
     * @param \MoodleQuickForm $mform The moodle form
     * @param string $provider The provider name
     */
    public function form_definition_for_provider(\MoodleQuickForm $mform, string $provider = processor::PROVIDER_NONE): void {
        if ($provider === processor::PROVIDER_NONE) {
            return;
        }

        // Room name for the communication provider.
        $mform->insertElementBefore(
            $mform->createElement(
                'text',
                $provider . 'roomname',
                get_string('communicationroomname', 'communication'),
                'maxlength="100" size="20"'
            ),
            'addcommunicationoptionshere'
        );
        $mform->setType($provider . 'roomname', PARAM_TEXT);

        $mform->insertElementBefore(
            $mform->createElement(
                'static',
                'communicationroomnameinfo',
                '',
                get_string('communicationroomnameinfo', 'communication'),
            ),
            'addcommunicationoptionshere',
        );

        processor::set_provider_specific_form_definition($provider, $mform);
    }

    /**
     * Get the avatar file.
     *
     * @return null|\stored_file
     */
    public function get_avatar(): ?\stored_file {
        $filename = $this->communication->get_avatar_filename();
        if ($filename === null) {
            return null;
        }
        $fs = get_file_storage();
        $args = (array) $this->get_avatar_filerecord($filename);
        return $fs->get_file(...$args) ?: null;
    }

    /**
     * Get the avatar file record for the avatar for filesystem.
     *
     * @param string $filename The filename of the avatar
     * @return stdClass
     */
    protected function get_avatar_filerecord(string $filename): stdClass {
        return (object) [
            'contextid' => \core\context\system::instance()->id,
            'component' => 'core_communication',
            'filearea' => 'avatar',
            'itemid' => $this->communication->get_id(),
            'filepath' => '/',
            'filename' => $filename,
        ];
    }

    /**
     * Get the avatar file.
     *
     * If null is set, then delete the old area file and set the avatarfilename to null.
     * This will make sure the plugin api deletes the avatar from the room.
     *
     * @param null|\stored_file $avatar The stored file for the avatar
     * @return bool
     */
    public function set_avatar(?\stored_file $avatar): bool {
        $currentfilename = $this->communication->get_avatar_filename();
        if ($avatar === null && empty($currentfilename)) {
            return false;
        }

        $currentfilerecord = $this->get_avatar();
        if ($avatar && $currentfilerecord) {
            $currentfilehash = $currentfilerecord->get_contenthash();
            $updatedfilehash = $avatar->get_contenthash();

            // No update required.
            if ($currentfilehash === $updatedfilehash) {
                return false;
            }
        }

        $context = \core\context\system::instance();

        $fs = get_file_storage();
        $fs->delete_area_files(
            $context->id,
            'core_communication',
            'avatar',
            $this->communication->get_id()
        );

        if ($avatar) {
            $fs->create_file_from_storedfile(
                $this->get_avatar_filerecord($avatar->get_filename()),
                $avatar,
            );
            $this->communication->set_avatar_filename($avatar->get_filename());
        } else {
            $this->communication->set_avatar_filename(null);
        }

        // Indicate that we need to sync the avatar when the update task is run.
        $this->communication->set_avatar_synced_flag(false);

        return true;
    }

    /**
     * A helper to fetch the room name
     *
     * @return string
     */
    public function get_room_name(): string {
        if (!$this->communication) {
            return '';
        }
        return $this->communication->get_room_name();
    }

    /**
     * Set the form data if the data is already available.
     *
     * @param \stdClass $instance The instance object
     */
    public function set_data(\stdClass $instance): void {
        if (!empty($instance->id) && $this->communication) {
            $instance->selectedcommunication = $this->communication->get_provider();
            $roomnameidentifier = $this->get_provider() . 'roomname';
            $instance->$roomnameidentifier = $this->communication->get_room_name();

            $this->communication->get_form_provider()->set_form_data($instance);
        }
    }

    /**
     * Get the communication provider.
     *
     * @return string
     */
    public function get_provider(): string {
        if (!$this->communication) {
            return '';
        }
        return $this->communication->get_provider();
    }

    /**
     * Configure the room and membership by provider selected for the communication instance.
     *
     * This method will add a task to the queue to configure the room and membership by comparing the change of provider.
     * There are some major cases to consider for this method to allow minimum duplication when this api is used.
     * Some of the major cases are:
     * 1. If the communication instance is not created at all, then create it and add members.
     * 2. If the current provider is none and the new provider is also none, then nothing to do.
     * 3. If the current and existing provider is the same, don't need to do anything.
     * 4. If provider set to none, remove all the members.
     * 5. If previous provider was not none and current provider is not none, but a different provider, remove members and add
     * for the new one.
     * 6. If previous provider was none and current provider is not none, don't need to remove, just
     * update the selected provider and add users to that provider. Do not queue the task to add members to room as the room
     * might not have created yet. The add room task adds the task to add members to room anyway.
     * 7. If it's a new provider, never used/created, now create the room after considering all these cases for a new provider.
     *
     * @param string $provider The provider name
     * @param \stdClass $instance The instance object
     * @param string $communicationroomname The communication room name
     * @param array $users The user ids to add to the room
     * @param null|\stored_file $instanceimage The stored file for the avatar
     * @param bool $queue Queue the task for the provider room or not
     */
    public function configure_room_and_membership_by_provider(
        string $provider,
        stdClass $instance,
        string $communicationroomname,
        array $users,
        ?\stored_file $instanceimage = null,
        bool $queue = true,
    ): void {
        // If the current provider is inactive and the new provider is also none, then nothing to do.
        if (
            $this->communication !== null &&
            $this->communication->get_provider_status() === processor::PROVIDER_INACTIVE &&
            $provider === processor::PROVIDER_NONE
        ) {
            return;
        }

        // If provider set to none, remove all the members.
        if (
            $this->communication !== null &&
            $this->communication->get_provider_status() === processor::PROVIDER_ACTIVE &&
            $provider === processor::PROVIDER_NONE
        ) {
            $this->remove_all_members_from_room();
            $this->update_room(
                active: processor::PROVIDER_INACTIVE,
                communicationroomname: $communicationroomname,
                avatar: $instanceimage,
                instance: $instance,
                queue: $queue,
            );
            return;
        }

        if (
            // If previous provider was active and not none and current provider is not none, but a different provider,
            // remove members and de-activate the previous provider.
            $this->communication !== null &&
            $this->communication->get_provider_status() === processor::PROVIDER_ACTIVE &&
            $provider !== $this->get_provider()
        ) {
            $this->remove_all_members_from_room();
            // Now deactivate the previous provider.
            $this->update_room(
                active: processor::PROVIDER_INACTIVE,
                instance: $instance,
                queue: $queue,
            );
        }

        // Now re-init the constructor for the new provider.
        $this->__construct(
            context: $this->context,
            component: $this->component,
            instancetype: $this->instancetype,
            instanceid: $this->instanceid,
            provider: $provider,
        );

        // If it's a new provider, never used/created, now create the room.
        if ($this->communication === null) {
            $this->create_and_configure_room(
                communicationroomname: $communicationroomname,
                avatar: $instanceimage,
                instance: $instance,
                queue: $queue,
            );
            $queueusertask = false;
        } else {
            // Otherwise update the room.
            $this->update_room(
                active: processor::PROVIDER_ACTIVE,
                communicationroomname: $communicationroomname,
                avatar: $instanceimage,
                instance: $instance,
                queue: $queue,
            );
            $queueusertask = true;
        }

        // Now add the members.
        $this->add_members_to_room(
            userids: $users,
            queue: $queueusertask,
        );

    }

    /**
     * Create a communication ad-hoc task for create operation.
     * This method will add a task to the queue to create the room.
     *
     * @param string $communicationroomname The communication room name
     * @param null|\stored_file $avatar The stored file for the avatar
     * @param \stdClass|null $instance The actual instance object
     * @param bool $queue Whether to queue the task or not
     */
    public function create_and_configure_room(
        string $communicationroomname,
        ?\stored_file $avatar = null,
        ?\stdClass $instance = null,
        bool $queue = true,
    ): void {
        if ($this->provider === processor::PROVIDER_NONE || $this->provider === '') {
            return;
        }
        // Create communication record.
        $this->communication = processor::create_instance(
            context: $this->context,
            provider: $this->provider,
            instanceid: $this->instanceid,
            component: $this->component,
            instancetype: $this->instancetype,
            roomname: $communicationroomname,
        );

        // Update provider record from form data.
        if ($instance !== null) {
            $this->communication->get_form_provider()->save_form_data($instance);
        }

        // Set the avatar.
        if (!empty($avatar)) {
            $this->set_avatar($avatar);
        }

        // Nothing else to do if the queue is false.
        if (!$queue) {
            return;
        }

        // Add ad-hoc task to create the provider room.
        create_and_configure_room_task::queue(
            $this->communication,
        );
    }

    /**
     * Create a communication ad-hoc task for update operation.
     * This method will add a task to the queue to update the room.
     *
     * @param null|int $active The selected active state of the provider
     * @param null|string $communicationroomname The communication room name
     * @param null|\stored_file $avatar The stored file for the avatar
     * @param \stdClass|null $instance The actual instance object
     * @param bool $queue Whether to queue the task or not
     */
    public function update_room(
        ?int $active = null,
        ?string $communicationroomname = null,
        ?\stored_file $avatar = null,
        ?\stdClass $instance = null,
        bool $queue = true,
    ): void {
        if (!$this->communication) {
            return;
        }

        // If the provider is none, we don't need to do anything from room point of view.
        if ($this->communication->get_provider() === processor::PROVIDER_NONE) {
            return;
        }

        $roomnamechange = null;
        $activestatuschange = null;

        // Check if the room name is being changed.
        if (
            $communicationroomname !== null &&
            $communicationroomname !== $this->communication->get_room_name()
        ) {
            $roomnamechange = $communicationroomname;
        }

        // Check if the active status of the provider is being changed.
        if (
            $active !== null &&
            $active !== $this->communication->is_instance_active()
        ) {
            $activestatuschange = $active;
        }

        if ($roomnamechange !== null || $activestatuschange !== null) {
            $this->communication->update_instance(
                active: $active,
                roomname: $communicationroomname,
            );
        }

        // Update provider record from form data.
        if ($instance !== null) {
            $this->communication->get_form_provider()->save_form_data($instance);
        }

        // Update the avatar.
        // If the value is `null`, then unset the avatar.
        $this->set_avatar($avatar);

        // Nothing else to do if the queue is false.
        if (!$queue) {
            return;
        }

        // Always queue a room update, even if none of the above standard fields have changed.
        // It is possible for providers to have custom fields that have been updated.
        update_room_task::queue(
            $this->communication,
        );
    }

    /**
     * Create a communication ad-hoc task for delete operation.
     * This method will add a task to the queue to delete the room.
     */
    public function delete_room(): void {
        if ($this->communication !== null) {
            // Add the ad-hoc task to remove the room data from the communication table and associated provider actions.
            delete_room_task::queue(
                $this->communication,
            );
        }
    }

    /**
     * Create a communication ad-hoc task for add members operation and add the user mapping.
     *
     * This method will add a task to the queue to add the room users.
     *
     * @param array $userids The user ids to add to the room
     * @param bool $queue Whether to queue the task or not
     */
    public function add_members_to_room(array $userids, bool $queue = true): void {
        // No communication object? something not done right.
        if (!$this->communication) {
            return;
        }

        // No user IDs or this provider does not manage users? No action required.
        if (empty($userids) || !$this->communication->supports_user_features()) {
            return;
        }

        $this->communication->create_instance_user_mapping($userids);

        if ($queue) {
            add_members_to_room_task::queue(
                $this->communication
            );
        }
    }

    /**
     * Create a communication ad-hoc task for updating members operation and update the user mapping.
     *
     * This method will add a task to the queue to update the room users.
     *
     * @param array $userids The user ids to add to the room
     * @param bool $queue Whether to queue the task or not
     */
    public function update_room_membership(array $userids, bool $queue = true): void {
        // No communication object? something not done right.
        if (!$this->communication) {
            return;
        }

        // No userids? don't bother doing anything.
        if (empty($userids)) {
            return;
        }

        $this->communication->reset_users_sync_flag($userids);

        if ($queue) {
            update_room_membership_task::queue(
                $this->communication
            );
        }
    }

    /**
     * Create a communication ad-hoc task for remove members operation or action immediately.
     *
     * This method will add a task to the queue to remove the room users.
     *
     * @param array $userids The user ids to remove from the room
     * @param bool $queue Whether to queue the task or not
     */
    public function remove_members_from_room(array $userids, bool $queue = true): void {
        // No communication object? something not done right.
        if (!$this->communication) {
            return;
        }

        $provider = $this->communication->get_provider();

        if ($provider === processor::PROVIDER_NONE) {
            return;
        }

        // No user IDs or this provider does not manage users? No action required.
        if (empty($userids) || !$this->communication->supports_user_features()) {
            return;
        }

        $this->communication->add_delete_user_flag($userids);

        if ($queue) {
            remove_members_from_room::queue(
                $this->communication
            );
        }
    }

    /**
     * Remove all users from the room.
     *
     * @param bool $queue Whether to queue the task or not
     */
    public function remove_all_members_from_room(bool $queue = true): void {
        // No communication object? something not done right.
        if (!$this->communication) {
            return;
        }

        if ($this->communication->get_provider() === processor::PROVIDER_NONE) {
            return;
        }

        // This provider does not manage users? No action required.
        if (!$this->communication->supports_user_features()) {
            return;
        }

        $this->communication->add_delete_user_flag($this->communication->get_all_userids_for_instance());

        if ($queue) {
            remove_members_from_room::queue(
                $this->communication
            );
        }
    }

    /**
     * Display the communication room status notification.
     */
    public function show_communication_room_status_notification(): void {
        // No communication, no room.
        if (!$this->communication) {
            return;
        }

        if ($this->communication->get_provider() === processor::PROVIDER_NONE) {
            return;
        }

        $roomstatus = $this->get_communication_room_url()
            ? constants::COMMUNICATION_STATUS_READY
            : constants::COMMUNICATION_STATUS_PENDING;
        $pluginname = get_string('pluginname', $this->get_provider());
        $message = get_string('communicationroom' . $roomstatus, 'communication', $pluginname);

        // We only show the ready notification once per user.
        // We check this with a custom user preference.
        $roomreadypreference = "{$this->component}_{$this->instancetype}_{$this->instanceid}_room_ready";

        switch ($roomstatus) {
            case constants::COMMUNICATION_STATUS_PENDING:
                \core\notification::add($message, \core\notification::INFO);
                unset_user_preference($roomreadypreference);
                break;

            case constants::COMMUNICATION_STATUS_READY:
                if (empty(get_user_preferences($roomreadypreference))) {
                    \core\notification::add($message, \core\notification::SUCCESS);
                    set_user_preference($roomreadypreference, true);
                }
                break;
        }
    }

    /**
     * Add the task to sync the provider data with local Moodle data.
     */
    public function sync_provider(): void {
        // No communication, return.
        if (!$this->communication) {
            return;
        }

        if ($this->communication->get_provider() === processor::PROVIDER_NONE) {
            return;
        }

        synchronise_provider_task::queue(
            $this->communication
        );
    }
}
