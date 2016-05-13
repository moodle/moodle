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
 * Competency data generator.
 *
 * @package    core_competency
 * @category   test
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_competency\competency;
use core_competency\competency_framework;
use core_competency\course_competency;
use core_competency\course_module_competency;
use core_competency\evidence;
use core_competency\external;
use core_competency\plan;
use core_competency\plan_competency;
use core_competency\related_competency;
use core_competency\template;
use core_competency\template_cohort;
use core_competency\template_competency;
use core_competency\user_competency;
use core_competency\user_competency_course;
use core_competency\user_competency_plan;
use core_competency\user_evidence;
use core_competency\user_evidence_competency;


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/grade/grade_scale.php');

/**
 * Competency data generator class.
 *
 * @package    core_competency
 * @category   test
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_competency_generator extends component_generator_base {

    /** @var int Number of created competencies. */
    protected $competencycount = 0;

    /** @var int Number of created frameworks. */
    protected $frameworkcount = 0;

    /** @var int Number of created plans. */
    protected $plancount = 0;

    /** @var int Number of created templates. */
    protected $templatecount = 0;

    /** @var int Number of created user_evidence. */
    protected $userevidencecount = 0;

    /** @var stdClass Scale that we might need. */
    protected $scale;

    /**
     * Reset process.
     *
     * Do not call directly.
     *
     * @return void
     */
    public function reset() {
        $this->competencycount = 0;
        $this->frameworkcount = 0;
        $this->scale = null;
    }

    /**
     * Create a new competency.
     *
     * @param array|stdClass $record
     * @return competency
     */
    public function create_competency($record = null) {
        $this->frameworkcount++;
        $i = $this->frameworkcount;
        $record = (object) $record;

        if (!isset($record->competencyframeworkid)) {
            throw new coding_exception('The competencyframeworkid value is required.');
        }
        if (!isset($record->shortname)) {
            $record->shortname = "Competency shortname $i";
        }
        if (!isset($record->idnumber)) {
            $record->idnumber = "cmp{$i}";
        }
        if (!isset($record->description)) {
            $record->description = "Competency $i description ";
        }
        if (!isset($record->descriptionformat)) {
            $record->descriptionformat = FORMAT_HTML;
        }
        if (isset($record->scaleconfiguration)
                && (is_array($record->scaleconfiguration) || is_object($record->scaleconfiguration))) {
            // Conveniently encode the config.
            $record->scaleconfiguration = json_encode($record->scaleconfiguration);
        }

        $competency = new competency(0, $record);
        $competency->create();

        return $competency;
    }

    /**
     * Create a new framework.
     *
     * @param array|stdClass $record
     * @return competency_framework
     */
    public function create_framework($record = null) {
        $generator = phpunit_util::get_data_generator();
        $this->frameworkcount++;
        $i = $this->frameworkcount;
        $record = (object) $record;

        if (!isset($record->shortname)) {
            $record->shortname = "Framework shortname $i";
        }
        if (!isset($record->idnumber)) {
            $record->idnumber = "frm{$i}";
        }
        if (!isset($record->description)) {
            $record->description = "Framework $i description ";
        }
        if (!isset($record->descriptionformat)) {
            $record->descriptionformat = FORMAT_HTML;
        }
        if (!isset($record->visible)) {
            $record->visible = 1;
        }
        if (!isset($record->scaleid)) {
            if (isset($record->scaleconfiguration)) {
                throw new coding_exception('Scale configuration must be provided with a scale.');
            }
            if (!$this->scale) {
                $this->scale = $generator->create_scale(array('scale' => 'A,B,C,D'));
            }
            $record->scaleid = $this->scale->id;
        }
        if (!isset($record->scaleconfiguration)) {
            $scale = grade_scale::fetch(array('id' => $record->scaleid));
            $values = $scale->load_items();
            foreach ($values as $key => $value) {
                // Add a key (make the first value 1).
                $values[$key] = array('id' => $key + 1, 'name' => $value);
            }
            if (count($values) < 2) {
                throw new coding_exception('Please provide the scale configuration for one-item scales.');
            }
            $scaleconfig = array();
            // Last item is proficient.
            $item = array_pop($values);
            array_unshift($scaleconfig, array(
                'id' => $item['id'],
                'proficient' => 1
            ));
            // Second-last item is default and proficient.
            $item = array_pop($values);
            array_unshift($scaleconfig, array(
                'id' => $item['id'],
                'scaledefault' => 1,
                'proficient' => 1
            ));
            array_unshift($scaleconfig, array('scaleid' => $record->scaleid));
            $record->scaleconfiguration = json_encode($scaleconfig);
        }
        if (is_array($record->scaleconfiguration) || is_object($record->scaleconfiguration)) {
            // Conveniently encode the config.
            $record->scaleconfiguration = json_encode($record->scaleconfiguration);
        }
        if (!isset($record->contextid)) {
            $record->contextid = context_system::instance()->id;
        }

        $framework = new competency_framework(0, $record);
        $framework->create();

        return $framework;
    }

    /**
     * Create a related competency.
     *
     * @param array|stdClass $record
     * @return related_competency
     */
    public function create_related_competency($record = null) {
        $record = (object) $record;

        if (!isset($record->competencyid)) {
            throw new coding_exception('Property competencyid is required.');
        }
        if (!isset($record->relatedcompetencyid)) {
            throw new coding_exception('Property relatedcompetencyid is required.');
        }

        $relation = related_competency::get_relation($record->competencyid, $record->relatedcompetencyid);
        if ($relation->get_id()) {
            throw new coding_exception('Relation already exists');
        }
        $relation->create();

        return $relation;
    }

    /**
     * Create a template.
     *
     * @param array|stdClass $record
     * @return template
     */
    public function create_template($record = null) {
        $this->templatecount++;
        $i = $this->templatecount;
        $record = (object) $record;

        if (!isset($record->shortname)) {
            $record->shortname = "Template shortname $i";
        }
        if (!isset($record->description)) {
            $record->description = "Template $i description ";
        }
        if (!isset($record->contextid)) {
            $record->contextid = context_system::instance()->id;
        }

        $template = new template(0, $record);
        $template->create();

        return $template;
    }

    /**
     * Create a template competency.
     *
     * @param array|stdClass $record
     * @return template_competency
     */
    public function create_template_competency($record = null) {
        $record = (object) $record;

        if (!isset($record->competencyid)) {
            throw new coding_exception('Property competencyid is required.');
        }
        if (!isset($record->templateid)) {
            throw new coding_exception('Property templateid is required.');
        }

        $relation = new template_competency(0, $record);
        $relation->create();

        return $relation;
    }

    /**
     * Create a new user competency.
     *
     * @param array|stdClass $record
     * @return user_competency
     */
    public function create_user_competency($record = null) {
        $record = (object) $record;

        if (!isset($record->userid)) {
            throw new coding_exception('The userid value is required.');
        }
        if (!isset($record->competencyid)) {
            throw new coding_exception('The competencyid value is required.');
        }

        $usercompetency = new user_competency(0, $record);
        $usercompetency->create();

        return $usercompetency;
    }

    /**
     * Create a new plan.
     *
     * @param array|stdClass $record
     * @return plan
     */
    public function create_plan($record = null) {
        $this->plancount++;
        $i = $this->plancount;
        $record = (object) $record;

        if (!isset($record->name)) {
            $record->name = "Plan shortname $i";
        }
        if (!isset($record->description)) {
            $record->description = "Plan $i description";
        }
        if (!isset($record->descriptionformat)) {
            $record->descriptionformat = FORMAT_HTML;
        }
        if (!isset($record->userid)) {
            throw new coding_exception('The userid value is required.');
        }

        $plan = new plan(0, $record);
        $plan->create();

        return $plan;
    }

    /**
     * Create a new user competency course.
     *
     * @param array|stdClass $record
     * @return user_competency_course
     */
    public function create_user_competency_course($record = null) {
        $record = (object) $record;

        if (!isset($record->userid)) {
            throw new coding_exception('The userid value is required.');
        }
        if (!isset($record->competencyid)) {
            throw new coding_exception('The competencyid value is required.');
        }

        if (!isset($record->courseid)) {
            throw new coding_exception('The courseid value is required.');
        }

        $usercompetencycourse = new user_competency_course(0, $record);
        $usercompetencycourse->create();

        return $usercompetencycourse;
    }

    /**
     * Create a new user competency plan.
     *
     * @param array|stdClass $record
     * @return user_competency_plan
     */
    public function create_user_competency_plan($record = null) {
        $record = (object) $record;

        if (!isset($record->userid)) {
            throw new coding_exception('The userid value is required.');
        }
        if (!isset($record->competencyid)) {
            throw new coding_exception('The competencyid value is required.');
        }

        if (!isset($record->planid)) {
            throw new coding_exception('The planid value is required.');
        }

        if (!isset($record->sortorder)) {
            $record->sortorder = 0;
        }

        $usercompetencyplan = new user_competency_plan(0, $record);
        $usercompetencyplan->create();

        return $usercompetencyplan;
    }

    /**
     * Create a new plan competency.
     *
     * @param array|stdClass $record
     * @return plan_competency
     */
    public function create_plan_competency($record = null) {
        $record = (object) $record;

        if (!isset($record->planid)) {
            throw new coding_exception('The planid value is required.');
        }
        if (!isset($record->competencyid)) {
            throw new coding_exception('The competencyid value is required.');
        }

        $plancompetency = new plan_competency(0, $record);
        $plancompetency->create();

        return $plancompetency;
    }

    /**
     * Create a new template cohort.
     *
     * @param array|stdClass $record
     * @return template_cohort
     */
    public function create_template_cohort($record = null) {
        $record = (object) $record;

        if (!isset($record->templateid)) {
            throw new coding_exception('The templateid value is required.');
        }
        if (!isset($record->cohortid)) {
            throw new coding_exception('The cohortid value is required.');
        }

        $tplcohort = new template_cohort(0, $record);
        $tplcohort->create();

        return $tplcohort;
    }

    /**
     * Create a new evidence.
     *
     * @param array|stdClass $record
     * @return evidence
     */
    public function create_evidence($record = null) {
        $record = (object) $record;

        if (!isset($record->usercompetencyid)) {
            throw new coding_exception('The usercompetencyid value is required.');
        }
        if (!isset($record->action) && !isset($record->grade)) {
            $record->action = evidence::ACTION_LOG;
        }
        if (!isset($record->action)) {
            throw new coding_exception('The action value is required with a grade.');
        }

        if (!isset($record->contextid)) {
            $record->contextid = context_system::instance()->id;
        }
        if (!isset($record->descidentifier)) {
            $record->descidentifier = 'invalidevidencedesc';
        }
        if (!isset($record->desccomponent)) {
            $record->desccomponent = 'core_competency';
        }
        $evidence = new evidence(0, $record);
        $evidence->create();

        return $evidence;
    }

    /**
     * Create a new course competency.
     *
     * @param array|stdClass $record
     * @return user_competency
     */
    public function create_course_competency($record = null) {
        $record = (object) $record;

        if (!isset($record->courseid)) {
            throw new coding_exception('The courseid value is required.');
        }
        if (!isset($record->competencyid)) {
            throw new coding_exception('The competencyid value is required.');
        }

        $cc = new course_competency(0, $record);
        $cc->create();

        return $cc;
    }

    /**
     * Create a new course module competency.
     *
     * @param array|stdClass $record
     * @return course_module_competency
     */
    public function create_course_module_competency($record = null) {
        $record = (object) $record;

        if (!isset($record->cmid)) {
            throw new coding_exception('The cmid value is required.');
        }
        if (!isset($record->competencyid)) {
            throw new coding_exception('The competencyid value is required.');
        }

        $cc = new course_module_competency(0, $record);
        $cc->create();

        return $cc;
    }

    /**
     * Create a new user_evidence.
     *
     * @param array|stdClass $record
     * @return evidence
     */
    public function create_user_evidence($record = null) {
        $this->userevidencecount++;
        $i = $this->userevidencecount;
        $record = (object) $record;

        if (!isset($record->userid)) {
            throw new coding_exception('The userid value is required.');
        }
        if (!isset($record->name)) {
            $record->name = "Evidence $i name";
        }
        if (!isset($record->description)) {
            $record->description = "Evidence $i description";
        }
        if (!isset($record->descriptionformat)) {
            $record->descriptionformat = FORMAT_HTML;
        }

        $ue = new user_evidence(0, $record);
        $ue->create();

        return $ue;
    }

    /**
     * Create a new user_evidence_comp.
     *
     * @param array|stdClass $record
     * @return evidence
     */
    public function create_user_evidence_competency($record = null) {
        $record = (object) $record;

        if (!isset($record->userevidenceid)) {
            throw new coding_exception('The userevidenceid value is required.');
        }
        if (!isset($record->competencyid)) {
            throw new coding_exception('The competencyid value is required.');
        }

        $uec = new user_evidence_competency(0, $record);
        $uec->create();

        return $uec;
    }

}

