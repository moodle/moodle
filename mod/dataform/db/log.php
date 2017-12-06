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
 * @package mod_dataform
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Definition of log events
 */

defined('MOODLE_INTERNAL') or die;

$logs = array(
    array('module' => 'dataform', 'action' => 'view', 'mtable' => 'dataform', 'field' => 'name'),
    array('module' => 'dataform', 'action' => 'add', 'mtable' => 'dataform', 'field' => 'name'),
    array('module' => 'dataform', 'action' => 'update', 'mtable' => 'dataform', 'field' => 'name'),
    array('module' => 'dataform', 'action' => 'record delete', 'mtable' => 'dataform', 'field' => 'name'),
    array('module' => 'dataform', 'action' => 'fields add', 'mtable' => 'dataform_fields', 'field' => 'name'),
    array('module' => 'dataform', 'action' => 'fields update', 'mtable' => 'dataform_fields', 'field' => 'name'),
    array('module' => 'dataform', 'action' => 'views add', 'mtable' => 'dataform_views', 'field' => 'name'),
    array('module' => 'dataform', 'action' => 'views update', 'mtable' => 'dataform_views', 'field' => 'name'),
    array('module' => 'dataform', 'action' => 'filters add', 'mtable' => 'dataform_filters', 'field' => 'name'),
    array('module' => 'dataform', 'action' => 'filters update', 'mtable' => 'dataform_filters', 'field' => 'name')
);
