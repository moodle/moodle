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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_qubitsbasic
 * @copyright  2023 Qubits Dev Team.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/theme/qubitsbasic/externallib.php");

class theme_qubitsbasic_core_renderer extends theme_boost\output\core_renderer {

    public function qubits_left_navigation(){
        global $CFG, $PAGE;
        $mycourses = enrol_get_my_courses(array('id', 'cacherev'), 'fullname');
        $myenrolcourses = array();
        foreach($mycourses as $mycourse){
            $myenrolcourses[] = array(
                "name" => $mycourse->fullname,
                "id" => $mycourse->id,
                "url" => $CFG->wwwroot.'/course/view.php?id='.$mycourse->id
            );
        }
        $primary = new core\navigation\output\primary($PAGE);
        $renderer = $PAGE->get_renderer('core');
        $primarymenu = $primary->export_for_template($renderer);
        $context = array(
            "myenrolcourses" => $myenrolcourses,
            'usermenu' => $primarymenu['user']
        );
        return $this->render_from_template("theme_qubitsbasic/custom/leftnavigation", $context);
    }

    public function qubits_page_header(){
        global $DB, $USER, $CFG, $SITE; 
        $heading = null;
        $context = $this->page->context;
        $homepage = get_home_page();
        $otherpage = true;

        // Make sure to use the heading if it has been set.
        if (isset($headerinfo['heading'])) {
            $heading = $headerinfo['heading'];
        } else {
            $heading = $this->page->heading;
        }

        if ($context->contextlevel == CONTEXT_MODULE) {
            if ($this->page->course->format === 'singleactivity') {
                $heading = $this->page->course->fullname;
            } else {
                $heading = $this->page->cm->get_formatted_name();
            }
        }
        $course = ($this->page->context->contextlevel == CONTEXT_COURSE) ? $this->page->course : null;

        $ismycoursepage = ($homepage == HOMEPAGE_MYCOURSES) ? true : false;
        $ismodulepage = ($context->contextlevel == CONTEXT_MODULE) ? true : false;
        $iscourseviewpage = ($context->contextlevel == CONTEXT_COURSE) ? true : false;

        if($ismycoursepage==true || $ismodulepage==true || $iscourseviewpage==true){
            $otherpage = false;
        }

        $outputcontext = array(
            "heading" =>  $heading,
            "ismycoursepage" => $ismycoursepage,
            "iscourseviewpage" => $iscourseviewpage,
            "ismodulepage" => $ismodulepage,
            "otherpage" => $otherpage,
            "coursefullname" => $this->page->course->fullname
        );
        return $this->render_from_template("theme_qubitsbasic/custom/pageheader", $outputcontext);
    }
    
}