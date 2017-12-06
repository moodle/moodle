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
 * @package     report_overviewstats
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for all charts to be reported
 */
abstract class report_overviewstats_chart {

    /** @var stdClass if this is course level report, holds the course record */
    protected $course = null;

    /** @var array data to pass to YUI Chart */
    protected $data = null;

    /**
     * Constructor.
     *
     * Keep this cheap, no actual data gathering here yet.
     *
     * @param stdClass should this be a course level report, pass the course record
     */
    public function __construct(stdClass $course = null) {
        if (!is_null($course)) {
            $this->set_report_course($course);
        }
    }

    /**
     * Returns the content to be displayed
     *
     * The simplest form of returned data is single item associative array like
     *
     *  array('Chart title' => 'HTML data to be displayed')
     *
     * Multiple items are allowed in the array. If the value is not a string but
     * another array, then it is considered as a subsection, for example:
     *
     *  array('Recent users' => array(
     *      'Last week' => 'HTML data to be displayed',
     *      'Last month' => 'HTML data to be displayed',
     *  ))
     *
     *  Only one level of such subsections is allowed.
     *
     * @return array
     */
    abstract public function get_content();

    /**
     * Gives the chart type a chance to inject its page requirements
     *
     * @param mooodle_page $page
     */
    public function inject_page_requirements(moodle_page $page) {
    }

    /**
     * Sets the course to produce the report for
     *
     * @param stdClass $course
     */
    protected function set_report_course(stdClass $course) {
        $this->course = $course;
    }

    /**
     * Prepares data to be displayed
     */
    protected function prepare_data() {

        if (!is_null($this->data)) {
            return;
        }

        // Gather and format the data here.
    }
}
