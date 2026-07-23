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

namespace core_course\route\scope\course\content;

/**
 * The core_course:course:content:delete scope.
 *
 * This scope is used to handle course content delete-related routes.
 *
 * @package    core_course
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\router\scope\identifier_attribute('delete')]
#[\core\router\scope\summary_attribute('course_content_delete_scope_summary', 'core_course')]
#[\core\router\scope\description_attribute('course_content_delete_scope_desc', 'core_course')]
class delete extends abstract_scope {
}
