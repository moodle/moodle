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
 * Utility class for browsing of content bank files.
 *
 * @package    repository_contentbank
 * @copyright  2020 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_contentbank\browser;

/**
 * Base class for the content bank browsers.
 *
 * @package    repository_contentbank
 * @copyright  2020 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class contentbank_browser {

    /** @var \context The current context. */
    protected $context;

    /**
     * Get all content nodes in the current context which can be viewed/accessed by the user.
     *
     * @return array[] The array containing all nodes which can be viewed/accessed by the user in the current context
     */
    public function get_content(): array {
        return array_merge($this->get_context_folders(), $this->get_contentbank_content());
    }

    /**
     * Generate the full navigation to the current node.
     *
     * @return array[] The array containing the path to each node in the navigation.
     *                 Each navigation node is an array with keys: name, path.
     */
    public function get_navigation(): array {
        // Get the current navigation node.
        $currentnavigationnode = \repository_contentbank\helper::create_navigation_node($this->context);
        $navigationnodes = [$currentnavigationnode];
        // Get the parent content bank browser.
        $parent = $this->get_parent();
        // Prepend parent navigation node in the navigation nodes array until there is no existing parent.
        while ($parent !== null) {
            $parentnavigationnode = \repository_contentbank\helper::create_navigation_node($parent->context);
            array_unshift($navigationnodes, $parentnavigationnode);
            $parent = $parent->get_parent();
        }
        return $navigationnodes;
    }

    /**
     * The required condition to enable the user to view/access the content bank content in this context.
     *
     * @return bool Whether the user can view/access the content bank content in the context
     */
    abstract public function can_access_content(): bool;

    /**
     * Define the allowed child context levels.
     *
     * @return int[] The array containing the relevant child context levels
     */
    abstract protected function allowed_child_context_levels(): array;

    /**
     * Get the relevant child contexts.
     *
     * @return \context[] The array containing the relevant, next-level children contexts
     */
    protected function get_child_contexts(): array {
        global $DB;

        if (empty($allowedcontextlevels = $this->allowed_child_context_levels())) {
            // Early return if there aren't any defined child context levels.
            return [];
        }

        list($contextlevelsql, $params) = $DB->get_in_or_equal($allowedcontextlevels, SQL_PARAMS_NAMED);
        $pathsql = $DB->sql_like('path', ':path', false, false);

        $select = "contextlevel {$contextlevelsql}
                   AND {$pathsql}
                   AND depth = :depth";

        $params['path'] = "{$this->context->path}/%";
        $params['depth'] = $this->context->depth + 1;

        $childcontexts = $DB->get_records_select('context', $select, $params);

        return array_map(function($childcontext) {
            return \context::instance_by_id($childcontext->id);
        }, $childcontexts);
    }

    /**
     * Get the content bank browser class of the parent context. Currently used to generate the navigation path.
     *
     * @return contentbank_browser|null The content bank browser of the parent context
     */
    private function get_parent(): ?self {
        if ($parentcontext = $this->context->get_parent_context()) {
            return \repository_contentbank\helper::get_contentbank_browser($parentcontext);
        }
        return null;
    }

    /**
     * Generate folder nodes for the relevant child contexts which can be accessed/viewed by the user.
     *
     * @return array[] The array containing the context folder nodes where each folder node is an array with keys:
     *                 title, datemodified, datecreated, path, thumbnail, children.
     */
    private function get_context_folders(): array {
        // Get all relevant child contexts.
        $children = $this->get_child_contexts();
        // Return all child context folder nodes which can be accessed by the user following the defined conditions
        // in can_access_content().
        return array_reduce($children, function ($list, $child) {
            $browser = \repository_contentbank\helper::get_contentbank_browser($child);
            if ($browser->can_access_content()) {
                $name = $child->get_context_name(false);
                $path = base64_encode(json_encode(['contextid' => $child->id]));
                $list[] = \repository_contentbank\helper::create_context_folder_node($name, $path);
            }
            return $list;
        }, []);
    }

    /**
     * Generate nodes for the content bank content in the current context which can be accessed/viewed by the user.
     *
     * @return array[] The array containing the content nodes where each content node is an array with keys:
     *                 shorttitle, title, datemodified, datecreated, author, license, isref, source, icon, thumbnail.
     */
    private function get_contentbank_content(): array {
        $cb = new \core_contentbank\contentbank();
        // Get all content bank files in the current context.
        $contents = $cb->search_contents(null, $this->context->id);
        // Return all content bank content nodes from the current context which can be accessed by the user following
        // the defined conditions in can_access_content().
        return array_reduce($contents, function($list, $content) {
            if ($this->can_access_content() &&
                    $contentnode = \repository_contentbank\helper::create_contentbank_content_node($content)) {
                $list[] = $contentnode;
            }
            return $list;
        }, []);
    }
}
