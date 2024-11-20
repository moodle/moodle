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

namespace repository_googledocs;

/**
 * Base class for presenting the googledocs repository contents.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class googledocs_content {

    /** @var rest The rest API object. */
    protected $service;

    /** @var string The current path. */
    protected $path;

    /** @var bool Whether sorting should be applied to the fetched content. */
    protected $sortcontent;

    /**
     * Constructor.
     *
     * @param rest $service The rest API object
     * @param string $path The current path
     * @param bool $sortcontent Whether sorting should be applied to the content
     */
    public function __construct(rest $service, string $path, bool $sortcontent = true) {
        $this->service = $service;
        $this->path = $path;
        $this->sortcontent = $sortcontent;
    }

    /**
     * Generate and return an array containing all repository node (files and folders) arrays for the existing content
     * based on the path or search query.
     *
     * @param string $query The search query
     * @param callable $isaccepted The callback function which determines whether a given file should be displayed
     *                             or filtered based on the existing file restrictions
     * @return array The array containing the repository content node arrays
     */
    public function get_content_nodes(string $query, callable $isaccepted): array {
        $files = [];
        $folders = [];

        foreach ($this->get_contents($query) as $gdcontent) {
            $node = helper::get_node($gdcontent, $this->path);
            // Create the repository node array.
            $nodearray = $node->create_node_array();
            // If the repository node array was successfully generated and the type of the content is accepted,
            // add it to the repository content nodes array.
            if ($nodearray && $isaccepted($nodearray)) {
                // Group the content nodes by type (files and folders). Generate unique array keys for each content node
                // which will be later used by the sorting function. Note: Using the item id along with the name as key
                // of the array because Google Drive allows files and folders with identical names.
                if (isset($nodearray['source'])) { // If the content node has a source attribute, it is a file node.
                    $files["{$nodearray['title']}{$nodearray['id']}"] = $nodearray;
                } else {
                    $folders["{$nodearray['title']}{$nodearray['id']}"] = $nodearray;
                }
            }
        }
        // If sorting is required, order the results alphabetically by their array keys.
        if ($this->sortcontent) {
            \core_collator::ksort($files, \core_collator::SORT_STRING);
            \core_collator::ksort($folders, \core_collator::SORT_STRING);
        }

        return array_merge(array_values($folders), array_values($files));
    }

    /**
     * Build the navigation (breadcrumb) from a given path.
     *
     * @return array Array containing name and path of each navigation node
     */
    public function get_navigation(): array {
        $nav = [];
        $navtrail = '';
        $pathnodes = explode('/', $this->path);

        foreach ($pathnodes as $node) {
            list($id, $name) = helper::explode_node_path($node);
            $name = empty($name) ? $id : $name;
            $nav[] = [
                'name' => $name,
                'path' => helper::build_node_path($id, $name, $navtrail),
            ];
            $tmp = end($nav);
            $navtrail = $tmp['path'];
        }

        return $nav;
    }

    /**
     * Returns all relevant contents (files and folders) based on the given path or search query.
     *
     * @param string $query The search query
     * @return array The array containing the contents
     */
    abstract protected function get_contents(string $query): array;
}
