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

namespace qbank_managecategories\external;

use context;
use context_module;
use moodle_url;
use qbank_managecategories\question_categories;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../manage_category_test_base.php');

/**
 * Unit tests for move_category
 *
 * @package qbank_managecategories
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qbank_managecategories\external\move_category
 */
final class move_category_test extends \qbank_managecategories\manage_category_test_base {

    /**
     * Return order of categories for a given context.
     *
     * @param context $context The context to get the category order for.
     * @return array Nested array, keyed by category IDs.
     */
    private function get_current_order(context $context): array {
        $categories = new question_categories(new moodle_url('/'), [$context]);
        return $this->reduce_tree($categories->editlists[$context->id]->items);
    }

    /**
     * Reduce the ordered tree of categories to a multi-dimensional array of IDs for easier comparison.
     *
     * @param array $tree Tree of categories from question_categories.
     * @return array
     */
    private function reduce_tree(array $tree): array {
        $result = [];
        foreach ($tree as $category) {
            $result[$category->id] = [];
            if (isset($category->children) && !empty((array)$category->children)) {
                $result[$category->id] = $this->reduce_tree($category->children);
            }
        }
        return $result;
    }

    /**
     * Move a category below another category within the same parent.
     *
     * @return void
     */
    public function test_move_category_down(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        // Create context for question categories.
        $course = $this->create_course();
        $qbank = $this->create_qbank($course);
        $context = context_module::instance($qbank->cmid);
        $this->create_course_category();

        // Question categories.
        $qcat1 = $this->create_question_category_for_a_qbank($qbank);
        $qcat2 = $this->create_question_category_for_a_qbank($qbank);
        $qcat3 = $this->create_question_category_for_a_qbank($qbank);

        // Check current order.
        $currentorder = $this->get_current_order($context);
        $expectedorder = [
            $qcat1->id => [],
            $qcat2->id => [],
            $qcat3->id => [],
        ];
        $this->assertEquals($expectedorder, $currentorder);

        // Move category 1 after category 2.
        $stateupdates = move_category::execute($context->id, $qcat1->id, $qcat1->parent, $qcat2->id);

        $neworder = $this->get_current_order($context);
        $newexpectedorder = [
            $qcat2->id => [],
            $qcat1->id => [],
            $qcat3->id => [],
        ];
        $this->assertEquals($newexpectedorder, $neworder);

        // We should have an update to the sortorder of the moved category and following categories.
        $expectedstateupdates = [
            (object)[
                'name' => 'categories',
                'action' => 'put',
                'fields' => (object)[
                    'id' => $qcat1->id,
                    'sortorder' => 3,
                ],
            ],
            (object)[
                'name' => 'categories',
                'action' => 'put',
                'fields' => (object)[
                    'id' => $qcat3->id,
                    'sortorder' => 4,
                ],
            ],
        ];
        $this->assertEquals($stateupdates, $expectedstateupdates);
    }

    /**
     * Move a category to the top of its parent.
     *
     * @return void
     * @throws \moodle_exception
     */
    public function test_move_category_to_top(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        // Create context for question categories.
        $qbank = $this->create_qbank($this->create_course());
        $context = context_module::instance($qbank->cmid);
        $this->create_course_category();

        // Question categories.
        $qcat1 = $this->create_question_category_for_a_qbank($qbank);
        $qcat2 = $this->create_question_category_for_a_qbank($qbank);
        $qcat3 = $this->create_question_category_for_a_qbank($qbank);

        // Check current order.
        $currentorder = $this->get_current_order($context);
        $expectedorder = [
            $qcat1->id => [],
            $qcat2->id => [],
            $qcat3->id => [],
        ];
        $this->assertEquals($expectedorder, $currentorder);

        // Move category 3 to the top.
        $stateupdates = move_category::execute($context->id, $qcat3->id, $qcat3->parent);

        $neworder = $this->get_current_order($context);
        $newexpectedorder = [
            $qcat3->id => [],
            $qcat1->id => [],
            $qcat2->id => [],
        ];
        $this->assertEquals($newexpectedorder, $neworder);

        // Expecting all categories to have an updated sortorder.
        $expectedstateupdates = [
            (object)[
                'name' => 'categories',
                'action' => 'put',
                'fields' => (object)[
                    'id' => $qcat3->id,
                    'sortorder' => 1,
                ],
            ],
            (object)[
                'name' => 'categories',
                'action' => 'put',
                'fields' => (object)[
                    'id' => $qcat1->id,
                    'sortorder' => 2,
                ],
            ],
            (object)[
                'name' => 'categories',
                'action' => 'put',
                'fields' => (object)[
                    'id' => $qcat2->id,
                    'sortorder' => 3,
                ],
            ],
        ];
        $this->assertEquals($stateupdates, $expectedstateupdates);
    }

