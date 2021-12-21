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
 * Renderable class for the action bar elements in the gradebook import pages.
 *
 * @package    core_grades
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_action_bar extends action_bar {

    /** @var moodle_url $importactiveurl The URL that should be set as active in the imports URL selector element. */
    protected $importactiveurl;

    /** @var string $activeplugin The plugin of the current import grades page (xml, csv, ...). */
    protected $activeplugin;

    /**
     * The class constructor.
     *
     * @param \context $context The context object.
     * @param moodle_url $importactiveurl The URL that should be set as active in the imports URL selector element.
     * @param string $activeplugin The plugin of the current import grades page (xml, csv, ...).
     */
    public function __construct(\context $context, moodle_url $importactiveurl, string $activeplugin) {
        parent::__construct($context);
        $this->importactiveurl = $importactiveurl;
        $this->activeplugin = $activeplugin;
    }

    /**
     * Returns the template for the action bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_grades/import_action_bar';
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
            new moodle_url('/grade/import/index.php', ['id' => $courseid]), 'import', $this->activeplugin);
        $data = $generalnavselector->export_for_template($output);

        // Get all grades import plugins. If there isn't any available import plugins there is no need to create and
        // display the imports navigation selector menu. Therefore, return only the current data.
        if (!$imports = \grade_helper::get_plugins_import($courseid)) {
            return $data;
        }

        // If imports key management is enabled, always display this item at the end of the list.
        if (array_key_exists('keymanager', $imports)) {
            $keymanager = $imports['keymanager'];
            unset($imports['keymanager']);
            $imports['keymanager'] = $keymanager;
        }

        $importsmenu = [];
        // Generate the data for the imports navigation selector menu.
        foreach ($imports as $import) {
            $importsmenu[$import->link->out()] = $import->string;
        }

        // This navigation selector menu will contain the links to all available grade export plugin pages.
        $importsurlselect = new \url_select($importsmenu, $this->importactiveurl->out(false), null,
            'gradesimportactionselect');
        $data['importselector'] = $importsurlselect->export_for_template($output);

        return $data;
    }
}
