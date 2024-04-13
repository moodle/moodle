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
class use_template_form_test extends \advanced_testcase {
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
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');

        // Create at least one page.
        $feedbackgenerator->create_item_multichoice($feedback, ['values' => "y\nn"]);

        feedback_save_as_template($feedback, 'my template', 0);
        $feedbackgenerator->create_item_multichoice($feedback, ['values' => "0\n1"]);
        feedback_save_as_template($feedback, 'mytemplate2', 1);
        $records = array_keys($DB->get_records('feedback_template', null, 'id ASC'));
        $feedbackparams = [
            'id' => $cm->id,
            'privatetemplate' => $records[0],
            'publictemplate' => $records[1],
        ];
        $PAGE->set_cm($cm);
        $PAGE->set_activity_record($feedback);

        return [$user, $feedbackparams];
    }

    /**
     * Test the form
     *
     * @param string $loginas Which user to log in as
     * @param bool $private Whether we are creating a private template
     * @param bool $expected Whether or not the form should be validated
     * @dataProvider usetemplate_form_provider
     */
    public function test_usetemplate_form(string $loginas, bool $private, bool $expected): void {
        [$user, $feedback] = $this->setup_instance();
        switch($loginas) {
            case 'admin':
                $this->setAdminUser();
                break;
            case 'student':
                $this->setUser($user);
                break;
        }

        $data = [
            'id' => $feedback['id'],
            'templateid' => $private ? $feedback['privatetemplate'] : $feedback['publictemplate'],
        ];

        $submitdata = use_template_form::mock_ajax_submit($data);
        if (!$expected) {
            $this->expectException(\moodle_exception::class);
        }
        $form = new use_template_form(null, null, 'post', '', null, true,
            $submitdata, true);
        $form->set_data_for_dynamic_submission();
        if ($expected) {
            $this->assertTrue($form->is_validated());
        }
        $form->process_dynamic_submission();
    }

    /**
     * Provider for the test_usetemplate_form test
     *
     * @return array
     */
    public static function usetemplate_form_provider(): array {
        return [
            'Test submission with a private template as an admin' => [
                'admin', true, true
            ],
            'Test submission with a public template as an admin' => [
                'admin', false, true
            ],
            'Test submission with a public template as a student' => [
                'student', false, false
            ],
            'Test submission with a private template as a student' => [
                'student', true, false
            ],
        ];
    }
}
