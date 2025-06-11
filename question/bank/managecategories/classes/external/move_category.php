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

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_question\category_manager;
use moodle_exception;
use context;
use qbank_managecategories\helper;

/**
 * External class used for category reordering.
 *
 * @package    qbank_managecategories
 * @category   external
 * @copyright  2024 Catalyst IT Europe Ltd.
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class move_category extends external_api {
    /**
     * Describes the parameters for update_category_order webservice.
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'pagecontextid' => new external_value(PARAM_INT, 'The context of the current page'),
            'categoryid' => new external_value(PARAM_INT, 'Category being moved'),
            'targetparentid' => new external_value(PARAM_INT, 'The ID of the parent category to move to.'),
            'precedingsiblingid' => new external_value(
                PARAM_INT,
                'The ID of the preceding category. Null if this is being moved to top of its parent',
                allownull: NULL_ALLOWED,
            ),
        ]);
    }

    /**
     * Returns description of method result value.
     *
     * This function will always return a set of state updates for the core/reactive state.
     * {@link https://moodledev.io/docs/4.2/guides/javascript/reactive#controlling-the-state-from-the-backend}
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'name' => new external_value(PARAM_ALPHA, 'State object name (always "categories" from this function).'),
                    'action' => new external_value(PARAM_ALPHA, 'State update type (always "put" from this function).'),
                    'fields' => new external_single_structure(
                        [
                            'id' => new external_value(PARAM_INT, 'The ID of the category that was updated.'),
                            'sortorder' => new external_value(PARAM_INT, 'The new sortorder', VALUE_OPTIONAL),
                            'parent' => new external_value(PARAM_INT, 'The ID of the new parent category.', VALUE_OPTIONAL),
                            'context' => new external_value(PARAM_INT, 'The ID of the new context.', VALUE_OPTIONAL),
                            'draghandle' => new external_value(
                                PARAM_BOOL,
                                'Should this category have a drag handle?',
                                VALUE_OPTIONAL
                            ),
                        ]
                    ),
                ],
                'An individual state update',
            ),
            'Category state updates',
        );
    }

    /**
     * Move category to new location.
     *
     * @param int $pagecontextid ID of the context of the current page.
     * @param int $categoryid ID of the category to move.
     * @param int $targetparentid The ID of the parent category to move to.
     * @param ?int $precedingsiblingid The ID of the preceding category. Null if this is being moved to top of its parent.
     * @return array Reactive state updates representing the changes made to the categories.
     */
    public static function execute(
        int $pagecontextid,
        int $categoryid,
        int $targetparentid,
        ?int $precedingsiblingid = null
    ): array {
        // Update category location.
        global $DB, $CFG;

        require_once($CFG->libdir . '/questionlib.php');

        $context = context::instance_by_id($pagecontextid);
        self::validate_context($context);
        $manager = new category_manager();

        $origincategory = $DB->get_record('question_categories', ['id' => $categoryid], '*', MUST_EXIST);
        $targetparent = $DB->get_record('question_categories', ['id' => $targetparentid], '*', MUST_EXIST);
        if ($precedingsiblingid) {
            $precedingsibling = $DB->get_record('question_categories', ['id' => $precedingsiblingid], '*', MUST_EXIST);
        }

        // Check permission for original and destination contexts.
        $manager->require_manage_category(context::instance_by_id($origincategory->contextid));

        if ($origincategory->contextid != $targetparent->contextid) {
            $manager->require_manage_category(context::instance_by_id($targetparent->contextid));
        }

        $originstateupdate = self::make_state_update($origincategory->id);
        $stateupdates = [];

        $transaction = $DB->start_delegated_transaction();

        // Set new parent.
        if ($origincategory->parent !== $targetparent->id) {
            $newsiblings = $DB->get_fieldset('question_categories', 'id', ['parent' => $targetparent->id]);
            if (
                count($newsiblings) == 1
                && $manager->is_only_child_of_top_category_in_context(reset($newsiblings))
            ) {
                // If we are moving to a top-level parent that only had 1 category before, allow re-ordering of that category.
                $stateupdates[] = self::make_state_update(reset($newsiblings), draghandle: true);
            }
            $originstateupdate->fields->parent = $targetparent->id;
        }

        // Change to the same context.
        if ($origincategory->contextid !== $targetparent->contextid) {
            // Check for duplicate idnumber.
            if (
                !is_null($origincategory->idnumber)
                && !$manager->idnumber_is_unique_in_context($origincategory->idnumber, $targetparent->contextid)
            ) {
                $transaction->rollback(new moodle_exception('idnumberexists', 'qbank_managecategories'));
            }
            $originstateupdate->fields->context = $targetparent->contextid;
        }

        // Update sort order.
        if ($precedingsiblingid) {
            $sortorder = $precedingsibling->sortorder + 1;
        } else {
            $sortorder = 1;
        }
        $originstateupdate->fields->sortorder = $sortorder;

        // Save the updated parent, context and sortorder.
        $manager->update_category(
            $categoryid,
            helper::combine_id_context($targetparent),
            $origincategory->name,
            $origincategory->info,
            $origincategory->infoformat,
            $origincategory->idnumber,
            $sortorder,
        );

        // Get other categories which are after the new position, and update their sortorder.
        $params = [
            'parent' => $targetparent->id,
            'sortorder' => $sortorder,
            'origincategoryid' => $origincategory->id,
        ];
        $select = "
            parent = :parent
            AND id <> :origincategoryid
            AND sortorder >= :sortorder";
        $sort = "sortorder ASC";
        $toupdatesortorder = $DB->get_records_select('question_categories', $select, $params, $sort);
        foreach ($toupdatesortorder as $category) {
            $DB->set_field('question_categories', 'sortorder', ++$sortorder, ['id' => $category->id]);
            $stateupdates[] = self::make_state_update($category->id, sortorder: $sortorder);
        }

        if (isset($originstateupdate->fields->parent)) {
            // If the category has moved parent, re-order the original siblings to fill the gap.
            $originsortorder = 1;
            $params = [
                'parent' => $origincategory->parent,
            ];
            $select = "parent = :parent";
            $sort = "sortorder ASC";
            $originsiblings = $DB->get_records_select('question_categories', $select, $params, $sort);
            if (
                count($originsiblings) == 1
                && $manager->is_only_child_of_top_category_in_context(reset($originsiblings)->id)
            ) {
                // If this is now the only category in the context, don't allow re-ordering.
                $stateupdates[] = self::make_state_update(
                    reset($originsiblings)->id,
                    sortorder: $originsortorder,
                    draghandle: false,
                );
            } else {
                foreach ($originsiblings as $category) {
                    if ($category->sortorder !== $originsortorder) {
                        $DB->set_field('question_categories', 'sortorder', $originsortorder, ['id' => $category->id]);
                        $stateupdates[] = self::make_state_update($category->id, sortorder: $originsortorder);
                    }
                    $originsortorder++;
                }
            }
        }

        $transaction->allow_commit();

        // Return the updated for the moved category, followed by any additional updates that happened as a result.
        array_unshift($stateupdates, $originstateupdate);

        return $stateupdates;
    }

    /**
     * Generate a category state update based on the provided fields.
     *
     * @param int $id Category ID, required.
     * @param int|null $sortorder New sortorder, optional.
     * @param int|null $parent Category ID of new parent, optional.
     * @param bool|null $draghandle Set display of the drag handle. Optional.
     * @return \stdClass The update object.
     */
    protected static function make_state_update(
        int $id,
        ?int $sortorder = null,
        ?int $parent = null,
        ?bool $draghandle = null,
    ): \stdClass {
        $update = (object)[
            'name' => 'categories',
            'action' => 'put',
            'fields' => (object)[
                'id' => $id,
            ],
        ];
        if (!is_null($sortorder)) {
            $update->fields->sortorder = $sortorder;
        }
        if (!is_null($parent)) {
            $update->fields->parent = $parent;
        }
        if (!is_null($draghandle)) {
            $update->fields->draghandle = $draghandle;
        }
        return $update;
    }
}
