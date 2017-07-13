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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Unit tests for the grading API defined in core_grading_external class.
 *
 * @package core_grading
 * @category external
 * @copyright 2013 Paul Charsley
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_grading_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Test get_definitions
     */
    public function test_get_definitions() {
        global $DB, $CFG, $USER;

        $this->resetAfterTest(true);
        // Create a course and assignment.
        $coursedata['idnumber'] = 'idnumbercourse';
        $coursedata['fullname'] = 'Lightwork Course';
        $coursedata['summary'] = 'Lightwork Course description';
        $coursedata['summaryformat'] = FORMAT_MOODLE;
        $course = self::getDataGenerator()->create_course($coursedata);

        $assigndata['course'] = $course->id;
        $assigndata['name'] = 'lightwork assignment';

        $cm = self::getDataGenerator()->create_module('assign', $assigndata);

        // Create manual enrolment record.
        $manualenroldata['enrol'] = 'manual';
        $manualenroldata['status'] = 0;
        $manualenroldata['courseid'] = $course->id;
        $enrolid = $DB->insert_record('enrol', $manualenroldata);

        // Create a teacher and give them capabilities.
        $coursecontext = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:viewparticipants', $coursecontext->id, 3);
        $modulecontext = context_module::instance($cm->cmid);
        $this->assignUserCapability('mod/assign:grade', $modulecontext->id, $roleid);

        // Create the teacher's enrolment record.
        $userenrolmentdata['status'] = 0;
        $userenrolmentdata['enrolid'] = $enrolid;
        $userenrolmentdata['userid'] = $USER->id;
        $DB->insert_record('user_enrolments', $userenrolmentdata);

        // Create a grading area.
        $gradingarea = array(
            'contextid' => $modulecontext->id,
            'component' => 'mod_assign',
            'areaname' => 'submissions',
            'activemethod' => 'rubric'
        );
        $areaid = $DB->insert_record('grading_areas', $gradingarea);

        // Create a rubric grading definition.
        $rubricdefinition = array (
            'areaid' => $areaid,
            'method' => 'rubric',
            'name' => 'test',
            'status' => 20,
            'copiedfromid' => 1,
            'timecreated' => 1,
            'usercreated' => $USER->id,
            'timemodified' => 1,
            'usermodified' => $USER->id,
            'timecopied' => 0
        );
        $definitionid = $DB->insert_record('grading_definitions', $rubricdefinition);

        // Create a criterion with levels.
        $rubriccriteria1 = array (
            'definitionid' => $definitionid,
            'sortorder' => 1,
            'description' => 'Demonstrate an understanding of disease control',
            'descriptionformat' => 0
        );
        $criterionid1 = $DB->insert_record('gradingform_rubric_criteria', $rubriccriteria1);
        $rubriclevel1 = array (
            'criterionid' => $criterionid1,
            'score' => 5,
            'definition' => 'pass',
            'definitionformat' => 0
        );
        $DB->insert_record('gradingform_rubric_levels', $rubriclevel1);
        $rubriclevel2 = array (
            'criterionid' => $criterionid1,
            'score' => 10,
            'definition' => 'excellent',
            'definitionformat' => 0
        );
        $DB->insert_record('gradingform_rubric_levels', $rubriclevel2);

        // Create a second criterion with levels.
        $rubriccriteria2 = array (
            'definitionid' => $definitionid,
            'sortorder' => 2,
            'description' => 'Demonstrate an understanding of brucellosis',
            'descriptionformat' => 0
        );
        $criterionid2 = $DB->insert_record('gradingform_rubric_criteria', $rubriccriteria2);
        $rubriclevel1 = array (
            'criterionid' => $criterionid2,
            'score' => 5,
            'definition' => 'pass',
            'definitionformat' => 0
        );
        $DB->insert_record('gradingform_rubric_levels', $rubriclevel1);
        $rubriclevel2 = array (
            'criterionid' => $criterionid2,
            'score' => 10,
            'definition' => 'excellent',
            'definitionformat' => 0
        );
        $DB->insert_record('gradingform_rubric_levels', $rubriclevel2);

        // Call the external function.
        $cmids = array ($cm->cmid);
        $areaname = 'submissions';
        $result = core_grading_external::get_definitions($cmids, $areaname);
        $result = external_api::clean_returnvalue(core_grading_external::get_definitions_returns(), $result);

        $this->assertEquals(1, count($result['areas']));
        $this->assertEquals(1, count($result['areas'][0]['definitions']));
        $definition = $result['areas'][0]['definitions'][0];

        $this->assertEquals($rubricdefinition['method'], $definition['method']);
        $this->assertEquals($USER->id, $definition['usercreated']);

        require_once("$CFG->dirroot/grade/grading/lib.php");
        require_once($CFG->dirroot.'/grade/grading/form/'.$rubricdefinition['method'].'/lib.php');

        $gradingmanager = get_grading_manager($areaid);

        $this->assertEquals(1, count($definition[$rubricdefinition['method']]));

        $rubricdetails = $definition[$rubricdefinition['method']];
        $details = call_user_func('gradingform_'.$rubricdefinition['method'].'_controller::get_external_definition_details');

        $this->assertEquals(2, count($rubricdetails[key($details)]));

        $found = false;
        foreach ($rubricdetails[key($details)] as $criterion) {
            if ($criterion['id'] == $criterionid1) {
                $this->assertEquals($rubriccriteria1['description'], $criterion['description']);
                $this->assertEquals(2, count($criterion['levels']));
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * Test get_gradingform_instances
     */
    public function test_get_gradingform_instances() {
        global $DB, $USER;

        $this->resetAfterTest(true);
        // Create a course and assignment.
        $coursedata['idnumber'] = 'idnumbercourse';
        $coursedata['fullname'] = 'Lightwork Course';
        $coursedata['summary'] = 'Lightwork Course description';
        $coursedata['summaryformat'] = FORMAT_MOODLE;
        $course = self::getDataGenerator()->create_course($coursedata);

        $assigndata['course'] = $course->id;
        $assigndata['name'] = 'lightwork assignment';

        $assign = self::getDataGenerator()->create_module('assign', $assigndata);

        // Create manual enrolment record.
        $manualenroldata['enrol'] = 'manual';
        $manualenroldata['status'] = 0;
        $manualenroldata['courseid'] = $course->id;
        $enrolid = $DB->insert_record('enrol', $manualenroldata);

        // Create a teacher and give them capabilities.
        $coursecontext = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:viewparticipants', $coursecontext->id, 3);
        $modulecontext = context_module::instance($assign->cmid);
        $this->assignUserCapability('mod/assign:grade', $modulecontext->id, $roleid);

        // Create the teacher's enrolment record.
        $userenrolmentdata['status'] = 0;
        $userenrolmentdata['enrolid'] = $enrolid;
        $userenrolmentdata['userid'] = $USER->id;
        $DB->insert_record('user_enrolments', $userenrolmentdata);

        // Create a student with an assignment grade.
        $student = self::getDataGenerator()->create_user();
        $assigngrade = new stdClass();
        $assigngrade->assignment = $assign->id;
        $assigngrade->userid = $student->id;
        $assigngrade->timecreated = time();
        $assigngrade->timemodified = $assigngrade->timecreated;
        $assigngrade->grader = $USER->id;
        $assigngrade->grade = 50;
        $assigngrade->attemptnumber = 0;
        $gid = $DB->insert_record('assign_grades', $assigngrade);

        // Create a grading area.
        $gradingarea = array(
            'contextid' => $modulecontext->id,
            'component' => 'mod_assign',
            'areaname' => 'submissions',
            'activemethod' => 'rubric'
        );
        $areaid = $DB->insert_record('grading_areas', $gradingarea);

        // Create a rubric grading definition.
        $rubricdefinition = array (
            'areaid' => $areaid,
            'method' => 'rubric',
            'name' => 'test',
            'status' => 20,
            'copiedfromid' => 1,
            'timecreated' => 1,
            'usercreated' => $USER->id,
            'timemodified' => 1,
            'usermodified' => $USER->id,
            'timecopied' => 0
        );
        $definitionid = $DB->insert_record('grading_definitions', $rubricdefinition);

        // Create a criterion with a level.
        $rubriccriteria = array (
            'definitionid' => $definitionid,
            'sortorder' => 1,
            'description' => 'Demonstrate an understanding of disease control',
            'descriptionformat' => 0
        );
        $criterionid = $DB->insert_record('gradingform_rubric_criteria', $rubriccriteria);
        $rubriclevel = array (
            'criterionid' => $criterionid,
            'score' => 50,
            'definition' => 'pass',
            'definitionformat' => 0
        );
        $levelid = $DB->insert_record('gradingform_rubric_levels', $rubriclevel);

        // Create a grading instance.
        $instance = array (
            'definitionid' => $definitionid,
            'raterid' => $USER->id,
            'itemid' => $gid,
            'status' => 1,
            'feedbackformat' => 0,
            'timemodified' => 1
        );
        $instanceid = $DB->insert_record('grading_instances', $instance);

        // Create a filling.
        $filling = array (
            'instanceid' => $instanceid,
            'criterionid' => $criterionid,
            'levelid' => $levelid,
            'remark' => 'excellent work',
            'remarkformat' => 0
        );
        $DB->insert_record('gradingform_rubric_fillings', $filling);

        // Call the external function.
        $result = core_grading_external::get_gradingform_instances($definitionid, 0);
        $result = external_api::clean_returnvalue(core_grading_external::get_gradingform_instances_returns(), $result);

        $this->assertEquals(1, count($result['instances']));
        $this->assertEquals($USER->id, $result['instances'][0]['raterid']);
        $this->assertEquals($gid, $result['instances'][0]['itemid']);
        $this->assertEquals(1, $result['instances'][0]['status']);
        $this->assertEquals(1, $result['instances'][0]['timemodified']);
        $this->assertEquals(1, count($result['instances'][0]['rubric']));
        $this->assertEquals(1, count($result['instances'][0]['rubric']['criteria']));
        $criteria = $result['instances'][0]['rubric']['criteria'];
        $this->assertEquals($criterionid, $criteria[0]['criterionid']);
        $this->assertEquals($levelid, $criteria[0]['levelid']);
        $this->assertEquals('excellent work', $criteria[0]['remark']);
    }

    /**
     *
     * Test save_definitions for rubric grading method
     */
    public function test_save_definitions_rubric() {
        global $DB, $CFG, $USER;

        $this->resetAfterTest(true);
        // Create a course and assignment.
        $course = self::getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $course->id;
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        $coursecontext = context_course::instance($course->id);

        // Create the teacher.
        $teacher = self::getDataGenerator()->create_user();
        $USER->id = $teacher->id;
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->assignUserCapability('moodle/grade:managegradingforms', $context->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($teacher->id,
                                              $course->id,
                                              $teacherrole->id);

        // The grading area to insert.
        $gradingarea = array(
            'cmid' => $cm->id,
            'contextid' => $context->id,
            'component' => 'mod_assign',
            'areaname'  => 'submissions',
            'activemethod' => 'rubric'
        );

        // The rubric definition to insert.
        $rubricdefinition = array(
            'method' => 'rubric',
            'name' => 'test',
            'description' => '',
            'status' => 20,
            'copiedfromid' => 1,
            'timecreated' => 1,
            'usercreated' => $teacher->id,
            'timemodified' => 1,
            'usermodified' => $teacher->id,
            'timecopied' => 0
        );

        // The criterion to insert.
        $rubriccriteria1 = array (
             'sortorder' => 1,
             'description' => 'Demonstrate an understanding of disease control',
             'descriptionformat' => 0
        );

        // 3 levels for the criterion.
        $rubriclevel1 = array (
            'score' => 50,
            'definition' => 'pass',
            'definitionformat' => 0
        );
        $rubriclevel2 = array (
            'score' => 100,
            'definition' => 'excellent',
            'definitionformat' => 0
        );
        $rubriclevel3 = array (
            'score' => 0,
            'definition' => 'fail',
            'definitionformat' => 0
        );

        $rubriccriteria1['levels'] = array($rubriclevel1, $rubriclevel2, $rubriclevel3);
        $rubricdefinition['rubric'] = array('rubric_criteria' => array($rubriccriteria1));
        $gradingarea['definitions'] = array($rubricdefinition);

        $results = core_grading_external::save_definitions(array($gradingarea));

        $area = $DB->get_record('grading_areas',
                                array('contextid' => $context->id, 'component' => 'mod_assign', 'areaname' => 'submissions'),
                                '*', MUST_EXIST);
        $this->assertEquals($area->activemethod, 'rubric');

        $definition = $DB->get_record('grading_definitions', array('areaid' => $area->id, 'method' => 'rubric'), '*', MUST_EXIST);
        $this->assertEquals($rubricdefinition['name'], $definition->name);

        $criterion1 = $DB->get_record('gradingform_rubric_criteria', array('definitionid' => $definition->id), '*', MUST_EXIST);
        $levels = $DB->get_records('gradingform_rubric_levels', array('criterionid' => $criterion1->id));
        $validlevelcount = 0;
        $expectedvalue = true;
        foreach ($levels as $level) {
            if ($level->score == 0) {
                $this->assertEquals('fail', $level->definition);
                $validlevelcount++;
            } else if ($level->score == 50) {
                $this->assertEquals('pass', $level->definition);
                $validlevelcount++;
            } else if ($level->score == 100) {
                $this->assertEquals('excellent', $level->definition);
                $excellentlevelid = $level->id;
                $validlevelcount++;
            } else {
                $expectedvalue = false;
            }
        }
        $this->assertEquals(3, $validlevelcount);
        $this->assertTrue($expectedvalue, 'A level with an unexpected score was found');

        // Test add a new level and modify an existing.
        // Test add a new criteria and modify an existing.
        // Test modify a definition.

        // The rubric definition to update.
        $rubricdefinition = array(
            'id' => $definition->id,
            'method' => 'rubric',
            'name' => 'test changed',
            'description' => '',
            'status' => 20,
            'copiedfromid' => 1,
            'timecreated' => 1,
            'usercreated' => $teacher->id,
            'timemodified' => 1,
            'usermodified' => $teacher->id,
            'timecopied' => 0
        );

        // A criterion to update.
        $rubriccriteria1 = array (
             'id' => $criterion1->id,
             'sortorder' => 1,
             'description' => 'Demonstrate an understanding of rabies control',
             'descriptionformat' => 0
        );

        // A new criterion to add.
        $rubriccriteria2 = array (
             'sortorder' => 2,
             'description' => 'Demonstrate an understanding of anthrax control',
             'descriptionformat' => 0
        );

        // A level to update.
        $rubriclevel2 = array (
            'id' => $excellentlevelid,
            'score' => 75,
            'definition' => 'excellent',
            'definitionformat' => 0
        );

        // A level to insert.
        $rubriclevel4 = array (
            'score' => 100,
            'definition' => 'superb',
            'definitionformat' => 0
        );

        $rubriccriteria1['levels'] = array($rubriclevel1, $rubriclevel2, $rubriclevel3, $rubriclevel4);
        $rubricdefinition['rubric'] = array('rubric_criteria' => array($rubriccriteria1, $rubriccriteria2));
        $gradingarea['definitions'] = array($rubricdefinition);

        $results = core_grading_external::save_definitions(array($gradingarea));

        // Test definition name change.
        $definition = $DB->get_record('grading_definitions', array('id' => $definition->id), '*', MUST_EXIST);
        $this->assertEquals('test changed', $definition->name);

        // Test criteria description change.
        $modifiedcriteria = $DB->get_record('gradingform_rubric_criteria', array('id' => $criterion1->id), '*', MUST_EXIST);
        $this->assertEquals('Demonstrate an understanding of rabies control', $modifiedcriteria->description);

        // Test new criteria added.
        $newcriteria = $DB->get_record('gradingform_rubric_criteria',
                                       array('definitionid' => $definition->id, 'sortorder' => 2), '*', MUST_EXIST);
        $this->assertEquals('Demonstrate an understanding of anthrax control', $newcriteria->description);

        // Test excellent level score change from 100 to 75.
        $modifiedlevel = $DB->get_record('gradingform_rubric_levels', array('id' => $excellentlevelid), '*', MUST_EXIST);
        $this->assertEquals(75, $modifiedlevel->score);

        // Test new superb level added.
        $newlevel = $DB->get_record('gradingform_rubric_levels',
                                       array('criterionid' => $criterion1->id, 'score' => 100), '*', MUST_EXIST);
        $this->assertEquals('superb', $newlevel->definition);

        // Test remove a level
        // Test remove a criterion
        // The rubric definition with the removed criterion and levels.
        $rubricdefinition = array(
            'id' => $definition->id,
            'method' => 'rubric',
            'name' => 'test changed',
            'description' => '',
            'status' => 20,
            'copiedfromid' => 1,
            'timecreated' => 1,
            'usercreated' => $teacher->id,
            'timemodified' => 1,
            'usermodified' => $teacher->id,
            'timecopied' => 0
        );

        $rubriccriteria1 = array (
             'id' => $criterion1->id,
             'sortorder' => 1,
             'description' => 'Demonstrate an understanding of rabies control',
             'descriptionformat' => 0
        );

        $rubriclevel1 = array (
            'score' => 0,
            'definition' => 'fail',
            'definitionformat' => 0
        );
        $rubriclevel2 = array (
            'score' => 100,
            'definition' => 'pass',
            'definitionformat' => 0
        );

        $rubriccriteria1['levels'] = array($rubriclevel1, $rubriclevel2);
        $rubricdefinition['rubric'] = array('rubric_criteria' => array($rubriccriteria1));
        $gradingarea['definitions'] = array($rubricdefinition);

        $results = core_grading_external::save_definitions(array($gradingarea));

        // Only 1 criterion should now exist.
        $this->assertEquals(1, $DB->count_records('gradingform_rubric_criteria', array('definitionid' => $definition->id)));
        $criterion1 = $DB->get_record('gradingform_rubric_criteria', array('definitionid' => $definition->id), '*', MUST_EXIST);
        $this->assertEquals('Demonstrate an understanding of rabies control', $criterion1->description);
        // This criterion should only have 2 levels.
        $this->assertEquals(2, $DB->count_records('gradingform_rubric_levels', array('criterionid' => $criterion1->id)));

        $gradingarea['activemethod'] = 'invalid';
        $this->expectException('moodle_exception');
        $results = core_grading_external::save_definitions(array($gradingarea));
    }

    /**
     *
     * Tests save_definitions for the marking guide grading method
     */
    public function test_save_definitions_marking_guide() {
        global $DB, $CFG, $USER;

        $this->resetAfterTest(true);
        // Create a course and assignment.
        $course = self::getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $course->id;
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        $coursecontext = context_course::instance($course->id);

        // Create the teacher.
        $teacher = self::getDataGenerator()->create_user();
        $USER->id = $teacher->id;
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->assignUserCapability('moodle/grade:managegradingforms', $context->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($teacher->id,
                                              $course->id,
                                              $teacherrole->id);

        // Test insert a grading area with guide definition, criteria and comments.
        $gradingarea = array(
            'cmid' => $cm->id,
            'contextid' => $context->id,
            'component' => 'mod_assign',
            'areaname'  => 'submissions',
            'activemethod' => 'guide'
        );

        $guidedefinition = array(
            'method' => 'guide',
            'name' => 'test',
            'description' => '',
            'status' => 20,
            'copiedfromid' => 1,
            'timecreated' => 1,
            'usercreated' => $teacher->id,
            'timemodified' => 1,
            'usermodified' => $teacher->id,
            'timecopied' => 0
        );

        $guidecomment = array(
             'sortorder' => 1,
             'description' => 'Students need to show that they understand the control of zoonoses',
             'descriptionformat' => 0
        );
        $guidecriteria1 = array (
             'sortorder' => 1,
             'shortname' => 'Rabies Control',
             'description' => 'Understand rabies control techniques',
             'descriptionformat' => 0,
             'descriptionmarkers' => 'Student must demonstrate that they understand rabies control',
             'descriptionmarkersformat' => 0,
             'maxscore' => 50
        );
        $guidecriteria2 = array (
             'sortorder' => 2,
             'shortname' => 'Anthrax Control',
             'description' => 'Understand anthrax control',
             'descriptionformat' => 0,
             'descriptionmarkers' => 'Student must demonstrate that they understand anthrax control',
             'descriptionmarkersformat' => 0,
             'maxscore' => 50
        );

        $guidedefinition['guide'] = array('guide_criteria' => array($guidecriteria1, $guidecriteria2),
                                          'guide_comments' => array($guidecomment));
        $gradingarea['definitions'] = array($guidedefinition);

        $results = core_grading_external::save_definitions(array($gradingarea));
        $area = $DB->get_record('grading_areas',
                                array('contextid' => $context->id, 'component' => 'mod_assign', 'areaname' => 'submissions'),
                                '*', MUST_EXIST);
        $this->assertEquals($area->activemethod, 'guide');

        $definition = $DB->get_record('grading_definitions', array('areaid' => $area->id, 'method' => 'guide'), '*', MUST_EXIST);
        $this->assertEquals($guidedefinition['name'], $definition->name);
        $this->assertEquals(2, $DB->count_records('gradingform_guide_criteria', array('definitionid' => $definition->id)));
        $this->assertEquals(1, $DB->count_records('gradingform_guide_comments', array('definitionid' => $definition->id)));

        // Test removal of a criteria.
        $guidedefinition['guide'] = array('guide_criteria' => array($guidecriteria1),
                                          'guide_comments' => array($guidecomment));
        $gradingarea['definitions'] = array($guidedefinition);
        $results = core_grading_external::save_definitions(array($gradingarea));
        $this->assertEquals(1, $DB->count_records('gradingform_guide_criteria', array('definitionid' => $definition->id)));

        // Test an invalid method in the definition.
        $guidedefinition['method'] = 'invalid';
        $gradingarea['definitions'] = array($guidedefinition);
        $this->expectException('invalid_parameter_exception');
        $results = core_grading_external::save_definitions(array($gradingarea));
    }
}
