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

use core\output\renderable;
use core\output\renderer_base;
use stdClass;
use templatable;

/**
 * Renderable class for the initial_dropdown_form.
 *
 * This form is the content for the initials_selector renderable, which itself is an extension of the comboboxsearch component.
 * {@see initials_selector}.
 *
 * @package    core_course
 * @copyright  2024 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class initials_dropdown_form implements renderable, templatable {

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
        protected stdClass $course,
        protected string $targeturl,
        protected string $firstinitial = '',
        protected string $lastinitial = '',
        protected string $firstinitialparam = 'sifirst',
        protected string $lastinitialparam = 'silast',
        protected array $additionalparams = []
    ) {
    }

    public function export_for_template(renderer_base $output) {
        global $PAGE;

        $PAGE->requires->js_call_amd('core_course/actionbar/initials', 'init',
            [$this->targeturl, $this->firstinitialparam, $this->lastinitialparam, $this->additionalparams]);
        $renderer = $PAGE->get_renderer('core_user');

        return (object) [
            'courseid' => $this->course->id,
            'initialsbars' => $renderer->partial_user_search($this->targeturl, $this->firstinitial, $this->lastinitial, true),
        ];
    }
}
