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

namespace tool_admin_presets;

/**
 * Admin tool presets helper class.
 *
 * @package    tool_admin_presets
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Create an empty preset.
     *
     * @param array $data Preset data. Supported values:
     *   - name. To define the preset name.
     *   - comments. To change the comments field.
     *   - author. To update the author field.
     *   - iscore. Whether the preset is a core preset or not.
     * @return int The identifier of the preset created.
     */
    public static function create_preset(array $data): int {
        global $CFG, $USER, $DB;

        $name = array_key_exists('name', $data) ? $data['name'] : '';
        $comments = array_key_exists('comments', $data) ? $data['comments'] : '';
        $author = array_key_exists('author', $data) ? $data['author'] : fullname($USER);
        $iscore = array_key_exists('iscore', $data) ? $data['iscore'] : 0;

        $preset = [
            'userid' => $USER->id,
            'name' => $name,
            'comments' => $comments,
            'site' => $CFG->wwwroot,
            'author' => $author,
            'moodleversion' => $CFG->version,
            'moodlerelease' => $CFG->release,
            'iscore' => $iscore,
            'timecreated' => time(),
            'timeimported' => 0,
        ];

        $presetid = $DB->insert_record('tool_admin_presets', $preset);
        return $presetid;
    }

    /**
     * Helper method to add a setting item to a preset.
     *
     * @param int $presetid Preset identifier where the item will belong.
     * @param string $name Item name.
     * @param string $value Item value.
     * @param string|null $plugin Item plugin.
     * @param string|null $advname If the item is an advanced setting, the name of the advanced setting should be specified here.
     * @param string|null $advvalue If the item is an advanced setting, the value of the advanced setting should be specified here.
     * @return int The item identificator.
     */
    public static function add_item(int $presetid, string $name, string $value, ?string $plugin = 'none',
            ?string $advname = null, ?string $advvalue = null): int {
        global $DB;

        $presetitem = [
            'adminpresetid' => $presetid,
            'plugin' => $plugin,
            'name' => $name,
            'value' => $value,
        ];
        $itemid = $DB->insert_record('tool_admin_presets_it', $presetitem);

        if (!empty($advname)) {
            $presetadv = [
                'itemid' => $itemid,
                'name' => $advname,
                'value' => $advvalue,
            ];
            $DB->insert_record('tool_admin_presets_it_a', $presetadv);
        }

        return $itemid;
    }

    /**
     * Helper method to add a plugin to a preset.
     *
     * @param int $presetid Preset identifier where the item will belong.
     * @param string $plugin Plugin type.
     * @param string $name Plugin name.
     * @param int $enabled Whether the plugin will be enabled or not.
     * @return int The plugin identificator.
     */
    public static function add_plugin(int $presetid, string $plugin, string $name, int $enabled): int {
        global $DB;

        $pluginentry = [
            'adminpresetid' => $presetid,
            'plugin' => $plugin,
            'name' => $name,
            'enabled' => $enabled,
        ];
        $pluginid = $DB->insert_record('tool_admin_presets_plug', $pluginentry);

        return $pluginid;
    }

    /**
     * Apply the given preset. If it's a filename, the preset will be imported and then applied.
     *
     * @param string $presetnameorfile The preset name to be applied or a valid preset file to be imported and applied.
     * @return int|null The preset identifier that has been applied or null if the given value was not valid.
     */
    public static function change_default_preset(string $presetnameorfile): ?int {
        global $DB;

        $presetid = null;

        // Check if the given variable points to a valid preset file to be imported and applied.
        if (is_readable($presetnameorfile)) {
            $xmlcontent = file_get_contents($presetnameorfile);
            $manager = new manager();
            list($xmlnotused, $preset) = $manager->import_preset($xmlcontent);
            if (!is_null($preset)) {
                list($applied) = $manager->apply_preset($preset->id);
                if (!empty($applied)) {
                    $presetid = $preset->id;
                }
            }
        } else {
            // Check if the given preset exists; if that's the case, it will be applied.
            $stringmanager = get_string_manager();
            if ($stringmanager->string_exists($presetnameorfile . 'preset', 'tool_admin_presets')) {
                $params = ['name' => get_string($presetnameorfile . 'preset', 'tool_admin_presets')];
            } else {
                $params = ['name' => $presetnameorfile];
            }
            if ($preset = $DB->get_record('tool_admin_presets', $params)) {
                $manager = new manager();
                list($applied) = $manager->apply_preset($preset->id);
                if (!empty($applied)) {
                    $presetid = $preset->id;
                }
            }
        }

        return $presetid;
    }
}
