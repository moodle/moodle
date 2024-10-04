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
                'switchids' => ['user' => 'userid', 'status' => 'status'],
            ],
            'plan_competencies' => [
                'singular' => 'plan_competency',
                'datagenerator' => 'plan_competency',
                'required' => ['plan', 'competency'],
                'switchids' => ['competency' => 'competencyid', 'plan' => 'planid'],
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
            'user_evidence' => [
                'singular' => 'user_evidence',
                'datagenerator' => 'user_evidence',
                'required' => ['user', 'name'],
                'switchids' => ['user' => 'userid'],
            ],
            'user_evidence_competency' => [
                'singular' => 'user_evidence_competency',
                'datagenerator' => 'user_evidence_competency',
                'required' => ['userevidence', 'competency'],
                'switchids' => ['userevidence' => 'userevidenceid', 'competency' => 'competencyid'],
            ],
            'templates' => [
                'singular' => 'template',
                'datagenerator' => 'template',
                'required' => ['shortname'],
                'switchids' => ['context' => 'contextid'],
            ],
            'template_competencies' => [
                'singular' => 'template_competency',
                'datagenerator' => 'template_competency',
                'required' => ['template', 'competency'],
                'switchids' => ['template' => 'templateid', 'competency' => 'competencyid'],
            ],
        ];
    }

    /**
     * Get the competency framework id using an idnumber.
     *
     * @param string $idnumber
     * @return int The competency framework id
     */
    protected function get_competencyframework_id(string $idnumber): int {
        global $DB;

        if (!$id = $DB->get_field('competency_framework', 'id', ['idnumber' => $idnumber])) {
            throw new Exception('The specified competency framework with idnumber "' . $idnumber . '" could not be found.');
        }

        return $id;
    }

    /**
     * Get the competency id using an idnumber.
     *
     * @param string $idnumber
     * @return int The competency id
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
     * Get the related competency id using an idnumber.
     *
     * @param string $idnumber
     * @return int The related competency id
     */
    protected function get_relatedcompetency_id(string $idnumber): int {
        return $this->get_competency_id($idnumber);
    }

    /**
     * Get the template id by shortname.
     *
     * @param string $shortname The template name.
     * @return int
     */
    protected function get_template_id(string $shortname): int {
        global $DB;

        if (!$id = $DB->get_field('competency_template', 'id', ['shortname' => $shortname])) {
            throw new Exception('The specified template with name "' . $shortname . '" could not be found.');
        }

        return $id;
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
        global $DB, $USER;

        if (isset($data['competencies'])) {
            $competencies = array_map('trim', str_getcsv($data['competencies'], escape: '\\'));
            $data['competencyids'] = array_map([$this, 'get_competency_id'], $competencies);

            unset($data['competencies']);
        }

        if (isset($data['reviewer'])) {
            if (is_number($data['reviewer'])) {
                $data['reviewerid'] = $data['reviewer'];
            } else {
                if (!$userid = $DB->get_field('user', 'id', ['username' => $data['reviewer']])) {
                    throw new Exception('The specified user "' . $data['reviewer'] . '" could not be found.');
                }
                $data['reviewerid'] = $userid;
            }
            unset($data['reviewer']);
        }

        return $data + [
            'userid' => $USER->id,
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

    /**
     * Get the user evidence id using a name.
     *
     * @param string $name User evidence name.
     * @return int The user evidence id
     */
    protected function get_userevidence_id(string $name): int {
        global $DB;

        if (!$id = $DB->get_field('competency_userevidence', 'id', ['name' => $name])) {
            throw new Exception('The specified user evidence with name "' . $name . '" could not be found.');
        }

        return $id;
    }

    /**
     * Get the template competency id using a name.
     *
     * @param string $name Template competency name.
     * @return int The template competency id
     */
    protected function get_templatecompetency_id(string $name): int {
        global $DB;

        if (!$id = $DB->get_field('competency_template', 'id', ['name' => $name])) {
            throw new Exception('The specified template competency with name "' . $name . '" could not be found.');
        }

        return $id;
    }

    /**
     * Get the context id using a contextid.
     *
     * @param string $contextid Context id.
     * @return int The context id
     */
    protected function get_context_id(string $contextid): int {
        global $DB;

        if (!$id = $DB->get_field('context', 'id', ['id' => $contextid])) {
            throw new Exception('The specified context with id "' . $contextid . '" could not be found.');
        }

        return $id;
    }

    /**
     * Get the status id by status name.
     *
     * @param string $name Status name.
     * @return int
     */
    protected function get_status_id(string $name): int {

        switch ($name) {
            case 'draft':
                $status = plan::STATUS_DRAFT;
                break;
            case 'in review':
                $status = plan::STATUS_IN_REVIEW;
                break;
            case 'waiting for review':
                $status = plan::STATUS_WAITING_FOR_REVIEW;
                break;
            case 'complete':
                $status = plan::STATUS_COMPLETE;
                break;
            default:
                $status = plan::STATUS_ACTIVE;
                break;
        }

        return $status;
    }
}
