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
 * Files that are processed for accessibility.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

/**
 * Files that are processed for accessibility.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class files_iterator implements \Iterator {
    /**
     * Number of records to fetch at a time.
     * @var int
     */
    private $pagesize;

    /**
     * @var file_validator
     */
    private $validator;

    /**
     * @var boolean
     */
    private $retrievevalid = true;

    /**
     * @var \file_storage
     */
    private $storage;

    /**
     * @var array
     */
    private $records = [];

    /**
     * @var int
     */
    private $page = 0;

    /**
     * @var \stored_file
     */
    private $current;

    /**
     * @var int|null
     */
    private $since;

    /**
     * @var \context|null
     */
    private $context;

    /**
     * @var string|null
     */
    private $component;

    /**
     * @var string|null
     */
    private $filearea;

    /**
     * @var int|null
     */
    private $itemid;

    /**
     * @var string
     */
    private $mimetype;

    /**
     * @var boolean
     */
    private $validfilter = true;

    /**
     * SQL sorting.
     * Set a default sort order, as some DBs (like postgres) may return results in an inconsistent order otherwise.
     *
     * @var string
     */
    private $sort = 'ORDER BY f.id ASC';

    /**
     * Start page to use when rewinding.
     *
     * @var int
     */
    private $countstartpage = 0;

    /**
     * Count of all files, used when skipping results.
     *
     * @var int
     */
    private $allresultscount = 0;

    /**
     * Stops iterating when a result count has been reached.
     *
     * @var int
     */
    private $stopatcount = 0;

    /**
     * Counts the number of rows set in iterator value.
     *
     * @var int
     */
    private $resultcount = 0;

    /**
     * @param file_validator $validator
     * @param \file_storage|null $storage
     */
    public function __construct(file_validator $validator, \file_storage $storage = null) {
        global $CFG;
        $this->validator = $validator;
        $this->storage   = $storage ?: get_file_storage();
        $this->pagesize  = !empty($CFG->tool_ally_optimize_iteration_for_db) ?
            10000 : 5000;
    }

    /**
     * @param \stdClass $row
     * @return \context
     */
    private function extract_context($row) {
        // This loads the context into cache and strips the context fields from the row.
        \context_helper::preload_from_record($row);

        return \context::instance_by_id($row->contextid);
    }

    /**
     * @return \stored_file
     */
    #[\ReturnTypeWillChange]
    public function current() {
        return $this->current;
    }

    #[\ReturnTypeWillChange]
    public function next() {
        if ($this->reached_count_limit()) {
            $this->current = null;
            return;
        }

        while (($row = current($this->records)) !== false) {
            if (next($this->records) === false) {
                if (count($this->records) !== 0 && count($this->records) === $this->pagesize) {
                    $this->next_page();
                }
            }

            if ($row->filename === '.') {
                continue;
            }

            $context = $this->extract_context($row);
            $file    = $this->storage->get_file_instance($row);

            if (!empty($this->validfilter)) {
                $filevalidation = $this->validator->validate_stored_file($file, $context);
                if (($this->retrievevalid && !$filevalidation) || (!$this->retrievevalid && $filevalidation)) {
                    continue;
                }
            }

            // Skip files when using result count paging until reaching expected page.
            if ($this->countstartpage > 0 && $this->stopatcount > 0) {
                $this->allresultscount++;
                if ($this->allresultscount <= ($this->countstartpage * $this->stopatcount)) {
                    continue;
                }
            }

            $this->current = $file;
            $this->resultcount++;
            return;
        }
        $this->current = null;
    }

    #[\ReturnTypeWillChange]
    public function key() {
        if ($this->current instanceof \stored_file) {
            return (int) $this->current->get_id();
        }

        return null;
    }

    #[\ReturnTypeWillChange]
    public function valid() {
        return $this->current instanceof \stored_file;
    }

    #[\ReturnTypeWillChange]
    public function rewind() {
        $this->page = 0;
        $this->resultcount = 0;
        $this->next_page();
        // Must populate current.
        $this->next();
    }

    private function next_page() {
        global $DB, $CFG;

        $contextsql = \context_helper::get_preload_record_columns_sql('c');
        $params     = ['usr' => CONTEXT_USER, 'cat' => CONTEXT_COURSECAT, 'sys' => CONTEXT_SYSTEM];
        $filtersql  = '1 = 1';

        if (empty($CFG->tool_ally_optimize_iteration_for_db)) {
            $filtersql .= ' AND f.filename <> \'.\'';
        }
        if (!empty($this->since)) {
            $filtersql .= ' AND f.timemodified > :since';
            $params['since'] = $this->since;
        }
        if ($this->context instanceof \context) {
            $filtersql .= ' AND (c.path LIKE :path OR c.id = :ctxid)';
            $params['path'] = $this->context->path.'/%';
            $params['ctxid'] = $this->context->id;
        }
        if (!empty($this->component)) {
            $filtersql .= ' AND f.component = :component ';
            $params['component'] = $this->component;
        }
        if (!empty($this->filearea)) {
            $filtersql .= ' AND f.filearea = :filearea ';
            $params['filearea'] = $this->filearea;
        }
        if (!empty($this->itemid) && is_numeric($this->itemid)) {
            $filtersql .= ' AND f.itemid = :itemid ';
            $params['itemid'] = $this->itemid;
        }
        if (!empty($this->mimetype)) {
            if (strpos($this->mimetype, '%') !== false) {
                $filtersql .= ' AND '.$DB->sql_like('f.mimetype', ':mimetype').' ';
            } else {
                $filtersql .= 'AND f.mimetype = :mimetype ';
            }
            $params['mimetype'] = $this->mimetype;
        }

        $this->records = $DB->get_records_sql("
            SELECT f.*, $contextsql
              FROM {files} f
              JOIN {context} c ON c.id = f.contextid
             WHERE $filtersql
               AND c.contextlevel NOT IN(:usr, :cat, :sys) {$this->sort}
        ", $params, $this->page * $this->pagesize, $this->pagesize);

        reset($this->records);
        $this->page++;
    }

    /**
     * Return files that have been modified after this time.
     *
     * @param int $timestamp
     * @return self
     */
    public function since($timestamp) {
        $this->since = $timestamp;

        return $this;
    }

    /**
     * Return files that belong to this context or lower.
     *
     * @param \context $context
     * @return self
     */
    public function in_context(\context $context) {
        $this->context = $context;

        return $this;
    }

    /**
     * Restrict files to a specific component.
     *
     * @param $component
     * @return $this
     */
    public function with_component($component) {
        $this->component = $component;

        return $this;
    }

    /**
     * Restrict files to a specific file area.
     *
     * @param $filearea
     * @return $this
     */
    public function with_filearea($filearea) {
        $this->filearea = $filearea;

        return $this;
    }

    /**
     * Restrict files to a specific item id.
     * @param $itemid
     * @return $this
     */
    public function with_itemid($itemid) {
        $this->itemid = $itemid;

        return $this;
    }

    /**
     * Restrict files to a specific mimetype.
     * @param $mimetype
     * @return $this
     */
    public function with_mimetype($mimetype) {
        $this->mimetype = $mimetype;

        return $this;
    }

    /**
     * Sort the files.
     *
     * @param string $field
     * @param int $direction
     * @return self
     */
    public function sort_by($field, $direction = SORT_ASC) {
        $this->sort = 'ORDER BY f.'.$field.' ';
        $this->sort .= $direction === SORT_ASC ? 'ASC' : 'DESC';

        return $this;
    }

    /**
     * @param int $pagesize
     */
    public function set_page_size($pagesize) {
        $this->pagesize = $pagesize;
    }

    /**
     * Enable/disable validation.
     *
     * @param boolean $validfilter
     * @return self
     */
    public function with_valid_filter($validfilter) {
        $this->validfilter = $validfilter;

        return $this;
    }

    /**
     * Set which kind of files return after validation.
     *
     * @param boolean $retrievevalid
     * @return self
     */
    public function with_retrieve_valid_files($retrievevalid) {
        $this->retrievevalid = $retrievevalid;

        return $this;
    }

    /**
     * Enables or disables the ability of the iterator to continue after reaching a result count.
     *
     * @param int $stopatcount
     * @return $this
     */
    public function with_stop_at_count($stopatcount) {
        $this->stopatcount = $stopatcount;

        return $this;
    }

    /**
     * Sets a start page, so when rewinding the iterator it'll start from there.
     *
     * @param int $countstartpage
     * @return $this
     */
    public function with_count_start_page($countstartpage) {
        $this->countstartpage = $countstartpage;

        return $this;
    }

    /**
     * @return bool
     */
    private function reached_count_limit() {
        if ($this->stopatcount === 0) {
            return false;
        } else {
            return $this->resultcount >= $this->stopatcount;
        }
    }
}
