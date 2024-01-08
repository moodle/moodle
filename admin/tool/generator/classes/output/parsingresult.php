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

namespace tool_generator\output;

use renderable;
use renderer_base;
use templatable;
use tool_generator\local\testscenario\parsedfeature;

/**
 * A report to show the feature file parsing process.
 *
 * @package tool_generator
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class parsingresult implements renderable, templatable {

    /** @var parsedfeature the processed feature object. */
    protected $parsedfeature;

    /**
     * Constructor.
     *
     * @param parsedfeature $parsedfeature
     */
    public function __construct(parsedfeature $parsedfeature) {
        $this->parsedfeature = $parsedfeature;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output The renderer.
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $data = [
            'scenarios' => [],
            'isvalid' => $this->parsedfeature->is_valid(),
            'generalerror' => $this->parsedfeature->get_general_error(),
        ];
        $haslines = false;
        foreach ($this->parsedfeature->get_scenarios() as $scenario) {
            $scenariodata = [
                'type' => $scenario->type,
                'name' => $scenario->name,
                'steps' => [],
            ];
            if (!empty($scenario->error)) {
                $scenariodata['scenarioerror'] = $scenario->error;
            }
            foreach ($scenario->steps as $step) {
                $scenariodata['steps'][] = [
                    'text' => $step->get_text(),
                    'arguments' => $step->get_arguments_string(),
                    'hasarguments' => !empty($step->get_arguments_string()),
                    'isvalid' => $step->is_valid(),
                    'error' => $step->get_error(),
                    'isexecuted' => $step->is_executed(),
                ];
                $haslines = true;
            }
            if (!empty($scenariodata['steps'])) {
                $scenariodata['hassteps'] = true;
            }
            $data['scenarios'][] = $scenariodata;
        }
        if ($haslines) {
            $data['haslines'] = $haslines;
        }
        return $data;
    }
}
