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
 * @package    core
 * @subpackage backup-convert
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include all the convert stuff needed
require_once($CFG->dirroot.'/backup/util/interfaces/checksumable.class.php');
require_once($CFG->dirroot.'/backup/util/interfaces/executable.class.php');
require_once($CFG->dirroot.'/backup/util/interfaces/loggable.class.php');
require_once($CFG->dirroot.'/backup/backup.class.php');
require_once($CFG->dirroot.'/backup/util/xml/xml_writer.class.php');
require_once($CFG->dirroot.'/backup/util/xml/output/xml_output.class.php');
require_once($CFG->dirroot.'/backup/util/xml/output/file_xml_output.class.php');
require_once($CFG->dirroot.'/backup/util/dbops/backup_dbops.class.php');
require_once($CFG->dirroot.'/backup/util/dbops/backup_controller_dbops.class.php');
require_once($CFG->dirroot.'/backup/util/factories/convert_factory.class.php');
require_once($CFG->dirroot.'/backup/util/converter/base_converter.class.php');
require_once($CFG->dirroot.'/backup/util/converter/plan_converter.class.php');
require_once($CFG->dirroot.'/backup/util/helper/convert_helper.class.php');
require_once($CFG->dirroot.'/backup/util/plan/base_plan.class.php');
require_once($CFG->dirroot.'/backup/util/plan/base_step.class.php');
require_once($CFG->dirroot.'/backup/util/plan/base_task.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_plan.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_step.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_task.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_structure_step.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_execution_step.class.php');
require_once($CFG->dirroot.'/backup/util/structure/restore_path_element.class.php');
require_once($CFG->dirroot.'/backup/util/structure/convert_path_element.class.php');
require_once($CFG->dirroot.'/backup/util/plan/convert_execution_step.class.php');
require_once($CFG->dirroot.'/backup/util/xml/parser/processors/grouped_parser_processor.class.php');
require_once($CFG->dirroot.'/backup/util/helper/convert_structure_parser_processor.class.php');
require_once($CFG->dirroot.'/backup/moodle2/convert_stepslib.php');
require_once($CFG->dirroot.'/backup/util/xml/parser/progressive_parser.class.php');

// And some moodle stuff too
require_once($CFG->libdir.'/filelib.php');
