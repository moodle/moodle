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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\components;

defined('MOODLE_INTERNAL') || die();

class component {

    protected $params;

    public function __construct($params = []) {
        $this->params = $params;
    }

    /**
     * Returns the given key from within the set params array, if any
     * Accepts optional second parameter, default value
     *
     * @param  string $key
     * @param  mixed $defaultvalue  an optional value to use if no results found
     * @return mixed
     */
    public function get_param($key, $defaultvalue = null) {
        return isset($this->params[$key]) ? $this->params[$key] : $defaultvalue;
    }

    /**
     * Returns a transformed array for template given a flat array of course id => course name
     *
     * @param  array  $coursearray
     * @param  int    $selectedcourseid
     * @return array
     */
    public function transform_course_array($coursearray, $selectedcourseid = 0) {
        $results = [];

        foreach ($coursearray as $id => $shortname) {
            $results[] = [
                'userCourseId' => (string) $id,
                'userCourseName' => $shortname,
                'selectedAttr' => $selectedcourseid == $id ? 'selected' : ''
            ];
        }

        return $results;
    }

    public function is_attr_sorted($attr) {
        return $this->sort_by == $attr;
    }

    /**
     * Includes the given pagination attributes (and all template helper booleans) in the given data object
     *
     * @param  object  $data
     * @param  paginated  $pagination
     * @return object
     */
    public function include_pagination($data, $pagination) {
        // Build pagination attributes.
        $data->paginationPageCount = $pagination->page_count;
        $data->paginationOffset = $pagination->offset;
        $data->paginationPerPage = $pagination->per_page;
        $data->paginationCurrentPage = $pagination->current_page;
        $data->paginationNextPage = $pagination->next_page;
        $data->paginationPreviousPage = $pagination->previous_page;
        $data->paginationTotalCount = $pagination->total_count;
        $data->paginationUriForPage = $pagination->uri_for_page;
        $data->paginationFirstPageUri = $pagination->first_page_uri;
        $data->paginationLastPageUri = $pagination->last_page_uri;
        $data->paginationNextPageUri = $pagination->next_page_uri;
        $data->paginationPreviousPageUri = $pagination->previous_page_uri;

        if ($pagination->page_count > 1) {
            $data->paginationShow = true;

            if ($pagination->current_page !== 1) {
                $data->paginationShowFirst = true;
            }

            if ($pagination->current_page !== $pagination->previous_page) {
                $data->paginationShowPrevious = true;
            }

            if ($pagination->current_page !== $pagination->next_page) {
                $data->paginationShowNext = true;
            }

            if ($pagination->current_page !== $pagination->page_count) {
                $data->paginationShowLast = true;
            }
        }

        return $data;
    }

}
