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
use templatable;
use tool_generator\local\testscenario\runner;

/**
 * Class stepsinformation
 *
 * @package    tool_generator
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stepsinformation implements templatable, \renderable {

    /**
     * Constructor.
     *
     * @param runner $runner the scenario runner
     */
    public function __construct(
        /** @var runner the runner instance. */
        public runner $runner
    ) {
    }

    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return \stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): \stdClass {
        $steps = [];
        $validsteps = $this->runner->get_valid_steps();
        foreach ($validsteps as $step) {
            $steps[] = (object)[
                'given' => $this->highlight_params($step->given),
                'example' => $step->example,
            ];
        }
        return (object) [
            'steps' => $steps,
        ];
    }

    /**
     * Highlight the parameters in a step.
     *
     * @param string $step the step to highlight
     * @return string the step with the parameters highlighted
     */
    private function highlight_params(string $step): string {
        $step = htmlentities($step);

        // Highlight params starting with ":".
        $step = preg_replace('/:(\w+)/', '<strong>$0</strong>', $step);

        // Highlight params enclosed in "(?P<...>)".
        $step = preg_replace('/\(\?P&lt;(\w+)&gt;((?:[^"]|\\")*)\)/', '<strong>$0</strong>', $step);

        return $step;
    }
}
