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
 * Definition of log events.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 */

defined('MOODLE_INTERNAL') || die();

global $DB;

$logs = [
    ['module' => 'bigbluebuttonbn', 'action' => 'add', 'mtable' => 'bigbluebuttonbn', 'field' => 'name'],
    ['module' => 'bigbluebuttonbn', 'action' => 'update', 'mtable' => 'bigbluebuttonbn', 'field' => 'name'],
    ['module' => 'bigbluebuttonbn', 'action' => 'view', 'mtable' => 'bigbluebuttonbn', 'field' => 'name'],
    ['module' => 'bigbluebuttonbn', 'action' => 'view all', 'mtable' => 'bigbluebuttonbn', 'field' => 'name'],
    ['module' => 'bigbluebuttonbn', 'action' => 'create', 'mtable' => 'bigbluebuttonbn', 'field' => 'name'],
    ['module' => 'bigbluebuttonbn', 'action' => 'end', 'mtable' => 'bigbluebuttonbn', 'field' => 'name'],
    ['module' => 'bigbluebuttonbn', 'action' => 'join', 'mtable' => 'bigbluebuttonbn', 'field' => 'name'],
    ['module' => 'bigbluebuttonbn', 'action' => 'left', 'mtable' => 'bigbluebuttonbn', 'field' => 'name'],
    ['module' => 'bigbluebuttonbn', 'action' => 'publish', 'mtable' => 'bigbluebuttonbn', 'field' => 'name'],
    ['module' => 'bigbluebuttonbn', 'action' => 'unpublish', 'mtable' => 'bigbluebuttonbn', 'field' => 'name'],
    ['module' => 'bigbluebuttonbn', 'action' => 'delete', 'mtable' => 'bigbluebuttonbn', 'field' => 'name'],
];
