<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace tool_generator\local\testscenario;

use stdClass;

/**
 * Class with a scenario feature parsed.
 *
 * @package    tool_generator
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class parsedfeature {
    /** @var int the number of steps. */
    private int $stepcount = 0;

    /** @var bool if the parser is ok or fail. */
    private bool $isvalid = true;

    /** @var stdClass[] the list of scenarios with all the steps.
     *
     * scenarionum => {type: string, title: string, steps: steprunner[]}.
     */
    private array $scenarios = [];

    /**
     * Get the general error, if any.
     * @return string
     */
    public function get_general_error(): string {
        if (!$this->isvalid) {
            return get_string('testscenario_invalidfile', 'tool_generator');
        }
        if ($this->stepcount == 0) {
            return get_string('testscenario_nosteps', 'tool_generator');
        }
        return '';
    }

    /**
     * Check if the parsed feature is valid.
     * @return bool
     */
    public function is_valid(): bool {
        return $this->isvalid && $this->stepcount > 0;
    }

    /**
     * Add a line to the current scenario.
     * @param steprunner $step the step to add.
     */
    public function add_step(steprunner $step) {
        if (empty($this->scenarios)) {
            $this->add_scenario('scenario', null);
        }
        $currentscenario = count($this->scenarios) - 1;
        $this->scenarios[$currentscenario]->steps[] = $step;
        $this->stepcount++;
        if (!$step->is_valid()) {
            $this->isvalid = false;
        }
    }

    /**
     * Insert a new scenario.
     * @param string $type the type of the scenario.
     * @param string|null $name the name of the scenario.
     */
    public function add_scenario(string $type, ?string $name) {
        $this->scenarios[] = (object) [
            'type' => $type,
            'name' => $name ?? '',
            'steps' => [],
            'error' => '',
        ];
    }

    /**
     * Add an error to the current scenario.
     * @param string $error
     */
    public function add_error(string $error) {
        $currentscenario = count($this->scenarios) - 1;
        $this->scenarios[$currentscenario]->error = $error;
    }

    /**
     * Get the list of scenarios.
     * @return stdClass[] array of scenarionum => {type: string, title: string, steps: steprunner[]}
     */
    public function get_scenarios(): array {
        return $this->scenarios;
    }

    /**
     * Get all the steps form all scenarios.
     * @return steprunner[]
     */
    public function get_all_steps(): array {
        $result = [];
        foreach ($this->scenarios as $scenario) {
            foreach ($scenario->steps as $step) {
                $result[] = $step;
            }
        }
        return $result;
    }
}
