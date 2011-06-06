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
 * @package moodlecore
 * @subpackage xml
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/backup/util/xml/parser/processors/simplified_parser_processor.class.php');

/**
 * Abstract xml parser processor able to group chunks as configured
 * and dispatch them to other arbitrary methods
 *
 * This @progressive_parser_processor handles the requested paths,
 * allowing to group information under any of them, dispatching them
 * to the methods specified
 *
 * Note memory increases as you group more and more paths, so use it for
 * well-known structures being smaller enough (never to group MBs into one
 * in-memory structure)
 *
 * TODO: Complete phpdocs
 */
abstract class grouped_parser_processor extends simplified_parser_processor {

    protected $groupedpaths; // Paths we are requesting grouped
    protected $currentdata;  // Where we'll be acummulating data

    public function __construct(array $paths = array()) {
        $this->groupedpaths = array();
        $this->currentdata = null;
        parent::__construct($paths);
    }

    public function add_path($path, $grouped = false) {
        if ($grouped) {
            // Check there is no parent in the branch being grouped
            if ($found = $this->grouped_parent_exists($path)) {
                $a = new stdclass();
                $a->path = $path;
                $a->parent = $found;
                throw new progressive_parser_exception('xml_grouped_parent_found', $a);
            }
            // Check there is no child in the branch being grouped
            if ($found = $this->grouped_child_exists($path)) {
                $a = new stdclass();
                $a->path = $path;
                $a->child = $found;
                throw new progressive_parser_exception('xml_grouped_child_found', $a);
            }
            $this->groupedpaths[] = $path;
        }
        parent::add_path($path);
    }

    /**
     * The parser fires this each time one path is going to be parsed
     *
     * @param string $path xml path which parsing has started
     */
    public function before_path($path) {
        if (!$this->grouped_parent_exists($path)) {
            parent::before_path($path);
        }
    }

    /**
     * The parser fires this each time one path has been parsed
     *
     * @param string $path xml path which parsing has ended
     */
    public function after_path($path) {
        // Have finished one grouped path, dispatch it
        if ($this->path_is_grouped($path)) {
            // Any accumulated information must be in
            // currentdata, properly built
            $data = $this->currentdata[$path];
            unset($this->currentdata[$path]);
            // TODO: If running under DEBUG_DEVELOPER notice about >1MB grouped chunks
            $this->dispatch_chunk($data);
        }
        // Normal notification of path end
        // Only if path is selected and not child of grouped
        if (!$this->grouped_parent_exists($path)) {
            parent::after_path($path);
        }
    }

// Protected API starts here

    /**
     * Override this method so grouping will be happening here
     * also deciding between accumulating/dispatching
     */
    protected function postprocess_chunk($data) {
        $path = $data['path'];
        // If the chunk is a grouped one, simply put it into currentdata
        if ($this->path_is_grouped($path)) {
            $this->currentdata[$path] = $data;

        // If the chunk is child of grouped one, add it to currentdata
        } else if ($grouped = $this->grouped_parent_exists($path)) {
            $this->build_currentdata($grouped, $data);
            $this->chunks--; // not counted, as it's accumulated

        // No grouped nor child of grouped, dispatch it
        } else {
            $this->dispatch_chunk($data);
        }
    }

    protected function path_is_grouped($path) {
        return in_array($path, $this->groupedpaths);
    }

    /**
     * Function that will look for any grouped
     * parent for the given path, returning it if found,
     * false if not
     */
    protected function grouped_parent_exists($path) {
        $parentpath = progressive_parser::dirname($path);
        while ($parentpath != '/') {
            if ($this->path_is_grouped($parentpath)) {
                return $parentpath;
            }
            $parentpath = progressive_parser::dirname($parentpath);
        }
        return false;
    }

    /**
     * Function that will look for any grouped
     * child for the given path, returning it if found,
     * false if not
     */
    protected function grouped_child_exists($path) {
        $childpath = $path . '/';
        foreach ($this->groupedpaths as $groupedpath) {
            if (strpos($groupedpath, $childpath) === 0) {
                return $groupedpath;
            }
        }
        return false;
    }

    /**
     * This function will accumulate the chunk into the specified
     * grouped element for later dispatching once it is complete
     */
    protected function build_currentdata($grouped, $data) {
        // Check the grouped already exists into currentdata
        if (!array_key_exists($grouped, $this->currentdata)) {
            $a = new stdclass();
            $a->grouped = $grouped;
            $a->child = $data['path'];
            throw new progressive_parser_exception('xml_cannot_add_to_grouped', $a);
        }
        $this->add_missing_sub($grouped, $data['path'], $data['tags']);
    }

    /**
     * Add non-existing subarray elements
     */
    protected function add_missing_sub($grouped, $path, $tags) {

        // Remember tag being processed
        $processedtag = basename($path);

        $info =& $this->currentdata[$grouped]['tags'];
        $hierarchyarr = explode('/', str_replace($grouped . '/', '', $path));

        $previouselement = '';
        $currentpath = '';

        foreach ($hierarchyarr as $index => $element) {

            $currentpath = $currentpath . '/' . $element;

            // If element is already set and it's not
            // the processed one (with tags) fast move the $info
            // pointer and continue
            if ($element !== $processedtag && isset($info[$element])) {
                $previouselement = $element;
                $info =& $info[$element];
                continue;
            }

            // If previous element already has occurrences
            // we move $info pointer there (only if last is
            // numeric occurrence)
            if (!empty($previouselement) && is_array($info) && count($info) > 0) {
                end($info);
                $key = key($info);
                if ((int) $key === $key) {
                    $info =& $info[$key];
                }
            }

            // Create element if not defined
            if (!isset($info[$element])) {
                // First into last element if present
                $info[$element] = array();
            }

            // If element is the current one, add information
            if ($element === $processedtag) {
                $info[$element][] = $tags;
            }

            $previouselement = $element;
            $info =& $info[$element];
        }
    }
}
