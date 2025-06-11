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
 * This file contains the definition for the renderable classes for the submissions
 *
 * @package mod_panoptosubmission
 * @copyright Panopto 2023
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Renderable feedback status
 *
 * @package   mod_panoptosubmission
 * @copyright Panopto 2023
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panoptosubmission_submissions_feedback_status implements renderable {

    /** @var string $gradefordisplay the student grade rendered into a format suitable for display */
    public $gradefordisplay = '';
    /** @var mixed the graded date (may be null) */
    public $gradeddate = 0;
    /** @var mixed the grader (may be null) */
    public $grader = null;
    /** @var stdClass grade record */
    public $grade = null;
    /** @var int coursemoduleid */
    public $coursemoduleid = 0;
    /** @var bool canviewfullnames */
    public $canviewfullnames = false;

    /**
     * Constructor
     * @param string $gradefordisplay
     * @param mixed $gradeddate
     * @param mixed $grader
     * @param mixed $grade
     * @param int $coursemoduleid
     * @param bool $canviewfullnames
     */
    public function __construct($gradefordisplay,
                                $gradeddate,
                                $grader,
                                $grade,
                                $coursemoduleid,
                                $canviewfullnames) {
        $this->gradefordisplay = $gradefordisplay;
        $this->gradeddate = $gradeddate;
        $this->grader = $grader;
        $this->grade = $grade;
        $this->coursemoduleid = $coursemoduleid;
        $this->canviewfullnames = $canviewfullnames;
    }
}
