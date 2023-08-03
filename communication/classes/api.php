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

use core_communication\task\add_members_to_room_task;
use core_communication\task\create_and_configure_room_task;
use core_communication\task\delete_room_task;
use core_communication\task\remove_members_from_room;
use core_communication\task\update_room_task;
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
     * @param string $component The component of the item for the instance
     * @param string $instancetype The type of the item for the instance
     * @param int $instanceid The id of the instance
     *
     */
    private function __construct(
        private string $component,
        private string $instancetype,
        private int $instanceid
    ) {
        $this->communication = processor::load_by_instance(
            $this->component,
            $this->instancetype,
            $this->instanceid,
        );
    }

    /**
     * Get the communication processor object.
     *
     * @param string $component The component of the item for the instance
     * @param string $instancetype The type of the item for the instance
     * @param int $instanceid The id of the instance
     * @return api
     */
    public static function load_by_instance(
        string $component,
        string $instancetype,
        int $instanceid
    ): self {
        return new self($component, $instancetype, $instanceid);
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
            $selection['communication_' . $pluginname] = get_string('pluginname', 'communication_'. $pluginname);
        }
        return $selection;
    }

    /**
     * Get the enabled communication providers and default provider according to the selected provider.
     *
     * @param string|null $selecteddefaulprovider
     * @return array
     */
    public static function get_enabled_providers_and_default(string $selecteddefaulprovider = null): array {
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

        list($communicationproviders, $defaulprovider) = self::
            get_enabled_providers_and_default($selectdefaultcommunication);

        $PAGE->requires->js_call_amd('core_communication/providerchooser', 'init');

        // List the communication providers.
        $mform->addElement(
            'select',
            'selectedcommunication',
            get_string('seleccommunicationprovider', 'communication'),
            $communicationproviders,
            ['data-communicationchooser-field' => 'selector'],
        );
        $mform->addHelpButton('selectedcommunication', 'seleccommunicationprovider', 'communication');
        $mform->setDefault('selectedcommunication', $defaulprovider);

        $mform->registerNoSubmitButton('updatecommunicationprovider');
        $mform->addElement('submit',
            'updatecommunicationprovider',
            'update communication',
            ['data-communicationchooser-field' => 'updateButton', 'class' => 'd-none',]);

        // Just a placeholder for the communication options.
        $mform->addElement('hidden', 'addcommunicationoptionshere');
        $mform->setType('addcommunicationoptionshere', PARAM_BOOL);
    }

    /**
     * Set the form definitions for the plugins.
     *
     * @param \MoodleQuickForm $mform
     * @return void
     */
    public function form_definition_for_provider(\MoodleQuickForm $mform): void {
        $provider = $mform->getElementValue('selectedcommunication');

        if ($provider[0] !== processor::PROVIDER_NONE) {
            // Room name for the communication provider.
            $mform->insertElementBefore(
                $mform->createElement(
                    'text',
                    'communicationroomname',
                    get_string('communicationroomname', 'communication'), 'maxlength="100" size="20"'),
                'addcommunicationoptionshere'
            );
            $mform->addHelpButton('communicationroomname', 'communicationroomname', 'communication');
            $mform->setType('communicationroomname', PARAM_TEXT);

            processor::set_proider_form_definition($provider[0], $mform);
        }

    }

    /**
     * Get the avatar file record for the avatar for filesystem.
     *
     * @param string $filename The filename of the avatar
     * @return stdClass
     */
    public function get_avatar_filerecord(string $filename): stdClass {
        return (object) [
            'contextid' => \context_system::instance()->id,
            'component' => 'core_communication',
            'filearea' => 'avatar',
            'filename' => $filename,
            'filepath' => '/',
            'itemid' => $this->communication->get_id(),
        ];
    }

    /**
     *
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

        $currentfilerecord = $this->communication->get_avatar();
        if ($avatar && !empty($currentfilerecord)) {
            $currentfilehash = $currentfilerecord->get_contenthash();
            $updatedfilehash = $avatar->get_contenthash();

            // No update required.
            if ($currentfilehash === $updatedfilehash) {
                return false;
            }
        }

        $context = \context_system::instance();

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
     * Set the form data if the data is already available.
     *
     * @param \stdClass $instance The instance object
     */
    public function set_data(\stdClass $instance): void {
        if (!empty($instance->id) && $this->communication) {
            $instance->selectedcommunication = $this->communication->get_provider();
            $instance->communicationroomname = $this->communication->get_room_name();

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
     * Create a communication ad-hoc task for create operation.
     * This method will add a task to the queue to create the room.
     *
     * @param string $selectedcommunication The selected communication provider
     * @param string $communicationroomname The communication room name
     * @param null|\stored_file $avatar The stored file for the avatar
     * @param \stdClass|null $instance The actual instance object
     */
    public function create_and_configure_room(
        string $selectedcommunication,
        string $communicationroomname,
        ?\stored_file $avatar = null,
        ?\stdClass $instance = null,
    ): void {
        if ($selectedcommunication !== processor::PROVIDER_NONE && $selectedcommunication !== '') {
            // Create communication record.
            $this->communication = processor::create_instance(
                $selectedcommunication,
                $this->instanceid,
                $this->component,
                $this->instancetype,
                $communicationroomname,
            );

            // Update provider record from form data.
            if ($instance !== null) {
                $this->communication->get_form_provider()->save_form_data($instance);
            }

            // Set the avatar.
            if (!empty($avatar)) {
                $this->set_avatar($avatar);
            }

            // Add ad-hoc task to create the provider room.
            create_and_configure_room_task::queue(
                $this->communication,
            );
        }
    }

    /**
     * Create a communication ad-hoc task for update operation.
     * This method will add a task to the queue to update the room.
     *
     * @param string $selectedprovider The selected communication provider
     * @param string $communicationroomname The communication room name
     * @param null|\stored_file $avatar The stored file for the avatar
     * @param \stdClass|null $instance The actual instance object
     */
    public function update_room(
        string $selectedprovider,
        string $communicationroomname,
        ?\stored_file $avatar = null,
        ?\stdClass $instance = null,
    ): void {

        // Existing object found, let's update the communication record and associated actions.
        if ($this->communication !== null) {
            // Get the previous data to compare for update.
            $previousroomname = $this->communication->get_room_name();
            $previousprovider = $this->communication->get_provider();

            // Update communication record.
            $this->communication->update_instance($selectedprovider, $communicationroomname);

            // Update provider record from form data.
            if ($instance !== null) {
                $this->communication->get_form_provider()->save_form_data($instance);
            }

            // Update the avatar.
            $imageupdaterequired = $this->set_avatar($avatar);

            // If the provider is none, we don't need to do anything from room point of view.
            if ($this->communication->get_provider() === processor::PROVIDER_NONE) {
                return;
            }

            // Add ad-hoc task to update the provider room if the room name changed.
            // TODO add efficiency considering dynamic fields.
            if (
                $previousprovider === $selectedprovider
            ) {
                update_room_task::queue(
                    $this->communication,
                );
            } else if (
                $previousprovider !== $selectedprovider
            ) {
                // Add ad-hoc task to create the provider room.
                create_and_configure_room_task::queue(
                    $this->communication,
                );
            }
        } else {
            // The instance didn't have any communication record, so create one.
            $this->create_and_configure_room($selectedprovider, $communicationroomname, $avatar, $instance);
        }
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

        // No userids? don't bother doing anything.
        if (empty($userids)) {
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

        if ($this->communication->get_provider() === processor::PROVIDER_NONE) {
            return;
        }

        // No user ids? don't bother doing anything.
        if (empty($userids)) {
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

        $roomstatus = $this->get_communication_room_url() ? 'ready' : 'pending';
        $pluginname = get_string('pluginname', $this->get_provider());
        $message = get_string('communicationroom' . $roomstatus, 'communication', $pluginname);

        switch ($roomstatus) {
            case 'pending':

                \core\notification::add($message, \core\notification::INFO);
                break;

            case 'ready':
                // We only show the ready notification once per user.
                // We check this with a custom user preference.
                $roomreadypreference = "{$this->component}_{$this->instancetype}_{$this->instanceid}_room_ready";

                if (empty(get_user_preferences($roomreadypreference))) {
                    \core\notification::add($message, \core\notification::SUCCESS);
                    set_user_preference($roomreadypreference, true);
                }
                break;
        }
    }
}
