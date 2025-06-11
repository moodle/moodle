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

namespace block_quickmail\repos\pagination;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\repos\pagination\paginated;

class paginator {

    public $total_count;
    public $page;
    public $per_page;
    public $page_uri;
    public $total_pages;
    public $offset;

    public function __construct($totalcount, $page = 1, $perpage = 10, $pageuri = '') {
        $this->total_count = $totalcount;
        $this->page = $page;
        $this->per_page = $perpage;
        $this->page_uri = $pageuri;
        $this->set_page_lower();
        $this->set_total_pages();
        $this->set_page_upper();
        $this->set_offset();
    }

    /**
     * Returns a paginated data object
     *
     * @return object
     */
    public function paginated() {
        return new paginated($this);
    }

    /**
     * Sets page number to "1" if input index is less than 1
     *
     * @return void
     */
    private function set_page_lower() {
        $this->page = $this->page <= 0
            ? 1
            : $this->page;
    }

    /**
     * Sets calculated count of total pages based on set results and parameters
     *
     * @return void
     */
    private function set_total_pages() {
        $this->total_pages = (int) ceil($this->total_count / $this->per_page);
    }

    /**
     * Sets page number to maximum possible page if set page exceeds total pages
     *
     * @return void
     */
    private function set_page_upper() {
        $this->page = min($this->page, $this->total_pages);
    }

    /**
     * Sets a calculated offset used to slice the results
     *
     * @return int
     */
    private function set_offset() {
        $offset = ($this->page - 1) * $this->per_page;
        $this->offset = $offset < 0 ? 0 : $offset;
    }

}
