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
 * Web service for mod assign
 * @package    mod_assign
 * @subpackage db
 * @since      Moodle 2.4
 * @copyright  2012 Paul Charsley
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
'block_reportdashboard_userlist' => array(
        'classname' => 'block_reportdashboard_external',
        'methodname' => 'userlist',
        'classpath' => 'blocks/reportdashboard/externallib.php',
        'description' => 'case userlist',
        'ajax' => true
    ),
'block_reportdashboard_reportlist' => array(
        'classname' => 'block_reportdashboard_external',
        'methodname' => 'reportlist',
        'classpath' => 'blocks/reportdashboard/externallib.php',
        'description' => 'case reportlist',
        'ajax' => true
    ),
'block_reportdashboard_sendemails' => array(
        'classname' => 'block_reportdashboard_external',
        'methodname' => 'sendemails',
        'classpath' => 'blocks/reportdashboard/externallib.php',
        'description' => 'case sendemails',
        'ajax' => true
    ),
'block_reportdashboard_inplace_editable_dashboard' => array(
        'classname' => 'block_reportdashboard_external',
        'methodname' => 'inplace_editable_dashboard',
        'classpath' => 'blocks/reportdashboard/externallib.php',
        'description' => 'case dashboard name edit',
        'ajax' => true
    ),
'block_reportdashboard_addtiles_to_dashboard' => array(
        'classname' => 'block_reportdashboard_external',
        'methodname' => 'addtiles_to_dashboard',
        'classpath' => 'blocks/reportdashboard/externallib.php',
        'description' => 'case Add Tiles to Dashboard',
        'ajax' => true
    ),
'block_reportdashboard_addwidget_to_dashboard' => array(
        'classname' => 'block_reportdashboard_external',
        'methodname' => 'addwidget_to_dashboard',
        'classpath' => 'blocks/reportdashboard/externallib.php',
        'description' => 'case Add widget to Dashboard',
        'ajax' => true
    )
);