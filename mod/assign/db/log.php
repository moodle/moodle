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
 * Definition of log events
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
    array('module'=>'assign', 'action'=>'add', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'delete mod', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'download all submissions', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'grade submission', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'lock submission', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'reveal identities', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'revert submission to draft', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'submission statement accepted', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'submit', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'submit for grading', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'unlock submission', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'update', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'upload', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'view', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'view all', 'mtable'=>'course', 'field'=>'fullname'),
    array('module'=>'assign', 'action'=>'view confirm submit assignment form', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'view grading form', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'view submission', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'view submission grading table', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'view submit assignment form', 'mtable'=>'assign', 'field'=>'name'),
    array('module'=>'assign', 'action'=>'view feedback', 'mtable'=>'assign', 'field'=>'name'),
);
