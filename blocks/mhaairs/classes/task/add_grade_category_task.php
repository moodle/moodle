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
 * An adhoc task for adding grade categories.
 *
 * @package block_mhaairs
 * @copyright 2016 Itamar Tzadok <itamar.tzadok@substantialmethods.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_mhaairs\task;

class add_grade_category_task extends \core\task\adhoc_task {

    /**
     * Adds a grade category and moves the specified item into it.
     * Uses locks to prevent race conditions (see MDL-37055).
     */
    public function execute() {
        $customdata = $this->get_custom_data();

        // Get lock timeout.
        $timeout = 5;
        // A namespace for the locks.
        $locktype = 'block_mhaairs_add_category';
        // Resource key - course id and category name.
        $resource = "course: $customdata->courseid; catname: $customdata->catname";

        // Get an instance of the currently configured lock_factory.
        $lockfactory = \core\lock\lock_config::get_lock_factory($locktype);

        // Open a lock.
        $lock = $lockfactory->get_lock($resource, $timeout);

        // Add the category.
        $catparams = array(
            'fullname' => $customdata->catname,
            'courseid' => $customdata->courseid
        );
        if (!$category = \grade_category::fetch($catparams)) {
            // If the category does not exist we create it.
            $gradeaggregation = get_config('core', 'grade_aggregation');
            if ($gradeaggregation === false) {
                $gradeaggregation = GRADE_AGGREGATE_WEIGHTED_MEAN2;
            }
            // Parent category is automatically added(created) during insert.
            $catparams['hidden'] = false;
            $catparams['aggregation'] = $gradeaggregation;

            try {
                $category = new \grade_category($catparams, false);
                $category->id = $category->insert();
            } catch (Exception $e) {
                // Must release the locks.
                $lock->release();
                // Rethrow to reschedule task.
                throw $e;
            }
        }

        // Release locks.
        $lock->release();

        // Add the item to the category.
        $gitem = \grade_item::fetch(array('id' => $customdata->itemid));
        $gitem->categoryid = $category->id;
        $gitem->update();
    }

}
