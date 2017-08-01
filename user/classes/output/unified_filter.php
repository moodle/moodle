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

/**
 * Class containing the filter options data for rendering the unified filter autocomplete element for the course participants page.
 *
 * @package    core_user
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_user\output;

use renderable;
use renderer_base;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die();

/**
 * Class containing the filter options data for rendering the unified filter autocomplete element for the course participants page.
 *
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unified_filter implements renderable, templatable {

    /** @var array $filteroptions The filter options. */
    protected $filteroptions;

    /** @var array $selectedoptions The list of selected filter option values. */
    protected $selectedoptions;

    /**
     * unified_filter constructor.
     *
     * @param array $filteroptions The filter options.
     * @param array $selectedoptions The list of selected filter option values.
     */
    public function __construct($filteroptions, $selectedoptions) {
        $this->filteroptions = $filteroptions;
        $this->selectedoptions = $selectedoptions;
    }

    /**
     * Function to export the renderer data in a format that is suitable for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $PAGE;
        $data = new stdClass();
        $data->action = $PAGE->url->out(false);

        foreach ($this->selectedoptions as $option) {
            if (!isset($this->filteroptions[$option])) {
                $this->filteroptions[$option] = $option;
            }
        }

        $data->filteroptions = [];
        $originalfilteroptions = [];
        foreach ($this->filteroptions as $value => $label) {
            $selected = in_array($value, $this->selectedoptions);
            $filteroption = (object)[
                'value' => $value,
                'label' => $label
            ];
            $originalfilteroptions[] = $filteroption;
            $filteroption->selected = $selected;
            $data->filteroptions[] = $filteroption;
        }
        $data->originaloptionsjson = json_encode($originalfilteroptions);
        return $data;
    }
}