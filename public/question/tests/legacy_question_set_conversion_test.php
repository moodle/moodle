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

    /**
     * Verifies that a legacy tag filter re-uses an existing tag that
     * lives in the default collection and does not create a duplicate.
     *
     * @covers \core_question\question_reference_manager::convert_legacy_set_reference_filter_condition
     * @return void
     */
    public function test_tag_conversion_uses_existing_tag_in_default_collection(): void {
        $this->resetAfterTest();

        // Prepare a tag that already exists in the default collection.
        $defaultcollectionid = \core_tag_collection::get_default();

        // Create a tag inside that collection.
        $tag = \core_tag_tag::create_if_missing($defaultcollectionid, ['legacytag'])['legacytag'];

        // Legacy random-question filter – tag specified as "id,rawname".
        $legacyfilter = ['tags' => ["{$tag->id},legacytag"]];
        $converted = question_reference_manager::convert_legacy_set_reference_filter_condition($legacyfilter);

        $this->assertEquals(
            [$tag->id],
            $converted['filter']['qtagids']['values'],
            'Converter should preserve the existing tag ID and avoid duplicates.'
        );
    }

    /**
     * Verifies that a legacy tag filter respects a custom collection after
     * the *question* tag-area has been moved there.
     *
     * @covers \core_question\question_reference_manager::convert_legacy_set_reference_filter_condition
     * @return void
     */
    public function test_tag_conversion_respects_custom_collection(): void {
        $this->resetAfterTest();

        // Create a custom collection and move the question tag-area to it.
        $customcollection = \core_tag_collection::create((object) [
            'name' => 'Questions',
            'component' => 'core_question',
            'searchable' => 0,
        ]);
        $questionarea = \core_tag_area::get_areas()['question']['core_question'];
        \core_tag_area::update($questionarea, ['tagcollid' => $customcollection->id]);

        // Create a tag inside that collection.
        $tag = \core_tag_tag::create_if_missing($customcollection->id, ['legacytag'])['legacytag'];

        $legacyfilter = ['tags' => ["{$tag->id},legacytag"]];
        $converted = question_reference_manager::convert_legacy_set_reference_filter_condition($legacyfilter);

        $this->assertEquals(
            [$tag->id],
            $converted['filter']['qtagids']['values'],
            'Converter should use the tag ID from the custom collection.'
        );
    }
}
