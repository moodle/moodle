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

use core_competency\competency;
use core_competency\competency_framework;
use core_competency\plan;

/**
 * Behat data generator for core_competency.
 *
 * @package   core_competency
 * @category  test
 * @copyright 2022 Noel De Martin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_core_competency_generator extends behat_generator_base {

    /**
     * Get a list of the entities that Behat can create using the generator step.
     *
     * @return array
     */
    protected function get_creatable_entities(): array {
        return [
            'competencies' => [
                'singular' => 'competency',
                'datagenerator' => 'competency',
                'required' => ['shortname', 'competencyframework'],
                'switchids' => ['competencyframework' => 'competencyframeworkid'],
            ],
            'course_competencies' => [
                'singular' => 'course_competency',
                'datagenerator' => 'course_competency',
                'required' => ['course', 'competency'],
                'switchids' => ['course' => 'courseid', 'competency' => 'competencyid'],
            ],
            'frameworks' => [
                'singular' => 'framework',
                'datagenerator' => 'framework',
                'required' => ['shortname'],
                'switchids' => ['scale' => 'scaleid'],
            ],
            'plans' => [
                'singular' => 'plan',
                'datagenerator' => 'plan',
                'required' => ['name'],
                'switchids' => ['user' => 'userid'],
            ],
            'related_competencies' => [
                'singular' => 'related_competency',
                'datagenerator' => 'related_competency',
                'required' => ['competency', 'relatedcompetency'],
                'switchids' => ['competency' => 'competencyid', 'relatedcompetency' => 'relatedcompetencyid'],
            ],
            'user_competency' => [
                'singular' => 'user_competency',
                'datagenerator' => 'user_competency',
                'required' => ['competency', 'user'],
                'switchids' => ['competency' => 'competencyid', 'user' => 'userid'],
            ],
            'user_competency_courses' => [
                'singular' => 'user_competency_course',
                'datagenerator' => 'user_competency_course',
                'required' => ['course', 'competency', 'user'],
                'switchids' => ['course' => 'courseid', 'competency' => 'competencyid', 'user' => 'userid'],
            ],
            'user_competency_plans' => [
                'singular' => 'user_competency_plan',
                'datagenerator' => 'user_competency_plan',
                'required' => ['plan', 'competency', 'user'],
                'switchids' => ['plan' => 'planid', 'competency' => 'competencyid', 'user' => 'userid'],
            ],
        ];
    }

    /**
     * Get the competecy framework id using an idnumber.
     *
     * @param string $idnumber
     * @return int The competecy framework id
     */
    protected function get_competencyframework_id(string $idnumber): int {
        global $DB;

        if (!$id = $DB->get_field('competency_framework', 'id', ['idnumber' => $idnumber])) {
            throw new Exception('The specified competency framework with idnumber "' . $idnumber . '" could not be found.');
        }

        return $id;
    }

    /**
     * Get the competecy id using an idnumber.
     *
     * @param string $idnumber
     * @return int The competecy id
     */
    protected function get_competency_id(string $idnumber): int {
        global $DB;

        if (!$id = $DB->get_field('competency', 'id', ['idnumber' => $idnumber])) {
            throw new Exception('The specified competency with idnumber "' . $idnumber . '" could not be found.');
        }

        return $id;
    }

    /**
     * Get the learning plan id using a name.
     *
     * @param string $name
     * @return int The learning plan id
     */
    protected function get_plan_id(string $name): int {
        global $DB;

        if (!$id = $DB->get_field('competency_plan', 'id', ['name' => $name])) {
            throw new Exception('The specified learning plan with name "' . $name . '" could not be found.');
        }

        return $id;
    }

    /**
     * Get the related competecy id using an idnumber.
     *
     * @param string $idnumber
     * @return int The related competecy id
     */
    protected function get_relatedcompetency_id(string $idnumber): int {
        return $this->get_competency_id($idnumber);
    }

    /**
     * Add a plan.
     *
     * @param array $data Plan data.
     */
    public function process_plan(array $data): void {
        $generator = $this->get_data_generator();
        $competencyids = $data['competencyids'] ?? [];

        unset($data['competencyids']);

        $plan = $generator->create_plan($data);

        foreach ($competencyids as $competencyid) {
            $generator->create_plan_competency([
                'planid' => $plan->get('id'),
                'competencyid' => $competencyid,
            ]);
        }
    }

    /**
     * Preprocess user competency data.
     *
     * @param array $data Raw data.
     * @return array Processed data.
     */
    protected function preprocess_user_competency(array $data): array {
        $this->prepare_grading($data);

        return $data;
    }

    /**
     * Preprocess user course competency data.
     *
     * @param array $data Raw data.
     * @return array Processed data.
     */
    protected function preprocess_user_competency_course(array $data): array {
        $this->prepare_grading($data);

        return $data;
    }

    /**
     * Preprocess user learning plan competency data.
     *
     * @param array $data Raw data.
     * @return array Processed data.
     */
    protected function preprocess_user_competency_plan(array $data): array {
        $this->prepare_grading($data);

        return $data;
    }

    /**
     * Preprocess plan data.
     *
     * @param array $data Raw data.
     * @return array Processed data.
     */
    protected function preprocess_plan(array $data): array {
        if (isset($data['competencies'])) {
            $competencies = array_map('trim', str_getcsv($data['competencies']));
            $data['competencyids'] = array_map([$this, 'get_competency_id'], $competencies);

            unset($data['competencies']);
        }

        global $USER;

        return $data + [
            'userid' => $USER->id,
            'status' => plan::STATUS_ACTIVE,
        ];
    }

    /**
     * Prepare grading attributes for record data.
     *
     * @param array $data Record data.
     */
    protected function prepare_grading(array &$data): void {
        if (!isset($data['grade'])) {
            return;
        }

        global $DB;

        $competency = competency::get_record(['id' => $data['competencyid']]);
        $competencyframework = competency_framework::get_record(['id' => $competency->get('competencyframeworkid')]);
        $scale = $DB->get_field('scale', 'scale', ['id' => $competencyframework->get('scaleid')]);
        $grades = array_map('trim', explode(',', $scale));
        $grade = array_search($data['grade'], $grades);

        if ($grade === false) {
            throw new Exception('The grade "'.$data['grade'].'" was not found in the "'.
                $competencyframework->get('shortname').'" competency framework.');
        }

        $data['proficiency'] = true;
        $data['grade'] = $grade + 1;
    }

    /**
     * Get the module data generator.
     *
     * @return core_competency_generator Competency data generator.
     */
    protected function get_data_generator(): core_competency_generator {
        return $this->componentdatagenerator;
    }
}
