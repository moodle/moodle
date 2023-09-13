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
 * Unit tests for mod_lti edit_form
 *
 * @package    mod_lti
 * @copyright  2023 Jackson D'Souza <jackson.dsouza@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/course_categories_trait.php');

/**
 * Unit tests for mod_lti edit_form
 *
 * @package    mod_lti
 * @copyright  2023 Jackson D'Souza <jackson.dsouza@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 */
class mod_lti_edit_types_form_test extends \advanced_testcase {
    // There are shared helpers for these tests in the helper course_categories_trait.
    use \mod_lti_course_categories_trait;

    /**
     * Tests the nested course categories JSON returned by public method mod_lti_edit_types_form::lti_build_category_tree().
     *
     * @covers \mod_lti_edit_types_form::lti_build_category_tree
     */
    public function test_set_nested_categories() {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/mod/lti/tests/fixtures/test_edit_form.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $ltiform = new test_edit_form(null);
        $ltiform->definition_after_data();

        // Setup fixture.
        $coursecategories = $this->setup_course_categories();

        $categoryarray[] = [
            "id" => $coursecategories['topcat']->id,
            "parent" => $coursecategories['topcat']->parent,
            "name" => $coursecategories['topcat']->name,
            "nodes" => [
                            [
                                "id" => $coursecategories['subcata']->id,
                                "parent" => $coursecategories['topcat']->id,
                                "name" => $coursecategories['subcata']->name,
                                "nodes" => [
                                    [
                                        "id" => $coursecategories['subcatca']->id,
                                        "parent" => $coursecategories['subcata']->id,
                                        "name" => $coursecategories['subcatca']->name,
                                        "nodes" => "",
                                        "haschildren" => ""
                                    ]
                                ],
                                "haschildren" => "1"
                            ],
                            [
                                "id" => $coursecategories['subcatb']->id,
                                "parent" => $coursecategories['topcat']->id,
                                "name" => $coursecategories['subcatb']->name,
                                "nodes" => [
                                    [
                                        "id" => $coursecategories['subcatcb']->id,
                                        "parent" => $coursecategories['subcatb']->id,
                                        "name" => $coursecategories['subcatcb']->name,
                                        "nodes" => "",
                                        "haschildren" => ""
                                    ]
                                ],
                                "haschildren" => "1"
                            ]
                    ],
            "haschildren" => "1"
        ];

        $records = $DB->get_records('course_categories', [], 'sortorder, id', 'id, parent, name');
        $allcategories = json_decode(json_encode($records), true);
        $coursecategoriesarray = $ltiform->lti_build_category_tree($allcategories);

        $this->assertEquals($categoryarray, $coursecategoriesarray);
    }

}
