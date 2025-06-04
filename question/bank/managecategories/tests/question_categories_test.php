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

namespace qbank_managecategories;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/bank/managecategories/tests/manage_category_test_base.php');

/**
 * Unit tests for question_categories
 *
 * @package   qbank_managecategories
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qbank_managecategories\question_categories
 */
final class question_categories_test extends manage_category_test_base {
    /**
     * Test creation of an ordered tree of categories in the constructor.
     */
    public function test_create_order_tree(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        // Create question categories for a course.
        $qbank = $this->create_qbank($this->create_course());
        $context = \context_module::instance($qbank->cmid);
        $qcat1 = question_get_default_category($context->id);
        $qcat2 = $this->create_question_category_for_a_qbank($qbank, ['parent' => $qcat1->id]);
        $qcat3 = $this->create_question_category_for_a_qbank($qbank);
        $qcat4 = $this->create_question_category_for_a_qbank($qbank, ['parent' => $qcat2->id]);

        // Create ordered tree.
        $questioncategories = new question_categories(
            new \moodle_url('/'),
            [$context],
        );
        $items = $questioncategories->editlists[$context->id]->items;

        // Two top categories (1 and 3) in the course.
        $this->assertCount(2, $items);
        $this->assertArrayHasKey($qcat1->id, $items);
        $this->assertArrayHasKey($qcat3->id, $items);

        // Category 2 is the only child of Category 1.
        $children = $items[$qcat1->id]->children;
        $this->assertCount(1, $children);
        $this->assertArrayHasKey($qcat2->id, $children);

        // Category 4 is the only child of Category 2.
        $children = $children[$qcat2->id]->children;
        $this->assertCount(1, $children);
        $this->assertArrayHasKey($qcat4->id, $children);
    }
}
