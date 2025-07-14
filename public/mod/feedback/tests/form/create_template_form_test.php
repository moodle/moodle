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

namespace mod_feedback\form;

/**
 * Tests the confirm use template form
 *
 * @author Peter Dias
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */
final class create_template_form_test extends \advanced_testcase {
    /**
     * Run the basic setup for the test
     */
    public function setup_instance(): array {
        global $DB, $PAGE;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id, $course->id);
        $user = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();

        // Enrol a student and teacher.
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');

        // Setup the site wide manager role.
        $managerrole = $DB->get_record('role', ['shortname' => 'manager']);
        role_assign($managerrole->id, $manager->id, SYSCONTEXTID);

        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');

        // Create at least one page.
        $feedbackgenerator->create_item_multichoice($feedback, ['values' => "y\nn"]);
        $feedbackgenerator->create_item_multichoice($feedback, ['values' => "0\n1"]);
        $feedbackparams = [
            'id' => $cm->id,
        ];
        $PAGE->set_cm($cm);
        $PAGE->set_activity_record($feedback);

        return [$manager, $teacher, $user, $managerrole, $feedbackparams];
    }

    /**
     * Test the create template for when capabilities have been modified
     *
     * @param array $unassignedroles
     * @param bool $accessallowed
     * @param bool $public
     * @param bool $expectedispublicvalue
     * @dataProvider createtemplate_form_with_modified_capabilities_provider
     */
    public function test_createtemplate_form_with_modified_capabilities(array $unassignedroles, bool $accessallowed,
            bool $public = false, bool $expectedispublicvalue = false): void {
        global $DB;
        [$manager, $teacher, $user, $managerrole, $feedback] = $this->setup_instance();
        $this->setAdminUser();
        foreach ($unassignedroles as $role) {
            unassign_capability($role, $managerrole->id);
        }
        $data = [
            'id' => $feedback['id'],
            'templatename' => 'mytemplate',
            'ispublic' => $public
        ];
        $this->setUser($manager);
        $submitdata = create_template_form::mock_ajax_submit($data);
        if (!$accessallowed) {
            $this->expectException(\moodle_exception::class);
        }
        $form = new create_template_form(null, null, 'post', '', null, true,
            $submitdata, true);
        $form->set_data_for_dynamic_submission();
        $this->assertTrue($form->is_validated());
        $form->process_dynamic_submission();
        $records = array_values($DB->get_records('feedback_template', null, 'id ASC'));
        $this->assertEquals($expectedispublicvalue, (bool) $records[0]->ispublic);
    }

    /**
     * Provider for the test_createtemplate_form_with_modified_capabilities
     *
     * @return array
     */
    public static function createtemplate_form_with_modified_capabilities_provider(): array {
        return [
            "Manager without edititems permission cannot create any templates" => [
                ['mod/feedback:edititems'], false
            ],
            "Manager without createprivatetemplate permission creating public template" => [
                ['mod/feedback:createprivatetemplate'], true, true, true
            ],
            "Manager without createprivatetemplate permission creating private template" => [
                ['mod/feedback:createprivatetemplate'], true
            ],
            "Manager without createpublictemplate permission creating private template" => [
                ['mod/feedback:createpublictemplate'], true
            ],
            "Manager without createpublictemplate permission creating public template" => [
                ['mod/feedback:createpublictemplate'], true, true
            ],
            "Manager without createprivatetemplate,createpublictemplate permission cannot create templates" => [
                ['mod/feedback:createpublictemplate', 'mod/feedback:createprivatetemplate'], false
            ]
        ];
    }

    /**
     * Test the form
     *
     * @param string $loginas
     * @param bool $public
     * @param bool $accessallowed
     * @dataProvider createtemplate_form_provider
     */
    public function test_createtemplate_form(string $loginas, bool $public,
            bool $accessallowed = true): void {
        global $DB;
        [$manager, $teacher, $user, $managerrole, $feedback] = $this->setup_instance();
        switch($loginas) {
            case 'admin':
                $this->setAdminUser();
                break;
            case 'student':
                $this->setUser($user);
                break;
            case 'teacher':
                $this->setUser($teacher);
                break;
            case 'manager':
                $this->setUser($manager);
                break;
        }

        $data = [
            'id' => $feedback['id'],
            'templatename' => 'mytemplate',
            'ispublic' => $public
        ];

        $submitdata = create_template_form::mock_ajax_submit($data);
        if (!$accessallowed) {
            $this->expectException(\moodle_exception::class);
        }
        $form = new create_template_form(null, null, 'post', '', null, true,
            $submitdata, true);
        $form->set_data_for_dynamic_submission();
        $this->assertTrue($form->is_validated());
        $form->process_dynamic_submission();

        // A teacher can access the form but cannot create public templates.
        if ($loginas == 'teacher' && $public) {
            $records = array_values($DB->get_records('feedback_template', null, 'id ASC'));
            $this->assertFalse((bool) $records[0]->ispublic);
        }
    }

    /**
     * Provider for the test_createtemplate_form
     *
     * @return array
     */
    public static function createtemplate_form_provider(): array {
        return [
            'Create a private template as an admin' => [
                'admin', false
            ],
            'Create a public template as an admin' => [
                'admin', true
            ],
            'Create a private template as a manager' => [
                'manager', false
            ],
            'Create a public template as a manager' => [
                'manager', true
            ],
            'Create a private template as a teacher' => [
                'teacher', false
            ],
            'Create a public template as a teacher' => [
                'teacher', true
            ],
            'Create a public template as a student' => [
                'student', true, false
            ],
            'Create a private template as a student' => [
                'student', false, false
            ],
        ];
    }
}
