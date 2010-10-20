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
 * @package    mod
 * @subpackage workshop
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
    // workshop instance log actions
    array('module'=>'workshop', 'action'=>'add', 'mtable'=>'workshop', 'field'=>'name'),
    array('module'=>'workshop', 'action'=>'update', 'mtable'=>'workshop', 'field'=>'name'),
    array('module'=>'workshop', 'action'=>'view', 'mtable'=>'workshop', 'field'=>'name'),
    array('module'=>'workshop', 'action'=>'view all', 'mtable'=>'workshop', 'field'=>'name'),
    // submission log actions
    array('module'=>'workshop', 'action'=>'add submission', 'mtable'=>'workshop_submissions', 'field'=>'title'),
    array('module'=>'workshop', 'action'=>'update submission', 'mtable'=>'workshop_submissions', 'field'=>'title'),
    array('module'=>'workshop', 'action'=>'view submission', 'mtable'=>'workshop_submissions', 'field'=>'title'),
    // assessment log actions
    array('module'=>'workshop', 'action'=>'add assessment', 'mtable'=>'workshop_submissions', 'field'=>'title'),
    array('module'=>'workshop', 'action'=>'update assessment', 'mtable'=>'workshop_submissions', 'field'=>'title'),
    // example log actions
    array('module'=>'workshop', 'action'=>'add example', 'mtable'=>'workshop_submissions', 'field'=>'title'),
    array('module'=>'workshop', 'action'=>'update example', 'mtable'=>'workshop_submissions', 'field'=>'title'),
    array('module'=>'workshop', 'action'=>'view example', 'mtable'=>'workshop_submissions', 'field'=>'title'),
    // example assessment log actions
    array('module'=>'workshop', 'action'=>'add reference assessment', 'mtable'=>'workshop_submissions', 'field'=>'title'),
    array('module'=>'workshop', 'action'=>'update reference assessment', 'mtable'=>'workshop_submissions', 'field'=>'title'),
    array('module'=>'workshop', 'action'=>'add example assessment', 'mtable'=>'workshop_submissions', 'field'=>'title'),
    array('module'=>'workshop', 'action'=>'update example assessment', 'mtable'=>'workshop_submissions', 'field'=>'title'),
    // grading evaluation log actions
    array('module'=>'workshop', 'action'=>'update aggregate grades', 'mtable'=>'workshop', 'field'=>'name'),
    array('module'=>'workshop', 'action'=>'update clear aggregated grades', 'mtable'=>'workshop', 'field'=>'name'),
    array('module'=>'workshop', 'action'=>'update clear assessments', 'mtable'=>'workshop', 'field'=>'name'),
);
