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

namespace core_admin\setting\tree;

use core\exception\coding_exception;
use core_admin\local\settings\linkable_settings_page;

/**
 * The object used to represent folders (a.k.a. categories) in the admin tree block.
 *
 * Each category object contains a number of part_of_admin_tree objects.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category implements
    linkable_settings_page,
    parentable_part_of_admin_tree
{
    /** @var part_of_admin_tree[] An array of part_of_admin_tree objects that are this object's children */
    protected $children;
    /** @var string An internal name for this category. Must be unique amongst ALL part_of_admin_tree objects */
    public $name;
    /** @var string The displayed name for this category. Usually obtained through get_string() */
    public $visiblename;
    /** @var bool Should this category be hidden in admin tree block? */
    public $hidden;
    /** @var mixed Either a string or an array or strings */
    public $path;
    /** @var mixed Either a string or an array or strings */
    public $visiblepath;

    /** @var array fast lookup category cache, all categories of one tree point to one cache */
    protected $category_cache;

    /** @var bool If set to true children will be sorted when calling {@link category::get_children()} */
    protected $sort = false;
    /** @var bool If set to true children will be sorted in ascending order. */
    protected $sortasc = true;
    /** @var bool If set to true sub categories and pages will be split and then sorted.. */
    protected $sortsplit = true;
    /** @var bool $sorted True if the children have been sorted and don't need resorting */
    protected $sorted = false;

    /**
     * Constructor for an empty admin category
     *
     * @param string $name The internal name for this category. Must be unique amongst ALL part_of_admin_tree objects
     * @param string $visiblename The displayed named for this category. Usually obtained through get_string()
     * @param bool $hidden hide category in admin tree block, defaults to false
     */
    public function __construct($name, $visiblename, $hidden=false) {
        $this->children    = array();
        $this->name        = $name;
        $this->visiblename = $visiblename;
        $this->hidden      = $hidden;
    }

    /**
     * Get the URL to view this settings page.
     *
     * @return moodle_url
     */
    public function get_settings_page_url(): \moodle_url {
        return new \moodle_url(
            '/admin/category.php',
            [
                'category' => $this->name,
            ]
        );
    }

    /**
     * Returns a reference to the part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want.
     * @param bool $findpath initialize path and visiblepath arrays
     * @return mixed A reference to the object with internal name $name if found, otherwise a reference to NULL.
     *                  defaults to false
     */
    public function locate($name, $findpath=false) {
        if (!isset($this->category_cache[$this->name])) {
            // somebody much have purged the cache
            $this->category_cache[$this->name] = $this;
        }

        if ($this->name == $name) {
            if ($findpath) {
                $this->visiblepath[] = $this->visiblename;
                $this->path[]        = $this->name;
            }
            return $this;
        }

        // quick category lookup
        if (!$findpath and isset($this->category_cache[$name])) {
            return $this->category_cache[$name];
        }

        $return = NULL;
        foreach($this->children as $childid=>$unused) {
            if ($return = $this->children[$childid]->locate($name, $findpath)) {
                break;
            }
        }

        if (!is_null($return) and $findpath) {
            $return->visiblepath[] = $this->visiblename;
            $return->path[]        = $this->name;
        }

        return $return;
    }

    /**
     * Search using query
     *
     * @param string query
     * @return mixed array-object structure of found settings and pages
     */
    public function search($query) {
        $result = array();
        foreach ($this->get_children() as $child) {
            $subsearch = $child->search($query);
            if (!is_array($subsearch)) {
                debugging('Incorrect search result from '.$child->name);
                continue;
            }
            $result = array_merge($result, $subsearch);
        }
        return $result;
    }

    /**
     * Removes part_of_admin_tree object with internal name $name.
     *
     * @param string $name The internal name of the object we want to remove.
     * @return bool success
     */
    public function prune($name) {

        if ($this->name == $name) {
            return false;  //can not remove itself
        }

        foreach($this->children as $precedence => $child) {
            if ($child->name == $name) {
                // clear cache and delete self
                while($this->category_cache) {
                    // delete the cache, but keep the original array address
                    array_pop($this->category_cache);
                }
                unset($this->children[$precedence]);
                return true;
            } else if ($this->children[$precedence]->prune($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Adds a part_of_admin_tree to a child or grandchild (or great-grandchild, and so forth) of this object.
     *
     * By default the new part of the tree is appended as the last child of the parent. You
     * can specify a sibling node that the new part should be prepended to. If the given
     * sibling is not found, the part is appended to the end (as it would be by default) and
     * a developer debugging message is displayed.
     *
     * @throws coding_exception if the $beforesibling is empty string or is not string at all.
     * @param string $destinationame The internal name of the immediate parent that we want for $something.
     * @param mixed $something A part_of_admin_tree or setting instance to be added.
     * @param string $beforesibling The name of the parent's child the $something should be prepended to.
     * @return bool True if successfully added, false if $something can not be added.
     */
    public function add($parentname, $something, $beforesibling = null) {
        global $CFG;

        $parent = $this->locate($parentname);
        if (is_null($parent)) {
            debugging('parent does not exist!');
            return false;
        }

        if ($something instanceof \part_of_admin_tree) {
            if (!($parent instanceof \parentable_part_of_admin_tree)) {
                debugging('error - parts of tree can be inserted only into parentable parts');
                return false;
            }
            if ($CFG->debugdeveloper && !is_null($this->locate($something->name))) {
                // The name of the node is already used, simply warn the developer that this should not happen.
                // It is intentional to check for the debug level before performing the check.
                debugging('Duplicate admin page name: ' . $something->name, DEBUG_DEVELOPER);
            }
            if (is_null($beforesibling)) {
                // Append $something as the parent's last child.
                $parent->children[] = $something;
            } else {
                if (!is_string($beforesibling) or trim($beforesibling) === '') {
                    throw new coding_exception('Unexpected value of the beforesibling parameter');
                }
                // Try to find the position of the sibling.
                $siblingposition = null;
                foreach ($parent->children as $childposition => $child) {
                    if ($child->name === $beforesibling) {
                        $siblingposition = $childposition;
                        break;
                    }
                }
                if (is_null($siblingposition)) {
                    debugging('Sibling '.$beforesibling.' not found', DEBUG_DEVELOPER);
                    $parent->children[] = $something;
                } else {
                    $parent->children = array_merge(
                        array_slice($parent->children, 0, $siblingposition),
                        array($something),
                        array_slice($parent->children, $siblingposition)
                    );
                }
            }
            if ($something instanceof category) {
                if (isset($this->category_cache[$something->name])) {
                    debugging('Duplicate admin category name: '.$something->name);
                } else {
                    $this->category_cache[$something->name] = $something;
                    $something->category_cache =& $this->category_cache;
                    foreach ($something->children as $child) {
                        // Just in case somebody already added subcategories.
                        if ($child instanceof category) {
                            if (isset($this->category_cache[$child->name])) {
                                debugging('Duplicate admin category name: '.$child->name);
                            } else {
                                $this->category_cache[$child->name] = $child;
                                $child->category_cache =& $this->category_cache;
                            }
                        }
                    }
                }
            }
            return true;

        } else {
            debugging('error - can not add this element');
            return false;
        }

    }

    /**
     * Checks if the user has access to anything in this category.
     *
     * @return bool True if the user has access to at least one child in this category, false otherwise.
     */
    public function check_access() {
        foreach ($this->children as $child) {
            if ($child->check_access()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is this category hidden in admin tree block?
     *
     * @return bool True if hidden
     */
    public function is_hidden() {
        return $this->hidden;
    }

    /**
     * Show we display Save button at the page bottom?
     * @return bool
     */
    public function show_save() {
        foreach ($this->children as $child) {
            if ($child->show_save()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sets sorting on this category.
     *
     * Please note this function doesn't actually do the sorting.
     * It can be called anytime.
     * Sorting occurs when the user calls get_children.
     * Code using the children array directly won't see the sorted results.
     *
     * @param bool $sort If set to true children will be sorted, if false they won't be.
     * @param bool $asc If true sorting will be ascending, otherwise descending.
     * @param bool $split If true we sort pages and sub categories separately.
     */
    public function set_sorting($sort, $asc = true, $split = true) {
        $this->sort = (bool)$sort;
        $this->sortasc = (bool)$asc;
        $this->sortsplit = (bool)$split;
    }

    /**
     * Returns the children associated with this category.
     *
     * @return part_of_admin_tree[]
     */
    public function get_children() {
        // If we should sort and it hasn't already been sorted.
        if ($this->sort && !$this->sorted) {
            if ($this->sortsplit) {
                $categories = array();
                $pages = array();
                foreach ($this->children as $child) {
                    if ($child instanceof category) {
                        $categories[] = $child;
                    } else {
                        $pages[] = $child;
                    }
                }
                \core_collator::asort_objects_by_property($categories, 'visiblename');
                \core_collator::asort_objects_by_property($pages, 'visiblename');
                if (!$this->sortasc) {
                    $categories = array_reverse($categories);
                    $pages = array_reverse($pages);
                }
                $this->children = array_merge($pages, $categories);
            } else {
                \core_collator::asort_objects_by_property($this->children, 'visiblename');
                if (!$this->sortasc) {
                    $this->children = array_reverse($this->children);
                }
            }
            $this->sorted = true;
        }
        return $this->children;
    }

    /**
     * Magically gets a property from this object.
     *
     * @param $property
     * @return part_of_admin_tree[]
     * @throws coding_exception
     */
    public function __get($property) {
        if ($property === 'children') {
            return $this->get_children();
        }
        throw new coding_exception('Invalid property requested.');
    }

    /**
     * Magically sets a property against this object.
     *
     * @param string $property
     * @param mixed $value
     * @throws coding_exception
     */
    public function __set($property, $value) {
        if ($property === 'children') {
            $this->sorted = false;
            $this->children = $value;
        } else {
            throw new coding_exception('Invalid property requested.');
        }
    }

    /**
     * Checks if an inaccessible property is set.
     *
     * @param string $property
     * @return bool
     * @throws coding_exception
     */
    public function __isset($property) {
        if ($property === 'children') {
            return isset($this->children);
        }
        throw new coding_exception('Invalid property requested.');
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(category::class, \admin_category::class);
