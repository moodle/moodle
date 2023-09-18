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

/**
 * Defines classes used for plugin info.
 *
 * @package    core
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\plugininfo;

use core_plugin_manager;
use moodle_url;

/**
 * Class for question behaviours.
 */
class qbehaviour extends base {

    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    /**
     * Finds all enabled plugins, the result may include missing plugins.
     * @return array|null of enabled plugins $pluginname=>$pluginname, null means unknown
     */
    public static function get_enabled_plugins() {
        $plugins = core_plugin_manager::instance()->get_installed_plugins('qbehaviour');
        if (!$plugins) {
            return array();
        }
        if ($disabled = get_config('question', 'disabledbehaviours')) {
            $disabled = explode(',', $disabled);
        } else {
            $disabled = array();
        }

        $enabled = array();
        foreach ($plugins as $plugin => $version) {
            if (in_array($plugin, $disabled)) {
                continue;
            }
            $enabled[$plugin] = $plugin;
        }

        return $enabled;
    }

    public static function enable_plugin(string $pluginname, int $enabled): bool {
        $haschanged = false;
        $plugins = [];
        $oldvalue = get_config('question', 'disabledbehaviours');
        if (!empty($oldvalue)) {
            $plugins = array_flip(explode(',', $oldvalue));
        }
        // Only set visibility if it's different from the current value.
        if ($enabled && array_key_exists($pluginname, $plugins)) {
            unset($plugins[$pluginname]);
            $haschanged = true;
        } else if (!$enabled && !array_key_exists($pluginname, $plugins)) {
            $plugins[$pluginname] = $pluginname;
            $haschanged = true;
        }

        if ($haschanged) {
            $new = implode(',', array_flip($plugins));
            add_to_config_log('disabledbehaviours', $oldvalue, $new, 'question');
            set_config('disabledbehaviours', $new, 'question');
            // Reset caches.
            \core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }

    public function is_uninstall_allowed() {
        global $DB;

        if ($this->name === 'missing') {
            // qbehaviour_missing is used by the system. It cannot be uninstalled.
            return false;
        }

        return !$DB->record_exists('question_attempts', array('behaviour' => $this->name));
    }

    /**
     * Pre-uninstall hook.
     *
     * This is intended for disabling of plugin, some DB table purging, etc.
     *
     * NOTE: to be called from uninstall_plugin() only.
     * @private
     */
    public function uninstall_cleanup() {
        if ($disabledbehaviours = get_config('question', 'disabledbehaviours')) {
            $disabledbehaviours = explode(',', $disabledbehaviours);
            $disabledbehaviours = array_unique($disabledbehaviours);
        } else {
            $disabledbehaviours = array();
        }
        if (($key = array_search($this->name, $disabledbehaviours)) !== false) {
            unset($disabledbehaviours[$key]);
            set_config('disabledbehaviours', implode(',', $disabledbehaviours), 'question');
        }

        if ($behaviourorder = get_config('question', 'behavioursortorder')) {
            $behaviourorder = explode(',', $behaviourorder);
            $behaviourorder = array_unique($behaviourorder);
        } else {
            $behaviourorder = array();
        }
        if (($key = array_search($this->name, $behaviourorder)) !== false) {
            unset($behaviourorder[$key]);
            set_config('behavioursortorder', implode(',', $behaviourorder), 'question');
        }

        parent::uninstall_cleanup();
    }

    /**
     * Return URL used for management of plugins of this type.
     * @return moodle_url
     */
    public static function get_manage_url() {
        return new moodle_url('/admin/qbehaviours.php');
    }
}
