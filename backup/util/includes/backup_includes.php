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
 * @package moodlecore
 * @subpackage backup-includes
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevent direct access to this file
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

// Include all the backup needed stuff
require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/executable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/processable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/annotable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/loggable.class.php');
require_once($CFG->dirroot . '/backup/backup.class.php');
require_once($CFG->dirroot . '/backup/util/output/output_controller.class.php');
require_once($CFG->dirroot . '/backup/util/factories/backup_factory.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_structure_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_controller_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_plan_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_question_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/checks/backup_check.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_atom.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_attribute.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_final_element.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_nested_element.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_optigroup.class.php');
require_once($CFG->dirroot . '/backup/util/structure/base_processor.class.php');
require_once($CFG->dirroot . '/backup/util/structure/backup_attribute.class.php');
require_once($CFG->dirroot . '/backup/util/structure/backup_final_element.class.php');
require_once($CFG->dirroot . '/backup/util/structure/backup_nested_element.class.php');
require_once($CFG->dirroot . '/backup/util/structure/backup_optigroup.class.php');
require_once($CFG->dirroot . '/backup/util/structure/backup_optigroup_element.class.php');
require_once($CFG->dirroot . '/backup/util/structure/backup_structure_processor.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_helper.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_general_helper.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_null_iterator.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_array_iterator.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_anonymizer_helper.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_file_manager.class.php');
require_once($CFG->dirroot . '/backup/util/xml/xml_writer.class.php');
require_once($CFG->dirroot . '/backup/util/xml/output/xml_output.class.php');
require_once($CFG->dirroot . '/backup/util/xml/output/file_xml_output.class.php');
require_once($CFG->dirroot . '/backup/util/xml/contenttransformer/xml_contenttransformer.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/base_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/error_log_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/file_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/database_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/output_indented_logger.class.php');
require_once($CFG->dirroot . '/backup/util/settings/setting_dependency.class.php');
require_once($CFG->dirroot . '/backup/util/settings/base_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/root/root_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/activity/activity_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/section/section_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/course/course_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_plan.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_plan.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_task.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_task.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_step.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_step.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_structure_step.class.php');
require_once($CFG->dirroot . '/backup/util/plan/backup_execution_step.class.php');
require_once($CFG->dirroot . '/backup/controller/backup_controller.class.php');
require_once($CFG->dirroot . '/backup/util/ui/base_moodleform.class.php');
require_once($CFG->dirroot . '/backup/util/ui/base_ui.class.php');
require_once($CFG->dirroot . '/backup/util/ui/base_ui_stage.class.php');
require_once($CFG->dirroot . '/backup/util/ui/backup_moodleform.class.php');
require_once($CFG->dirroot . '/backup/util/ui/backup_ui.class.php');
require_once($CFG->dirroot . '/backup/util/ui/backup_ui_stage.class.php');
require_once($CFG->dirroot . '/backup/util/ui/backup_ui_setting.class.php');

// And some moodle stuff too
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/gradelib.php');
