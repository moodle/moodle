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
 * This file contains mappings for classes that have been renamed so that they meet the requirements of the autoloader.
 *
 * Renaming isn't always the recommended approach, but can provide benefit in situations where we've already got a
 * close structure, OR where lots of classes get included and not necessarily used, or checked for often.
 *
 * When renaming a class delete the original class and add an entry to the db/renamedclasses.php directory for that
 * component.
 * This way we don't need to keep around old classes, instead creating aliases only when required.
 * One big advantage to this method is that we provide consistent debugging for renamed classes when they are used.
 *
 * @package    core
 * @copyright  2014 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Like other files in the db directory this file uses an array.
// The old class name is the key, the new class name is the value.
// The array must be called $renamedclasses.
$renamedclasses = [
    // Since Moodle 4.0.
    'format_base' => 'core_courseformat\\base',
    'format_topics_renderer' => 'format_topics\\output\\renderer',
    'format_section_renderer_base' => 'core_courseformat\\output\\section_renderer',
    'format_singleactivity_renderer' => 'format_singleactivity\\output\\renderer',
    'format_site_renderer' => 'core_courseformat\\output\\site_renderer',
    'format_weeks_renderer' => 'format_weeks\\output\\renderer',
    'core_question\\bank\\action_column_base' => 'core_question\\local\\bank\\action_column_base',
    'core_question\\bank\\checkbox_column' => 'core_question\\local\\bank\\checkbox_column',
    'core_question\\bank\\column_base' => 'core_question\\local\\bank\\column_base',
    'core_question\\bank\\edit_menu_column' => 'core_question\\local\\bank\\edit_menu_column',
    'core_question\\bank\\menu_action_column_base' => 'core_question\\local\\bank\\menu_action_column_base',
    'core_question\\bank\\menuable_action' => 'core_question\\local\\bank\\menuable_action',
    'core_question\\bank\\random_question_loader' => 'core_question\\local\\bank\\random_question_loader',
    'core_question\\bank\\row_base' => 'core_question\\local\\bank\\row_base',
    'core_question\\bank\\view' => 'core_question\\local\\bank\\view',
    'core_question\\bank\\copy_action_column' => 'qbank_editquestion\\copy_action_column',
    'core_question\\bank\\edit_action_column' => 'qbank_editquestion\\edit_action_column',
    'core_question\\bank\\creator_name_column' => 'qbank_viewcreator\\creator_name_column',
    'core_question\\bank\\question_name_column' => 'qbank_viewquestionname\\viewquestionname_column_helper',
    'core_question\\bank\\question_name_idnumber_tags_column' => 'qbank_viewquestionname\\question_name_idnumber_tags_column',
    'core_question\\bank\\delete_action_column' => 'qbank_deletequestion\\delete_action_column',
    'core_question\\bank\\export_xml_action_column' => 'qbank_exporttoxml\\export_xml_action_column',
    'core_question\\bank\\preview_action_column' => 'qbank_previewquestion\\preview_action_column',
    'core_question\\bank\\question_text_row' => 'qbank_viewquestiontext\\question_text_row',
    'core_question\\bank\\question_type_column' => 'qbank_viewquestiontype\\question_type_column',
    'core_question\\bank\\tags_action_column' => 'qbank_tagquestion\\tags_action_column',
    'core_question\\output\\qbank_chooser' => 'qbank_editquestion\\qbank_chooser',
    'core_question\\output\\qbank_chooser_item' => 'qbank_editquestion\\qbank_chooser_item',
    'question_move_form' => 'qbank_managecategories\\form\\question_move_form',
    'question_import_form' => 'qbank_importquestions\\form\\question_import_form',
    'question_category_list' => 'qbank_managecategories\\question_category_list',
    'question_category_list_item' => 'qbank_managecategories\\question_category_list_item',
    'question_category_object' => 'qbank_managecategories\\question_category_object',
    'category_form' => 'qbank_managecategories\\form\\category_form',
    'export_form' => 'qbank_exportquestions\\form\\export_form',
    'preview_options_form' => 'qbank_previewquestion\\form\\preview_options_form',
    'question_preview_options' => 'qbank_previewquestion\\output\\question_preview_options',
    'core_question\\form\\tags' => 'qbank_tagquestion\\form\\tags_form',
    'context_to_string_translator' => 'core_question\\local\\bank\\context_to_string_translator',
    'question_edit_contexts' => 'core_question\\local\\bank\\question_edit_contexts',
    // Since Moodle 4.1.
    'core_admin\\local\\systemreports\\task_logs' => 'core_admin\\reportbuilder\\local\\systemreports\\task_logs',
    'core_admin\\local\\entities\\task_log' => 'core_admin\\reportbuilder\\local\\entities\\task_log',
    'core_course\\local\\entities\\course_category' => 'core_course\\reportbuilder\\local\\entities\\course_category',
    'core_cohort\\local\\entities\\cohort' => 'core_cohort\\reportbuilder\\local\\entities\\cohort',
    'core_cohort\\local\\entities\\cohort_member' => 'core_cohort\\reportbuilder\\local\\entities\\cohort_member',
    'core_block\\local\\views\\secondary' => 'core_block\\navigation\\views\\secondary',
    // Since Moodle 4.2.
    'Box\\Spout' => 'OpenSpout',
    // Since Moodle 4.3.
    'core_question\\bank\\search\\condition' => 'core_question\\local\\bank\\condition',
    'core_question\\bank\\search\\category_condition' => 'qbank_managecategories\\category_condition',
    'core_question\\bank\\search\\hidden_condition' => 'qbank_deletequestion\\hidden_condition',
];
