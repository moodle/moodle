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
 * Unit tests for Random allocation
 *
 * @package    workshopallocation_random
 * @category   phpunit
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the code to test
global $CFG;
require_once($CFG->dirroot . '/mod/workshop/locallib.php');
require_once($CFG->dirroot . '/mod/workshop/allocation/random/lib.php');


class workshopallocation_random_testcase extends advanced_testcase {

    /** workshop instance emulation */
    protected $workshop;

    /** allocator instance */
    protected $allocator;

    protected function setUp() {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', array('course' => $course));
        $cm = get_fast_modinfo($course)->instances['workshop'][$workshop->id];
        $this->workshop = new workshop($workshop, $cm, $course);
        $this->allocator = new testable_workshop_random_allocator($this->workshop);
    }

    protected function tearDown() {
        $this->allocator    = null;
        $this->workshop     = null;
        parent::tearDown();
    }

    public function test_self_allocation_empty_values() {
        // fixture setup & exercise SUT & verify
        $this->assertEquals(array(), $this->allocator->self_allocation());
    }

    public function test_self_allocation_equal_user_groups() {
        // fixture setup
        $authors    = array(0 => array_fill_keys(array(4, 6, 10), new stdclass()));
        $reviewers  = array(0 => array_fill_keys(array(4, 6, 10), new stdclass()));
        // exercise SUT
        $newallocations = $this->allocator->self_allocation($authors, $reviewers);
        // verify
        $this->assertEquals(array(array(4 => 4), array(6 => 6), array(10 => 10)), $newallocations);
    }

    public function test_self_allocation_different_user_groups() {
        // fixture setup
        $authors    = array(0 => array_fill_keys(array(1, 4, 5, 10, 13), new stdclass()));
        $reviewers  = array(0 => array_fill_keys(array(4, 7, 10), new stdclass()));
        // exercise SUT
        $newallocations = $this->allocator->self_allocation($authors, $reviewers);
        // verify
        $this->assertEquals(array(array(4 => 4), array(10 => 10)), $newallocations);
    }

    public function test_self_allocation_skip_existing() {
        // fixture setup
        $authors        = array(0 => array_fill_keys(array(3, 4, 10), new stdclass()));
        $reviewers      = array(0 => array_fill_keys(array(3, 4, 10), new stdclass()));
        $assessments    = array(23 => (object)array('authorid' => 3, 'reviewerid' => 3));
        // exercise SUT
        $newallocations = $this->allocator->self_allocation($authors, $reviewers, $assessments);
        // verify
        $this->assertEquals(array(array(4 => 4), array(10 => 10)), $newallocations);
    }

    public function test_get_author_ids() {
        // fixture setup
        $newallocations = array(array(1 => 3), array(2 => 1), array(3 => 1));
        // exercise SUT & verify
        $this->assertEquals(array(3, 1), $this->allocator->get_author_ids($newallocations));
    }

    public function test_index_submissions_by_authors() {
        // fixture setup
        $submissions = array(
            676 => (object)array('id' => 676, 'authorid' => 23),
            121 => (object)array('id' => 121, 'authorid' => 56),
        );
        // exercise SUT
        $submissions = $this->allocator->index_submissions_by_authors($submissions);
        // verify
        $this->assertEquals(array(
            23 => (object)array('id' => 676, 'authorid' => 23),
            56 => (object)array('id' => 121, 'authorid' => 56),
        ), $submissions);
    }

    /**
     * @expectedException moodle_exception
     */
    public function test_index_submissions_by_authors_duplicate_author() {
        // fixture setup
        $submissions = array(
            14 => (object)array('id' => 676, 'authorid' => 3),
            87 => (object)array('id' => 121, 'authorid' => 3),
        );
        // exercise SUT
        $submissions = $this->allocator->index_submissions_by_authors($submissions);
    }

    public function test_get_unique_allocations() {
        // fixture setup
        $newallocations = array(array(4 => 5), array(6 => 6), array(1 => 16), array(4 => 5), array(16 => 1));
        // exercise SUT
        $newallocations = $this->allocator->get_unique_allocations($newallocations);
        // verify
        $this->assertEquals(array(array(4 => 5), array(6 => 6), array(1 => 16), array(16 => 1)), $newallocations);
    }

    public function test_get_unkept_assessments_no_keep_selfassessments() {
        // fixture setup
        $assessments = array(
            23 => (object)array('authorid' => 3, 'reviewerid' => 3),
            45 => (object)array('authorid' => 5, 'reviewerid' => 11),
            12 => (object)array('authorid' => 6, 'reviewerid' => 3),
        );
        $newallocations = array(array(4 => 5), array(11 => 5), array(1 => 16), array(4 => 5), array(16 => 1));
        // exercise SUT
        $delassessments = $this->allocator->get_unkept_assessments($assessments, $newallocations, false);
        // verify
        // we want to keep $assessments[45] because it has been re-allocated
        $this->assertEquals(array(23, 12), $delassessments);
    }

    public function test_get_unkept_assessments_keep_selfassessments() {
        // fixture setup
        $assessments = array(
            23 => (object)array('authorid' => 3, 'reviewerid' => 3),
            45 => (object)array('authorid' => 5, 'reviewerid' => 11),
            12 => (object)array('authorid' => 6, 'reviewerid' => 3),
        );
        $newallocations = array(array(4 => 5), array(11 => 5), array(1 => 16), array(4 => 5), array(16 => 1));
        // exercise SUT
        $delassessments = $this->allocator->get_unkept_assessments($assessments, $newallocations, true);
        // verify
        // we want to keep $assessments[45] because it has been re-allocated
        // we want to keep $assessments[23] because if is self assessment
        $this->assertEquals(array(12), $delassessments);
    }

