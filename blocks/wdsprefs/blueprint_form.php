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
 * @package    block_wdsprefs
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");

class blueprint_form extends moodleform {

    // Define the form elements.
    public function definition() {
        global $USER;
        
        $mform = $this->_form;
        
        // Get the courses array from custom data.
        $courses = $this->_customdata['courses'] ?? [];
        
        // Add explanatory text.
        $mform->addElement('html', '<div class="alert alert-info">' .
                          get_string('wdsprefs:blueprintexplanation', 'block_wdsprefs') .
                          '</div>');
        
        // Select dropdown for course.
        $courseoptions = [];
        foreach ($courses as $course) {
            $abbreviation = $course->course_subject_abbreviation ?? '';
            $number = $course->course_number ?? '';
            $title = $course->course_title ?? '';
            
            // Create the display name.
            $displayname = "$abbreviation $number - $title";
            
            // Use course_definition_id as the key.
            $courseoptions[$course->course_definition_id] = $displayname;
        }
        
        // Sort the courses alphabetically.
        asort($courseoptions);
        
        // Add the dropdown to select a course.
        $mform->addElement('select', 
                          'course_definition_id', 
                          get_string('wdsprefs:selectcourseforblueprint', 'block_wdsprefs'), 
                          $courseoptions);
        $mform->addRule('course_definition_id', null, 'required', null, 'client');
        
        // Add action buttons.
        $this->add_action_buttons(true, get_string('wdsprefs:createblueprint', 'block_wdsprefs'));
    }

    // Custom validation if needed.
    public function validation($data, $files) {
        global $USER, $DB;
        
        $errors = parent::validation($data, $files);
        
        // Check if user already has a blueprint for this course.
        if (!empty($data['course_definition_id'])) {
            $exists = $DB->record_exists('block_wdsprefs_blueprints', [
                'userid' => $USER->id,
                'course_definition_id' => $data['course_definition_id']
            ]);

            if ($exists) {
                $errors['course_definition_id'] = get_string('wdsprefs:blueprintalreadyexists', 'block_wdsprefs');
            }
        }
        
        return $errors;
    }
}
