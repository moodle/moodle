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

namespace mod_data\output;

use action_menu;
use action_menu_link_secondary;
use mod_data\manager;
use mod_data\preset;
use moodle_url;
use templatable;
use renderable;
use renderer_base;
use stdClass;

/**
 * Renderable class for the presets table in the database activity.
 *
 * @package    mod_data
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class presets implements templatable, renderable {

    /** @var array $presets The array containing the existing presets. */
    private $presets;

    /** @var moodle_url $formactionurl The action url for the form. */
    private $formactionurl;

    /** @var bool $manage Whether the manage preset options should be displayed. */
    private $manage;

    /** @var int $id instance id */
    private $id;

    /** @var int $cmid course module id */
    private $cmid;

    /**
     * The class constructor.
     *
     * @param manager $manager The database manager
     * @param array $presets The array containing the existing presets
     * @param moodle_url $formactionurl The action url for the form
     * @param bool $manage Whether the manage preset options should be displayed
     */
    public function __construct(manager $manager, array $presets, moodle_url $formactionurl, bool $manage = false) {
        $this->id = $manager->get_instance()->id;
        $this->cmid = $manager->get_coursemodule()->id;
        $this->presets = $presets;
        $this->formactionurl = $formactionurl;
        $this->manage = $manage;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param renderer_base $output The renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $presets = $this->get_presets($output);
        return [
            'id' => $this->cmid,
            'formactionurl' => $this->formactionurl->out(),
            'showmanage' => $this->manage,
            'presets' => $presets,
        ];
    }

    /**
     * Returns the presets list with the information required to display them.
     *
     * @param renderer_base $output The renderer to be used to render the action bar elements.
     * @return array Presets list.
     */
    private function get_presets(renderer_base $output): array {
        $presets = [];
        foreach ($this->presets as $index => $preset) {
            $presetname = $preset->name;
            $userid = $preset instanceof preset ? $preset->get_userid() : $preset->userid;
            if (!empty($userid)) {
                // If the preset has the userid field, the full name of creator it will be added to the end of the name.
                $userfieldsapi = \core_user\fields::for_name();
                $namefields = $userfieldsapi->get_sql('', false, '', '', false)->selects;
                $fields = 'id, ' . $namefields;
                $presetuser = \core_user::get_user($userid, $fields, MUST_EXIST);
                $username = fullname($presetuser, true);
                $presetname = "{$presetname} ({$username})";
            }
            $actions = $this->get_preset_action_menu($output, $preset, $userid);

            $fullname = $preset->get_fullname();
            $previewurl = new moodle_url(
                    '/mod/data/preset.php',
                    ['d' => $this->id, 'fullname' => $fullname, 'action' => 'preview']
            );

            $presets[] = [
                'id' => $this->id,
                'cmid' => $this->cmid,
                'name' => $preset->name,
                'url' => $previewurl->out(),
                'shortname' => $preset->shortname,
                'fullname' => $presetname,
                'description' => $preset->description,
                'userid' => $userid,
                'actions' => $actions,
                'presetindex' => $index,
            ];
        }

        return $presets;
    }

    /**
     * Return the preset action menu data.
     *
     * @param renderer_base $output The renderer to be used to render the action bar elements.
     * @param preset|stdClass $preset the preset object
     * @param int|null $userid the user id (null for plugin presets)
     * @return stdClass the resulting action menu
     */
    private function get_preset_action_menu(renderer_base $output, $preset, ?int $userid): stdClass {

        $actions = new stdClass();
        // If we cannot manage then return an empty menu.
        if (!$this->manage) {
            return $actions;
        }
        $actionmenu = new action_menu();
        $actionmenu->set_kebab_trigger();
        $actionmenu->set_action_label(get_string('actions'));
        $actionmenu->set_additional_classes('presets-actions');
        $canmanage = $preset->can_manage();

        $usepreseturl = new moodle_url('/mod/data/preset.php', [
            'action' => 'usepreset',
            'cmid' => $this->cmid,
        ]);
        $this->add_action_menu($actionmenu, get_string('usepreset', 'mod_data'), $usepreseturl, [
                'data-action' => 'selectpreset',
                'data-presetname' => $preset->get_fullname(),
                'data-cmid' => $this->cmid,
            ]
        );

        // Attention: the id here is the cm->id, not d->id.
        $previewpreseturl = new moodle_url('/mod/data/preset.php', [
            'fullname' => $preset->get_fullname(),
            'action' => 'preview',
            'id' => $this->cmid,
        ]);
        $this->add_action_menu($actionmenu, get_string('previewaction', 'mod_data'), $previewpreseturl, [
                'data-action' => 'preview',
            ]
        );

        // Presets saved by users can be edited or removed.
        if (!$preset->isplugin) {
            // Edit.
            if ($canmanage) {
                $editactionurl = new moodle_url('/mod/data/preset.php', [
                    'action' => 'edit',
                    'd' => $this->id,
                ]);
                $this->add_action_menu($actionmenu, get_string('edit'), $editactionurl, [
                    'data-action' => 'editpreset',
                    'data-presetname' => $preset->name,
                    'data-presetdescription' => $preset->description,
                ]);
            }

            // Export.
            $exporturl = new moodle_url('/mod/data/preset.php', [
                'presetname' => $preset->name,
                'action' => 'export',
                'd' => $this->id,
            ]);
            $this->add_action_menu($actionmenu, get_string('export', 'mod_data'), $exporturl, [
                'data-action' => 'exportpreset',
                'data-presetname' => $preset->name,
                'data-presetdescription' => $preset->description,
            ]);

            // Delete.
            if ($canmanage) {

                $deleteactionurl = new moodle_url('/mod/data/preset.php', [
                    'action' => 'delete',
                    'd' => $this->id,
                ]);
                $this->add_action_menu($actionmenu, get_string('delete'), $deleteactionurl, [
                    'data-action' => 'deletepreset',
                    'data-presetname' => $preset->name,
                ]);
            }
        }
        $actions = $actionmenu->export_for_template($output);
        return $actions;
    }

    /**
     * Add action to the action menu
     *
     * @param action_menu $actionmenu
     * @param string $actionlabel
     * @param moodle_url $actionurl
     * @param array $otherattributes
     * @return void
     */
    private function add_action_menu(action_menu &$actionmenu, string $actionlabel, moodle_url $actionurl,
        array $otherattributes) {
        $attributes = [
            'data-dataid' => $this->id,
        ];
        $actionmenu->add(new action_menu_link_secondary(
            $actionurl,
            null,
            $actionlabel,
            array_merge($attributes, $otherattributes),
        ));
    }
}
