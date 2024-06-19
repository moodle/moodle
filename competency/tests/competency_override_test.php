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

namespace core_competency;

/**
 * Competency ruleoutcome override grade tests
 *
 * @package    core_competency
 * @copyright  2022 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_override_test extends \advanced_testcase {

    /** @var \stdClass course record. */
    protected $course;

    /** @var \stdClass user record. */
    protected $user;

    /** @var \stdClass block instance record. */
    protected $scale;

    /** @var competency_framework loading competency frameworks from the DB. */
    protected $framework;

    /** @var plan loading competency plans from the DB. */
    protected $plan;

    /** @var competency loading competency from the DB. */
    protected $comp1;

    /** @var competency loading competency from the DB. */
    protected $comp2;

    /** @var \stdClass course module. */
    protected $cm;

    /** @var \completion_info completion information. */
    protected $completion;

    /** @var \context_course context course. */
    protected $context;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        // Create user in course.
        $c1 = $dg->create_course((object) ['enablecompletion' => true]);
        $u1 = $dg->create_user();
        $dg->enrol_user($u1->id, $c1->id);

        // Create framework with three values.
        $scale = $dg->create_scale(["scale" => "not,partially,fully"]);
        $scaleconfiguration = json_encode([
            ['scaleid' => $scale->id],
            ['id' => 1, 'scaledefault' => 1, 'proficient' => 1]
        ]);
        $framework = $lpg->create_framework([
            'scaleid' => $scale->id,
            'scaleconfiguration' => $scaleconfiguration
        ]);

        $plan = $lpg->create_plan(['userid' => $u1->id]);

        $comp1 = $lpg->create_competency([
            'competencyframeworkid' => $framework->get('id'),
            'scaleid' => $scale->id,
            'scaleconfiguration' => $scaleconfiguration
        ]);

        $comp2 = $lpg->create_competency([
            'competencyframeworkid' => $framework->get('id'),
            'scaleid' => $scale->id,
            'scaleconfiguration' => $scaleconfiguration
        ]);

        api::add_competency_to_plan($plan->get('id'), $comp1->get('id'));
        api::add_competency_to_plan($plan->get('id'), $comp2->get('id'));

        $lpg->create_course_competency([
            'courseid' => $c1->id,
            'competencyid' => $comp1->get('id'),
            'ruleoutcome' => \core_competency\course_competency::OUTCOME_COMPLETE,
        ]);

        $lpg->create_course_competency([
            'courseid' => $c1->id,
            'competencyid' => $comp2->get('id'),
            'ruleoutcome' => \core_competency\course_competency::OUTCOME_COMPLETE,
        ]);

        $label = $dg->create_module('label', ['course' => $c1, 'completion' => COMPLETION_VIEWED, 'completionview' => 1]);
        $cm = get_coursemodule_from_instance('label', $label->id);
        $completion = new \completion_info($c1);
        $this->assertEquals(COMPLETION_ENABLED, $completion->is_enabled($cm));

        // Link course module with the competency and setup a rule to complete the competency when the module is completed.
        api::add_competency_to_course_module($cm, $comp1->get('id'));
        api::add_competency_to_course_module($cm, $comp2->get('id'));

        $coursemodulecomps = api::list_course_module_competencies_in_course_module($cm);
        $this->assertCount(2, $coursemodulecomps);
        api::set_course_module_competency_ruleoutcome($coursemodulecomps[0], \core_competency\course_competency::OUTCOME_COMPLETE);
        api::set_course_module_competency_ruleoutcome($coursemodulecomps[1], \core_competency\course_competency::OUTCOME_COMPLETE);

        $this->course = $c1;
        $this->user = $u1;
        $this->scale = $scale;
        $this->framework = $framework;
        $this->plan = $plan;
        $this->comp1 = $comp1;
        $this->comp2 = $comp2;
        $this->cm = $cm;
        $this->completion = new \completion_info($c1);
        $this->context = \context_course::instance($this->course->id);
    }

    /**
     * Test ruleoutcome overridegrade is correctly applied when coursemodule completion is processed.
     *
     * @covers \core_competency\api::set_course_module_competency_ruleoutcome
     */
    public function test_ruleoutcome_overridegrade(): void {
        // Initially the competency (and hence all the child competencies) should not be complete for the user.
        [$coursecomp, $plancomp, $usercomp] = $this->get_related_competencies($this->comp1->get('id'));
        $this->assertEquals(0, $plancomp->usercompetency->get('grade'));
        $this->assertEquals(0, $usercomp->get('grade'));
        $this->assertEquals(0, $coursecomp->get('grade'));

        [$coursecomp2, $plancomp2, $usercomp2] = $this->get_related_competencies($this->comp2->get('id'));
        $this->assertEquals(0, $plancomp2->usercompetency->get('grade'));
        $this->assertEquals(0, $usercomp2->get('grade'));
        $this->assertEquals(0, $coursecomp2->get('grade'));

        // Update the course module completion state to complete and trigger a competency update.
        $data = $this->completion->get_data($this->cm, false, $this->user->id);
        $data->completionstate = COMPLETION_COMPLETE;
        $data->timemodified = time();
        $this->completion->internal_set_data($this->cm, $data);

        // Comptency should now be complete for user, plan, and course now that the course module is completed.
        [$coursecomp, $plancomp, $usercomp] = $this->get_related_competencies($this->comp1->get('id'));
        $this->assertEquals(1, $plancomp->usercompetency->get('grade'));
        $this->assertEquals(1, $usercomp->get('grade'));
        $this->assertEquals(1, $coursecomp->get('grade'));

        [$coursecomp2, $plancomp2, $usercomp2] = $this->get_related_competencies($this->comp2->get('id'));
        $this->assertEquals(1, $plancomp2->usercompetency->get('grade'));
        $this->assertEquals(1, $usercomp2->get('grade'));
        $this->assertEquals(1, $coursecomp2->get('grade'));

        // Change the competency completion for the user by adding evidence.
        api::add_evidence($this->user->id, $this->comp1, $this->context,
            evidence::ACTION_OVERRIDE, 'commentincontext', 'core', null, false, null, 2);
        api::add_evidence($this->user->id, $this->comp2, $this->context,
            evidence::ACTION_OVERRIDE, 'commentincontext', 'core', null, false, null, 2);

        // After adding evidence, the competencies should now reflect the new grade value.
        [$coursecomp, $plancomp, $usercomp] = $this->get_related_competencies($this->comp1->get('id'));
        $this->assertEquals(2, $plancomp->usercompetency->get('grade'));
        $this->assertEquals(2, $usercomp->get('grade'));
        $this->assertEquals(2, $coursecomp->get('grade'));

        [$coursecomp2, $plancomp2, $usercomp2] = $this->get_related_competencies($this->comp2->get('id'));
        $this->assertEquals(2, $plancomp2->usercompetency->get('grade'));
        $this->assertEquals(2, $usercomp2->get('grade'));
        $this->assertEquals(2, $coursecomp2->get('grade'));

        // Update the course module competency to incomplete. This will not change the competency status.
        $data = $this->completion->get_data($this->cm, false, $this->user->id);
        $data->completionstate = COMPLETION_INCOMPLETE;
        $data->timemodified = time();
        $this->completion->internal_set_data($this->cm, $data);

        [$coursecomp, $plancomp, $usercomp] = $this->get_related_competencies($this->comp1->get('id'));
        $this->assertEquals(2, $plancomp->usercompetency->get('grade'));
        $this->assertEquals(2, $usercomp->get('grade'));
        $this->assertEquals(2, $coursecomp->get('grade'));

        [$coursecomp2, $plancomp2, $usercomp2] = $this->get_related_competencies($this->comp2->get('id'));
        $this->assertEquals(2, $plancomp2->usercompetency->get('grade'));
        $this->assertEquals(2, $usercomp2->get('grade'));
        $this->assertEquals(2, $coursecomp2->get('grade'));

        // Re-complete the course module, so that it attempts to re-complete the competencies.
        $data = $this->completion->get_data($this->cm, false, $this->user->id);
        $data->completionstate = COMPLETION_COMPLETE;
        $data->timemodified = time();
        $this->completion->internal_set_data($this->cm, $data);

        // By default, this will not override the existing grade, so it should remain the same as before.
        [$coursecomp, $plancomp, $usercomp] = $this->get_related_competencies($this->comp1->get('id'));
        $this->assertEquals(2, $plancomp->usercompetency->get('grade'));
        $this->assertEquals(2, $usercomp->get('grade'));
        $this->assertEquals(2, $coursecomp->get('grade'));

        [$coursecomp2, $plancomp2, $usercomp2] = $this->get_related_competencies($this->comp2->get('id'));
        $this->assertEquals(2, $plancomp2->usercompetency->get('grade'));
        $this->assertEquals(2, $usercomp2->get('grade'));
        $this->assertEquals(2, $coursecomp2->get('grade'));

        // Update the completion rule for only competency 1 to $overridegrade = true.
        $coursemodulecomps = api::list_course_module_competencies_in_course_module($this->cm);
        api::set_course_module_competency_ruleoutcome($coursemodulecomps[0], \core_competency\course_competency::OUTCOME_COMPLETE,
            true);

        // Mark as incomplete then re-complete the course module.
        $data = $this->completion->get_data($this->cm, false, $this->user->id);
        $data->completionstate = COMPLETION_INCOMPLETE;
        $data->timemodified = time();
        $this->completion->internal_set_data($this->cm, $data);

        $data = $this->completion->get_data($this->cm, false, $this->user->id);
        $data->completionstate = COMPLETION_COMPLETE;
        $data->timemodified = time();
        $this->completion->internal_set_data($this->cm, $data);

        // Because the rule is now set to override existing grades, the grade should have now updated as per the ruleoutcome.
        // However the second competency didn't have this rule set, so it will not be overriden.
        [$coursecomp, $plancomp, $usercomp] = $this->get_related_competencies($this->comp1->get('id'));
        $this->assertEquals(1, $plancomp->usercompetency->get('grade'));
        $this->assertEquals(1, $usercomp->get('grade'));
        $this->assertEquals(1, $coursecomp->get('grade'));

        [$coursecomp2, $plancomp2, $usercomp2] = $this->get_related_competencies($this->comp2->get('id'));
        $this->assertEquals(2, $plancomp2->usercompetency->get('grade'));
        $this->assertEquals(2, $usercomp2->get('grade'));
        $this->assertEquals(2, $coursecomp2->get('grade'));

        // If competency 2 is changed now to override and re-completed, it will update the same as competency 1.
        api::set_course_module_competency_ruleoutcome($coursemodulecomps[1], \core_competency\course_competency::OUTCOME_COMPLETE,
            true);

        $data = $this->completion->get_data($this->cm, false, $this->user->id);
        $data->completionstate = COMPLETION_INCOMPLETE;
        $data->timemodified = time();
        $this->completion->internal_set_data($this->cm, $data);

        $data = $this->completion->get_data($this->cm, false, $this->user->id);
        $data->completionstate = COMPLETION_COMPLETE;
        $data->timemodified = time();
        $this->completion->internal_set_data($this->cm, $data);

        // Now both the competencies have $overridegrade = true,
        // they should both reflect the ruleoutcome after the completion above was processed.
        [$coursecomp, $plancomp, $usercomp] = $this->get_related_competencies($this->comp1->get('id'));
        $this->assertEquals(1, $plancomp->usercompetency->get('grade'));
        $this->assertEquals(1, $usercomp->get('grade'));
        $this->assertEquals(1, $coursecomp->get('grade'));

        [$coursecomp2, $plancomp2, $usercomp2] = $this->get_related_competencies($this->comp2->get('id'));
        $this->assertEquals(1, $plancomp2->usercompetency->get('grade'));
        $this->assertEquals(1, $usercomp2->get('grade'));
        $this->assertEquals(1, $coursecomp2->get('grade'));
    }

    /**
     * Test competency backup and restore correctly restores the ruleoutcome overridegrade value.
     *
     * @covers \core_competency\api::set_course_module_competency_ruleoutcome
     */
    public function test_override_backup_restore(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/externallib.php');

        // Set one to override grade and another to not override grade.
        $coursemodulecomps = api::list_course_module_competencies_in_course_module($this->cm);
        api::set_course_module_competency_ruleoutcome($coursemodulecomps[0], \core_competency\course_competency::OUTCOME_COMPLETE,
            false);
        api::set_course_module_competency_ruleoutcome($coursemodulecomps[1], \core_competency\course_competency::OUTCOME_COMPLETE,
            true);

        // Duplicate the course (backup and restore).
        $duplicated = \core_course_external::duplicate_course($this->course->id, 'test', 'test', $this->course->category);

        // Get the new course modules.
        $newcoursemodules = get_coursemodules_in_course('label', $duplicated['id']);
        $this->assertCount(1, $newcoursemodules);
        $cm = array_pop($newcoursemodules);

        // Get the comeptencies for this cm.
        $newcoursemodulecomps = api::list_course_module_competencies_in_course_module($cm);
        $this->assertCount(2, $newcoursemodulecomps);

        // Ensure the override grade settings are restored properly.
        $this->assertEquals($coursemodulecomps[0]->get('overridegrade'), $newcoursemodulecomps[0]->get('overridegrade'));
        $this->assertEquals($coursemodulecomps[1]->get('overridegrade'), $newcoursemodulecomps[1]->get('overridegrade'));
    }

    /**
     * Gets the course, user and plan competency for the given competency ID
     *
     * @param int $compid ID of the competency.
     * @return array array containing the three related competencies
     */
    private function get_related_competencies(int $compid): array {
        $coursecomp = api::get_user_competency_in_course($this->course->id, $this->user->id, $compid);
        $usercomp = api::get_user_competency($this->user->id, $compid);
        $plancomp = api::get_plan_competency($this->plan, $compid);
        return [$coursecomp, $plancomp, $usercomp];
    }
}
