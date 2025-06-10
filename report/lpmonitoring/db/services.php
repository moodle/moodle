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
 * Competency report webservice functions
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'report_lpmonitoring_get_scales_from_framework' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'get_scales_from_framework',
        'classpath'    => '',
        'description'  => 'Get scales from framework',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:competencyview',
        'ajax'         => true
    ),
    'report_lpmonitoring_read_report_competency_config' => array(
        'classname'     => 'report_lpmonitoring\external',
        'methodname'    => 'read_report_competency_config',
        'description'   => 'Returns the report competency configuration associated to a scale in a framework',
        'type'          => 'read',
        'capabilities'  => 'moodle/competency:competencyview',
        'ajax'         => true
    ),
    'report_lpmonitoring_create_report_competency_config' => array(
        'classname'     => 'report_lpmonitoring\external',
        'methodname'    => 'create_report_competency_config',
        'description'   => 'Create report competency configuration associated to a scale in a framework',
        'type'          => 'write',
        'capabilities'  => 'moodle/competency:competencyview',
        'ajax'         => true
    ),
    'report_lpmonitoring_update_report_competency_config' => array(
        'classname'     => 'report_lpmonitoring\external',
        'methodname'    => 'update_report_competency_config',
        'description'   => 'Update report competency configuration associated to a scale in a framework',
        'type'          => 'write',
        'capabilities'  => 'moodle/competency:competencyview',
        'ajax'         => true
    ),
    'report_lpmonitoring_search_users_by_templateid' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'search_users_by_templateid',
        'classpath'    => '',
        'description'  => 'Get users learning plan from learning plan template',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:templateview',
        'ajax'         => true
    ),
    'report_lpmonitoring_read_plan' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'read_plan',
        'classpath'    => '',
        'description'  => 'Get the plan information',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
    'report_lpmonitoring_get_competency_detail' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'get_competency_detail',
        'classpath'    => '',
        'description'  => 'Get the plan information',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
    'report_lpmonitoring_get_scales_from_template' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'get_scales_from_template',
        'classpath'    => '',
        'description'  => 'Get scales from template',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:templateview',
        'ajax'         => true
    ),
    'report_lpmonitoring_list_plan_competencies' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'list_plan_competencies',
        'classpath'    => '',
        'description'  => 'Get the list plan competencies',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
    'report_lpmonitoring_get_scales_from_template' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'get_scales_from_template',
        'classpath'    => '',
        'description'  => 'Get scales from template',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:templateview',
        'ajax'         => true
    ),
    'report_lpmonitoring_get_competency_statistics' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'get_competency_statistics',
        'classpath'    => '',
        'description'  => 'Get the competency statistics',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
    'report_lpmonitoring_get_competency_statistics_incourse' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'get_competency_statistics_incourse',
        'classpath'    => '',
        'description'  => 'Get the competency statistics in courses',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
    'report_lpmonitoring_get_competency_statistics_incoursemodules' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'get_competency_statistics_incoursemodules',
        'classpath'    => '',
        'description'  => 'Get the competency statistics in courses modules',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
    'report_lpmonitoring_search_templates' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'search_templates',
        'classpath'    => '',
        'description'  => 'Search template by contextid',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:templateview',
        'ajax'         => true
    ),
    'report_lpmonitoring_submit_manage_tags_form' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'submit_manage_tags_form',
        'classpath'    => '',
        'description'  => 'Save the tags submitted by a form',
        'type'         => 'write',
        'capabilities' => 'moodle/competency:competencygrade',
        'ajax'         => true
    ),
    'report_lpmonitoring_search_plans_with_tag' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'search_plans_with_tag',
        'classpath'    => '',
        'description'  => 'Search all learning plans for a specific tag',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
    'report_lpmonitoring_search_tags_for_accessible_plans' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'search_tags_for_accessible_plans',
        'classpath'    => '',
        'description'  => 'Search all tags associated to learning plans the user can view',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
    'report_lpmonitoring_get_comment_area_for_plan' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'get_comment_area_for_plan',
        'classpath'    => '',
        'description'  => 'Get the comment area for the specified learning plan',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
    'report_lpmonitoring_list_plan_competencies_report' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'list_plan_competencies_report',
        'classpath'    => '',
        'description'  => 'Get the list plan competencies report',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
    'report_lpmonitoring_add_rating_task' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'add_rating_task',
        'classpath'    => '',
        'description'  => 'Add task for rating competencies in learning plan template',
        'type'         => 'write',
        'capabilities' => 'moodle/competency:templateview',
        'ajax'         => true,
    ),
    'report_lpmonitoring_reset_grading' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'reset_grading',
        'classpath'    => '',
        'description'  => 'Reset the grade for one or all competencies of a learning plan',
        'type'         => 'write',
        'capabilities' => 'moodle/competency:competencygrade',
        'ajax'         => true
    ),
    'report_lpmonitoring_user_competency_viewed_in_course' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'user_competency_viewed_in_course',
        'classpath'    => '',
        'description'  => 'Log the user competency viewed in course event',
        'type'         => 'write',
        'capabilities' => 'moodle/competency:usercompetencyview',
        'ajax'         => true,
        'services'     => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'report_lpmonitoring_data_for_user_competency_summary_in_course' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'data_for_user_competency_summary_in_course',
        'classpath'    => '',
        'description'  => 'Load a summary of a user competency.',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:coursecompetencyview',
        'ajax'         => true,
        'services'     => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'report_lpmonitoring_list_plan_competencies_summary' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'list_plan_competencies_summary',
        'classpath'    => '',
        'description'  => 'Get the list plan competencies summary',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
    'report_lpmonitoring_get_user_pdfs' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'get_user_pdfs',
        'classpath'    => '',
        'description'  => 'Get many users\' reports in PDF format',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
    'report_lpmonitoring_get_user_pdf' => array(
        'classname'    => 'report_lpmonitoring\external',
        'methodname'   => 'get_user_pdf',
        'classpath'    => '',
        'description'  => 'Get the user\'s report in PDF format',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:planview',
        'ajax'         => true
    ),
);

