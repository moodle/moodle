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
 * Learning Plan form definition.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coursematrix\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for editing a learning plan.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plan_form extends \moodleform {
    /**
     * Define the form definition.
     */
    public function definition() {
        global $DB, $PAGE;

        $mform = $this->_form;
        $planid = $this->_customdata['id'] ?? 0;
        $existingcourses = $this->_customdata['courses'] ?? [];

        $mform->addElement('hidden', 'id', $planid);
        $mform->setType('id', PARAM_INT);

        // Plan name.
        $mform->addElement('text', 'name', get_string('planname', 'local_coursematrix'), ['size' => 50]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        // Description.
        $mform->addElement('editor', 'description', get_string('plandescription', 'local_coursematrix'));
        $mform->setType('description', PARAM_RAW);

        // Get all courses for dropdown.
        $allcourses = $DB->get_records_menu('course', [], 'fullname', 'id, fullname');
        if (isset($allcourses[1])) {
            unset($allcourses[1]); // Remove site course.
        }

        // Courses configuration section.
        $mform->addElement('header', 'coursesheader', get_string('plancourses', 'local_coursematrix'));

        $mform->addElement('static', 'courses_info', '',
            '<div class="alert alert-info">' .
            get_string('courseorder_help', 'local_coursematrix') .
            '</div>'
        );

        // Add course button - opens selector.
        $mform->addElement('autocomplete', 'add_course', get_string('addcourse', 'local_coursematrix'), $allcourses, [
            'multiple' => false,
            'noselectionstring' => get_string('selectcourses', 'local_coursematrix'),
        ]);

        // Hidden field to store course configuration as JSON.
        $mform->addElement('hidden', 'course_config', '[]');
        $mform->setType('course_config', PARAM_RAW);

        // Container for the dynamic course table.
        $mform->addElement('html', '<div id="course-config-container" class="mb-3"></div>');

        // Add JavaScript to handle dynamic course table.
        $PAGE->requires->js_amd_inline("
require(['jquery'], function($) {
    var courseData = [];
    var allCourses = " . json_encode($allcourses) . ";
    
    // Load existing data.
    var existingConfig = $('#id_course_config').val();
    if (existingConfig && existingConfig !== '[]') {
        try {
            courseData = JSON.parse(existingConfig);
        } catch(e) {
            courseData = [];
        }
    }
    
    function renderTable() {
        var html = '';
        if (courseData.length > 0) {
            html = '<table class=\"table table-striped table-sm\" id=\"course-config-table\">';
            html += '<thead><tr>';
            html += '<th style=\"width:60px\">" . get_string('order', 'local_coursematrix') . "</th>';
            html += '<th>" . get_string('course') . "</th>';
            html += '<th style=\"width:120px\">" . get_string('duedays', 'local_coursematrix') . "</th>';
            html += '<th style=\"width:180px\">" . get_string('reminders', 'local_coursematrix') . "</th>';
            html += '<th style=\"width:80px\">" . get_string('actions', 'local_coursematrix') . "</th>';
            html += '</tr></thead><tbody>';
            
            for (var i = 0; i < courseData.length; i++) {
                var c = courseData[i];
                var courseName = allCourses[c.courseid] || 'Course ' + c.courseid;
                html += '<tr data-index=\"' + i + '\">';
                html += '<td><input type=\"number\" class=\"form-control form-control-sm course-order\" value=\"' + c.sortorder + '\" min=\"1\" style=\"width:60px\"></td>';
                html += '<td>' + courseName + '</td>';
                html += '<td><input type=\"number\" class=\"form-control form-control-sm course-duedays\" value=\"' + c.duedays + '\" min=\"1\" style=\"width:80px\"> " . get_string('days') . "</td>';
                html += '<td><input type=\"text\" class=\"form-control form-control-sm course-reminders\" value=\"' + c.reminders + '\" placeholder=\"7, 3, 1\"></td>';
                html += '<td><button type=\"button\" class=\"btn btn-sm btn-danger remove-course\" title=\"" . get_string('remove') . "\"><i class=\"fa fa-trash\"></i></button></td>';
                html += '</tr>';
            }
            
            html += '</tbody></table>';
            html += '<p class=\"text-muted small\"><i class=\"fa fa-info-circle\"></i> " . addslashes(get_string('reminders_help_short', 'local_coursematrix')) . "</p>';
        } else {
            html = '<div class=\"alert alert-secondary\">" . get_string('nocourses', 'local_coursematrix') . "</div>';
        }
        
        $('#course-config-container').html(html);
        updateHiddenField();
    }
    
    function updateHiddenField() {
        $('#id_course_config').val(JSON.stringify(courseData));
    }
    
    function sortByOrder() {
        courseData.sort(function(a, b) {
            return a.sortorder - b.sortorder;
        });
    }
    
    // Add course handler.
    $('#id_add_course').on('change', function() {
        var courseId = parseInt($(this).val());
        if (!courseId) return;
        
        // Check if already added.
        for (var i = 0; i < courseData.length; i++) {
            if (courseData[i].courseid == courseId) {
                alert('" . addslashes(get_string('coursealreadyadded', 'local_coursematrix')) . "');
                $(this).val('');
                return;
            }
        }
        
        // Add new course.
        courseData.push({
            courseid: courseId,
            sortorder: courseData.length + 1,
            duedays: 14,
            reminders: '7, 3, 1'
        });
        
        $(this).val('');
        renderTable();
    });
    
    // Remove course handler.
    $(document).on('click', '.remove-course', function() {
        var index = $(this).closest('tr').data('index');
        courseData.splice(index, 1);
        // Re-order remaining.
        for (var i = 0; i < courseData.length; i++) {
            courseData[i].sortorder = i + 1;
        }
        renderTable();
    });
    
    // Update order handler.
    $(document).on('change', '.course-order', function() {
        var index = $(this).closest('tr').data('index');
        courseData[index].sortorder = parseInt($(this).val()) || 1;
        sortByOrder();
        renderTable();
    });
    
    // Update duedays handler.
    $(document).on('change', '.course-duedays', function() {
        var index = $(this).closest('tr').data('index');
        courseData[index].duedays = parseInt($(this).val()) || 14;
        updateHiddenField();
    });
    
    // Update reminders handler.
    $(document).on('change', '.course-reminders', function() {
        var index = $(this).closest('tr').data('index');
        courseData[index].reminders = $(this).val();
        updateHiddenField();
    });
    
    // Initial render.
    renderTable();
});
        ");

        $this->add_action_buttons(true, get_string('save', 'local_coursematrix'));
    }

    /**
     * Process incoming data.
     */
    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return null;
        }

        // Parse course config from JSON.
        $courseconfig = [];
        if (!empty($data->course_config)) {
            $decoded = json_decode($data->course_config, true);
            if (is_array($decoded)) {
                $courseconfig = $decoded;
            }
        }

        // Convert to expected format.
        $data->courses = [];
        $data->duedays = [];
        $data->reminders = [];
        $data->course_reminders = []; // Per-course reminders.

        // Sort by sortorder.
        usort($courseconfig, function($a, $b) {
            return ($a['sortorder'] ?? 1) - ($b['sortorder'] ?? 1);
        });

        foreach ($courseconfig as $cc) {
            $data->courses[] = $cc['courseid'];
            $data->duedays[] = $cc['duedays'] ?? 14;
            
            // Parse per-course reminders.
            $reminderStr = $cc['reminders'] ?? '7, 3, 1';
            $reminderDays = array_filter(array_map('intval', preg_split('/[,\s]+/', $reminderStr)));
            $data->course_reminders[$cc['courseid']] = $reminderDays;
            
            // Also keep a flat reminder list for backward compatibility.
            foreach ($reminderDays as $day) {
                if (!in_array($day, $data->reminders)) {
                    $data->reminders[] = $day;
                }
            }
        }

        rsort($data->reminders); // Sort descending.

        return $data;
    }

    /**
     * Set data for editing.
     */
    public function set_data($data) {
        global $DB;

        // Build course config JSON from existing data.
        $courseconfig = [];
        
        if (!empty($data->id)) {
            // Load existing course configuration.
            $plancourses = $DB->get_records('local_coursematrix_plan_courses', 
                ['planid' => $data->id], 'sortorder');
            
            foreach ($plancourses as $pc) {
                // Get reminders for this specific course.
                $reminders = $DB->get_records('local_coursematrix_reminders', 
                    ['planid' => $data->id, 'courseid' => $pc->courseid], 'daysbefore DESC');
                
                $reminderDays = [];
                foreach ($reminders as $r) {
                    $reminderDays[] = $r->daysbefore;
                }
                
                // If no course-specific reminders, check plan-level.
                if (empty($reminderDays)) {
                    $planReminders = $DB->get_records('local_coursematrix_reminders', 
                        ['planid' => $data->id, 'courseid' => null], 'daysbefore DESC');
                    foreach ($planReminders as $r) {
                        $reminderDays[] = $r->daysbefore;
                    }
                }
                
                if (empty($reminderDays)) {
                    $reminderDays = [7, 3, 1];
                }
                
                $courseconfig[] = [
                    'courseid' => $pc->courseid,
                    'sortorder' => $pc->sortorder,
                    'duedays' => $pc->duedays,
                    'reminders' => implode(', ', $reminderDays),
                ];
            }
        }
        
        $data->course_config = json_encode($courseconfig);
        
        parent::set_data($data);
    }
}

