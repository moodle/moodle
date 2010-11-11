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
require_once($CFG->dirroot . '/backup/util/interfaces/loggable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/executable.class.php');
require_once($CFG->dirroot . '/backup/util/interfaces/processable.class.php');
require_once($CFG->dirroot . '/backup/backup.class.php');
require_once($CFG->dirroot . '/backup/util/structure/restore_path_element.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_file_manager.class.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_prechecks_helper.class.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_moodlexml_parser_processor.class.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_inforef_parser_processor.class.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_users_parser_processor.class.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_roles_parser_processor.class.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_questions_parser_processor.class.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_structure_parser_processor.class.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_decode_rule.class.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_decode_content.class.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_decode_processor.class.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_logs_processor.class.php');
require_once($CFG->dirroot . '/backup/util/helper/restore_log_rule.class.php');
require_once($CFG->dirroot . '/backup/util/xml/parser/progressive_parser.class.php');
require_once($CFG->dirroot . '/backup/util/output/output_controller.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/restore_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/backup_controller_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/dbops/restore_controller_dbops.class.php');
require_once($CFG->dirroot . '/backup/util/checks/restore_check.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/base_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/error_log_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/file_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/database_logger.class.php');
require_once($CFG->dirroot . '/backup/util/loggers/output_indented_logger.class.php');
require_once($CFG->dirroot . '/backup/util/factories/backup_factory.class.php');
require_once($CFG->dirroot . '/backup/util/factories/restore_factory.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_helper.class.php');
require_once($CFG->dirroot . '/backup/util/helper/backup_general_helper.class.php');
require_once($CFG->dirroot . '/backup/util/settings/setting_dependency.class.php');
require_once($CFG->dirroot . '/backup/util/settings/base_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/root/root_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/activity/activity_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/section/section_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/course/course_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_plan.class.php');
require_once($CFG->dirroot . '/backup/util/plan/restore_plan.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_task.class.php');
require_once($CFG->dirroot . '/backup/util/plan/restore_task.class.php');
require_once($CFG->dirroot . '/backup/util/plan/base_step.class.php');
require_once($CFG->dirroot . '/backup/util/plan/restore_step.class.php');
require_once($CFG->dirroot . '/backup/util/plan/restore_structure_step.class.php');
require_once($CFG->dirroot . '/backup/util/plan/restore_execution_step.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_plan_builder.class.php');
require_once($CFG->dirroot . '/backup/controller/restore_controller.class.php');
require_once($CFG->dirroot . '/backup/util/ui/base_moodleform.class.php');
require_once($CFG->dirroot . '/backup/util/ui/base_ui.class.php');
require_once($CFG->dirroot . '/backup/util/ui/base_ui_stage.class.php');
require_once($CFG->dirroot . '/backup/util/ui/backup_ui_setting.class.php');
require_once($CFG->dirroot . '/backup/util/ui/restore_ui_stage.class.php');
require_once($CFG->dirroot . '/backup/util/ui/restore_ui.class.php');
require_once($CFG->dirroot . '/backup/util/ui/restore_moodleform.class.php');
require_once($CFG->dirroot . '/backup/util/ui/restore_ui_components.php');

// And some moodle stuff too
require_once($CFG->dirroot . '/tag/lib.php');
require_once($CFG->dirroot . '/lib/gradelib.php');
require_once($CFG->dirroot . '/lib//questionlib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once ($CFG->dirroot . '/blocks/moodleblock.class.php');
