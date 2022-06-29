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

/**
 * Renderable class for the action bar elements in the gradebook export pages.
 *
 * @package    core_grades
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export_action_bar extends action_bar {

    /** @var moodle_url $exportactiveurl The URL that should be set as active in the exports URL selector element. */
    protected $exportactiveurl;

    /** @var string $activeplugin The plugin of the current export grades page (xml, ods, ...). */
    protected $activeplugin;

    /**
     * The class constructor.
     *
     * @param \context $context The context object.
     * @param moodle_url $exportactiveurl The URL that should be set as active in the exports URL selector element.
     * @param string $activeplugin The plugin of the current export grades page (xml, ods, ...).
     */
    public function __construct(\context $context, moodle_url $exportactiveurl, string $activeplugin) {
        parent::__construct($context);
        $this->exportactiveurl = $exportactiveurl;
        $this->activeplugin = $activeplugin;
    }

    /**
     * Returns the template for the action bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_grades/export_action_bar';
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        if ($this->context->contextlevel !== CONTEXT_COURSE) {
            return [];
        }
        $courseid = $this->context->instanceid;
        // Get the data used to output the general navigation selector.
        $generalnavselector = new general_action_bar($this->context,
            new moodle_url('/grade/export/index.php', ['id' => $courseid]), 'export', $this->activeplugin);
        $data = $generalnavselector->export_for_template($output);

        // Get all grades export plugins. If there isn't any available export plugins there is no need to create and
        // display the exports navigation selector menu. Therefore, return only the current data.
        if (!$exports = \grade_helper::get_plugins_export($courseid)) {
            return $data;
        }

        // If exports key management is enabled, always display this item at the end of the list.
        if (array_key_exists('keymanager', $exports)) {
            $keymanager = $exports['keymanager'];
            unset($exports['keymanager']);
            $exports['keymanager'] = $keymanager;
        }

        $exportsmenu = [];
        // Generate the data for the exports navigation selector menu.
        foreach ($exports as $export) {
            $exportsmenu[$export->link->out()] = $export->string;
        }

        // This navigation selector menu will contain the links to all available grade export plugin pages.
        $exportsurlselect = new \url_select($exportsmenu, $this->exportactiveurl->out(false), null,
            'gradesexportactionselect');
        $data['exportselector'] = $exportsurlselect->export_for_template($output);

        return $data;
    }
}
