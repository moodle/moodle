<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/completionlib.php');

class course_completion_form extends moodleform {

    function definition() {
        global $USER, $CFG, $DB, $js_enabled;

        $courseconfig = get_config('moodlecourse');
        $mform    =& $this->_form;

        $course   = $this->_customdata['course'];
        $completion = new completion_info($course);

        $params = array(
            'course'  => $course->id
        );


/// form definition
//--------------------------------------------------------------------------------

        // Check if there is existing criteria completions
        if ($completion->is_course_locked()) {
            $mform->addElement('header', '', get_string('completionsettingslocked', 'completion'));
            $mform->addElement('static', '', '', get_string('err_settingslocked', 'completion'));
            $mform->addElement('submit', 'settingsunlock', get_string('unlockcompletiondelete', 'completion'));
        }

        // Get array of all available aggregation methods
        $aggregation_methods = $completion->get_aggregation_methods();

        // Overall criteria aggregation
        $mform->addElement('header', 'overallcriteria', get_string('overallcriteriaaggregation', 'completion'));
        $mform->addElement('select', 'overall_aggregation', get_string('aggregationmethod', 'completion'), $aggregation_methods);
        $mform->setDefault('overall_aggregation', $completion->get_aggregation_method());

        // Course prerequisite completion criteria
        $mform->addElement('header', 'courseprerequisites', get_string('courseprerequisites', 'completion'));

        // Get applicable courses
        $courses = $DB->get_records_sql(
            "
                SELECT DISTINCT
                    c.id,
                    c.category,
                    c.fullname,
                    cc.id AS selected
                FROM
                    {course} c
                LEFT JOIN
                    {course_completion_criteria} cc
                 ON cc.courseinstance = c.id
                AND cc.course = {$course->id}
                INNER JOIN
                    {course_completion_criteria} ccc
                 ON ccc.course = c.id
                WHERE
                    c.enablecompletion = ".COMPLETION_ENABLED."
                AND c.id <> {$course->id}
            "
        );

        if (!empty($courses)) {
            if (count($courses) > 1) {
                $mform->addElement('select', 'course_aggregation', get_string('aggregationmethod', 'completion'), $aggregation_methods);
                $mform->setDefault('course_aggregation', $completion->get_aggregation_method(COMPLETION_CRITERIA_TYPE_COURSE));
            }

            // Get category list
            $list = array();
            $parents = array();
            make_categories_list($list, $parents);

            // Get course list for select box
            $selectbox = array();
            $selected = array();
            foreach ($courses as $c) {
                $selectbox[$c->id] = $list[$c->category] . ' / ' . format_string($c->fullname, true, array('context' => get_context_instance(CONTEXT_COURSE, $c->id)));

                // If already selected
                if ($c->selected) {
                    $selected[] = $c->id;
                }
            }

            // Show multiselect box
            $mform->addElement('select', 'criteria_course', get_string('coursesavailable', 'completion'), $selectbox, array('multiple' => 'multiple', 'size' => 6));

            // Select current criteria
            $mform->setDefault('criteria_course', $selected);

            // Explain list
            $mform->addElement('static', 'criteria_courses_explaination', '', get_string('coursesavailableexplaination', 'completion'));

        } else {
            $mform->addElement('static', 'nocourses', '', get_string('err_nocourses', 'completion'));
        }

        // Manual self completion
        $mform->addElement('header', 'manualselfcompletion', get_string('manualselfcompletion', 'completion'));
        $criteria = new completion_criteria_self($params);
        $criteria->config_form_display($mform);

        // Role completion criteria
        $mform->addElement('header', 'roles', get_string('manualcompletionby', 'completion'));

        $roles = get_roles_with_capability('moodle/course:markcomplete', CAP_ALLOW, get_context_instance(CONTEXT_COURSE, $course->id));

        if (!empty($roles)) {
            $mform->addElement('select', 'role_aggregation', get_string('aggregationmethod', 'completion'), $aggregation_methods);
            $mform->setDefault('role_aggregation', $completion->get_aggregation_method(COMPLETION_CRITERIA_TYPE_ROLE));

            foreach ($roles as $role) {
                $params_a = array('role' => $role->id);
                $criteria = new completion_criteria_role(array_merge($params, $params_a));
                $criteria->config_form_display($mform, $role);
            }
        } else {
            $mform->addElement('static', 'noroles', '', get_string('err_noroles', 'completion'));
        }

        // Activity completion criteria
        $mform->addElement('header', 'activitiescompleted', get_string('activitiescompleted', 'completion'));

        $activities = $completion->get_activities();
        if (!empty($activities)) {
            if (count($activities) > 1) {
                $mform->addElement('select', 'activity_aggregation', get_string('aggregationmethod', 'completion'), $aggregation_methods);
                $mform->setDefault('activity_aggregation', $completion->get_aggregation_method(COMPLETION_CRITERIA_TYPE_ACTIVITY));
            }

            foreach ($activities as $activity) {
                $params_a = array('moduleinstance' => $activity->id);
                $criteria = new completion_criteria_activity(array_merge($params, $params_a));
                $criteria->config_form_display($mform, $activity);
            }
        } else {
            $mform->addElement('static', 'noactivities', '', get_string('err_noactivities', 'completion'));
        }

        // Completion on date
        $mform->addElement('header', 'date', get_string('date'));
        $criteria = new completion_criteria_date($params);
        $criteria->config_form_display($mform);

        // Completion after enrolment duration
        $mform->addElement('header', 'duration', get_string('durationafterenrolment', 'completion'));
        $criteria = new completion_criteria_duration($params);
        $criteria->config_form_display($mform);

        // Completion on course grade
        $mform->addElement('header', 'grade', get_string('grade'));

        // Grade enable and passing grade
        $course_grade = $DB->get_field('grade_items', 'gradepass', array('courseid' => $course->id, 'itemtype' => 'course'));
        $criteria = new completion_criteria_grade($params);
        $criteria->config_form_display($mform, $course_grade);

        // Completion on unenrolment
        $mform->addElement('header', 'unenrolment', get_string('unenrolment', 'completion'));
        $criteria = new completion_criteria_unenrol($params);
        $criteria->config_form_display($mform);


//--------------------------------------------------------------------------------
        $this->add_action_buttons();
//--------------------------------------------------------------------------------
        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);

        // If the criteria are locked, freeze values and submit button
        if ($completion->is_course_locked()) {
            $except = array('settingsunlock');
            $mform->hardFreezeAllVisibleExcept($except);
            $mform->addElement('cancel');
        }
    }


/// perform some extra moodle validation
    function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);

        return $errors;
    }
}
?>
