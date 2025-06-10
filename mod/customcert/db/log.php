<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * Definition of log events
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = [
    ['module' => 'customcert', 'action' => 'view', 'mtable' => 'customcert', 'field' => 'name'],
    ['module' => 'customcert', 'action' => 'add', 'mtable' => 'customcert', 'field' => 'name'],
    ['module' => 'customcert', 'action' => 'update', 'mtable' => 'customcert', 'field' => 'name'],
    ['module' => 'customcert', 'action' => 'received', 'mtable' => 'customcert', 'field' => 'name'],
];
