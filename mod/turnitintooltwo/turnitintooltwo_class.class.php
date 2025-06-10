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
 * @package   turnitintooltwo
 * @copyright 2012 iParadigms LLC *
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/turnitintooltwo_comms.class.php');

class turnitintooltwo_class {

    private $id;
    private $turnitinid;
    private $title;
    private $turnitintitle;
    public $sharedrubrics;

    public function __construct($id) {
        global $DB;

        $this->id = $id;

        if ($turnitincourse = $DB->get_record('turnitintooltwo_courses',
                                array("courseid" => $id, "course_type" => "TT"))) {
            $this->turnitinid = $turnitincourse->turnitin_cid;
            $this->turnitintitle = $turnitincourse->turnitin_ctl;
        }
    }

    /**
     * Update class from Turnitin, mainly to get shared rubrics
     *
     * @return void
     */
    public function read_class_from_tii() {
        // Initialise Comms Object.
        $turnitincomms = new turnitintooltwo_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $tiiclass = new TiiClass();

        try {
            $tiiclass->setClassId($this->turnitinid);
            $response = $turnitincall->readClass($tiiclass);
            $readclass = $response->getClass();

            $rubrics = $readclass->getSharedRubrics();
            $rubricarray = array();
            foreach ($rubrics as $rubric) {
                $rubricarray[$rubric->getRubricGroupName()][$rubric->getRubricId()] = $rubric->getRubricName();
            }

            $this->sharedrubrics = $rubricarray;

        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'coursegeterror', false);
        }
    }
}