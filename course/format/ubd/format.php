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
 * UbD course format. Display the whole course as "UbD" made of modules.
 *
 * @package format_ubd
 * @copyright 2025 Moodle Evolved Team
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');

// Retrieve course format option fields and add them to the $course object.
$course = course_get_format($course)->get_course();

$context = context_course::instance($course->id);

// Add UbD planning interface if user has editing capabilities
if (has_capability('moodle/course:manageactivities', $context)) {
    echo '<div class="ubd-planning-interface">';
    echo '<h2>'.get_string('pluginname', 'format_ubd').'</h2>';
    
    // UbD Stage 1: Desired Results
    echo '<div class="ubd-stage ubd-stage1">';
    echo '<h3>'.get_string('ubd_stage1', 'format_ubd').'</h3>';
    echo '<p class="ubd-stage-description">'.get_string('ubd_stage1_desc_zh', 'format_ubd').'</p>';
    
    // Enduring Understandings
    echo '<div class="ubd-field">';
    echo '<label for="ubd_enduring">'.get_string('ubd_enduring_understandings', 'format_ubd').':</label>';
    echo '<textarea id="ubd_enduring" name="ubd_stage1_enduring" rows="4" cols="80" class="form-control">';
    echo format_text($course->ubd_stage1_enduring ?? '', FORMAT_PLAIN);
    echo '</textarea>';
    echo '</div>';
    
    // Essential Questions
    echo '<div class="ubd-field">';
    echo '<label for="ubd_questions">'.get_string('ubd_essential_questions', 'format_ubd').':</label>';
    echo '<textarea id="ubd_questions" name="ubd_stage1_questions" rows="4" cols="80" class="form-control">';
    echo format_text($course->ubd_stage1_questions ?? '', FORMAT_PLAIN);
    echo '</textarea>';
    echo '</div>';
    
    // Knowledge & Skills
    echo '<div class="ubd-field">';
    echo '<label for="ubd_knowledge">'.get_string('ubd_knowledge_skills', 'format_ubd').':</label>';
    echo '<textarea id="ubd_knowledge" name="ubd_stage1_knowledge" rows="4" cols="80" class="form-control">';
    echo format_text($course->ubd_stage1_knowledge ?? '', FORMAT_PLAIN);
    echo '</textarea>';
    echo '</div>';
    
    echo '</div>'; // End Stage 1
    
    // UbD Stage 2: Assessment Evidence
    echo '<div class="ubd-stage ubd-stage2">';
    echo '<h3>'.get_string('ubd_stage2', 'format_ubd').'</h3>';
    echo '<p class="ubd-stage-description">'.get_string('ubd_stage2_desc_zh', 'format_ubd').'</p>';
    
    // Performance Tasks
    echo '<div class="ubd-field">';
    echo '<label for="ubd_performance">'.get_string('ubd_performance_tasks', 'format_ubd').':</label>';
    echo '<textarea id="ubd_performance" name="ubd_stage2_performance" rows="4" cols="80" class="form-control">';
    echo format_text($course->ubd_stage2_performance ?? '', FORMAT_PLAIN);
    echo '</textarea>';
    echo '</div>';
    
    // Other Evidence
    echo '<div class="ubd-field">';
    echo '<label for="ubd_evidence">'.get_string('ubd_other_evidence', 'format_ubd').':</label>';
    echo '<textarea id="ubd_evidence" name="ubd_stage2_evidence" rows="4" cols="80" class="form-control">';
    echo format_text($course->ubd_stage2_evidence ?? '', FORMAT_PLAIN);
    echo '</textarea>';
    echo '</div>';
    
    echo '</div>'; // End Stage 2
    
    // UbD Stage 3: Learning Plan
    echo '<div class="ubd-stage ubd-stage3">';
    echo '<h3>'.get_string('ubd_stage3', 'format_ubd').'</h3>';
    echo '<p class="ubd-stage-description">'.get_string('ubd_stage3_desc_zh', 'format_ubd').'</p>';
    
    // Learning Activities
    echo '<div class="ubd-field">';
    echo '<label for="ubd_activities">'.get_string('ubd_learning_activities', 'format_ubd').':</label>';
    echo '<textarea id="ubd_activities" name="ubd_stage3_activities" rows="6" cols="80" class="form-control">';
    echo format_text($course->ubd_stage3_activities ?? '', FORMAT_PLAIN);
    echo '</textarea>';
    echo '</div>';
    
    echo '</div>'; // End Stage 3
    
    // Save button
    echo '<div class="ubd-save-section">';
    echo '<button type="button" id="save-ubd-plan" class="btn btn-primary">'.get_string('save_ubd_plan', 'format_ubd').'</button>';
    echo '</div>';
    
    echo '</div>'; // End UbD planning interface
    
    // Add some basic styling
    echo '<style>
    .ubd-planning-interface {
        margin: 20px 0;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #f9f9f9;
    }
    .ubd-stage {
        margin: 20px 0;
        padding: 15px;
        border-left: 4px solid #0073aa;
        background-color: white;
    }
    .ubd-stage1 { border-left-color: #d63384; }
    .ubd-stage2 { border-left-color: #fd7e14; }
    .ubd-stage3 { border-left-color: #198754; }
    .ubd-field {
        margin: 15px 0;
    }
    .ubd-field label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .ubd-stage-description {
        font-style: italic;
        color: #666;
        margin-bottom: 15px;
    }
    .ubd-save-section {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #ddd;
    }
    </style>';
    
    // Add JavaScript for saving UbD plan
    echo '<script>
    // Set course ID for JavaScript
    if (typeof M === "undefined") { var M = {}; }
    if (typeof M.cfg === "undefined") { M.cfg = {}; }
    M.cfg.courseId = '.$course->id.';
    M.cfg.sesskey = "'.sesskey().'";
    M.cfg.wwwroot = "'.$CFG->wwwroot.'";

    document.addEventListener("DOMContentLoaded", function() {
        var saveButton = document.getElementById("save-ubd-plan");
        if (saveButton) {
            saveButton.addEventListener("click", function() {
                // Call the saveUbDPlan function from format.js
                if (typeof saveUbDPlan === "function") {
                    saveUbDPlan();
                } else {
                    console.error("saveUbDPlan function not found");
                }
            });
        }
    });
    </script>';
}

if (($marker >=0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

// Make sure section 0 is created.
course_create_sections_if_missing($course, 0);

$renderer = $PAGE->get_renderer('format_ubd');

if (!empty($displaysection)) {
    $renderer->print_single_section_page($course, null, null, null, null, $displaysection);
} else {
    $renderer->print_multiple_section_page($course, null, null, null, null);
}

// Include course format js module
$PAGE->requires->js('/course/format/ubd/format.js');
