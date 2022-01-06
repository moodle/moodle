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
 * Web service for assignfeedback_editpdfplus
 * @package    assignfeedback_editpdfplus
 * @subpackage db
 * @copyright  2017 Université de Lausanne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$functions = array(
    'assignfeedback_editpdfplus_submit_axis_form' => array(
        'classname' => 'assignfeedback_editpdfplus_external',
        'methodname' => 'submit_axis_form',
        'classpath' => 'mod/assign/feedback/editpdfplus/externallib.php',
        'description' => 'Test add axis',
        'type' => 'write',
        'ajax' => true,
        'requiredcapability' => 'assignfeedback/editpdfplus:managetools',
        'enabled' => 1,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'assignfeedback_editpdfplus_submit_axis_edit_form' => array(
        'classname' => 'assignfeedback_editpdfplus_external',
        'methodname' => 'submit_axis_form',
        'classpath' => 'mod/assign/feedback/editpdfplus/externallib.php',
        'description' => 'Edit an axis',
        'type' => 'write',
        'ajax' => true,
        'requiredcapability' => 'assignfeedback/editpdfplus:managetools',
        'enabled' => 1,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'assignfeedback_editpdfplus_submit_axis_del_form' => array(
        'classname' => 'assignfeedback_editpdfplus_external',
        'methodname' => 'submit_axis_del_form',
        'classpath' => 'mod/assign/feedback/editpdfplus/externallib.php',
        'description' => 'Remove an axis',
        'type' => 'write',
        'ajax' => true,
        'requiredcapability' => 'assignfeedback/editpdfplus:managetools',
        'enabled' => 1,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'assignfeedback_editpdfplus_submit_tool_edit_form' => array(
        'classname' => 'assignfeedback_editpdfplus_external',
        'methodname' => 'submit_tool_edit_form',
        'classpath' => 'mod/assign/feedback/editpdfplus/externallib.php',
        'description' => 'Edit a tool',
        'type' => 'write',
        'ajax' => true,
        'requiredcapability' => 'assignfeedback/editpdfplus:managetools',
        'enabled' => 1,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'assignfeedback_editpdfplus_submit_tool_add_form' => array(
        'classname' => 'assignfeedback_editpdfplus_external',
        'methodname' => 'submit_tool_add_form',
        'classpath' => 'mod/assign/feedback/editpdfplus/externallib.php',
        'description' => 'Add a tool',
        'type' => 'write',
        'ajax' => true,
        'requiredcapability' => 'assignfeedback/editpdfplus:managetools',
        'enabled' => 1,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'assignfeedback_editpdfplus_submit_tool_del_form' => array(
        'classname' => 'assignfeedback_editpdfplus_external',
        'methodname' => 'submit_tool_del_form',
        'classpath' => 'mod/assign/feedback/editpdfplus/externallib.php',
        'description' => 'Remove a tool',
        'type' => 'write',
        'ajax' => true,
        'requiredcapability' => 'assignfeedback/editpdfplus:managetools',
        'enabled' => 1,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'assignfeedback_editpdfplus_submit_axis_export_form' => array(
        'classname' => 'assignfeedback_editpdfplus_external',
        'methodname' => 'submit_axis_export_form',
        'classpath' => 'mod/assign/feedback/editpdfplus/externallib.php',
        'description' => 'Export an axis with its tools',
        'type' => 'write',
        'ajax' => true,
        'requiredcapability' => 'assignfeedback/editpdfplus:managetools',
        'enabled' => 1,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'assignfeedback_editpdfplus_submit_model_del_form' => array(
        'classname' => 'assignfeedback_editpdfplus_external',
        'methodname' => 'submit_model_del_form',
        'classpath' => 'mod/assign/feedback/editpdfplus/externallib.php',
        'description' => 'Remove a model',
        'type' => 'write',
        'ajax' => true,
        'requiredcapability' => 'assignfeedback/editpdfplus:managetools',
        'enabled' => 1,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'assignfeedback_editpdfplus_submit_axis_import_form' => array(
        'classname' => 'assignfeedback_editpdfplus_external',
        'methodname' => 'submit_axis_import_form',
        'classpath' => 'mod/assign/feedback/editpdfplus/externallib.php',
        'description' => 'Import an axis with its tools',
        'type' => 'write',
        'ajax' => true,
        'requiredcapability' => 'assignfeedback/editpdfplus:managetools',
        'enabled' => 1,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'assignfeedback_editpdfplus_submit_tool_order_form' => array(
        'classname' => 'assignfeedback_editpdfplus_external',
        'methodname' => 'submit_tool_order_form',
        'classpath' => 'mod/assign/feedback/editpdfplus/externallib.php',
        'description' => 'Order a tool',
        'type' => 'write',
        'ajax' => true,
        'requiredcapability' => 'assignfeedback/editpdfplus:managetools',
        'enabled' => 1,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    )
);
