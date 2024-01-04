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
use stdClass;
use stored_file;

/**
 * Class processor to manage the base operations of the providers.
 *
 * This class is responsible for creating, updating, deleting and loading the communication instance, associated actions.
 *
 * @package    core_communication
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class processor {
    /** @var string The magic 'none' provider */
    public const PROVIDER_NONE = 'none';

    /** @var int The provider active flag */
    public const PROVIDER_ACTIVE = 1;

    /** @var int The provider inactive flag */
    public const PROVIDER_INACTIVE = 0;

    /**
     * @var communication_provider|room_chat_provider|room_user_provider|synchronise_provider|user_provider|null The provider class
     */
    private communication_provider|user_provider|room_chat_provider|room_user_provider|synchronise_provider|null $provider = null;

    /**
     * Communication processor constructor.
     *
     * @param stdClass $instancedata The instance data object
     */
    protected function __construct(
        private stdClass $instancedata,
    ) {
        $providercomponent = $this->instancedata->provider;
        $providerclass = $this->get_classname_for_provider($providercomponent);
        if (!class_exists($providerclass)) {
            throw new \moodle_exception('communicationproviderclassnotfound', 'core_communication', '', $providerclass);
        }

        if (!is_a($providerclass, communication_provider::class, true)) {
            // At the moment we only have one communication provider interface.
            // In the future, we may have others, at which point we will support the newest first and
            // emit a debugging notice for older ones.
            throw new \moodle_exception('communicationproviderclassinvalid', 'core_communication', '', $providerclass);
        }

        $this->provider = $providerclass::load_for_instance($this);
    }

    /**
     * Create communication instance.
     *
     * @param context $context The context of the item for the instance
     * @param string $provider The communication provider
     * @param int $instanceid The instance id
     * @param string $component The component name
     * @param string $instancetype The instance type
     * @param string $roomname The room name
     * @return processor|null
     */
    public static function create_instance(
        context $context,
        string $provider,
        int $instanceid,
        string $component,
        string $instancetype,
        string $roomname,
    ): ?self {
        global $DB;

        if ($provider === self::PROVIDER_NONE) {
            return null;
        }
        $record = (object) [
            'contextid' => $context->id,
            'provider' => $provider,
            'instanceid' => $instanceid,
            'component' => $component,
            'instancetype' => $instancetype,
            'roomname' => $roomname,
            'avatarfilename' => null,
            'active' => self::PROVIDER_ACTIVE,
            'avatarsynced' => 0,
        ];
        $record->id = $DB->insert_record('communication', $record);

        return new self($record);
    }

    /**
     * Update the communication instance with any changes.
     *
     * @param null|string $active Active state of the instance (processor::PROVIDER_ACTIVE or processor::PROVIDER_INACTIVE)
     * @param null|string $roomname The room name
     */
    public function update_instance(
        ?string $active = null,
        ?string $roomname = null,
    ): void {
        global $DB;

        if ($active !== null && in_array($active, [self::PROVIDER_ACTIVE, self::PROVIDER_INACTIVE])) {
            $this->instancedata->active = $active;
        }

        if ($roomname !== null) {
            $this->instancedata->roomname = $roomname;
        }

        $DB->update_record('communication', $this->instancedata);
    }

    /**
     * Delete communication data.
     */
    public function delete_instance(): void {
        global $DB;
        $DB->delete_records('communication', ['id' => $this->instancedata->id]);
    }

    /**
     * Get non synced instance user ids for the instance.
     *
     * @param bool $synced The synced status
     * @param bool $deleted The deleted status
     * @return array
     */
    public function get_instance_userids(bool $synced = false, bool $deleted = false): array {
        global $DB;
        return $DB->get_fieldset_select(
            'communication_user',
            'userid',
            'commid = ? AND synced = ? AND deleted = ?',
            [$this->instancedata->id, (int) $synced, (int) $deleted]
        );
    }

    /**
     * Get existing instance user ids.
     *
     * @return array
     */
    public function get_all_userids_for_instance(): array {
        global $DB;
        return $DB->get_fieldset_select(
            'communication_user',
            'userid',
            'commid = ?',
            [$this->instancedata->id]
        );
    }

    /**
     * Get all the user ids flagged as deleted.
     *
     * @return array
     */
    public function get_all_delete_flagged_userids(): array {
        global $DB;
        return $DB->get_fieldset_select(
            'communication_user',
            'userid',
            'commid = ? AND deleted = ?',
            [$this->instancedata->id, 1]
        );
    }

    /**
     * Create communication user record for mapping and sync.
     *
     * @param array $userids The user ids
     */
    public function create_instance_user_mapping(array $userids): void {
        global $DB;

        // Check if user ids exits in existing user ids.
        $useridstoadd = array_diff($userids, $this->get_all_userids_for_instance());

        foreach ($useridstoadd as $userid) {
            $record = (object) [
                'commid' => $this->instancedata->id,
                'userid' => $userid,
            ];
            $DB->insert_record('communication_user', $record);
        }
        $this->mark_users_as_not_deleted($userids);
    }

    /**
     * Mark users as not deleted for the instance.
     *
     * @param array $userids The user ids
     */
    public function mark_users_as_not_deleted(array $userids): void {
        global $DB;

        if (empty($userids)) {
            return;
        }

        $DB->set_field_select(
            'communication_user',
            'deleted',
            0,
            'commid = ? AND userid IN (' . implode(',', $userids) . ')',
            [$this->instancedata->id]
        );
    }

    /**
     * Mark users as synced for the instance.
     *
     * @param array $userids The user ids
     */
    public function mark_users_as_synced(array $userids): void {
        global $DB;

        if (empty($userids)) {
            return;
        }

        $DB->set_field_select(
            'communication_user',
            'synced',
            1,
            'commid = ? AND userid IN (' . implode(',', $userids) . ')',
            [$this->instancedata->id]
        );
    }

    /**
     * Reset users sync flag for the instance.
     *
     * @param array $userids The user ids
     */
    public function reset_users_sync_flag(array $userids): void {
        global $DB;

        if (empty($userids)) {
            return;
        }

        $DB->set_field_select(
            'communication_user',
            'synced',
            0,
            'commid = ? AND userid IN (' . implode(',', $userids) . ')',
            [$this->instancedata->id]
        );
    }

    /**
     * Delete users flag for the instance users.
     *
     * @param array $userids The user ids
     */
    public function add_delete_user_flag(array $userids): void {
        global $DB;

        if (empty($userids)) {
            return;
        }

        $DB->set_field_select(
            'communication_user',
            'deleted',
            1,
            'commid = ? AND userid IN (' . implode(',', $userids) . ')',
            [$this->instancedata->id]
        );
    }

    /**
     * Delete communication user record for userid.
     *
     * @param array $userids The user ids
     */
    public function delete_instance_user_mapping(array $userids): void {
        global $DB;

        if (empty($userids)) {
            return;
        }

        $DB->delete_records_select(
            'communication_user',
            'commid = ? AND userid IN (' . implode(',', $userids) . ')',
            [$this->instancedata->id]
        );
    }

    /**
     * Delete communication user record for userid who are not synced.
     *
     * @param array $userids The user ids
     */
    public function delete_instance_non_synced_user_mapping(array $userids): void {
        global $DB;

        if (empty($userids)) {
            return;
        }

        $DB->delete_records_select(
            'communication_user',
            'commid = ? AND userid IN (' . implode(',', $userids) . ') AND synced = ?',
            [$this->instancedata->id, 0]
        );
    }

    /**
     * Delete communication user record for instance.
     */
    public function delete_user_mappings_for_instance(): void {
        global $DB;
        $DB->delete_records('communication_user', [
            'commid' => $this->instancedata->id,
        ]);
    }

    /**
     * Load communication instance by id.
     *
     * @param int $id The communication instance id
     * @return processor|null
     */
    public static function load_by_id(int $id): ?self {
        global $DB;
        $record = $DB->get_record('communication', ['id' => $id]);
        if ($record && self::is_provider_available($record->provider)) {
            return new self($record);
        }

        return null;
    }

    /**
     * Load communication instance by instance id.
     *
     * @param context $context The context of the item for the instance
     * @param string $component The component name
     * @param string $instancetype The instance type
     * @param int $instanceid The instance id
     * @param string|null $provider The provider type - if null will load for this context's active provider.
     * @return processor|null
     */
    public static function load_by_instance(
        context $context,
        string $component,
        string $instancetype,
        int $instanceid,
        ?string $provider = null,
    ): ?self {

        global $DB;

        $params = [
            'contextid' => $context->id,
            'instanceid' => $instanceid,
            'component' => $component,
            'instancetype' => $instancetype,
        ];

        if ($provider === null) {
            // Fetch the active provider in this context.
            $params['active'] = 1;
        } else {
            // Fetch a specific provider in this context (which may be inactive).
            $params['provider'] = $provider;
        }

        $record = $DB->get_record('communication', $params);
        if ($record && self::is_provider_available($record->provider)) {
            return new self($record);
        }

        return null;
    }

    /**
     * Check if communication instance is active.
     *
     * @return bool
     */
    public function is_instance_active(): bool {
        return $this->instancedata->active;
    }

    /**
     * Get communication provider class name.
     *
     * @param string $component The component name.
     * @return string
     */
    private function get_classname_for_provider(string $component): string {
        return "{$component}\\communication_feature";
    }

    /**
     * Get communication instance id after creating the instance in communication table.
     *
     * @return int
     */
    public function get_id(): int {
        return $this->instancedata->id;
    }

    /**
     * Get the context of the communication instance.
     *
     * @return context
     */
    public function get_context(): context {
        return context::instance_by_id($this->get_context_id());
    }

    /**
     * Get the context id of the communication instance.
     *
     * @return int
     */
    public function get_context_id(): int {
        return $this->instancedata->contextid;
    }

    /**
     * Get communication instance type.
     *
     * @return string
     */
    public function get_instance_type(): string {
        return $this->instancedata->instancetype;
    }

    /**
     * Get communication instance id.
     *
     * @return int
     */
    public function get_instance_id(): int {
        return $this->instancedata->instanceid;
    }

    /**
     * Get communication instance component.
     *
     * @return string
     */
    public function get_component(): string {
        return $this->instancedata->component;
    }

    /**
     * Get communication provider type.
     *
     * @return string|null
     */
    public function get_provider(): ?string {
        return $this->instancedata->provider;
    }

    /**
     * Get room name.
     *
     * @return string|null
     */
    public function get_room_name(): ?string {
        return $this->instancedata->roomname;
    }

    /**
     * Get provider active status.
     *
     * @return int
     */
    public function get_provider_status(): int {
        return $this->instancedata->active;
    }

    /**
     * Get communication instance id.
     *
     * @return room_chat_provider
     */
    public function get_room_provider(): room_chat_provider {
        $this->require_api_enabled();
        $this->require_room_features();
        return $this->provider;
    }

    /**
     * Get communication instance id.
     *
     * @return user_provider
     */
    public function get_user_provider(): user_provider {
        $this->require_api_enabled();
        $this->require_user_features();
        return $this->provider;
    }

    /**
     * Get communication instance id.
     *
     * @return room_user_provider
     */
    public function get_room_user_provider(): room_user_provider {
        $this->require_api_enabled();
        $this->require_room_features();
        $this->require_room_user_features();
        return $this->provider;
    }

    /**
     * Get the provider after checking if it supports sync features.
     *
     * @return synchronise_provider
     */
    public function get_sync_provider(): synchronise_provider {
        $this->require_api_enabled();
        $this->require_sync_provider_features();
        return $this->provider;
    }

    /**
     * Set provider specific form definition.
     *
     * @param string $provider The provider name
     * @param \MoodleQuickForm $mform The moodle form
     */
    public static function set_provider_specific_form_definition(string $provider, \MoodleQuickForm $mform): void {
        $providerclass = "{$provider}\\communication_feature";
        $providerclass::set_form_definition($mform);
    }

    /**
     * Get communication instance for form feature.
     *
     * @return form_provider
     */
    public function get_form_provider(): form_provider {
        $this->requires_form_features();
        return $this->provider;
    }

    /**
     * Get communication instance id.
     *
     * @return bool
     */
    public function supports_user_features(): bool {
        return ($this->provider instanceof user_provider);
    }

    /**
     * Get communication instance id.
     *
     * @return bool
     */
    public function supports_room_user_features(): bool {
        if (!$this->supports_user_features()) {
            return false;
        }

        if (!$this->supports_room_features()) {
            return false;
        }

        return ($this->provider instanceof room_user_provider);
    }

    /**
     * Check form feature available.
     *
     * @return bool
     */
    public function requires_form_features(): void {
        if (!$this->supports_form_features()) {
            throw new \coding_exception('Form features are not supported by the provider');
        }
    }

    /**
     * Check support for form feature.
     *
     * @return bool
     */
    public function supports_form_features(): bool {
        return ($this->provider instanceof form_provider);
    }

    /**
     * Get communication instance id.
     */
    public function require_user_features(): void {
        if (!$this->supports_user_features()) {
            throw new \coding_exception('User features are not supported by the provider');
        }
    }

    /**
     * Get communication instance id.
     *
     * @return bool
     */
    public function supports_room_features(): bool {
        return ($this->provider instanceof room_chat_provider);
    }

    /**
     * Check if communication api is enabled.
     */
    public function require_api_enabled(): void {
        if (!api::is_available()) {
            throw new \coding_exception('Communication API is not enabled, please enable it from experimental features');
        }
    }

    /**
     * Get communication instance id.
     */
    public function require_room_features(): void {
        if (!$this->supports_room_features()) {
            throw new \coding_exception('room features are not supported by the provider');
        }
    }

    /**
     * Get communication instance id.
     */
    public function require_room_user_features(): void {
        if (!$this->supports_room_user_features()) {
            throw new \coding_exception('room features are not supported by the provider');
        }
    }

    /**
     * Check if the provider supports sync features.
     *
     * @return bool whether the provider supports sync features or not
     */
    public function supports_sync_provider_features(): bool {
        return ($this->provider instanceof synchronise_provider);
    }

    /**
     * Check if the provider supports sync features when required.
     */
    public function require_sync_provider_features(): void {
        if (!$this->supports_sync_provider_features()) {
            throw new \coding_exception('sync features are not supported by the provider');
        }
    }

    /**
     * Get communication instance id.
     *
     * @return bool|\stored_file
     */
    public function get_avatar(): ?stored_file {
        $fs = get_file_storage();
        $file = $fs->get_file(
            (\context_system::instance())->id,
            'core_communication',
            'avatar',
            $this->instancedata->id,
            '/',
            $this->instancedata->avatarfilename,
        );

        return $file ?: null;
    }


    /**
     * Set the avatar file name.
     *
     * @param string|null $filename
     */
    public function set_avatar_filename(?string $filename): void {
        global $DB;

        $this->instancedata->avatarfilename = $filename;
        $DB->set_field('communication', 'avatarfilename', $filename, ['id' => $this->instancedata->id]);
    }

    /**
     * Get the avatar file name.
     *
     * @return string|null
     */
    public function get_avatar_filename(): ?string {
        return $this->instancedata->avatarfilename;
    }

    /**
     * Check if the avatar has been synced with the provider.
     *
     * @return bool
     */
    public function is_avatar_synced(): bool {
        return (bool) $this->instancedata->avatarsynced;
    }

    /**
     * Indicate if the avatar has been synced with the provider.
     *
     * @param boolean $synced True if avatar has been synced.
     */
    public function set_avatar_synced_flag(bool $synced): void {
        global $DB;

        $this->instancedata->avatarsynced = (int) $synced;
        $DB->set_field('communication', 'avatarsynced', (int) $synced, ['id' => $this->instancedata->id]);
    }

    /**
     * Get a room url.
     *
     * @return string|null
     */
    public function get_room_url(): ?string {
        if ($this->provider && $this->is_instance_active()) {
            return $this->get_room_provider()->get_chat_room_url();
        }
        return null;
    }

    /**
     * Is the communication provider enabled and configured, or disabled.
     *
     * @param string $provider provider component name
     * @return bool
     */
    public static function is_provider_available(string $provider): bool {
        if (\core\plugininfo\communication::is_plugin_enabled($provider)) {
            $providerclass = "{$provider}\\communication_feature";
            return $providerclass::is_configured();
        }
        return false;
    }
}
