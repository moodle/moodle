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
        global $CFG, $PAGE,$DB;
        $mycourses = enrol_get_my_courses(array('id', 'cacherev'), 'fullname');
        $myenrolcourses = array();
        foreach($mycourses as $mycourse){
            $category = $DB->get_record('course_categories',array('id'=>$mycourse->category));
            $categoryName = $category->name;
            $mform = new theme_qubitsbasic_external();
            $course_customdata = $mform->get_custom_fields_data_by_cid($mycourse->id);
            $level = isset($course_customdata["level"]) ? $course_customdata["level"] : "1";

            $myenrolcourses[] = array(
                "name" => $mycourse->fullname,
                "id" => $mycourse->id,
                "categoryname" => $categoryName,
                "level" => "Level ".$level,
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

        $ismycoursepage = ($_SERVER['SCRIPT_NAME'] == "/my/courses.php") ? true : false;
        $ismodulepage = ($context->contextlevel == CONTEXT_MODULE) ? true : false;
        $iscourseviewpage = ($context->contextlevel == CONTEXT_COURSE) ? true : false;

        if($ismycoursepage==true || $ismodulepage==true || $iscourseviewpage==true){
            $otherpage = false;
        }

        $category = $DB->get_record('course_categories',array('id'=>$this->page->course->category));
        $categoryName = $category->name;
        $mform = new theme_qubitsbasic_external();
        $course_customdata = $mform->get_custom_fields_data_by_cid($this->page->course->id);
        $level = isset($course_customdata["level"]) ? $course_customdata["level"] : "1";

        $outputcontext = array(
            "heading" =>  $heading,
            "ismycoursepage" => $ismycoursepage,
            "iscourseviewpage" => $iscourseviewpage,
            "ismodulepage" => $ismodulepage,
            "otherpage" => $otherpage,
            "coursefullname" => $this->page->course->fullname,
            "courseid" => $this->page->course->id,
            "categoryname" => $categoryName,
            "level" => "Level ".$level
        );
        return $this->render_from_template("theme_qubitsbasic/custom/pageheader", $outputcontext);
    }

    public function activity_navigation() {
        // First we should check if we want to add navigation.
        $context = $this->page->context;
        if (($this->page->pagelayout !== 'incourse' && $this->page->pagelayout !== 'executablebook' && $this->page->pagelayout !== 'frametop')
            || $context->contextlevel != CONTEXT_MODULE) {
            return '';
        }

        // If the activity is in stealth mode, show no links.
        if ($this->page->cm->is_stealth()) {
            return '';
        }

        $course = $this->page->cm->get_course();
        $courseformat = course_get_format($course);

        // If the theme implements course index and the current course format uses course index and the current
        // page layout is not 'frametop' (this layout does not support course index), show no links.
        if ($this->page->theme->usescourseindex && $courseformat->uses_course_index() &&
                $this->page->pagelayout !== 'frametop') {
            return '';
        }

        // Get a list of all the activities in the course.
        $modules = get_fast_modinfo($course->id)->get_cms();

        // Put the modules into an array in order by the position they are shown in the course.
        $mods = [];
        $activitylist = [];
        foreach ($modules as $module) {
            // Only add activities the user can access, aren't in stealth mode and have a url (eg. mod_label does not).
            if (!$module->uservisible || $module->is_stealth() || empty($module->url)) {
                continue;
            }
            $mods[$module->id] = $module;

            // No need to add the current module to the list for the activity dropdown menu.
            if ($module->id == $this->page->cm->id) {
                continue;
            }
            // Module name.
            $modname = $module->get_formatted_name();
            // Display the hidden text if necessary.
            if (!$module->visible) {
                $modname .= ' ' . get_string('hiddenwithbrackets');
            }
            // Module URL.
            $linkurl = new moodle_url($module->url, array('forceview' => 1));
            // Add module URL (as key) and name (as value) to the activity list array.
            $activitylist[$linkurl->out(false)] = $modname;
        }

        $nummods = count($mods);

        // If there is only one mod then do nothing.
        if ($nummods == 1) {
            return '';
        }

        // Get an array of just the course module ids used to get the cmid value based on their position in the course.
        $modids = array_keys($mods);

        // Get the position in the array of the course module we are viewing.
        $position = array_search($this->page->cm->id, $modids);

        $prevmod = null;
        $nextmod = null;

        // Check if we have a previous mod to show.
        if ($position > 0) {
            $prevmod = $mods[$modids[$position - 1]];
        }

        // Check if we have a next mod to show.
        if ($position < ($nummods - 1)) {
            $nextmod = $mods[$modids[$position + 1]];
        }

        $activitynav = new \core_course\output\activity_navigation($prevmod, $nextmod, $activitylist);
        $renderer = $this->page->get_renderer('core', 'course');
        return $renderer->render($activitynav);
    }

    public function executable_book_files(){
        global $CFG; 
        if($this->page->pagelayout !== 'executablebook'){
            return '';
        }
        $context = [
            'wwwroot' => $CFG->wwwroot,
            'qmurl' => $CFG->wwwroot.'/third_party/qubits'
        ];
        return $this->render_from_template("theme_qubitsbasic/custom/exbookfiles", $context);
    }

    public function third_party_editor_files(){
        global $CFG; 
        if($this->page->pagelayout !== 'thirdparty'){
            return '';
        }
        $context = [
            'wwwroot' => $CFG->wwwroot,
            'qmurl' => $CFG->wwwroot.'/third_party/qubits'
        ];
        return $this->render_from_template("theme_qubitsbasic/custom/trdptyfiles", $context);
    }
    
}