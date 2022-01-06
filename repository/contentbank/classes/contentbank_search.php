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
 * Utility class for searching of content bank files.
 *
 * @package    repository_contentbank
 * @copyright  2020 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_contentbank;

/**
 * Represents the content bank search related functionality.
 *
 * @package    repository_contentbank
 * @copyright  2020 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contentbank_search {

    /**
     * Generate and return content nodes for all content bank files that match the search criteria
     * and can be viewed/accessed by the user.
     *
     * @param string $search The search string
     * @return array[] The array containing all content file nodes that match the search criteria. Each content node is
     *                 an array with keys: shorttitle, title, datemodified, datecreated, author, license, isref, source,
     *                 icon, thumbnail.
     */
    public static function get_search_contents(string $search): array {
        $contentbank = new \core_contentbank\contentbank();
        // Return all content bank content that matches the search criteria and can be viewed/accessed by the user.
        $contents = $contentbank->search_contents($search);
        return array_reduce($contents, function($list, $content) {
            $contentcontext = \context::instance_by_id($content->get_content()->contextid);
            $browser = \repository_contentbank\helper::get_contentbank_browser($contentcontext);
            // If the user can access the content and content node can be created, add the node into the
            // search results list.
            if ($browser->can_access_content() &&
                    $contentnode = \repository_contentbank\helper::create_contentbank_content_node($content)) {
                $list[] = $contentnode;
            }
            return $list;
        }, []);
    }
}
