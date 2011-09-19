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
 * Link to InnoDB conversion tool
 *
 * @package    tool
 * @subpackage dbtransfer
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    // DB transfer related pages
    $ADMIN->add('experimental', new admin_externalpage('tooldbtransfer', get_string('dbtransfer', 'tool_dbtransfer'), $CFG->wwwroot.'/'.$CFG->admin.'/tool/dbtransfer/index.php', 'moodle/site:config', true));
    $ADMIN->add('experimental', new admin_externalpage('tooldbexport', get_string('dbexport', 'tool_dbtransfer'), $CFG->wwwroot.'/'.$CFG->admin.'/tool/dbtransfer/dbexport.php', 'moodle/site:config', true));
}