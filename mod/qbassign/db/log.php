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
 * @package   mod_qbassign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
    array('module'=>'qbassign', 'action'=>'add', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'delete mod', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'download all submissions', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'grade submission', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'lock submission', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'reveal identities', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'revert submission to draft', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'set marking workflow state', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'submission statement accepted', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'submit', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'submit for grading', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'unlock submission', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'update', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'upload', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'view', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'view all', 'mtable'=>'course', 'field'=>'fullname'),
    array('module'=>'qbassign', 'action'=>'view confirm submit qbassignment form', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'view grading form', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'view submission', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'view submission grading table', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'view submit qbassignment form', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'view feedback', 'mtable'=>'qbassign', 'field'=>'name'),
    array('module'=>'qbassign', 'action'=>'view batch set marking workflow state', 'mtable'=>'qbassign', 'field'=>'name'),
);
