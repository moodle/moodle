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

namespace block_quickmail\repos;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\repos\pagination\paginator;
use block_quickmail\repos\pagination\paginated;

abstract class repo {

    public $sort;
    public $dir;
    public $paginate;
    public $page;
    public $per_page;
    public $uri;
    public $result;

    public function __construct($params = []) {
        $this->set_sort($params);
        $this->set_dir($params);
        $this->set_paginate($params);
        $this->set_page($params);
        $this->set_per_page($params);
        $this->set_uri($params);
        $this->set_result();
    }

    /**
     * Sets the sort field parameter (default to "id")
     *
     * @param array $params
     */
    private function set_sort($params) {
        $this->sort = array_key_exists('sort', $params) && in_array($params['sort'], array_keys($this->sortableattrs))
            ? $params['sort']
            : 'id';
    }

    /**
     * Sets the sort direction parameter (default to asc)
     *
     * @param array $params
     */
    private function set_dir($params) {
        $this->dir = array_key_exists('dir', $params) && in_array($params['dir'], ['asc', 'desc'])
            ? $params['dir']
            : 'asc';
    }

    /**
     * Sets the pagination flag parameter, default to false (no pagination)
     *
     * @param array $params
     */
    private function set_paginate($params) {
        $this->paginate = array_key_exists('paginate', $params)
            ? $params['paginate']
            : false;
    }

    /**
     * Sets the current page number parameter, default to 1
     *
     * @param array $params
     */
    private function set_page($params) {
        $this->page = array_key_exists('page', $params) && is_int($params['page'] + 0)
            ? $params['page']
            : 1;
    }

    /**
     * Sets the sort field parameter, default to 10
     *
     * @param array $params
     */
    private function set_per_page($params) {
        $this->per_page = array_key_exists('per_page', $params)
            ? $params['per_page']
            : 10;
    }

    /**
     * Sets the uri parameter, default to empty
     *
     * @param array $params
     */
    private function set_uri($params) {
        $this->uri = array_key_exists('uri', $params)
            ? $params['uri']
            : '';
    }

    /**
     * Returns the database column name to sort by, given the "sortable_attr" key
     *
     * @param  string  $key
     * @return string
     */
    public function get_sort_column_name($key) {
        return $this->sortableattrs[$key];
    }

    /**
     * Sets the initial result object parameter (to be filled later)
     */
    private function set_result() {
        $this->result = (object) [
            'data' => [],
            'pagination' => (object) []
        ];
    }

    /**
     * Sets the data property on the result object
     *
     * @param  array  $data   collection of data to be set
     * @return void
     */
    public function set_result_data($data = []) {
        $this->result->data = $data;
    }

    /**
     * Sets the pagination property on the result object
     *
     * @param  paginated  $paginated  the paginated object created by the paginator
     * @return void
     */
    public function set_result_pagination(paginated $paginated) {
        $this->result->pagination = $paginated;
    }

    /**
     * Returns a paginated object for the result given a total count of records
     *
     * @param  int  $count
     * @return paginated
     */
    public function get_paginated($count) {
        $paginator = $this->make_paginator($count);

        $paginated = $paginator->paginated();

        return $paginated;
    }

    /**
     * Instantiates and returns a paginator object given a total count of records
     *
     * @param  int  $count
     * @return paginator
     */
    private function make_paginator($count) {
        $paginator = new paginator($count, $this->page, $this->per_page, $this->uri);

        return $paginator;
    }

}
