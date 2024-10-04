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
 * Renderable class for the action bar elements in the gradebook publish export page.
 *
 * @package    core_grades
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export_publish_action_bar extends action_bar {

    /** @var string $activeplugin The plugin of the current export grades page (xml, ods, ...). */
    protected $activeplugin;

    /**
     * The class constructor.
     *
     * @param \context $context The context object.
     * @param string $activeplugin The plugin of the current export grades page (xml, ods, ...).
     */
    public function __construct(\context $context, string $activeplugin) {
        parent::__construct($context);
        $this->activeplugin = $activeplugin;
    }

    /**
     * Returns the template for the action bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_grades/export_publish_action_bar';
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

        // Add a back button to the action bar.
        $backlink = new moodle_url("/grade/export/{$this->activeplugin}/index.php", ['id' => $courseid]);
        $backbutton = new \single_button($backlink, get_string('back'), 'get');

        return [
            'backbutton' => $backbutton->export_for_template($output)
        ];
    }
}
