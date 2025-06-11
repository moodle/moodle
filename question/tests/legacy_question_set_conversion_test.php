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

namespace core_question;

/**
 * Test the conversion of legacy random question sets into the newer format.
 *
 * @package    core_question
 * @copyright  2025 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Conn Warwicker <conn.warwicker@catalyst-eu.net>
 * @covers     \core_question\question_reference_manager
 */
final class legacy_question_set_conversion_test extends \advanced_testcase {

    /**
     * Test the conversion of the old formatted `filtercondition` value to the newer format.
     * @covers \core_question\question_reference_manager::convert_legacy_set_reference_filter_condition
     * @return void
     */
    public function test_legacy_question_set_conversion(): void {

        $this->resetAfterTest(false);

        // Test conversion without a valid question category.
        $old = [
            'questioncategoryid' => 123,
            'includingsubcategories' => 0,
        ];

        $new = question_reference_manager::convert_legacy_set_reference_filter_condition($old);
        $expected = [
            'filter' => [
                'category' => [
                    'jointype' => 1,
                    'values' => [123],
                    'filteroptions' => [
                        'includesubcategories' => 0,
                    ],
                ],
            ],
            'cat' => '',
            'tabname' => 'questions',
            'qpage' => 0,
            'qperpage' => 100,
            'jointype' => 2,
        ];
        $this->assertEquals($new, $expected);

        // Test conversion with a valid question category.
        // Generate a question category.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();

        // Add the valid category into the arrays.
        $old['questioncategoryid'] = $cat->id;
        $expected['cat'] = "{$cat->id},{$cat->contextid}";
        $expected['filter']['category']['values'] = [$cat->id];
        $new = question_reference_manager::convert_legacy_set_reference_filter_condition($old);
        $this->assertEquals($new, $expected);

    }

}