    /**
     * Move a category to a new parent that doesn't currently have any children.
     *
     * @return void
     */
    public function test_move_category_as_new_child(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        // Create context for question categories.
        $qbank = $this->create_qbank($this->create_course());
        $context = context_module::instance($qbank->cmid);
        $this->create_course_category();

        // Question categories.
        $qcat1 = $this->create_question_category_for_a_qbank($qbank);
        $qcat2 = $this->create_question_category_for_a_qbank($qbank);
        $qcat3 = $this->create_question_category_for_a_qbank($qbank);

        // Check current order.
        $currentorder = $this->get_current_order($context);
        $expectedorder = [
            $qcat1->id => [],
            $qcat2->id => [],
            $qcat3->id => [],
        ];
        $this->assertEquals($expectedorder, $currentorder);

        // Set Category 2 as the parent of Category 1.
        $stateupdates = move_category::execute($context->id, $qcat1->id, $qcat2->id);

        $neworder = $this->get_current_order($context);
        $newexpectedorder = [
            $qcat2->id => [
                $qcat1->id => [],
            ],
            $qcat3->id => [],
        ];
        $this->assertEquals($newexpectedorder, $neworder);

        // Expecting an update to the parent and sortorder of the moved category, and updated sortorders for the children
        // of the original parent.
        $expectedstateupdates = [
            (object)[
                'name' => 'categories',
                'action' => 'put',
                'fields' => (object)[
                    'id' => $qcat1->id,
                    'sortorder' => 1,
                    'parent' => $qcat2->id,
                ],
            ],
            (object)[
                'name' => 'categories',
                'action' => 'put',
                'fields' => (object)[
                    'id' => $qcat2->id,
                    'sortorder' => 1,
                ],
            ],
            (object)[
                'name' => 'categories',
                'action' => 'put',
                'fields' => (object)[
                    'id' => $qcat3->id,
                    'sortorder' => 2,
                ],
            ],
        ];
        $this->assertEquals($stateupdates, $expectedstateupdates);
    }

    /**
     * Move a category from one parent to another that already has a child.
     *
     * @return void
     * @throws \moodle_exception
     */
    public function test_move_category_between_parents(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        // Create context for question categories.
        $qbank = $this->create_qbank($this->create_course());
        $context = context_module::instance($qbank->cmid);
        $this->create_course_category();

        // Question categories.
        $qcat1 = $this->create_question_category_for_a_qbank($qbank);
        $qcat2 = $this->create_question_category_for_a_qbank($qbank);
        $qcat3 = $this->create_question_category_for_a_qbank($qbank, ['parent' => $qcat1->id]);
        $qcat4 = $this->create_question_category_for_a_qbank($qbank, ['parent' => $qcat2->id]);

        // Check current order.
        $currentorder = $this->get_current_order($context);
        $expectedorder = [
            $qcat1->id => [
                $qcat3->id => [],
            ],
            $qcat2->id => [
                $qcat4->id => [],
            ],
        ];
        $this->assertEquals($expectedorder, $currentorder);

        // Set Category 2 as the parent of Category 1.
        $stateupdates = move_category::execute($context->id, $qcat3->id, $qcat2->id, $qcat4->id);

        $neworder = $this->get_current_order($context);
        $newexpectedorder = [
            $qcat1->id => [],
            $qcat2->id => [
                $qcat4->id => [],
                $qcat3->id => [],
            ],
        ];
        $this->assertEquals($newexpectedorder, $neworder);

        // As there are no remaining children of the original parent, and this was moved to the bottom of the new parent,
        // just the moved category is updated.
        $expectedstateupdates = [
            (object)[
                'name' => 'categories',
                'action' => 'put',
                'fields' => (object)[
                    'id' => $qcat3->id,
                    'sortorder' => 2,
                    'parent' => $qcat2->id,
                ],
            ],
        ];
        $this->assertEquals($stateupdates, $expectedstateupdates);
    }

