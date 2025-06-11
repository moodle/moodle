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

namespace core\output;

use core\context\system as context_system;
use moodle_url;
use stdClass;

/**
 * Custom menu item
 *
 * This class is used to represent one item within a custom menu that may or may
 * not have children.
 *
 * @copyright 2010 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class custom_menu_item implements renderable, templatable {
    /**
     * @var string The text to show for the item
     */
    protected $text;

    /**
     * @var string A title to apply to the item. By default the text
     */
    protected $title;

    /**
     * @var int A sort order for the item, not necessary if you order things in
     * the CFG var.
     */
    protected $sort;

    /**
     * @var array A array in which to store children this item has.
     */
    protected $children = [];

    /**
     * @var int A reference to the sort var of the last child that was added
     */
    protected $lastsort = 0;

    /**
     * Constructs the new custom menu item
     *
     * @param string $text
     * @param null|moodle_url $url A moodle url to apply as the link for this item [Optional]
     * @param string $title A title to apply to this item [Optional]
     * @param int $sort A sort or to use if we need to sort differently [Optional]
     * @param null|custom_menu_item $parent A reference to the parent custom_menu_item this child
     *        belongs to, only if the child has a parent. [Optional]
     * @param array $attributes Array of other HTML attributes for the custom menu item.
     */
    public function __construct(
        $text,
        /** @var moodle_url The link to give the icon if it has no children */
        protected ?moodle_url $url = null,
        $title = null,
        $sort = null,
        /**
         * @var custom_menu_item A reference to the parent for this item or NULL if
         * it is a top level item
         */
        protected ?custom_menu_item $parent = null,
        /** @var array Array of other HTML attributes for the custom menu item. */
        protected array $attributes = [],
    ) {

        // Use class setter method for text to ensure it's always a string type.
        $this->set_text($text);

        $this->title = $title;
        $this->sort = (int)$sort;
    }

    /**
     * Adds a custom menu item as a child of this node given its properties.
     *
     * @param string $text
     * @param null|moodle_url $url
     * @param string $title
     * @param int $sort
     * @param array $attributes Array of other HTML attributes for the custom menu item.
     * @return custom_menu_item
     */
    public function add(
        $text,
        ?moodle_url $url = null,
        $title = null,
        $sort = null,
        $attributes = [],
    ) {
        $key = count($this->children);
        if (empty($sort)) {
            $sort = $this->lastsort + 1;
        }
        $this->children[$key] = new custom_menu_item($text, $url, $title, $sort, $this, $attributes);
        $this->lastsort = (int)$sort;
        return $this->children[$key];
    }

    /**
     * Removes a custom menu item that is a child or descendant to the current menu.
     *
     * Returns true if child was found and removed.
     *
     * @param custom_menu_item $menuitem
     * @return bool
     */
    public function remove_child(custom_menu_item $menuitem) {
        $removed = false;
        if (($key = array_search($menuitem, $this->children)) !== false) {
            unset($this->children[$key]);
            $this->children = array_values($this->children);
            $removed = true;
        } else {
            foreach ($this->children as $child) {
                if ($removed = $child->remove_child($menuitem)) {
                    break;
                }
            }
        }
        return $removed;
    }

    /**
     * Returns the text for this item
     * @return string
     */
    public function get_text() {
        return $this->text;
    }

    /**
     * Returns the url for this item
     * @return moodle_url
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * Returns the title for this item
     * @return string
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Sorts and returns the children for this item
     * @return array
     */
    public function get_children() {
        $this->sort();
        return $this->children;
    }

    /**
     * Gets the sort order for this child
     * @return int
     */
    public function get_sort_order() {
        return $this->sort;
    }

    /**
     * Gets the parent this child belong to
     * @return custom_menu_item
     */
    public function get_parent() {
        return $this->parent;
    }

    /**
     * Sorts the children this item has
     */
    public function sort() {
        usort($this->children, ['custom_menu', 'sort_custom_menu_items']);
    }

    /**
     * Returns true if this item has any children
     * @return bool
     */
    public function has_children() {
        return (count($this->children) > 0);
    }

    /**
     * Sets the text for the node
     * @param string $text
     */
    public function set_text($text) {
        $this->text = (string)$text;
    }

    /**
     * Sets the title for the node
     * @param string $title
     */
    public function set_title($title) {
        $this->title = (string)$title;
    }

    /**
     * Sets the url for the node
     * @param moodle_url $url
     */
    public function set_url(moodle_url $url) {
        $this->url = $url;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $syscontext = context_system::instance();

        $context = new stdClass();
        $context->moremenuid = uniqid();
        $context->text = \core_external\util::format_string($this->text, $syscontext->id);
        $context->url = $this->url ? $this->url->out() : null;
        // No need for the title if it's the same with text.
        if ($this->text !== $this->title) {
            // Show the title attribute only if it's different from the text.
            $context->title = \core_external\util::format_string($this->title, $syscontext->id);
        }
        $context->sort = $this->sort;
        if (!empty($this->attributes)) {
            $context->attributes = $this->attributes;
        }
        $context->children = [];
        if (preg_match("/^#+$/", $this->text)) {
            $context->divider = true;
        }
        $context->haschildren = !empty($this->children) && (count($this->children) > 0);
        foreach ($this->children as $child) {
            $child = $child->export_for_template($output);
            array_push($context->children, $child);
        }

        return $context;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(custom_menu_item::class, \custom_menu_item::class);
