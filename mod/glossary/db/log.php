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
 * @package    mod_glossary
 * @category   log
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
    array('module'=>'glossary', 'action'=>'add', 'mtable'=>'glossary', 'field'=>'name'),
    array('module'=>'glossary', 'action'=>'update', 'mtable'=>'glossary', 'field'=>'name'),
    array('module'=>'glossary', 'action'=>'view', 'mtable'=>'glossary', 'field'=>'name'),
    array('module'=>'glossary', 'action'=>'view all', 'mtable'=>'glossary', 'field'=>'name'),
    array('module'=>'glossary', 'action'=>'add entry', 'mtable'=>'glossary', 'field'=>'name'),
    array('module'=>'glossary', 'action'=>'update entry', 'mtable'=>'glossary', 'field'=>'name'),
    array('module'=>'glossary', 'action'=>'add category', 'mtable'=>'glossary', 'field'=>'name'),
    array('module'=>'glossary', 'action'=>'update category', 'mtable'=>'glossary', 'field'=>'name'),
    array('module'=>'glossary', 'action'=>'delete category', 'mtable'=>'glossary', 'field'=>'name'),
    array('module'=>'glossary', 'action'=>'approve entry', 'mtable'=>'glossary', 'field'=>'name'),
    array('module'=>'glossary', 'action'=>'view entry', 'mtable'=>'glossary_entries', 'field'=>'concept'),
);