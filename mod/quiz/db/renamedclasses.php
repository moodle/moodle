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
 * This file contains mappings for classes that have been renamed.
 *
 * @package mod_quiz
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$renamedclasses = [
    // Since Moodle 4.1.
    'mod_quiz\local\views\secondary' => 'mod_quiz\navigation\views\secondary',
    // Since Moodle 4.2.
    'mod_quiz_display_options' => 'mod_quiz\question\display_options',
    'qubaids_for_quiz' => 'mod_quiz\question\qubaids_for_quiz',
    'qubaids_for_quiz_user' => 'mod_quiz\question\qubaids_for_quiz_user',
    'mod_quiz_admin_setting_browsersecurity' => 'mod_quiz\admin\browser_security_setting',
    'mod_quiz_admin_setting_grademethod' => 'mod_quiz\admin\grade_method_setting',
    'mod_quiz_admin_setting_overduehandling' => 'mod_quiz\admin\overdue_handling_setting',
    'mod_quiz_admin_review_setting' => 'mod_quiz\admin\review_setting',
    'mod_quiz_admin_setting_user_image' => 'mod_quiz\admin\user_image_setting',
    'mod_quiz\adminpresets\adminpresets_mod_quiz_admin_setting_browsersecurity' =>
            'mod_quiz\adminpresets\adminpresets_browser_security_setting',
    'mod_quiz\adminpresets/adminpresets_mod_quiz_admin_setting_grademethod' =>
            'mod_quiz\adminpresets\adminpresets_grade_method_setting',
    'mod_quiz\adminpresets\adminpresets_mod_quiz_admin_setting_overduehandling' =>
            'mod_quiz\adminpresets\adminpresets_overdue_handling_setting',
    'mod_quiz\adminpresets\adminpresets_mod_quiz_admin_review_setting' =>
            'mod_quiz\adminpresets\adminpresets_review_setting',
    'mod_quiz\adminpresets\adminpresets_mod_quiz_admin_setting_user_image' =>
            'mod_quiz\adminpresets\adminpresets_user_image_setting',
    'quiz_default_report' => 'mod_quiz\local\reports\report_base',
    'quiz_attempts_report' => 'mod_quiz\local\reports\attempts_report',
    'mod_quiz_attempts_report_form' => 'mod_quiz\local\reports\attempts_report_options_form',
    'mod_quiz_attempts_report_options' => 'mod_quiz\local\reports\attempts_report_options',
    'quiz_attempts_report_table' => 'mod_quiz\local\reports\attempts_report_table',
    'quiz_access_manager' => 'mod_quiz\access_manager',
    'mod_quiz_preflight_check_form' => 'mod_quiz\form\preflight_check_form',
    'quiz_override_form' => 'mod_quiz\form\edit_override_form',
    'quiz_access_rule_base' => 'mod_quiz\local\access_rule_base',
];
