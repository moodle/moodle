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
 * Defines the tab bar used on the manage/allow assign/allow overrides pages.
 *
 * @package    core
 * @subpackage role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page
}

$toprow = array();
$toprow[] = new tabobject('manage', new moodle_url('/admin/roles/manage.php'), get_string('manageroles', 'role'));
$toprow[] = new tabobject('assign', new moodle_url('/admin/roles/allow.php', array('mode'=>'assign')), get_string('allowassign', 'role'));
$toprow[] = new tabobject('override', new moodle_url('/admin/roles/allow.php', array('mode'=>'override')), get_string('allowoverride', 'role'));
$toprow[] = new tabobject('switch', new moodle_url('/admin/roles/allow.php', array('mode'=>'switch')), get_string('allowswitch', 'role'));
$tabs = array($toprow);

print_tabs($tabs, $currenttab);

