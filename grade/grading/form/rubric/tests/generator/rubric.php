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
 * Generator for the gradingforum_rubric plugin.
 *
 * @package    gradingform_rubric
 * @category   test
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tests\gradingform_rubric\generator;

use gradingform_controller;
use stdClass;

/**
 * Test rubric.
 *
 * @package    gradingform_rubric
 * @category   test
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rubric {

    /** @var array $criteria The criteria for this rubric. */
    protected $criteria = [];

    /** @var string The name of this rubric. */
    protected $name;

    /** @var string A description for this rubric. */
    protected $description;

    /** @var array The rubric options. */
    protected $options = [];

    /**
     * Create a new gradingform_rubric_generator_rubric.
     *
     * @param string $name
     * @param string $description
     */
    public function __construct(string $name, string $description) {
        $this->name = $name;
        $this->description = $description;

        $this->set_option('sortlevelsasc', 1);
        $this->set_option('lockzeropoints', 1);
        $this->set_option('showdescriptionteacher', 1);
        $this->set_option('showdescriptionstudent', 1);
        $this->set_option('showscoreteacher', 1);
        $this->set_option('showscorestudent', 1);
        $this->set_option('enableremarks', 1);
        $this->set_option('showremarksstudent', 1);
    }

    /**
     * Creates the rubric using the appropriate APIs.
     */
    public function get_definition(): stdClass {
        return (object) [
            'name' => $this->name,
            'description_editor' => [
                'text' => $this->description,
                'format' => FORMAT_HTML,
                'itemid' => 1
            ],
            'rubric' => [
                'criteria' => $this->get_all_criterion_values(),
                'options' => $this->options,
            ],
            'saverubric' => 'Save rubric and make it ready',
            'status' => gradingform_controller::DEFINITION_STATUS_READY,
        ];
    }

    /**
     * Set an option for the rubric.
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function set_option(string $key, $value): self {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * Adds a criterion to the rubric.
     *
     * @param criterion $criterion The criterion object (class below).
     * @return self
     */
    public function add_criteria(criterion $criterion): self {
        $this->criteria[] = $criterion;

        return $this;
    }

    /**
     * Get all criterion values.
     *
     * @return array
     */
    protected function get_all_criterion_values(): array {
        $result = [];

        foreach ($this->criteria as $index => $criterion) {
            $id = $index + 1;
            $result["NEWID{$id}"] = $criterion->get_all_values($id);
        }

        return $result;

    }
}
