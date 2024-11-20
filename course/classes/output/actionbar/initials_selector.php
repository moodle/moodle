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
class initials_selector extends comboboxsearch {

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
        // The second and third arguments (buttoncontent and dropdowncontent) need to be rendered here, since the comboboxsearch
        // template expects HTML in its respective context properties. Ideally, children of comboboxsearch would leverage Mustache's
        // blocks pragma, meaning a child template could extend the comboboxsearch, allowing rendering of the child component,
        // instead of needing to inject the child's content HTML as part of rendering the comboboxsearch parent, as is the case
        // here. Achieving this, however, requires a refactor of comboboxsearch. For now, this must be pre-rendered and injected.
        $filterstatestring = $this->get_current_filter_state_string();
        parent::__construct(
            false,
            $filterstatestring !== '' ? $filterstatestring : get_string('filterbyname', 'course'),
            $this->render_initials_dropdown_form(),
            'initials-selector',
            'initialswidget',
            'initialsdropdown',
            $filterstatestring !== '' ? get_string('name') : null,
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
     * Method to generate the current filter string for the initial selector label.
     *
     * @return string the HTML string representing the current initials filter state. E.g. "First (A)", or empty if none selected.
     */
    private function get_current_filter_state_string(): string {
        if ($this->firstinitial !== '' && $this->lastinitial !== '') {
            return get_string('filterbothactive', 'course', ['first' => $this->firstinitial, 'last' => $this->lastinitial]);
        } else if ($this->firstinitial !== '') {
            return get_string('filterfirstactive', 'course', ['first' => $this->firstinitial]);
        } else if ($this->lastinitial !== '') {
            return get_string('filterlastactive', 'course', ['last' => $this->lastinitial]);
        }

        return '';
    }

    /**
     * Method to generate the output for the initial selector.
     *
     * @return string the rendered HTML content.
     */
    private function render_initials_dropdown_form(): string {
        global $PAGE;

        $initialsdropdownform = new initials_dropdown_form(
            $this->course,
            $this->targeturl,
            $this->firstinitial,
            $this->lastinitial,
            $this->firstinitialparam,
            $this->lastinitialparam,
            $this->additionalparams
        );
        return $PAGE->get_renderer('core', 'course')->render($initialsdropdownform);
    }
}
