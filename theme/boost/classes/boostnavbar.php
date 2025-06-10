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

namespace theme_boost;

use core\navigation\views\view;
use navigation_node;
use moodle_url;
use action_link;
use lang_string;

/**
 * Creates a navbar for boost that allows easy control of the navbar items.
 *
 * @package    theme_boost
 * @copyright  2021 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class boostnavbar implements \renderable {

    /** @var array The individual items of the navbar. */
    protected $items = [];
    /** @var moodle_page The current moodle page. */
    protected $page;

    /**
     * Takes a navbar object and picks the necessary parts for display.
     *
     * @param \moodle_page $page The current moodle page.
     */
    public function __construct(\moodle_page $page) {
        $this->page = $page;
        foreach ($this->page->navbar->get_items() as $item) {
            $this->items[] = $item;
        }
        $this->prepare_nodes_for_boost();
    }

    /**
     * Prepares the navigation nodes for use with boost.
     */
    protected function prepare_nodes_for_boost(): void {
        global $PAGE;

        // Remove the navbar nodes that already exist in the primary navigation menu.
        $this->remove_items_that_exist_in_navigation($PAGE->primarynav);

        // Defines whether section items with an action should be removed by default.
        $removesections = true;

        if ($this->page->context->contextlevel == CONTEXT_COURSECAT) {
            // Remove the 'Permissions' navbar node in the Check permissions page.
            if ($this->page->pagetype === 'admin-roles-check') {
                $this->remove('permissions');
            }
        }
        if ($this->page->context->contextlevel == CONTEXT_COURSE) {
            // Remove any duplicate navbar nodes.
            $this->remove_duplicate_items();
            // Remove 'My courses' and 'Courses' if we are in the course context.
            $this->remove('mycourses');
            $this->remove('courses');
            // Remove the course category breadcrumb nodes.
            foreach ($this->items as $key => $item) {
                // Remove if it is a course category breadcrumb node.
                $this->remove($item->key, \breadcrumb_navigation_node::TYPE_CATEGORY);
            }
            // Remove the course breadcrumb node.
            $this->remove($this->page->course->id, \breadcrumb_navigation_node::TYPE_COURSE);
            // Remove the navbar nodes that already exist in the secondary navigation menu.
            $this->remove_items_that_exist_in_navigation($PAGE->secondarynav);

            switch ($this->page->pagetype) {
                case 'group-groupings':
                case 'group-grouping':
                case 'group-overview':
                case 'group-assign':
                    // Remove the 'Groups' navbar node in the Groupings, Grouping, group Overview and Assign pages.
                    $this->remove('groups');
                case 'backup-backup':
                case 'backup-restorefile':
                case 'backup-copy':
                case 'course-reset':
                    // Remove the 'Import' navbar node in the Backup, Restore, Copy course and Reset pages.
                    $this->remove('import');
                case 'course-user':
                    $this->remove('mygrades');
                    $this->remove('grades');
            }
        }

        // Remove 'My courses' if we are in the module context.
        if ($this->page->context->contextlevel == CONTEXT_MODULE) {
            $this->remove('mycourses');
            $this->remove('courses');
            // Remove the course category breadcrumb nodes.
            foreach ($this->items as $key => $item) {
                // Remove if it is a course category breadcrumb node.
                $this->remove($item->key, \breadcrumb_navigation_node::TYPE_CATEGORY);
            }
            $courseformat = course_get_format($this->page->course)->get_course();
            // Section items can be only removed if a course layout (coursedisplay) is not explicitly set in the
            // given course format or the set course layout is not 'One section per page'.
            $removesections = !isset($courseformat->coursedisplay) ||
                $courseformat->coursedisplay != COURSE_DISPLAY_MULTIPAGE;
            if ($removesections) {
                // If the course sections are removed, we need to add the anchor of current section to the Course.
                $coursenode = $this->get_item($this->page->course->id);
                if (!is_null($coursenode) && $this->page->cm->sectionnum !== null) {
                    $coursenode->action = course_get_format($this->page->course)->get_view_url($this->page->cm->sectionnum);
                }
            }
        }

        if ($this->page->context->contextlevel == CONTEXT_SYSTEM) {
            // Remove the navbar nodes that already exist in the secondary navigation menu.
            $this->remove_items_that_exist_in_navigation($PAGE->secondarynav);
        }

        // Set the designated one path for courses.
        $mycoursesnode = $this->get_item('mycourses');
        if (!is_null($mycoursesnode)) {
            $url = new \moodle_url('/my/courses.php');
            $mycoursesnode->action = $url;
            $mycoursesnode->text = get_string('mycourses');
        }

        $this->remove_no_link_items($removesections);

        // Don't display the navbar if there is only one item. Apparently this is bad UX design.
        if ($this->item_count() <= 1) {
            $this->clear_items();
            return;
        }

        // Make sure that the last item is not a link. Not sure if this is always a good idea.
        $this->remove_last_item_action();
    }

    /**
     * Get all the boostnavbaritem elements.
     *
     * @return boostnavbaritem[] Boost navbar items.
     */
    public function get_items(): array {
        return $this->items;
    }

    /**
     * Removes all navigation items out of this boost navbar
     */
    protected function clear_items(): void {
        $this->items = [];
    }

    /**
     * Retrieve a single navbar item.
     *
     * @param  string|int $key The identifier of the navbar item to return.
     * @return \breadcrumb_navigation_node|null The navbar item.
     */
    protected function get_item($key): ?\breadcrumb_navigation_node {
        foreach ($this->items as $item) {
            if ($key === $item->key) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Counts all of the navbar items.
     *
     * @return int How many navbar items there are.
     */
    protected function item_count(): int {
        return count($this->items);
    }

    /**
     * Remove a boostnavbaritem from the boost navbar.
     *
     * @param  string|int $itemkey An identifier for the boostnavbaritem
     * @param  int|null $itemtype An additional type identifier for the boostnavbaritem (optional)
     */
    protected function remove($itemkey, ?int $itemtype = null): void {

        $itemfound = false;
        foreach ($this->items as $key => $item) {
            if ($item->key === $itemkey) {
                // If a type identifier is also specified, check whether the type of the breadcrumb item matches the
                // specified type. Skip if types to not match.
                if (!is_null($itemtype) && $item->type !== $itemtype) {
                    continue;
                }
                unset($this->items[$key]);
                $itemfound = true;
                break;
            }
        }
        if (!$itemfound) {
            return;
        }

        $itemcount = $this->item_count();
        if ($itemcount <= 0) {
            return;
        }

        $this->items = array_values($this->items);
        // Set the last item to last item if it is not.
        $lastitem = $this->items[$itemcount - 1];
        if (!$lastitem->is_last()) {
            $lastitem->set_last(true);
        }
    }

    /**
     * Removes the action from the last item of the boostnavbaritem.
     */
    protected function remove_last_item_action(): void {
        $item = end($this->items);
        $item->action = null;
        reset($this->items);
    }

    /**
     * Returns the second last navbar item. This is for use in the mobile view where we are showing just the second
     * last item in the breadcrumb navbar.
     *
     * @return breakcrumb_navigation_node|null The second last navigation node.
     */
    public function get_penultimate_item(): ?\breadcrumb_navigation_node {
        $number = $this->item_count() - 2;
        return ($number >= 0) ? $this->items[$number] : null;
    }

    /**
     * Remove items that have no actions associated with them and optionally remove items that are sections.
     *
     * The only exception is the last item in the list which may not have a link but needs to be displayed.
     *
     * @param bool $removesections Whether section items should be also removed (only applies when they have an action)
     */
    protected function remove_no_link_items(bool $removesections = true): void {
        foreach ($this->items as $key => $value) {
            if (!$value->is_last() &&
                    (!$value->has_action() || ($value->type == \navigation_node::TYPE_SECTION && $removesections))) {
                unset($this->items[$key]);
            }
        }
        $this->items = array_values($this->items);
    }

    /**
     * Remove breadcrumb items that already exist in a given navigation view.
     *
     * This method removes the breadcrumb items that have a text => action match in a given navigation view
     * (primary or secondary).
     *
     * @param view $navigationview The navigation view object.
     */
    protected function remove_items_that_exist_in_navigation(view $navigationview): void {
        // Loop through the navigation view items and create a 'text' => 'action' array which will be later used
        // to compare whether any of the breadcrumb items matches these pairs.
        $navigationviewitems = [];
        foreach ($navigationview->children as $child) {
            list($childtext, $childaction) = $this->get_node_text_and_action($child);
            if ($childaction) {
                $navigationviewitems[$childtext] = $childaction;
            }
        }
        // Loop through the breadcrumb items and if the item's 'text' and 'action' values matches with any of the
        // existing navigation view items, remove it from the breadcrumbs.
        foreach ($this->items as $item) {
            list($itemtext, $itemaction) = $this->get_node_text_and_action($item);
            if ($itemaction) {
                if (array_key_exists($itemtext, $navigationviewitems) &&
                        $navigationviewitems[$itemtext] === $itemaction) {
                    $this->remove($item->key);
                }
            }
        }
    }

    /**
     * Remove duplicate breadcrumb items.
     *
     * This method looks for breadcrumb items that have identical text and action values and removes the first item.
     */
    protected function remove_duplicate_items(): void {
        $taken = [];
        // Reverse the order of the items before filtering so that the first occurrence is removed instead of the last.
        $filtereditems = array_values(array_filter(array_reverse($this->items), function($item) use (&$taken) {
            list($itemtext, $itemaction) = $this->get_node_text_and_action($item);
            if ($itemaction) {
                if (array_key_exists($itemtext, $taken) && $taken[$itemtext] === $itemaction) {
                    return false;
                }
                $taken[$itemtext] = $itemaction;
            }
            return true;
        }));
        // Reverse back the order.
        $this->items = array_reverse($filtereditems);
    }

    /**
     * Helper function that returns an array of the text and the outputted action url (if exists) for a given
     * navigation node.
     *
     * @param navigation_node $node The navigation node object.
     * @return array
     */
    protected function get_node_text_and_action(navigation_node $node): array {
        $text = $node->text instanceof lang_string ? $node->text->out() : $node->text;
        $action = null;
        if ($node->has_action()) {
            if ($node->action instanceof moodle_url) {
                $action = $node->action->out();
            } else if ($node->action instanceof action_link) {
                $action = $node->action->url->out();
            } else {
                $action = $node->action;
            }
        }
        return [$text, $action];
    }
}