    /**
     * Aggregates assessment info per author and per reviewer
     */
    public function test_convert_assessments_to_links() {
        // fixture setup
        $assessments = array(
            23 => (object)array('authorid' => 3, 'reviewerid' => 3),
            45 => (object)array('authorid' => 5, 'reviewerid' => 11),
            12 => (object)array('authorid' => 5, 'reviewerid' => 3),
        );
        // exercise SUT
        list($authorlinks, $reviewerlinks) = $this->allocator->convert_assessments_to_links($assessments);
        // verify
        $this->assertEquals(array(3 => array(3), 5 => array(11, 3)), $authorlinks);
        $this->assertEquals(array(3 => array(3, 5), 11 => array(5)), $reviewerlinks);
    }

    /**
     * Trivial case
     */
    public function test_convert_assessments_to_links_empty() {
        // fixture setup
        $assessments = array();
        // exercise SUT
        list($authorlinks, $reviewerlinks) = $this->allocator->convert_assessments_to_links($assessments);
        // verify
        $this->assertEquals(array(), $authorlinks);
        $this->assertEquals(array(), $reviewerlinks);
    }

    /**
     * If there is a single element with the lowest workload, it should be chosen
     */
    public function test_get_element_with_lowest_workload_deterministic() {
        // fixture setup
        $workload = array(4 => 6, 9 => 1, 10 => 2);
        // exercise SUT
        $chosen = $this->allocator->get_element_with_lowest_workload($workload);
        // verify
        $this->assertEquals(9, $chosen);
    }

    /**
     * If there are no elements available, must return false
     */
    public function test_get_element_with_lowest_workload_impossible() {
        // fixture setup
        $workload = array();
        // exercise SUT
        $chosen = $this->allocator->get_element_with_lowest_workload($workload);
        // verify
        $this->assertTrue($chosen === false);
    }

    /**
     * If there are several elements with the lowest workload, one of them should be chosen randomly
     */
    public function test_get_element_with_lowest_workload_random() {
        // fixture setup
        $workload = array(4 => 6, 9 => 2, 10 => 2);
        // exercise SUT
        $elements = $this->allocator->get_element_with_lowest_workload($workload);
        // verify
        // in theory, this test can fail even if the function works well. However, the probability of getting
        // a row of a hundred same ids in this use case is 1/pow(2, 100)
        // also, this just tests that each of the two elements has been chosen at least once. this is not to
        // measure the quality or randomness of the algorithm
        $counts = array(4 => 0, 9 => 0, 10 => 0);
        for ($i = 0; $i < 100; $i++) {
            $chosen = $this->allocator->get_element_with_lowest_workload($workload);
            if (!in_array($chosen, array(4, 9, 10))) {
                $this->fail('Invalid element ' . var_export($chosen, true) . ' chosen');
                break;
            } else {
                $counts[$this->allocator->get_element_with_lowest_workload($workload)]++;
            }
        }
        $this->assertTrue(($counts[9] > 0) && ($counts[10] > 0));
    }

    /**
     * Floats should be rounded before they are compared
     *
     * This should test
     */
    public function test_get_element_with_lowest_workload_random_floats() {
        // fixture setup
        $workload = array(1 => 1/13, 2 => 0.0769230769231); // should be considered as the same value
        // exercise SUT
        $elements = $this->allocator->get_element_with_lowest_workload($workload);
        // verify
        $counts = array(1 => 0, 2 => 0);
        for ($i = 0; $i < 100; $i++) {
            $chosen = $this->allocator->get_element_with_lowest_workload($workload);
            if (!in_array($chosen, array(1, 2))) {
                $this->fail('Invalid element ' . var_export($chosen, true) . ' chosen');
                break;
            } else {
                $counts[$this->allocator->get_element_with_lowest_workload($workload)]++;
            }
        }
        $this->assertTrue(($counts[1] > 0) && ($counts[2] > 0));

    }

    /**
     * Filter new assessments so they do not contain existing
     */
    public function test_filter_current_assessments() {
        // fixture setup
        $newallocations = array(array(3 => 5), array(11 => 5), array(2 => 9), array(3 => 5));
        $assessments = array(
            23 => (object)array('authorid' => 3, 'reviewerid' => 3),
            45 => (object)array('authorid' => 5, 'reviewerid' => 11),
            12 => (object)array('authorid' => 5, 'reviewerid' => 3),
        );
        // exercise SUT
        $this->allocator->filter_current_assessments($newallocations, $assessments);
        // verify
        $this->assertEquals(array_values($newallocations), array(array(2 => 9)));
    }


}


/**
 * Make protected methods we want to test public
 */
class testable_workshop_random_allocator extends workshop_random_allocator {
    public function self_allocation($authors=array(), $reviewers=array(), $assessments=array()) {
        return parent::self_allocation($authors, $reviewers, $assessments);
    }
    public function get_author_ids($newallocations) {
        return parent::get_author_ids($newallocations);
    }
    public function index_submissions_by_authors($submissions) {
        return parent::index_submissions_by_authors($submissions);
    }
    public function get_unique_allocations($newallocations) {
        return parent::get_unique_allocations($newallocations);
    }
    public function get_unkept_assessments($assessments, $newallocations, $keepselfassessments) {
        return parent::get_unkept_assessments($assessments, $newallocations, $keepselfassessments);
    }
    public function convert_assessments_to_links($assessments) {
        return parent::convert_assessments_to_links($assessments);
    }
    public function get_element_with_lowest_workload($workload) {
        return parent::get_element_with_lowest_workload($workload);
    }
    public function filter_current_assessments(&$newallocations, $assessments) {
        return parent::filter_current_assessments($newallocations, $assessments);
    }
}
