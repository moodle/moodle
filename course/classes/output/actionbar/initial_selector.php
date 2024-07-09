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

namespace core_course\output\actionbar;

use core\output\comboboxsearch;
use stdClass;

/**
 * Renderable class for the initial selector element in the action bar.
 *
 * @package    core_course
 * @copyright  2024 Kevin Percy <kevin.percy@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class initial_selector extends comboboxsearch {

    /**
     * The class constructor.
     *
     * @param stdClass $course The course object.
     * @param string $targeturl The target URL to send the form to.
     * @param string $firstinitial The selected first initial.
     * @param string $lastinitial The selected last initial.
     * @param string $firstinitialparam The parameter name for the first initial.
     * @param string $lastinitialparam The parameter name for the last initial.
     * @param array $additionalparams Any additional parameters required for the form submission URL.
     */
    public function __construct(
        stdClass $course,
        string $targeturl,
        string $firstinitial = '',
        string $lastinitial = '',
        string $firstinitialparam = 'sifirst',
        string $lastinitialparam = 'silast',
        array $additionalparams = []
    ) {
        $initialselectorcontent = $this->initial_selector_output(
            course: $course,
            targeturl: $targeturl,
            firstinitial: $firstinitial,
            lastinitial: $lastinitial,
            firstinitialparam: $firstinitialparam,
            lastinitialparam: $lastinitialparam,
            additionalparams: $additionalparams
        );
        $currentfilteroutput = $this->current_filter_output($firstinitial, $lastinitial);

        parent::__construct(
            false,
            $currentfilteroutput !== '' ? $currentfilteroutput : get_string('filterbyname', 'course'),
            $initialselectorcontent,
            'initials-selector',
            'initialswidget',
            'initialsdropdown',
            $currentfilteroutput !== '' ? get_string('name') : null,
            true,
            get_string('filterbyname', 'course'),
            'nameinitials',
            json_encode([
                'first' => $firstinitial,
                'last' => $lastinitial,
            ])
        );
    }

    /**
     * Method to generate the current filter information for the initial selector label.
     *
     * @param string $firstinitial The selected first initial.
     * @param string $lastinitial The selected last initial.
     * @return string
     */
    public function current_filter_output(string $firstinitial, string $lastinitial): string {
        if ($firstinitial !== '' && $lastinitial !== '') {
            return get_string('filterbothactive', 'course', ['first' => $firstinitial, 'last' => $lastinitial]);
        } else if ($firstinitial !== '') {
            return get_string('filterfirstactive', 'course', ['first' => $firstinitial]);
        } else if ($lastinitial !== '') {
            return get_string('filterlastactive', 'course', ['last' => $lastinitial]);
        }

        return '';
    }

    /**
     * Method to generate the output for the initial selector.
     *
     * @param stdClass $course The course object.
     * @param string $targeturl The target URL to send the form to.
     * @param string $firstinitial The selected first initial.
     * @param string $lastinitial The selected last initial.
     * @param string $firstinitialparam The parameter name for the first initial.
     * @param string $lastinitialparam The parameter name for the last initial.
     * @param array $additionalparams Any additional parameters required for the form submission URL.
     * @return string
     */
    public function initial_selector_output(
        stdClass $course,
        string $targeturl,
        string $firstinitial = '',
        string $lastinitial = '',
        string $firstinitialparam = 'sifirst',
        string $lastinitialparam = 'silast',
        array $additionalparams = []
    ): string {
        global $OUTPUT, $PAGE;

        $renderer = $PAGE->get_renderer('core_user');
        $initialsbar = $renderer->partial_user_search($targeturl, $firstinitial, $lastinitial, true);

        $PAGE->requires->js_call_amd('core_course/actionbar/initials', 'init',
            [$targeturl, $firstinitialparam, $lastinitialparam, $additionalparams]);

        $formdata = (object) [
            'courseid' => $course->id,
            'initialsbars' => $initialsbar,
        ];
        return $OUTPUT->render_from_template('core_course/initials_dropdown_form', $formdata);
    }
}
