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
 * Unit tests for (some of) mod/feedback/classes/lib.php.
 *
 * @package    mod_feedback
 * @copyright  2019 Tobias Reischmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/mod/feedback/classes/completion.php');

/**
 * Unit tests for (some of) mod/feedback/classes/completion.php.
 *
 * @copyright  2019 Tobias Reischmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_feedback_completion_testcase extends advanced_testcase {
    /**
     * Returns the number of pages with visible elements for the current state of the feedback completion.
     * @param mod_feedback_completion $completion
     * @return int number of pages with at least one visible item.
     */
    private function get_number_of_visible_pages(mod_feedback_completion $completion) {
        $pages = $completion->get_pages();
        $result = 0;
        foreach ($pages as $items) {
            if (count($items) > 0) {
                $result++;
            }
        }
        return $result;
    }

    /**
     * Tests get_pages for transitive dependencies.
     * @throws coding_exception
     */
    public function test_get_pages() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $feedback = $this->getDataGenerator()->create_module('feedback',
            array('course' => $course->id));
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);

        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $itemscreated = [];

        // Create at least one page.
        $itemscreated[] = $feedbackgenerator->create_item_multichoice($feedback,
            $record = ['values' => "y\nn"]);
        $itemscreated[] = $feedbackgenerator->create_item_pagebreak($feedback);
        $itemscreated[] = $feedbackgenerator->create_item_multichoice($feedback,
            $record = ['values' => "y\nn", 'dependitem' => $itemscreated[0]->id, 'dependvalue' => 'n']);
        $itemscreated[] = $feedbackgenerator->create_item_pagebreak($feedback);
        $itemscreated[] = $feedbackgenerator->create_item_multichoice($feedback,
            $record = ['values' => "y\nn", 'dependitem' => $itemscreated[0]->id, 'dependvalue' => 'y']);
        $itemscreated[] = $feedbackgenerator->create_item_pagebreak($feedback);
        $itemscreated[] = $feedbackgenerator->create_item_multichoice($feedback,
            $record = ['values' => "y\nn", 'dependitem' => $itemscreated[2]->id, 'dependvalue' => 'y']);

        // Test hiding item since transitive dependency is not met.
        // Answering the first multichoice with 'y', should hide the second and therefor also the fourth.
        $user1 = $this->getDataGenerator()->create_and_enrol($course);
        $completion = new mod_feedback_completion($feedback, $cm, $course,
            false, null, $user1->id);

        // Initially, all pages should be visible.
        $this->assertEquals(4, $this->get_number_of_visible_pages($completion));

        // Answer the first multichoice with 'y', which should exclude the second and the fourth.
        $answers = ['multichoice_' . $itemscreated[0]->id => [1]];
        $completion->save_response_tmp((object) $answers);

        $this->assertEquals(2, $this->get_number_of_visible_pages($completion));

        // Answer the third multichoice with 'n', which should exclude the last one.
        $answers = ['multichoice_' . $itemscreated[4]->id => [2]];
        $completion->save_response_tmp((object) $answers);

        $this->assertEquals(2, $this->get_number_of_visible_pages($completion));

        $completion->save_response();

        // Test showing item since transitive dependency is met.
        // Answering the first multichoice with 'n' should hide the third multichoice.
        $user2 = $this->getDataGenerator()->create_and_enrol($course);
        $completion2 = new mod_feedback_completion($feedback, $cm, $course,
            false, null, $user2->id);

        // Initially, all pages should be visible.
        $this->assertEquals(4, $this->get_number_of_visible_pages($completion2));

        // Answer the first multichoice with 'n' should hide the third multichoice.
        $answers = ['multichoice_' . $itemscreated[0]->id => [2]];
        $completion2->save_response_tmp((object) $answers);

        $this->assertEquals(3, $this->get_number_of_visible_pages($completion2));

        // Answering the second multichoice with 'n' should hide the fourth one.
        $answers = ['multichoice_' . $itemscreated[2]->id => [2]];
        $completion2->save_response_tmp((object) $answers);

        $this->assertEquals(2, $this->get_number_of_visible_pages($completion2));
    }

}
