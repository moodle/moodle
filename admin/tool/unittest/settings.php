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
 * Unittest settings
 *
 * @package    tool
 * @subpackage unittest
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;

$ADMIN->add('development', new admin_externalpage('toolsimpletest', get_string('pluginname', 'tool_unittest'), "$CFG->wwwroot/$CFG->admin/tool/unittest/index.php", 'tool/unittest:execute'));
$ADMIN->add('development', new admin_externalpage('tooldbtest', get_string('dbtest', 'tool_unittest'), "$CFG->wwwroot/$CFG->admin/tool/unittest/dbtest.php", 'tool/unittest:execute'));
