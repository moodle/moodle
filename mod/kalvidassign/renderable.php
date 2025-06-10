<?php
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
 * Kaltura video assignment renderable script.
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Renderable course index summary
 */
class kalvidassign_course_index_summary implements renderable {
    /** @var array assignments A list of course module info and submission counts or statuses */
    public $assignments = array();
    /** @var boolean usesections Does this course format support sections? */
    public $usesections = false;
    /** @var string courseformat The current course format name */
    public $courseformatname = '';

    /**
     * constructor
     *
     * @param $usesections boolean True if this course format uses sections
     * @param $courseformatname string The id of this course format
     */
    public function __construct($usesections, $courseformatname) {
        $this->usesections = $usesections;
        $this->courseformatname = $courseformatname;
    }

    /**
     * Add a row of data to display on the course index page
     *
     * @param int $cmid The course module id for generating a link
     * @param string $cmname The course module name for generating a link
     * @param string $sectionname The name of the course section (only if $usesections is true)
     * @param int $timedue The due date for the assignment - may be 0 if no duedate
     * @param string $submissioninfo A string with either the number of submitted assignments, or the
     *                               status of the current users submission depending on capabilities.
     * @param string $gradeinfo The current users grade if they have been graded and it is not hidden.
     */
    public function add_assign_info($cmid, $cmname, $sectionname, $timedue, $submissioninfo, $gradeinfo) {
        $this->assignments[] = array(
            'cmid' => $cmid,
            'cmname' => $cmname,
            'sectionname' => $sectionname,
            'timedue' => $timedue,
            'submissioninfo' => $submissioninfo,
            'gradeinfo' => $gradeinfo
        );
    }
}