    /**
     * Move a category that has its own children.
     *
     * The children should move with the parent.
     *
     * @return void
     */
    public function test_move_category_with_children(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        // Create context for question categories.
        $qbank = $this->create_qbank($this->create_course());
        $context = context_module::instance($qbank->cmid);
        $this->create_course_category();

        // Question categories.
        $qcat1 = $this->create_question_category_for_a_qbank($qbank);
        $qcat2 = $this->create_question_category_for_a_qbank($qbank);
        $qcat3 = $this->create_question_category_for_a_qbank($qbank);
        $qcat4 = $this->create_question_category_for_a_qbank($qbank, ['parent' => $qcat3->id]);
        $qcat5 = $this->create_question_category_for_a_qbank($qbank, ['parent' => $qcat3->id]);

        // Check current order.
        $currentorder = $this->get_current_order($context);
        $expectedorder = [
            $qcat1->id => [],
            $qcat2->id => [],
            $qcat3->id => [
                $qcat4->id => [],
                $qcat5->id => [],
            ],
        ];
        $this->assertEquals($expectedorder, $currentorder);

        $stateupdates = move_category::execute($context->id, $qcat3->id, $qcat3->parent, $qcat1->id);

        $neworder = $this->get_current_order($context);
        $newexpectedorder = [
            $qcat1->id => [],
            $qcat3->id => [
                $qcat4->id => [],
                $qcat5->id => [],
            ],
            $qcat2->id => [],
        ];
        $this->assertEquals($neworder, $newexpectedorder);

        // Update the sortorder of the moving category and the following sibling. No updates to the children are required.
        $expectedstateupdates = [
            (object)[
                'name' => 'categories',
                'action' => 'put',
                'fields' => (object)[
                    'id' => $qcat3->id,
                    'sortorder' => 2,
                ],
            ],
            (object)[
                'name' => 'categories',
                'action' => 'put',
                'fields' => (object)[
                    'id' => $qcat2->id,
                    'sortorder' => 3,
                ],
            ],
        ];
        $this->assertEquals($stateupdates, $expectedstateupdates);
    }

    /**
     * Move a category that has its own children to a new parent.
     *
     * The children should also move and become descendants of the new parent.
     *
     * @return void
     */
    public function test_change_parent_with_children(): void {
        $this->setAdminUser();
        $this->resetAfterTest();

        // Create context for question categories.
        $qbank = $this->create_qbank($this->create_course());
        $context = context_module::instance($qbank->cmid);
        $this->create_course_category();

        // Question categories.
        $qcat1 = $this->create_question_category_for_a_qbank($qbank);
        $qcat2 = $this->create_question_category_for_a_qbank($qbank);
        $qcat3 = $this->create_question_category_for_a_qbank($qbank);
        $qcat4 = $this->create_question_category_for_a_qbank($qbank, ['parent' => $qcat3->id]);
        $qcat5 = $this->create_question_category_for_a_qbank($qbank, ['parent' => $qcat4->id]);

        // Check current order.
        $currentorder = $this->get_current_order($context);
        $expectedorder = [
            $qcat1->id => [],
            $qcat2->id => [],
            $qcat3->id => [
                $qcat4->id => [
                    $qcat5->id => [],
                ],
            ],
        ];
        $this->assertEquals($expectedorder, $currentorder);

        $stateupdates = move_category::execute($context->id, $qcat4->id, $qcat2->id);

        $neworder = $this->get_current_order($context);
        $newexpectedorder = [
            $qcat1->id => [],
            $qcat2->id => [
                $qcat4->id => [
                    $qcat5->id => [],
                ],
            ],
            $qcat3->id => [],
        ];
        $this->assertEquals($newexpectedorder, $neworder);

        // Expecting an update to the sortorder and parent of the moved category. No updates to the children are required.
        $expectedstateupdates = [
            (object)[
                'name' => 'categories',
                'action' => 'put',
                'fields' => (object)[
                    'id' => $qcat4->id,
                    'parent' => $qcat2->id,
                    'sortorder' => 1,
                ],
            ],
        ];
        $this->assertEquals($expectedstateupdates, $stateupdates);
    }
}
