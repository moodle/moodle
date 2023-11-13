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
namespace mod_bigbluebuttonbn;

use cache;
use cm_info;
use mod_bigbluebuttonbn\local\extension\action_url_addons;
use mod_bigbluebuttonbn\local\extension\custom_completion_addons;
use mod_bigbluebuttonbn\local\extension\mod_form_addons;
use mod_bigbluebuttonbn\local\extension\mod_instance_helper;
use stdClass;
use core_plugin_manager;

/**
 * Generic subplugin management helper
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class extension {
    /**
     * Plugin name for extension
     */
    const BBB_EXTENSION_PLUGIN_NAME = 'bbbext';

    /**
     * Invoke a subplugin hook that will return additional parameters
     *
     * @param string $action
     * @param array $data
     * @param array $metadata
     * @param int|null $instanceid
     * @return array associative array with the additional data and metadata (indexed by 'data' and
     * 'metadata' keys).
     */
    public static function action_url_addons(
        string $action = '',
        array $data = [],
        array $metadata = [],
        ?int $instanceid = null
    ): array {
        $allmutationclass = self::get_instances_implementing(action_url_addons::class);
        $additionaldata = [];
        $additionalmetadata = [];
        foreach ($allmutationclass as $mutationclass) {
            // Here we intentionally just pass data and metadata and not the result as we
            // do not want subplugin to assume that another subplugin is doing a modification.
            ['data' => $newdata, 'metadata' => $newmetadata] = $mutationclass->execute($action, $data, $metadata, $instanceid);
            $additionaldata = array_merge($additionaldata, $newdata ?? []);
            $additionalmetadata = array_merge($additionalmetadata, $newmetadata ?? []);
        }
        return [
            'data' => $additionaldata,
            'metadata' => $additionalmetadata
        ];
    }

    /**
     * Get new instance of classes that are named on the base of this classname and implementing this class
     *
     * @param string $classname
     * @param array|null $newparameters additional parameters for the constructor.
     * @return array
     */
    protected static function get_instances_implementing(string $classname, ?array $newparameters = []): array {
        $classes = self::get_classes_implementing($classname);
        sort($classes); // Make sure all extension classes are returned in the same order. This is arbitrarily in
        // alphabetical order and depends on the classname but this one way to ensure consistency across calls.
        return array_map(function($targetclassname) use ($newparameters) {
            // If $newparameters is null, the constructor will be called without parameters.
            return new $targetclassname(...$newparameters);
        }, $classes);
    }

    /**
     * Get classes are named on the base of this classname and implementing this class
     *
     * @param string $classname
     * @return array
     */
    protected static function get_classes_implementing(string $classname): array {
        // Get the class basename without Reflection API.
        $classnamecomponents = explode("\\", $classname);
        $classbasename = end($classnamecomponents);
        $allsubs = core_plugin_manager::instance()->get_plugins_of_type(self::BBB_EXTENSION_PLUGIN_NAME);
        $extensionclasses = [];
        foreach ($allsubs as $sub) {
            if (!$sub->is_enabled()) {
                continue;
            }
            $targetclassname = "\\bbbext_{$sub->name}\\bigbluebuttonbn\\$classbasename";
            if (!class_exists($targetclassname)) {
                continue;
            }
            if (!is_subclass_of($targetclassname, $classname)) {
                debugging("The class $targetclassname should extend $classname in the subplugin {$sub->name}. Ignoring.");
                continue;
            }
            $extensionclasses[] = $targetclassname;
        }
        return $extensionclasses;
    }

    /**
     * Get all custom_completion addons classes.
     *
     * @return array of custom completion addon classes.
     */
    public static function custom_completion_addons_classes(): array {
        return self::get_classes_implementing(custom_completion_addons::class);
    }

    /**
     * Get all custom_completion addons classes instances.
     *
     * @param cm_info $cm
     * @param int $userid
     * @param array|null $completionstate
     * @return array of custom completion addon instances.
     */
    public static function custom_completion_addons_instances(cm_info $cm, int $userid, ?array $completionstate = null): array {
        return self::get_instances_implementing(custom_completion_addons::class, [$cm, $userid, $completionstate]);
    }

    /**
     * Get all mod_form addons classes instances
     *
     * @param \MoodleQuickForm $mform
     * @param stdClass|null $bigbluebuttondata
     * @param string|null $suffix
     * @return array of custom completion addon classes instances
     */
    public static function mod_form_addons_instances(\MoodleQuickForm $mform, ?stdClass $bigbluebuttondata = null,
        string $suffix = null): array {
        return self::get_instances_implementing(mod_form_addons::class, [$mform, $bigbluebuttondata, $suffix]);
    }

    /**
     * Get additional join tables for instance when extension activated
     *
     * @return array of additional tables names. They all have a field called bigbluebuttonbnid that identifies the bbb instance.
     */
    public static function get_join_tables(): array {
        global $DB;
        // We use cache here as it will be called very often.
        $cache = cache::make('mod_bigbluebuttonbn', 'subplugins');
        if ($cache->get('additionaltables')) {
            return $cache->get('additionaltables');
        }
        $additionaltablesclasses = self::get_instances_implementing(mod_instance_helper::class);
        $tables = [];
        foreach ($additionaltablesclasses as $tableclass) {
            $tables = array_merge($tables, $tableclass->get_join_tables() ?? []);
        }
        // Warning and removal for tables that do not have the bigbluebuttonid field.
        foreach ($tables as $index => $table) {
            $columns = $DB->get_columns($table);
            if (empty($columns['bigbluebuttonbnid'])) {
                debugging("get_instance_additional_tables: $table should have a column named bigbluebuttonid");
                unset($tables[$index]);
            }
        }
        $cache->set('additionaltables', $tables);
        return $tables;
    }

    /**
     * Add instance processing
     *
     * @param stdClass $data data to persist
     * @return void
     */
    public static function add_instance(stdClass $data): void {
        $formmanagersclasses = self::get_instances_implementing(mod_instance_helper::class);
        foreach ($formmanagersclasses as $fmclass) {
            $fmclass->add_instance($data);
        }
    }

    /**
     * Update instance processing
     *
     * @param stdClass $data data to persist
     * @return void
     */
    public static function update_instance(stdClass $data): void {
        $formmanagersclasses = self::get_instances_implementing(mod_instance_helper::class);
        foreach ($formmanagersclasses as $fmclass) {
            $fmclass->update_instance($data);
        }
    }

    /**
     * Delete instance processing
     *
     * @param int $id instance id
     * @return void
     */
    public static function delete_instance(int $id): void {
        $formmanagersclasses = self::get_instances_implementing(mod_instance_helper::class);
        foreach ($formmanagersclasses as $fmclass) {
            $fmclass->delete_instance($id);
        }
    }
}
