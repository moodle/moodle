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
 * Renderable class for the action bar elements in the manage outcomes page.
 *
 * @package    core_grades
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_outcomes_action_bar extends action_bar {

    /** @var bool $hasoutcomes Whether there are existing outcomes. */
    protected $hasoutcomes;

    /**
     * The class constructor.
     *
     * @param \context $context The context object.
     * @param bool $hasoutcomes Whether there are existing outcomes.
     */
    public function __construct(\context $context, bool $hasoutcomes) {
        parent::__construct($context);
        $this->hasoutcomes = $hasoutcomes;
    }

    /**
     * Returns the template for the action bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_grades/manage_outcomes_action_bar';
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $data = [];
        $courseid = 0;
        // Display the following buttons only if the user is in course gradebook.
        if ($this->context->contextlevel === CONTEXT_COURSE) {
            $courseid = $this->context->instanceid;
            // Add a button to the action bar with a link to the 'course outcomes' page.
            $backlink = new moodle_url('/grade/edit/outcome/course.php', ['id' => $courseid]);
            $backbutton = new \single_button($backlink, get_string('back'), 'get');
            $data['backbutton'] = $backbutton->export_for_template($output);

            // Add a button to the action bar with a link to the 'import outcomes' page. The import outcomes
            // functionality is currently only available in the course context.
            $importoutcomeslink = new moodle_url('/grade/edit/outcome/import.php', ['courseid' => $courseid]);
            $importoutcomesbutton = new \single_button($importoutcomeslink, get_string('importoutcomes', 'grades'),
                'get');
            $data['importoutcomesbutton'] = $importoutcomesbutton->export_for_template($output);
        }

        // Add a button to the action bar with a link to the 'add new outcome' page.
        $addoutcomelink = new moodle_url('/grade/edit/outcome/edit.php', ['courseid' => $courseid]);
        $addoutcomebutton = new \single_button($addoutcomelink, get_string('outcomecreate', 'grades'),
            'get', true);
        $data['addoutcomebutton'] = $addoutcomebutton->export_for_template($output);

        if ($this->hasoutcomes) {
            // Add a button to the action bar which enables export of all existing outcomes.
            $exportoutcomeslink = new moodle_url('/grade/edit/outcome/export.php',
                ['id' => $courseid, 'sesskey' => sesskey()]);
            $exportoutcomesbutton = new \single_button($exportoutcomeslink, get_string('exportalloutcomes', 'grades'),
                'get');
            $data['exportoutcomesbutton'] = $exportoutcomesbutton->export_for_template($output);
        }

        return $data;
    }
}
