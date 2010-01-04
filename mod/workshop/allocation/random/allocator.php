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
 * Allocates the submissions randomly
 * 
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__FILE__)) . '/lib.php');  // interface definition


class workshop_random_allocator implements workshop_allocator {

    /** workshop instance */
    protected $workshop;

    /** array of allocations */
    protected $allocation = array();

    public function __construct(stdClass $workshop) {
        global $DB, $USER;

        $this->workshop = $workshop;

        // submissions to be allocated
        $submissions = $DB->get_records('workshop_submissions', array('workshopid' => $this->workshop->id, 'example' => 0),
                                         '', 'id,userid,title');


        // dummy allocation - allocate all submissions to the current USER
        foreach ($submissions as $submissionid => $submission) {
            $this->allocation[$submissionid]                = new stdClass;
            $this->allocation[$submissionid]->submissionid  = $submissionid;
            $this->allocation[$submissionid]->title         = $submission->title;
            $this->allocation[$submissionid]->authorid      = $submission->userid;
            $this->allocation[$submissionid]->reviewerid    = $USER->id;
            $this->allocation[$submissionid]->assessmentid  = NULL;
        }

        // already created assessments
        $assessments = $DB->get_records_list('workshop_assessments', 'submissionid', array_keys($submissions),
                                                '', 'id,submissionid,userid');
        
        foreach ($assessments as $assessmentid => $assessment) {
            $this->allocation[$assessment->submissionid]->assessmentid  = $assessmentid;
        }
    }


    public function init() {
    }


    public function ui() {
        return 'TODO';
    }


}
