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

namespace core\navigation;

use core\context\course as context_course;
use core\context_helper;
use core_course_category;
use core\context\coursecat as context_coursecat;
use core\output\action_link;
use core\output\pix_icon;
use core\url;
use moodle_page;

/**
 * Navbar class
 *
 * This class is used to manage the navbar, which is initialised from the navigation
 * object held by PAGE
 *
 * @package   core
 * @category  navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navbar extends navigation_node {
    /** @var bool A switch for whether the navbar is initialised or not */
    protected $initialised = false;
    /** @var mixed keys used to reference the nodes on the navbar */
    protected $keys = [];
    /** @var null|string content of the navbar */
    protected $content = null;
    /** @var moodle_page object the moodle page that this navbar belongs to */
    protected $page;
    /** @var bool A switch for whether to ignore the active navigation information */
    protected $ignoreactive = false;
    /** @var bool A switch to let us know if we are in the middle of an install */
    protected $duringinstall = false;
    /** @var bool A switch for whether the navbar has items */
    protected $hasitems = false;
    /** @var array An array of navigation nodes for the navbar */
    protected $items;
    /** @var array An array of child node objects */
    public $children = [];
    /** @var bool A switch for whether we want to include the root node in the navbar */
    public $includesettingsbase = false;
    /** @var breadcrumb_navigation_node[] $prependchildren */
    protected $prependchildren = [];

    /**
     * The almighty constructor
     *
     * @param moodle_page $page
     */
    public function __construct(moodle_page $page) {
        global $CFG;
        if (during_initial_install()) {
            $this->duringinstall = true;
            return;
        }

        $this->page = $page;
        $this->text = get_string('home');
        $this->shorttext = get_string('home');
        $this->action = new url($CFG->wwwroot);
        $this->nodetype = self::NODETYPE_BRANCH;
        $this->type = self::TYPE_SYSTEM;
    }

    /**
     * Quick check to see if the navbar will have items in.
     *
     * @return bool Returns true if the navbar will have items, false otherwise
     */
    public function has_items() {
        if ($this->duringinstall) {
            return false;
        } else if ($this->hasitems !== false) {
            return true;
        }
        $outcome = false;
        if (count($this->children) > 0 || count($this->prependchildren) > 0) {
            // There have been manually added items - there are definitely items.
            $outcome = true;
        } else if (!$this->ignoreactive) {
            // We will need to initialise the navigation structure to check if there are active items.
            $this->page->navigation->initialise($this->page);
            $outcome = ($this->page->navigation->contains_active_node() || $this->page->settingsnav->contains_active_node());
        }
        $this->hasitems = $outcome;
        return $outcome;
    }

    /**
     * Turn on/off ignore active
     *
     * @param bool $setting
     */
    public function ignore_active($setting = true) {
        $this->ignoreactive = ($setting);
    }

    /**
     * Gets a navigation node
     *
     * @param string|int $key for referencing the navbar nodes
     * @param int $type breadcrumb_navigation_node::TYPE_*
     * @return breadcrumb_navigation_node|bool
     */
    public function get($key, $type = null) {
        foreach ($this->children as &$child) {
            if ($child->key === $key && ($type == null || $type == $child->type)) {
                return $child;
            }
        }
        foreach ($this->prependchildren as &$child) {
            if ($child->key === $key && ($type == null || $type == $child->type)) {
                return $child;
            }
        }
        return false;
    }
    /**
     * Returns an array of breadcrumb_navigation_nodes that make up the navbar.
     *
     * @return array
     */
    public function get_items() {
        global $CFG;
        $items = [];
        // Make sure that navigation is initialised.
        if (!$this->has_items()) {
            return $items;
        }
        if ($this->items !== null) {
            return $this->items;
        }

        if (count($this->children) > 0) {
            // Add the custom children.
            $items = array_reverse($this->children);
        }

        // Check if navigation contains the active node.
        if (!$this->ignoreactive) {
            // We will need to ensure the navigation has been initialised.
            $this->page->navigation->initialise($this->page);
            // Now find the active nodes on both the navigation and settings.
            $navigationactivenode = $this->page->navigation->find_active_node();
            $settingsactivenode = $this->page->settingsnav->find_active_node();

            if ($navigationactivenode && $settingsactivenode) {
                // Parse a combined navigation tree.
                while ($settingsactivenode && $settingsactivenode->parent !== null) {
                    if (!$settingsactivenode->mainnavonly) {
                        $items[] = new breadcrumb_navigation_node($settingsactivenode);
                    }
                    $settingsactivenode = $settingsactivenode->parent;
                }
                if (!$this->includesettingsbase) {
                    // Removes the first node from the settings (root node) from the list.
                    array_pop($items);
                }
                while ($navigationactivenode && $navigationactivenode->parent !== null) {
                    if (!$navigationactivenode->mainnavonly) {
                        $items[] = new breadcrumb_navigation_node($navigationactivenode);
                    }
                    if (
                        !empty($CFG->navshowcategories) &&
                            $navigationactivenode->type === self::TYPE_COURSE &&
                            $navigationactivenode->parent->key === 'currentcourse'
                    ) {
                        foreach ($this->get_course_categories() as $item) {
                            $items[] = new breadcrumb_navigation_node($item);
                        }
                    }
                    $navigationactivenode = $navigationactivenode->parent;
                }
            } else if ($navigationactivenode) {
                // Parse the navigation tree to get the active node.
                while ($navigationactivenode && $navigationactivenode->parent !== null) {
                    if (!$navigationactivenode->mainnavonly) {
                        $items[] = new breadcrumb_navigation_node($navigationactivenode);
                    }
                    if (
                        !empty($CFG->navshowcategories) &&
                            $navigationactivenode->type === self::TYPE_COURSE &&
                            $navigationactivenode->parent->key === 'currentcourse'
                    ) {
                        foreach ($this->get_course_categories() as $item) {
                            $items[] = new breadcrumb_navigation_node($item);
                        }
                    }
                    $navigationactivenode = $navigationactivenode->parent;
                }
            } else if ($settingsactivenode) {
                // Parse the settings navigation to get the active node.
                while ($settingsactivenode && $settingsactivenode->parent !== null) {
                    if (!$settingsactivenode->mainnavonly) {
                        $items[] = new breadcrumb_navigation_node($settingsactivenode);
                    }
                    $settingsactivenode = $settingsactivenode->parent;
                }
            }
        }

        $items[] = new breadcrumb_navigation_node([
            'text' => $this->page->navigation->text,
            'shorttext' => $this->page->navigation->shorttext,
            'key' => $this->page->navigation->key,
            'action' => $this->page->navigation->action,
        ]);

        if (count($this->prependchildren) > 0) {
            // Add the custom children.
            $items = array_merge($items, array_reverse($this->prependchildren));
        }

        $last = reset($items);
        if ($last) {
            $last->set_last(true);
        }
        $this->items = array_reverse($items);
        return $this->items;
    }

    /**
     * Get the list of categories leading to this course.
     *
     * This function is used by {@link navbar::get_items()} to add back the "courses"
     * node and category chain leading to the current course.  Note that this is only ever
     * called for the current course, so we don't need to bother taking in any parameters.
     *
     * @return array
     */
    private function get_course_categories() {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        $categories = [];
        $cap = 'moodle/category:viewhiddencategories';
        $showcategories = !core_course_category::is_simple_site();

        if ($showcategories) {
            foreach ($this->page->categories as $category) {
                $context = context_coursecat::instance($category->id);
                if (!core_course_category::can_view_category($category)) {
                    continue;
                }

                $displaycontext = context_helper::get_navigation_filter_context($context);
                $url = new url('/course/index.php', ['categoryid' => $category->id]);
                $name = format_string($category->name, true, ['context' => $displaycontext]);
                $categorynode = breadcrumb_navigation_node::create($name, $url, self::TYPE_CATEGORY, null, $category->id);
                if (!$category->visible) {
                    $categorynode->hidden = true;
                }
                $categories[] = $categorynode;
            }
        }

        // Don't show the 'course' node if enrolled in this course.
        $coursecontext = context_course::instance($this->page->course->id);
        if (!is_enrolled($coursecontext, null, '', true)) {
            $courses = $this->page->navigation->get('courses');
            if (!$courses) {
                // Courses node may not be present.
                $courses = breadcrumb_navigation_node::create(
                    get_string('courses'),
                    new url('/course/index.php'),
                    self::TYPE_CONTAINER
                );
            }
            $categories[] = $courses;
        }

        return $categories;
    }

    /**
     * Add a new breadcrumb_navigation_node to the navbar, overrides parent::add
     *
     * This function overrides {@link breadcrumb_navigation_node::add()} so that we can change
     * the way nodes get added to allow us to simply call add and have the node added to the
     * end of the navbar
     *
     * @param string $text
     * @param string|url|action_link $action An action to associate with this node.
     * @param int $type One of navigation_node::TYPE_*
     * @param string $shorttext
     * @param string|int $key A key to identify this node with. Key + type is unique to a parent.
     * @param pix_icon $icon An optional icon to use for this node.
     * @return navigation_node
     */
    public function add($text, $action = null, $type = self::TYPE_CUSTOM, $shorttext = null, $key = null, ?pix_icon $icon = null) {
        if ($this->content !== null) {
            debugging('Nav bar items must be printed before $OUTPUT->header() has been called', DEBUG_DEVELOPER);
        }

        // Properties array used when creating the new navigation node.
        $itemarray = [
            'text' => $text,
            'type' => $type,
        ];
        // Set the action if one was provided.
        if ($action !== null) {
            $itemarray['action'] = $action;
        }
        // Set the shorttext if one was provided.
        if ($shorttext !== null) {
            $itemarray['shorttext'] = $shorttext;
        }
        // Set the icon if one was provided.
        if ($icon !== null) {
            $itemarray['icon'] = $icon;
        }
        // Default the key to the number of children if not provided.
        if ($key === null) {
            $key = count($this->children);
        }
        // Set the key.
        $itemarray['key'] = $key;
        // Set the parent to this node.
        $itemarray['parent'] = $this;
        // Add the child using the navigation_node_collections add method.
        $this->children[] = new breadcrumb_navigation_node($itemarray);
        return $this;
    }

    /**
     * Prepends a new navigation_node to the start of the navbar
     *
     * @param string $text
     * @param string|url|action_link $action An action to associate with this node.
     * @param int $type One of navigation_node::TYPE_*
     * @param string $shorttext
     * @param string|int $key A key to identify this node with. Key + type is unique to a parent.
     * @param pix_icon $icon An optional icon to use for this node.
     * @return navigation_node
     */
    public function prepend(
        $text,
        $action = null,
        $type = self::TYPE_CUSTOM,
        $shorttext = null,
        $key = null,
        ?pix_icon $icon = null,
    ) {
        if ($this->content !== null) {
            debugging('Nav bar items must be printed before $OUTPUT->header() has been called', DEBUG_DEVELOPER);
        }
        // Properties array used when creating the new navigation node.
        $itemarray = [
            'text' => $text,
            'type' => $type,
        ];
        // Set the action if one was provided.
        if ($action !== null) {
            $itemarray['action'] = $action;
        }
        // Set the shorttext if one was provided.
        if ($shorttext !== null) {
            $itemarray['shorttext'] = $shorttext;
        }
        // Set the icon if one was provided.
        if ($icon !== null) {
            $itemarray['icon'] = $icon;
        }
        // Default the key to the number of children if not provided.
        if ($key === null) {
            $key = count($this->children);
        }
        // Set the key.
        $itemarray['key'] = $key;
        // Set the parent to this node.
        $itemarray['parent'] = $this;
        // Add the child node to the prepend list.
        $this->prependchildren[] = new breadcrumb_navigation_node($itemarray);
        return $this;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(navbar::class, \navbar::class);
