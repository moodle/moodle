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

namespace core_enrol\route\scope\enrolment;

/**
 * The core_enrol:enrolment:delete scope.
 *
 * This scope is used to handle enrolment delete-related routes.
 *
 * @package    core_enrol
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\router\scope\identifier_attribute('delete')]
#[\core\router\scope\summary_attribute('enrolment_delete_scope_summary', 'core_enrol')]
#[\core\router\scope\description_attribute('enrolment_delete_scope_desc', 'core_enrol')]
class delete extends abstract_scope {
}
