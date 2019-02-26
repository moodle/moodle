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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_bootstrapbase
 * @copyright  2013
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/theme/bootstrapbase/renderers/core_renderer.php');
require_once($CFG->dirroot . '/theme/bootstrapbase/renderers/block_settings_renderer.php');
require_once($CFG->dirroot . '/theme/bootstrapbase/renderers/gradereport_history_renderer.php');
require_once($CFG->dirroot . '/theme/bootstrapbase/renderers/core/course_renderer.php');
require_once($CFG->dirroot . '/theme/bootstrapbase/renderers/core/files_renderer.php');
require_once($CFG->dirroot . '/theme/bootstrapbase/renderers/core_course/management/renderer.php');
require_once($CFG->dirroot . '/theme/bootstrapbase/renderers/core_question/bank_renderer.php');