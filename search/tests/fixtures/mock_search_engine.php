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

namespace mock_search;

/**
 * Search engine for testing purposes.
 *
 * @package   core_search
 * @category  phpunit
 * @copyright David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_search\manager;

defined('MOODLE_INTERNAL') || die;

class engine extends \core_search\engine {

    /** @var float If set, waits when adding each document (seconds) */
    protected $adddelay = 0;

    /** @var \core_search\document[] Documents added */
    protected $added = [];

    /** @var array Schema updates applied */
    protected $schemaupdates = [];

    public function is_installed() {
        return true;
    }

    public function is_server_ready() {
        return true;
    }

    public function add_document($document, $fileindexing = false) {
        if ($this->adddelay) {
            \testable_core_search::fake_current_time(manager::get_current_time() + $this->adddelay);
        }
        $this->added[] = $document;
        return true;
    }

    public function execute_query($data, $usercontexts, $limit = 0) {
        // No need to implement.
    }

    public function delete($areaid = null) {
        return null;
    }

    public function to_document(\core_search\base $searcharea, $docdata) {
        return parent::to_document($searcharea, $docdata);
    }

    public function get_course($courseid) {
        return parent::get_course($courseid);
    }

    public function get_search_area($areaid) {
        return parent::get_search_area($areaid);
    }

    public function get_query_total_count() {
        return 0;
    }

    /**
     * Sets an add delay to simulate time taken indexing.
     *
     * @param float $seconds Delay in seconds for each document
     */
    public function set_add_delay($seconds) {
        $this->adddelay = $seconds;
    }

    /**
     * Gets the list of indexed (added) documents since last time this function
     * was called.
     *
     * @return \core_search\document[] List of documents, in order added.
     */
    public function get_and_clear_added_documents() {
        $added = $this->added;
        $this->added = [];
        return $added;
    }

    public function update_schema($oldversion, $newversion) {
        $this->schemaupdates[] = [$oldversion, $newversion];
    }

    /**
     * Gets all schema updates applied, as an array. Each entry has an array with two values,
     * old and new version.
     *
     * @return array List of schema updates for comparison
     */
    public function get_and_clear_schema_updates() {
        $result = $this->schemaupdates;
        $this->schemaupdates = [];
        return $result;
    }
}
