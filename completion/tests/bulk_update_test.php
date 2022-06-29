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

namespace core_completion;

use core_completion_bulkedit_form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/completionlib.php');

/**
 * External completion functions unit tests
 *
 * @package    core_completion
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bulk_update_test extends \advanced_testcase {

    /**
     * Provider for test_bulk_form_submit_single
     * @return array
     */
    public function bulk_form_submit_single_provider() {
        return [
            'assign-1' => ['assign', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionsubmit' => 1]],
            'assign-2' => ['assign', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'book-1' => ['book', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]],
            'book-2' => ['book', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'chat-1' => ['chat', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]],
            'chat-2' => ['chat', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'choice-1' => ['choice', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionsubmit' => 1]],
            'choice-2' => ['choice', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'data-1' => ['data', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]],
            'data-2' => ['data', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'data-3' => ['data',
                ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1, 'completionentries' => 3,
                    'completionentriesenabled' => 1],
                ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1, 'completionentries' => 3]],
            'feedback-1' => ['feedback', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 0,
                'completionsubmit' => 1]],
            'feedback-2' => ['feedback', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'folder-1' => ['folder', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]],
            'folder-2' => ['folder', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'forum-1' => ['forum',
                ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completiondiscussions' => 1,
                    'completiondiscussionsenabled' => 1],
                ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completiondiscussions' => 1]],
            'forum-2' => ['forum', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'glossary-1' => ['glossary',
                ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1, 'completionentries' => 3,
                    'completionentriesenabled' => 1],
                ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1, 'completionentries' => 3]],
            'glossary-2' => ['glossary', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'imscp-1' => ['imscp', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]],
            'imscp-2' => ['imscp', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'label-1' => ['label', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'lesson-1' => ['lesson', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionendreached' => 1]],
            'lesson-2' => ['lesson', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'lti-1' => ['lti', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]],
            'lti-2' => ['lti', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'page-1' => ['page', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]],
            'page-2' => ['page', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'quiz-1' => ['quiz', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionpassgrade' => 1]],
            'quiz-2' => ['quiz', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'resource-1' => ['resource', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]],
            'resource-2' => ['resource', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'scorm-1' => ['scorm',
                ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionscorerequired' => 1,
                    'completionstatusrequired' => [2 => 'passed']],
                ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionscorerequired' => 1,
                    'completionstatusrequired' => 2]],
            'scorm-2' => ['scorm', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'survey-1' => ['survey', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionsubmit' => 1]],
            'survey-2' => ['survey', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'url-1' => ['url', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]],
            'url-2' => ['url', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'wiki-1' => ['wiki', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]],
            'wiki-2' => ['wiki', ['completion' => COMPLETION_TRACKING_MANUAL]],
            'workshop-1' => ['workshop', ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]],
            'workshop-2' => ['workshop', ['completion' => COMPLETION_TRACKING_MANUAL]],
        ];
    }

    /**
     * Creates an instance of bulk edit completion form for one activity, validates and saves it
     *
     * @dataProvider bulk_form_submit_single_provider
     * @param string $modname
     * @param array $submitdata data to use in mock form submit
     * @param array|null $validatedata data to validate the
     */
    public function test_bulk_form_submit_single($modname, $submitdata, $validatedata = null) {
        global $DB;

        if ($validatedata === null) {
            $validatedata = $submitdata;
        }

        $this->resetAfterTest();
        $this->setAdminUser();
        list($course, $cms) = $this->create_course_and_modules([$modname]);

        // Submit the bulk completion form with the provided data and make sure it returns the same data.
        core_completion_bulkedit_form::mock_submit(['id' => $course->id, 'cmid' => array_keys($cms)] + $submitdata, []);
        $form = new core_completion_bulkedit_form(null, ['cms' => $cms]);
        $this->assertTrue($form->is_validated());
        $data = $form->get_data();
        foreach ($validatedata as $key => $value) {
            $this->assertEquals($value, $data->$key);
        }

        // Apply completion rules to the modules.
        $manager = new manager($course->id);
        $manager->apply_completion($data, $form->has_custom_completion_rules());

        // Make sure either course_modules or instance table was respectfully updated.
        $cm = reset($cms);
        $cmrec = $DB->get_record('course_modules', ['id' => $cm->id]);
        $instancerec = $DB->get_record($modname, ['id' => $cm->instance]);
        foreach ($validatedata as $key => $value) {
            if (property_exists($cmrec, $key)) {
                $this->assertEquals($value, $cmrec->$key);
            } else {
                $this->assertEquals($value, $instancerec->$key);
            }
        }
    }

    /**
     * Creates a course and the number of modules
     * @param array $modulenames
     * @return array array of two elements - course and list of cm_info objects
     */
    protected function create_course_and_modules($modulenames) {
        global $CFG, $PAGE;

        $CFG->enablecompletion = true;
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1], ['createsections' => true]);
        $PAGE->set_course($course);

        $cmids = [];
        foreach ($modulenames as $modname) {
            $module = $this->getDataGenerator()->create_module($modname, ['course' => $course->id]);
            $cmids[] = $module->cmid;
        }
        $modinfo = get_fast_modinfo($course);
        $cms = [];
        foreach ($cmids as $cmid) {
            $cms[$cmid] = $modinfo->get_cm($cmid);
        }
        return [$course, $cms];
    }

    /**
     * Provider for test_bulk_form_submit_multiple
     * @return array
     */
    public function bulk_form_submit_multiple_provider() {
        return [
            'Several modules with the same module type (choice)' => [
                [
                    'modulenames' => ['choice', 'choice', 'choice'],
                    'submitdata' => ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionsubmit' => 1],
                    'validatedata' => ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionsubmit' => 1],
                    'cmdata' => ['completion' => COMPLETION_TRACKING_AUTOMATIC],
                    'instancedata' => [['completionsubmit' => 1], ['completionsubmit' => 1], ['completionsubmit' => 1]]
                ]
            ],
            'Several modules with different module type' => [
                [
                    'modulenames' => ['choice', 'forum'],
                    'submitdata' => ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1],
                    'validatedata' => ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1],
                    'cmdata' => ['completion' => COMPLETION_TRACKING_AUTOMATIC],
                    'instancedata' => null
                ]
            ],
            'Setting manual completion (completionview shoud be ignored)' => [
                [
                    'modulenames' => ['scorm', 'forum', 'label', 'assign'],
                    'submitdata' => ['completion' => COMPLETION_TRACKING_MANUAL, 'completionview' => 1],
                    'validatedata' => [],
                    'cmdata' => ['completion' => COMPLETION_TRACKING_MANUAL, 'completionview' => 0],
                    'instancedata' => null
                ]
            ],
            'If at least one module does not support completionsubmit it can\'t be set' => [
                [
                    'modulenames' => ['survey', 'wiki'],
                    'submitdata' => ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1, 'completionsubmit' => 1],
                    'validatedata' => [],
                    'cmdata' => ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1],
                    'instancedata' => [['completionsubmit' => 0], []]
                ]
            ]
        ];
    }

    /**
     * Use bulk completion edit for updating multiple modules
     *
     * @dataProvider bulk_form_submit_multiple_provider
     * @param array $providerdata
     */
    public function test_bulk_form_submit_multiple($providerdata) {
        global $DB;

        $modulenames = $providerdata['modulenames'];
        $submitdata = $providerdata['submitdata'];
        $validatedata = $providerdata['validatedata'];
        $cmdata = $providerdata['cmdata'];
        $instancedata = $providerdata['instancedata'];

        $this->resetAfterTest();
        $this->setAdminUser();
        list($course, $cms) = $this->create_course_and_modules($modulenames);

        // Submit the bulk completion form with the provided data and make sure it returns the same data.
        core_completion_bulkedit_form::mock_submit(['id' => $course->id, 'cmid' => array_keys($cms)] + $submitdata, []);
        $form = new core_completion_bulkedit_form(null, ['cms' => $cms]);
        $this->assertTrue($form->is_validated());
        $data = $form->get_data();
        foreach ($validatedata as $key => $value) {
            $this->assertEquals($value, $data->$key);
        }

        // Apply completion rules to the modules.
        $manager = new manager($course->id);
        $manager->apply_completion($data, $form->has_custom_completion_rules());

        // Make sure either course_modules or instance table was respectfully updated.
        $cnt = 0;
        foreach ($cms as $cm) {
            $cmrec = $DB->get_record('course_modules', ['id' => $cm->id]);
            $instancerec = $DB->get_record($cm->modname, ['id' => $cm->instance]);
            foreach ($cmdata as $key => $value) {
                $this->assertEquals($value, $cmrec->$key, 'Error asserting that value for the field ' . $key.' ' .
                    $cmrec->$key . ' matches expected value ' . $value);
            }
            if ($instancedata) {
                foreach ($instancedata[$cnt] as $key => $value) {
                    $this->assertEquals($value, $instancerec->$key, 'Error asserting that value for the field ' . $key . ' '.
                        $instancerec->$key . ' matches expected value ' . $value);
                }
            }
            $cnt++;
        }
    }
}
