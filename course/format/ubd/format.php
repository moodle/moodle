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
    echo '<div class="ubd-planning-interface" data-courseid="'.$course->id.'">';
    echo '<div class="ubd-header">';
    echo '<h2>'.get_string('pluginname', 'format_ubd').'</h2>';
    echo '<p class="ubd-description">Use this structured framework to design your course following Understanding by Design principles.</p>';
    echo '</div>';
    
    // UbD Stage 1: Desired Results
    echo '<div class="ubd-stage ubd-stage1">';
    echo '<h3><span class="ubd-stage-number">1</span>'.get_string('ubd_stage1', 'format_ubd').'</h3>';
    echo '<div class="ubd-stage-content">';
    echo '<p class="ubd-stage-description">'.get_string('ubd_stage1_desc_zh', 'format_ubd').'</p>';

    // Enduring Understandings
    echo '<div class="ubd-field">';
    echo '<label for="ubd_enduring">'.get_string('ubd_enduring_understandings', 'format_ubd').':</label>';
    echo '<textarea id="ubd_enduring" name="ubd_stage1_enduring" rows="4" class="form-control ubd-textarea" placeholder="What should students understand long after they forget the details?">';
    echo htmlspecialchars($course->ubd_stage1_enduring ?? '', ENT_QUOTES, 'UTF-8');
    echo '</textarea>';
    echo '</div>';

    // Essential Questions
    echo '<div class="ubd-field">';
    echo '<label for="ubd_questions">'.get_string('ubd_essential_questions', 'format_ubd').':</label>';
    echo '<textarea id="ubd_questions" name="ubd_stage1_questions" rows="4" class="form-control ubd-textarea" placeholder="What provocative questions will foster inquiry and understanding?">';
    echo htmlspecialchars($course->ubd_stage1_questions ?? '', ENT_QUOTES, 'UTF-8');
    echo '</textarea>';
    echo '</div>';

    // Knowledge & Skills
    echo '<div class="ubd-field">';
    echo '<label for="ubd_knowledge">'.get_string('ubd_knowledge_skills', 'format_ubd').':</label>';
    echo '<textarea id="ubd_knowledge" name="ubd_stage1_knowledge" rows="4" class="form-control ubd-textarea" placeholder="What should students know and be able to do?">';
    echo htmlspecialchars($course->ubd_stage1_knowledge ?? '', ENT_QUOTES, 'UTF-8');
    echo '</textarea>';
    echo '</div>';

    echo '</div>'; // End Stage 1 content
    echo '</div>'; // End Stage 1
    
    // UbD Stage 2: Assessment Evidence
    echo '<div class="ubd-stage ubd-stage2">';
    echo '<h3><span class="ubd-stage-number">2</span>'.get_string('ubd_stage2', 'format_ubd').'</h3>';
    echo '<div class="ubd-stage-content">';
    echo '<p class="ubd-stage-description">'.get_string('ubd_stage2_desc_zh', 'format_ubd').'</p>';

    // Performance Tasks
    echo '<div class="ubd-field">';
    echo '<label for="ubd_performance">'.get_string('ubd_performance_tasks', 'format_ubd').':</label>';
    echo '<textarea id="ubd_performance" name="ubd_stage2_performance" rows="4" class="form-control ubd-textarea" placeholder="What authentic tasks will reveal evidence of understanding?">';
    echo htmlspecialchars($course->ubd_stage2_performance ?? '', ENT_QUOTES, 'UTF-8');
    echo '</textarea>';
    echo '</div>';

    // Other Evidence
    echo '<div class="ubd-field">';
    echo '<label for="ubd_evidence">'.get_string('ubd_other_evidence', 'format_ubd').':</label>';
    echo '<textarea id="ubd_evidence" name="ubd_stage2_evidence" rows="4" class="form-control ubd-textarea" placeholder="What other evidence will confirm understanding?">';
    echo htmlspecialchars($course->ubd_stage2_evidence ?? '', ENT_QUOTES, 'UTF-8');
    echo '</textarea>';
    echo '</div>';

    echo '</div>'; // End Stage 2 content
    echo '</div>'; // End Stage 2
    
    // UbD Stage 3: Learning Plan
    echo '<div class="ubd-stage ubd-stage3">';
    echo '<h3><span class="ubd-stage-number">3</span>'.get_string('ubd_stage3', 'format_ubd').'</h3>';
    echo '<div class="ubd-stage-content">';
    echo '<p class="ubd-stage-description">'.get_string('ubd_stage3_desc_zh', 'format_ubd').'</p>';

    // Learning Activities
    echo '<div class="ubd-field">';
    echo '<label for="ubd_activities">'.get_string('ubd_learning_activities', 'format_ubd').':</label>';
    echo '<textarea id="ubd_activities" name="ubd_stage3_activities" rows="6" class="form-control ubd-textarea" placeholder="What learning experiences will enable students to achieve desired results?">';
    echo htmlspecialchars($course->ubd_stage3_activities ?? '', ENT_QUOTES, 'UTF-8');
    echo '</textarea>';
    echo '</div>';

    echo '</div>'; // End Stage 3 content
    echo '</div>'; // End Stage 3
    
    // Save button
    echo '<div class="ubd-save-section">';
    echo '<button type="button" id="save-ubd-plan" class="btn btn-primary">'.get_string('save_ubd_plan', 'format_ubd').'</button>';
    echo '</div>';
    
    echo '</div>'; // End UbD planning interface
    
    // Add enhanced styling
    echo '<style>
    .ubd-planning-interface {
        margin: 20px 0;
        padding: 25px;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .ubd-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #dee2e6;
    }

    .ubd-header h2 {
        color: #495057;
        margin-bottom: 10px;
        font-size: 2.2em;
        font-weight: 300;
    }

    .ubd-description {
        color: #6c757d;
        font-size: 1.1em;
        margin: 0;
    }

    .ubd-stage {
        margin: 25px 0;
        padding: 0;
        border-radius: 10px;
        background-color: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .ubd-stage:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .ubd-stage h3 {
        margin: 0;
        padding: 20px 25px;
        color: white;
        font-size: 1.4em;
        font-weight: 500;
        cursor: pointer;
        user-select: none;
        display: flex;
        align-items: center;
        transition: background-color 0.3s ease;
    }

    .ubd-stage-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.2);
        margin-right: 15px;
        font-weight: bold;
        font-size: 1.1em;
    }

    .ubd-stage1 h3 { background: linear-gradient(135deg, #e91e63, #ad1457); }
    .ubd-stage2 h3 { background: linear-gradient(135deg, #ff9800, #f57c00); }
    .ubd-stage3 h3 { background: linear-gradient(135deg, #4caf50, #388e3c); }

    .ubd-stage1 h3:hover { background: linear-gradient(135deg, #ad1457, #880e4f); }
    .ubd-stage2 h3:hover { background: linear-gradient(135deg, #f57c00, #ef6c00); }
    .ubd-stage3 h3:hover { background: linear-gradient(135deg, #388e3c, #2e7d32); }

    .ubd-stage-content {
        padding: 25px;
    }

    .ubd-stage.collapsed .ubd-stage-content {
        display: none;
    }

    .ubd-field {
        margin: 20px 0;
    }

    .ubd-field label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #495057;
        font-size: 1.05em;
    }

    .ubd-textarea {
        width: 100%;
        min-height: 120px;
        padding: 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 14px;
        line-height: 1.5;
        resize: vertical;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .ubd-textarea:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .ubd-textarea.ubd-changed {
        border-color: #ffc107;
        background-color: #fffbf0;
    }

    .ubd-stage-description {
        font-style: italic;
        color: #6c757d;
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border-left: 4px solid #17a2b8;
    }

    .ubd-save-section {
        text-align: center;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #dee2e6;
    }

    .ubd-templates {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .ubd-templates h4 {
        margin: 0 0 15px 0;
        color: #495057;
        font-size: 1.2em;
    }

    .ubd-template-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .ubd-template-buttons .btn {
        flex: 1;
        min-width: 120px;
    }

    .ubd-toggle-icon {
        margin-left: auto;
        transition: transform 0.3s ease;
    }

    .ubd-stage.collapsed .ubd-toggle-icon {
        transform: rotate(-90deg);
    }

    .ubd-char-count {
        font-size: 0.85em;
        color: #6c757d;
        text-align: right;
        margin-top: 5px;
        transition: color 0.3s ease;
    }

    .ubd-unsaved-indicator {
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    @media (max-width: 768px) {
        .ubd-planning-interface {
            margin: 10px 0;
            padding: 15px;
        }

        .ubd-stage-content {
            padding: 15px;
        }

        .ubd-template-buttons {
            flex-direction: column;
        }

        .ubd-template-buttons .btn {
            flex: none;
        }
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
