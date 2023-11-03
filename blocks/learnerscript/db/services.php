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
 * Web service for Block LearnerScript
 * @package    block_learnerscript
 * @subpackage db
 * @since      Moodle 3.3
 * @copyright  eAbyas Info Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
$functions = array(
'block_learnerscript_rolewiseusers' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'rolewiseusers',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case rolewiseusers',
        'ajax' => true
    ),
'block_learnerscript_roleusers' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'roleusers',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case roleusers'   ,
        'ajax' => true
    ),
'block_learnerscript_viewschuserstable' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'viewschuserstable',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case viewschuserstable'   ,
        'ajax' => true
    ),
'block_learnerscript_manageschusers' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'manageschusers',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case manageschusers',
        'ajax' => true
    ),
'block_learnerscript_schreportform' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'schreportform',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case schreportform',
        'ajax' => true
    ),
'block_learnerscript_scheduledtimings' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'scheduledtimings',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case scheduledtimings'    ,
        'ajax' => true
    ),
'block_learnerscript_generate_plotgraph' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'generate_plotgraph',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case generate_plotgraph',
        'ajax' => true
    ),
'block_learnerscript_pluginlicence' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'pluginlicence',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case pluginlicence',
        'ajax' => true
    ),
'block_learnerscript_frequency_schedule' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'frequency_schedule',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case frequency_schedule',
        'ajax' => true
    ),
'block_learnerscript_reportobject' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'reportobject',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case reportobject'    ,
        'ajax' => true
    ),
'block_learnerscript_advancedcolumns' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'advancedcolumns',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case advancedcolumns'  ,
        'ajax' => true
    ),
'block_learnerscript_reportcalculations' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'reportcalculations',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case reportcalculations',
        'ajax' => true
    ),
'block_learnerscript_updatereport_conditions' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'updatereport_conditions',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case updatereport_conditions'  ,
        'ajax' => true
    ),
'block_learnerscript_plotforms' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'plotforms',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case plotforms'  ,
        'ajax' => true
    ),
'block_learnerscript_designdata' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'designdata',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case designdata'  ,
        'ajax' => true
    ),
'block_learnerscript_deletecomponenet' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'deletecomponenet',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'Delete a perticular component for the report',
        'ajax' => true
    ),
'block_learnerscript_reportfilter' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'reportfilterform',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'case dashboard name edit',
        'ajax' => true
    ),
'block_learnerscript_importreports' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'importreports',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'Import Reports',
        'ajax' => true
    ),
'block_learnerscript_lsreportconfigimport' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'lsreportconfigimport',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'Import Reports Status',
        'ajax' => true
    ),
'block_learnerscript_resetlsconfig' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'resetlsconfig',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'Reset LearnerScript Configurations',
        'ajax' => true
    ),
'block_learnerscript_filtercourses' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'filtercourses',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'Filter Courses',
        'ajax' => true
    ),
'block_learnerscript_filterusers' => array(
        'classname' => 'block_learnerscript_external',
        'methodname' => 'filterusers',
        'classpath' => 'blocks/learnerscript/externallib.php',
        'description' => 'Filter users',
        'ajax' => true
    )
);