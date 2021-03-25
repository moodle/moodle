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
 * Legacy course format renderer.
 *
 * Since Moodle 4.0, renderer.php file was optional (although highly recommended) for course formats. From Moodle 4.0 onwards,
 * renderer is required to support the new course editor implementation.
 * This legacy class has been created for backward compatibility, to avoid some errors with course formats (such as social)
 * without this renderer.php file.
 *
 * @package   core_course
 * @copyright 2021 Sara Arjona (sara@moodle.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\output\course_format;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/course/format/renderer.php');

class legacy_format_renderer extends \format_section_renderer_base {

}
