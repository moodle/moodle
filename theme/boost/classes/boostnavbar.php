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

        // Don't show the navigation if we are in the course context.
        if ($this->page->context->contextlevel == CONTEXT_COURSE) {
            $this->clear_items();
            return;
        }

        $this->remove('myhome'); // Dashboard.
        $this->remove('home');

        // Remove 'My courses' if we are in the module context.
        if ($this->page->context->contextlevel == CONTEXT_MODULE) {
            $this->remove('mycourses');
        }

        if (!is_null($this->get_item('root'))) { // We are in site administration.
            // Remove the 'Site administration' navbar node as it already exists in the primary navigation menu.
            $this->remove('root');
            // Loop through the remaining navbar nodes and remove the ones that already exist in the secondary
            // navigation menu.
            foreach ($this->items as $item) {
                if ($PAGE->secondarynav->get($item->key)) {
                    $this->remove($item->key);
                }
            }
        }

        // Set the designated one path for courses.
        $mycoursesnode = $this->get_item('mycourses');
        if (!is_null($mycoursesnode)) {
            $url = new \moodle_url('/my/courses.php');
            $mycoursesnode->action = $url;
            $mycoursesnode->text = get_string('mycourses');
        }

        $this->remove_no_link_items();

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
     */
    protected function remove($itemkey): void {

        $itemfound = false;
        foreach ($this->items as $key => $item) {
            if ($item->key === $itemkey) {
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
     * Remove items that are categories or have no actions associated with them.
     *
     * The only exception is the last item in the list which may not have a link but needs to be displayed.
     */
    protected function remove_no_link_items(): void {
        foreach ($this->items as $key => $value) {
            if (!$value->is_last() && (!$value->has_action() || $value->type == \navigation_node::TYPE_SECTION)) {
                unset($this->items[$key]);
            }
        }
        $this->items = array_values($this->items);
    }
}
