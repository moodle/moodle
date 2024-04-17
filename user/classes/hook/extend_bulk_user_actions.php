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

namespace core_user\hook;

use action_link;
use core\hook\described_hook;
use core\hook\deprecated_callback_replacement;

/**
 * Class extend_bulk_user_actions
 *
 * @package    core_user
 * @copyright  2024 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class extend_bulk_user_actions implements deprecated_callback_replacement, described_hook {
    /**
     * Describes the hook purpose.
     *
     * @return string
     */
    public static function get_hook_description(): string {
        return 'Extend bulk user actions menu';
    }

    /**
     * List of tags that describe this hook.
     *
     * @return string[]
     */
    public static function get_hook_tags(): array {
        return ['user'];
    }

    /**
     * Returns list of lib.php plugin callbacks that were deprecated by the hook.
     *
     * @return array
     */
    public static function get_deprecated_plugin_callbacks(): array {
        return ['bulk_user_actions'];
    }

    /** @var array Stores all added user actions */
    private $actions = [];

    /**
     * To be called by callback to add an action to the bulk user actions menu
     *
     * Callbacks with higher priority will be called first and actions they added will be displayed first.
     * Callbacks with lower priority can override actions added by callbacks with higher priority.
     *
     * To prevent accidental overrides plugins should prefix the action identifier with the plugin name.
     *
     * @param string $identifier Unique key for the action, recommended to prefix with plugin name
     * @param action_link|null $action an object containing the action URL and text,
     *        other properties are ignored. Can be set to null to remove an action added by somebody else.
     * @param ?string $category Label for the option group in the action select dropdown
     */
    public function add_action(string $identifier, ?action_link $action, ?string $category = null): void {
        $category = $category ?? get_string('actions', 'moodle');

        // If an action with the same identifier already exists in another option group, remove it.
        $oldcategory = $this->find_action_category($identifier);
        if ($oldcategory !== null && ($oldcategory !== $category || $action === null)) {
            unset($this->actions[$oldcategory][$identifier]);
            if (empty($this->actions[$oldcategory])) {
                unset($this->actions[$oldcategory]);
            }
        }

        // Add the new action.
        if ($action !== null) {
            $this->actions += [$category => []];
            $this->actions[$category][$identifier] = $action;
        }
    }

    /**
     * Returns all actions groupped by category
     *
     * @return action_link[][]
     */
    public function get_actions(): array {
        return $this->actions;
    }

    /**
     * Allows to locate an action by the identifier
     *
     * This method returns the option group label. The action itself can be found as:
     *    $category = $this->find_action_category($identifier);
     *    $action = $this->get_actions()[$category][$identifier];
     *
     * @param string $identifier
     * @return string|null
     */
    public function find_action_category(string $identifier): ?string {
        foreach ($this->actions as $category => $actions) {
            if (array_key_exists($identifier, $actions)) {
                return $category;
            }
        }
        return null;
    }
}
