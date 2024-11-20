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

namespace core_grades\output;

use moodle_url;
use core\output\select_menu;

/**
 * Renderable class for the general action bar in the gradebook pages.
 *
 * This class is responsible for rendering the general navigation select menu in the gradebook pages.
 *
 * @package    core_grades
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class general_action_bar extends action_bar {

    /** @var moodle_url $activeurl The URL that should be set as active in the URL selector element. */
    protected $activeurl;

    /**
     * The type of the current gradebook page (report, settings, import, export, scales, outcomes, letters).
     *
     * @var string $activetype
     */
    protected $activetype;

    /** @var string $activeplugin The plugin of the current gradebook page (grader, fullview, ...). */
    protected $activeplugin;

    /**
     * The class constructor.
     *
     * @param \context $context The context object.
     * @param moodle_url $activeurl The URL that should be set as active in the URL selector element.
     * @param string $activetype The type of the current gradebook page (report, settings, import, export, scales,
     *                           outcomes, letters).
     * @param string $activeplugin The plugin of the current gradebook page (grader, fullview, ...).
     */
    public function __construct(\context $context, moodle_url $activeurl, string $activetype, string $activeplugin) {
        parent::__construct($context);
        $this->activeurl = $activeurl;
        $this->activetype = $activetype;
        $this->activeplugin = $activeplugin;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $selectmenu = $this->get_action_selector();

        if (is_null($selectmenu)) {
            return [];
        }

        return [
            'generalnavselector' => $selectmenu->export_for_template($output),
        ];
    }

    /**
     * Returns the template for the action bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_grades/general_action_bar';
    }

    /**
     * Returns the URL selector object.
     *
     * @return \select_menu|null The URL select object.
     */
    private function get_action_selector(): ?select_menu {
        if ($this->context->contextlevel !== CONTEXT_COURSE) {
            return null;
        }
        $courseid = $this->context->instanceid;
        $plugininfo = grade_get_plugin_info($courseid, $this->activetype, $this->activeplugin);
        $menu = [];
        $viewgroup = [];
        $setupgroup = [];
        $moregroup = [];

        foreach ($plugininfo as $plugintype => $plugins) {
            // Skip if the plugintype value is 'strings'. This particular item only returns an array of strings
            // which we do not need.
            if ($plugintype == 'strings') {
                continue;
            }

            // If $plugins is actually the definition of a child-less parent link.
            if (!empty($plugins->id)) {
                $string = $plugins->string;
                if (!empty($plugininfo[$this->activetype]->parent)) {
                    $string = $plugininfo[$this->activetype]->parent->string;
                }
                $menu[$plugins->link->out(false)] = $string;
                continue;
            }

            foreach ($plugins as $key => $plugin) {
                // Depending on the plugin type, include the plugin to the appropriate item group for the URL selector
                // element.
                switch ($plugintype) {
                    case 'report':
                        $viewgroup[$plugin->link->out(false)] = $plugin->string;
                        break;
                    case 'settings':
                        $setupgroup[$plugin->link->out(false)] = $plugin->string;
                        break;
                    case 'scale':
                        // We only need the link to the 'view scales' page, otherwise skip and continue to the next
                        // plugin.
                        if ($key !== 'view') {
                            continue 2;
                        }
                        $moregroup[$plugin->link->out(false)] = get_string('scales');
                        break;
                    case 'outcome':
                        // We only need the link to the 'outcomes used in course' page, otherwise skip and continue to
                        // the next plugin.
                        if ($key !== 'course') {
                            continue 2;
                        }
                        $moregroup[$plugin->link->out(false)] = get_string('outcomes', 'grades');
                        break;
                    case 'letter':
                        // We only need the link to the 'view grade letters' page, otherwise skip and continue to the
                        // next plugin.
                        if ($key !== 'view') {
                            continue 2;
                        }
                        $moregroup[$plugin->link->out(false)] = get_string('gradeletters', 'grades');
                        break;
                    case 'import':
                        $link = new moodle_url('/grade/import/index.php', ['id' => $courseid]);
                        // If the link to the grade import options is already added to the group, skip and continue to
                        // the next plugin.
                        if (array_key_exists($link->out(false), $moregroup)) {
                            continue 2;
                        }
                        $moregroup[$link->out(false)] = get_string('import', 'grades');
                        break;
                    case 'export':
                        $link = new moodle_url('/grade/export/index.php', ['id' => $courseid]);
                        // If the link to the grade export options is already added to the group, skip and continue to
                        // the next plugin.
                        if (array_key_exists($link->out(false), $moregroup)) {
                            continue 2;
                        }
                        $moregroup[$link->out(false)] = get_string('export', 'grades');
                        break;
                }
            }
        }

        if (!empty($viewgroup)) {
            $menu[][get_string('view')] = $viewgroup;
        }

        if (!empty($setupgroup)) {
            $menu[][get_string('setup', 'grades')] = $setupgroup;
        }

        if (!empty($moregroup)) {
            $menu[][get_string('moremenu')] = $moregroup;
        }

        $selectmenu = new select_menu('gradesactionselect', $menu, $this->activeurl->out(false));
        $selectmenu->set_label(get_string('gradebooknavigationmenu', 'grades'), ['class' => 'sr-only']);

        return $selectmenu;
    }
}
