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

defined('MOODLE_INTERNAL') || die();

use core_adminpresets\local\setting\adminpresets_setting;
use core_adminpresets\manager;
use core_adminpresets\helper;

global $CFG;
require_once($CFG->libdir . '/adminlib.php');

/**
 * Data generator for adminpresets component.
 *
 * @package    core_adminpresets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_adminpresets_generator extends \component_generator_base {

    /**
     * Create a preset. This preset will have only 3 settings and 3 plugins.
     * Settings:
     *  - none.enablebadges = 0
     *  - none.allowemojipicker = 1
     *  - mod_lesson.mediawidth = 900
     *  - mod_lesson.maxanswers = 2 with advanced disabled.
     * Plugins:
     * - enrol_guest = 0
     * - mod_glossary = 0
     * - qtype_truefalse = 1
     *
     * @param array $data Preset data. Supported values:
     *   - name. To define the preset name.
     *   - comments. To change the comments field.
     *   - author. To set the author.
     *   - applypreset. Whether the preset should be applied too or not.
     * @return int Identifier of the preset created.
     */
    public function create_preset(array $data = []): int {
        global $DB, $USER, $CFG;

        if (!isset($data['name'])) {
            $data['name'] = 'Preset default name';
        }
        if (!isset($data['comments'])) {
            $data['comments'] = 'Preset default comment';
        }
        if (!isset($data['author'])) {
            $data['author'] = 'Default author';
        }
        if (!isset($data['iscore'])) {
            $data['iscore'] = manager::NONCORE_PRESET;
        }
        // Validate iscore value.
        $allowed = [manager::NONCORE_PRESET, manager::STARTER_PRESET, manager::FULL_PRESET];
        if (!in_array($data['iscore'], $allowed)) {
            $data['iscore'] = manager::NONCORE_PRESET;
        }

        $preset = [
            'userid' => $USER->id,
            'name' => $data['name'],
            'comments' => $data['comments'],
            'site' => $CFG->wwwroot,
            'author' => $data['author'],
            'moodleversion' => $CFG->version,
            'moodlerelease' => $CFG->release,
            'timecreated' => time(),
            'timeimported' => 0,
            'iscore' => $data['iscore'],
        ];

        $presetid = $DB->insert_record('adminpresets', $preset);
        $preset['id'] = $presetid;

        // Setting: enablebadges = 0.
        helper::add_item($presetid, 'enablebadges', '0');
        // Setting: allowemojipicker = 1.
        helper::add_item($presetid, 'allowemojipicker', '1');
        // Setting: mediawidth = 900.
        helper::add_item($presetid, 'mediawidth', '900', 'mod_lesson');
        // Setting: maxanswers = 2 (with advanced disabled).
        helper::add_item($presetid, 'maxanswers', '2', 'mod_lesson', 'maxanswers_adv', 0);

        // Plugin: enrol_guest = 0.
        helper::add_plugin($presetid, 'enrol', 'guest', 0);
        // Plugin: mod_glossary = 0.
        helper::add_plugin($presetid, 'mod', 'glossary', 0);
        // Plugin: qtype_truefalse.
        helper::add_plugin($presetid, 'qtype', 'truefalse', 1);

        // Check if the preset should be created as applied preset too, to fill in the rest of the tables.
        $applypreset = isset($data['applypreset']) && $data['applypreset'];
        if ($applypreset) {
            $presetapp = [
                'adminpresetid' => $presetid,
                'userid' => $USER->id,
                'time' => time(),
            ];
            $appid = $DB->insert_record('adminpresets_app', $presetapp);

            $this->apply_setting($appid, 'enablebadges', '1', '0');
            // The allowemojipicker setting shouldn't be applied because the value matches the current one.
            $this->apply_setting($appid, 'mediawidth', '640', '900', 'mod_lesson');
            $this->apply_setting($appid, 'maxanswers', '5', '2', 'mod_lesson');
            $this->apply_setting($appid, 'maxanswers_adv', '1', '0', 'mod_lesson', 'maxanswers');

            $this->apply_plugin($appid, 'enrol', 'guest', 1, 0);
            $this->apply_plugin($appid, 'mod', 'glossary', 1, 0);
            // The qtype_truefalse plugin shouldn't be applied because the value matches the current one.
        }

        return $presetid;
    }

    /**
     * Helper method to create an applied setting item.
     *
     * @param int $appid The applied preset identifier.
     * @param string $name The setting name.
     * @param string $oldvalue The setting old value.
     * @param string $newvalue The setting new value.
     * @param string|null $plugin The setting plugin (or null if none).
     * @param string|null $itemname Whether it should be treated as advanced item or not.
     *
     * @return bool|int true or new id.
     */
    private function apply_setting(int $appid, string $name, string $oldvalue, string $newvalue, ?string $plugin = null,
            ?string $itemname = null) {
        global $DB;

        set_config($name, $newvalue, $plugin);
        $configlogid = $this->add_to_config_log($name, $oldvalue, $newvalue, $plugin);
        $presetappitem = [
            'adminpresetapplyid' => $appid,
            'configlogid' => $configlogid,
        ];
        $table = 'adminpresets_app_it';
        if (!is_null($itemname)) {
            $table = 'adminpresets_app_it_a';
            $presetappitem['itemname'] = $itemname;
        }
        $appitemid = $DB->insert_record($table, $presetappitem);

        return $appitemid;

    }

    /**
     * Helper method to create an applied plugin.
     *
     * @param int $appid The applied preset identifier.
     * @param string $plugin The plugin type.
     * @param string $name The plugin name.
     * @param int $oldvalue The setting old value.
     * @param int $newvalue The setting new value.
     *
     * @return bool|int true or new id.
     */
    private function apply_plugin(int $appid, string $plugin, string $name, int $oldvalue, int $newvalue) {
        global $DB;

        // Change plugin visibility.
        $pluginclass = \core_plugin_manager::resolve_plugininfo_class($plugin);
        $pluginclass::enable_plugin($name, $newvalue);

        // Create entry in applied plugins table.
        $presetappplug = [
            'adminpresetapplyid' => $appid,
            'plugin' => $plugin,
            'name' => $name,
            'value' => $newvalue,
            'oldvalue' => $oldvalue,
        ];
        $appplugid = $DB->insert_record('adminpresets_app_plug', $presetappplug);

        return $appplugid;
    }

    /**
     * Helper method to add entry in config_log.
     *
     * @param string $name The setting name.
     * @param string $oldvalue The setting old value.
     * @param string $value The setting new value.
     * @param string|null $plugin The setting plugin (or null if the setting doesn't belong to any plugin).
     * @return int The id of the config_log entry created.
     */
    private function add_to_config_log(string $name, string $oldvalue, string $value, ?string $plugin = null): int {
        global $DB, $USER;

        $log = new stdClass();
        $log->userid = $USER->id;
        $log->timemodified = time();
        $log->name = $name;
        $log->oldvalue = $oldvalue;
        $log->value = $value;
        $log->plugin = $plugin;
        $id = $DB->insert_record('config_log', $log);

        return $id;
    }

    /**
     * Helper method to access to a protected property.
     *
     * @param string|object $object The class.
     * @param string $property The private/protected property in $object to access.
     * @return mixed The current value of the property.
     */
    public function access_protected($object, string $property) {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        return $property->getValue($object);
    }


    /**
     * Given a tree category and setting name, it gets the adminpresets_setting class.
     *
     * @param string $category Tree category name where the setting is located.
     * @param string $settingname Setting name to get the class.
     * @return adminpresets_setting
     */
    public function get_admin_preset_setting(string $category, string $settingname): adminpresets_setting {
        $adminroot = admin_get_root();

        // Set method accessibility.
        $method = new ReflectionMethod(manager::class, 'get_setting');

        // Get the proper adminpresets_setting instance.
        $settingpage = $adminroot->locate($category);
        $settingdata = $settingpage->settings->$settingname;
        return $method->invokeArgs(new manager(), [$settingdata, '']);
    }
}
