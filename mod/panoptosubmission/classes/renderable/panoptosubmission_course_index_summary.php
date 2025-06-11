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
 * Panopto renderable script for index table
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Renderable panopto submission index summary
 */
class panoptosubmission_course_index_summary implements renderable {
    /** @var array activities A list of course modules and their status or submission counts depending on the user capabilities */
    public $activities = [];
    /** @var bool usesections True if the parent course supports sections */
    public $usesections = false;
    /** @var string courseformat The current course format name */
    public $courseformatname = '';

    /**
     * This is the constructor for the object
     *
     * @param bool $usesections True if this course format uses sections
     * @param string $courseformatname The id of this course format
     */
    public function __construct($usesections, $courseformatname) {
        $this->usesections = $usesections;
        $this->courseformatname = $courseformatname;
    }

    /**
     * Adds information for an activity on the activity index page
     *
     * @param int $cmid The course module id of the activity
     * @param string $cmname The name of the activity
     * @param string $sectionname The name of the target course section
     * @param int $timedue The due date for the activity, 0 if no duedate is defined
     * @param string $submissioninfo The number of assignments that were submitted, or depending on the
     *  user capabilities the status of their submission.
     * @param string $gradeinfo The current users grade if the activity has been graded.
     */
    public function add_assign_info($cmid, $cmname, $sectionname, $timedue, $submissioninfo, $gradeinfo) {
        $this->activities[] = [
            'cmid' => $cmid,
            'cmname' => $cmname,
            'sectionname' => $sectionname,
            'timedue' => $timedue,
            'submissioninfo' => $submissioninfo,
            'gradeinfo' => $gradeinfo,
        ];
    }
}
