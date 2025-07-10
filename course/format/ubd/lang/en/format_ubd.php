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
 * Language strings for UbD course format.
 *
 * @package    format_ubd
 * @copyright  2025 Moodle Evolved Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'UbD Format (Understanding by Design)';
$string['privacy:metadata'] = 'The UbD format plugin does not store any personal data.';

// UbD specific strings
$string['ubd_stage1'] = 'Stage 1: Desired Results (预期学习成果)';
$string['ubd_stage2'] = 'Stage 2: Assessment Evidence (评估证据)';
$string['ubd_stage3'] = 'Stage 3: Learning Plan (教学活动)';

$string['ubd_stage1_desc'] = 'What should students know, understand, and be able to do? What content is worthy of understanding?';
$string['ubd_stage1_desc_zh'] = '学生应该知道、理解和能够做什么？什么内容值得理解？';

$string['ubd_stage2_desc'] = 'How will we know if students have achieved the desired results? What will we accept as evidence?';
$string['ubd_stage2_desc_zh'] = '我们如何知道学生是否达到了预期的结果？我们将接受什么作为证据？';

$string['ubd_stage3_desc'] = 'What learning experiences and instruction will enable students to achieve the desired results?';
$string['ubd_stage3_desc_zh'] = '什么样的学习体验和教学将使学生能够达到预期的结果？';

// Form labels
$string['ubd_enduring_understandings'] = 'Enduring Understandings (持久理解)';
$string['ubd_essential_questions'] = 'Essential Questions (核心问题)';
$string['ubd_knowledge_skills'] = 'Knowledge & Skills (知识与技能)';
$string['ubd_performance_tasks'] = 'Performance Tasks (表现性任务)';
$string['ubd_other_evidence'] = 'Other Evidence (其他证据)';
$string['ubd_learning_activities'] = 'Learning Activities (学习活动)';

// Settings
$string['sectionname'] = 'Topic';
$string['section0name'] = 'General';
$string['hidefromothers'] = 'Hide topic';
$string['showfromothers'] = 'Show topic';
$string['currentsection'] = 'This topic';
$string['editsection'] = 'Edit topic';
$string['deletesection'] = 'Delete topic';
$string['newsectionname'] = 'New name for topic {$a}';
$string['addsections'] = 'Add topics';

// UbD form settings
$string['ubd_enable'] = 'Enable UbD Planning';
$string['ubd_enable_desc'] = 'Enable the Understanding by Design planning interface for this course';
$string['save_ubd_plan'] = 'Save UbD Plan';
$string['edit_ubd_plan'] = 'Edit UbD Plan';

// Templates and actions
$string['ubd_templates'] = 'UbD Templates';
$string['load_elementary_template'] = 'Elementary Template';
$string['load_secondary_template'] = 'Secondary Template';
$string['load_university_template'] = 'University Template';
$string['clear_all_fields'] = 'Clear All Fields';
$string['export_ubd_plan'] = 'Export UbD Plan';

// Notifications and messages
$string['ubd_plan_saved'] = 'UbD plan saved successfully!';
$string['ubd_plan_save_error'] = 'Error saving UbD plan';
$string['ubd_plan_exported'] = 'UbD plan exported successfully!';
$string['template_loaded'] = 'Template loaded successfully! Remember to save your changes.';
$string['fields_cleared'] = 'All fields cleared. Remember to save your changes.';
$string['unsaved_changes'] = 'You have unsaved changes. Are you sure you want to leave?';
$string['auto_saving'] = 'Auto-saving...';
$string['validation_error'] = 'Validation error';
$string['content_too_long'] = 'Content exceeds maximum length';
$string['total_content_too_long'] = 'Total content exceeds maximum allowed length';

// Help and guidance
$string['ubd_help_title'] = 'Understanding by Design Help';
$string['ubd_help_content'] = 'UbD is a framework for designing curriculum that emphasizes teaching and assessing for understanding and learning transfer.';
$string['stage1_help'] = 'Start with the end in mind - what should students understand?';
$string['stage2_help'] = 'Determine acceptable evidence of understanding';
$string['stage3_help'] = 'Plan learning experiences and instruction';

// Placeholders
$string['placeholder_enduring'] = 'What should students understand long after they forget the details?';
$string['placeholder_questions'] = 'What provocative questions will foster inquiry and understanding?';
$string['placeholder_knowledge'] = 'What should students know and be able to do?';
$string['placeholder_performance'] = 'What authentic tasks will reveal evidence of understanding?';
$string['placeholder_evidence'] = 'What other evidence will confirm understanding?';
$string['placeholder_activities'] = 'What learning experiences will enable students to achieve desired results?';

// Settings
$string['autosave_interval'] = 'Auto-save interval';
$string['autosave_interval_desc'] = 'How often to automatically save UbD plan data (0 to disable)';
$string['max_field_length'] = 'Maximum field length';
$string['max_field_length_desc'] = 'Maximum number of characters allowed per field';
$string['max_total_length'] = 'Maximum total content length';
$string['max_total_length_desc'] = 'Maximum total number of characters allowed across all fields';
$string['enable_templates'] = 'Enable templates';
$string['enable_templates_desc'] = 'Allow users to load pre-defined UbD templates';
$string['enable_export'] = 'Enable export';
$string['enable_export_desc'] = 'Allow users to export their UbD plans';
$string['default_expanded_stages'] = 'Default expanded stages';
$string['default_expanded_stages_desc'] = 'Which stages should be expanded by default when loading the page';

// Theme customization
$string['theme_customization'] = 'Theme Customization';
$string['theme_customization_desc'] = 'Customize the appearance of UbD stages';
$string['stage1_color'] = 'Stage 1 color';
$string['stage1_color_desc'] = 'Color for Stage 1: Desired Results';
$string['stage2_color'] = 'Stage 2 color';
$string['stage2_color_desc'] = 'Color for Stage 2: Assessment Evidence';
$string['stage3_color'] = 'Stage 3 color';
$string['stage3_color_desc'] = 'Color for Stage 3: Learning Plan';
