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
 * External assign API
 *
 * @package    assignfeedback_editpdfplus
 * @copyright  2017 Université de Lausanne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/user/externallib.php");
require_once("$CFG->dirroot/mod/assign/locallib.php");
require_once("locallib.php");
require_once("locallib_admin.php");

use \assignfeedback_editpdfplus\form\axis_form;
use \assignfeedback_editpdfplus\form\axis_del_form;
use \assignfeedback_editpdfplus\form\axis_import_form;
use \assignfeedback_editpdfplus\form\axis_export_form;
use \assignfeedback_editpdfplus\form\model_del_form;
use \assignfeedback_editpdfplus\admin_editor;

class assignfeedback_editpdfplus_external extends external_api {

    const PLUGINNAME = "assignfeedback_editpdfplus";
    const DATAJSON = 'jsonformdata';
    const MESSAGELIB = 'message';
    const COURSELIB = "course";
    const CONTEXTID = "contextid";
    const AXEID = "axeid";
    const AXEIDDESC = "Axe ID";
    const AXELIB = "axelabel";
    const AXELIBDESC = "Axe label";
    const TOOLID = "toolid";
    const TOOLIDDESC = "Tool ID";
    const TOOLLIBDESC = "Tool label";
    const TOOLTYPE = "typetool";
    const TOOLTYPEDESC = "Type of tool";
    const TOOLSELECTED = "selecttool";
    const BOUTONLIBTOOL = "button";
    const ENABLETOOL = "enable";
    const ENABLETOOLDESC = "Tool is enabled";

    /**
     * Returns description of method parameters for general calling
     * @return \external_function_parameters
     */
    public static function submit_generic_form_parameters() {
        $message = 'The data from the grading form, encoded as a json array';
        return new external_function_parameters(
                array(
            self::DATAJSON => new external_value(PARAM_RAW, $message)
                )
        );
    }

    /**
     * 
     * Form return generic structure
     * @return \external_single_structure
     */
    public static function submit_generic_form_returns() {
        return new external_single_structure(
                array(
            self::MESSAGELIB => new external_value(PARAM_TEXT, self::MESSAGELIB, VALUE_OPTIONAL)
                )
        );
    }

    /**
     * Form return tool structure
     * @return \external_multiple_structure
     */
    public static function submit_tool_form_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                        array(
                    self::AXEID => new external_value(PARAM_INT, self::AXEIDDESC),
                    self::TOOLSELECTED => new external_value(PARAM_INT, self::TOOLIDDESC),
                    self::ENABLETOOL => new external_value(PARAM_INT, self::ENABLETOOLDESC, VALUE_OPTIONAL),
                    self::TOOLID => new external_value(PARAM_INT, self::TOOLIDDESC),
                    self::TOOLTYPE => new external_value(PARAM_INT, self::TOOLTYPEDESC, VALUE_OPTIONAL),
                    self::BOUTONLIBTOOL => new external_value(PARAM_TEXT, self::TOOLLIBDESC, VALUE_OPTIONAL),
                    self::MESSAGELIB => new external_value(PARAM_TEXT, self::MESSAGELIB, VALUE_OPTIONAL)
                        )
                )
        );
    }

    /**
     * Extract and parse json data string into an array
     * @param external_function_parameters $externalFunctionParameter
     * @param String $jsonformdata
     * @return array decoded data
     */
    public static function getParseData($externalFunctionParameter, $jsonformdata) {
        $params = self::validate_parameters($externalFunctionParameter, array(
                    self::DATAJSON => $jsonformdata
        ));
        $serialiseddata = json_decode($params[self::DATAJSON]);
        $data = array();
        parse_str($serialiseddata, $data);
        return $data;
    }

    /**
     * Set Page context from the course id given. It returns the context found.
     * @global $DB
     * @global $PAGE
     * @param int $contextid Current context id
     * @return context course's context 
     */
    public static function setPageContext($contextid) {
        global $PAGE;
        $context = context::instance_by_id($contextid, MUST_EXIST);
        $PAGE->set_context($context);
        return $context;
    }

    public static function getMessageError() {
        return array(self::MESSAGELIB => get_string("admin_messageko", self::PLUGINNAME));
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function submit_axis_form_parameters() {
        return self::submit_generic_form_parameters();
    }

    /**
     * Submit axis form for adding or edditing
     * @param String $jsonformdata
     * @return array
     */
    public static function submit_axis_form($jsonformdata) {
        $data = self::getParseData(self::submit_axis_form_parameters(), $jsonformdata);

        $context = self::setPageContext($data[self::CONTEXTID]);

        $customdata = (object) $data;
        $formparams = array($customdata);

        // Data is injected into the form by the last param for the constructor.
        $mform = new axis_form(null, $formparams, 'post', '', null, true, $data);
        $validateddata = $mform->get_data();

        if ($validateddata) {
            if ($validateddata->axeid) {
                admin_editor::edit_axis($validateddata->axeid, $validateddata->label);
                $axeid = $validateddata->axeid;
            } else {
                $axeid = admin_editor::add_axis($validateddata->label, $context->id);
            }
            return array(array(self::AXEID => $axeid, self::AXELIB => $validateddata->label));
        }
        return array(self::getMessageError());
    }

    /**
     * Form return structure
     * @return \external_multiple_structure
     */
    public static function submit_axis_form_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                        array(
                    self::AXEID => new external_value(PARAM_INT, self::AXEIDDESC),
                    self::AXELIB => new external_value(PARAM_TEXT, self::AXELIBDESC),
                    self::MESSAGELIB => new external_value(PARAM_TEXT, self::MESSAGELIB, VALUE_OPTIONAL)
                        )
                )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function submit_axis_del_form_parameters() {
        return self::submit_generic_form_parameters();
    }

    /**
     * Submit axis form for deleting
     * @param String $jsonformdata
     * @return array
     */
    public static function submit_axis_del_form($jsonformdata) {
        $data = self::getParseData(self::submit_axis_form_parameters(), $jsonformdata);

        self::setPageContext($data[self::CONTEXTID]);

        $customdata = (object) $data;
        $formparams = array($customdata);

        // Data is injected into the form by the last param for the constructor.
        $mform = new axis_del_form(null, $formparams, 'post', '', null, true, $data);
        $validateddata = $mform->get_data();

        if ($validateddata && $validateddata->axeid && admin_editor::del_axis($validateddata->axeid)) {
            $message = "1";
            return array(array(self::MESSAGELIB => $message));
        }
        $warnings = array();
        $message = get_string('admindeltool_messageko', self::PLUGINNAME);
        $warnings[] = array(self::MESSAGELIB => $message);
        return $warnings;
    }

    /**
     * Form return structure
     * @return \external_multiple_structure
     */
    public static function submit_axis_del_form_returns() {
        return new external_multiple_structure(
                self::submit_generic_form_returns()
        );
    }

    /**
     * Submit tool form for adding or edditing
     * @param String $jsonformdata json tool
     * @param String $mode add or edit the tool
     * @return array
     */
    public static function submit_tool_form($jsonformdata, $mode) {
        $data = self::getParseData(self::submit_axis_form_parameters(), $jsonformdata);

        $context = self::setPageContext($data[self::CONTEXTID]);

        $customdata = (object) $data;

        $sessionkey = sesskey();
        if ($sessionkey == $customdata->sesskey && $mode) {
            $tool = null;
            if ($mode == "add") {
                $tool = admin_editor::add_tool($customdata, $context->id);
            } elseif ($mode == "edit") {
                $tool = admin_editor::edit_tool($customdata);
            }
            if ($tool) {
                $tools = admin_editor::get_tools_by_axis($tool->axis);
                $res = array();
                foreach ($tools as $toolTmp) {
                    $res[] = array(self::AXEID => $tool->axis, self::TOOLSELECTED => $tool->id, self::ENABLETOOL => $toolTmp->enabled, self::TOOLID => $toolTmp->id, self::TOOLTYPE => $toolTmp->type, self::BOUTONLIBTOOL => $toolTmp->label, self::MESSAGELIB => '');
                }
                return $res;
            }
        }
        return array(self::getMessageError());
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function submit_tool_edit_form_parameters() {
        return self::submit_generic_form_parameters();
    }

    /**
     * Submit tool form for edditing
     * @param String $jsonformdata
     * @return array
     */
    public static function submit_tool_edit_form($jsonformdata) {
        return self::submit_tool_form($jsonformdata, "edit");
    }

    /**
     * Form return structure
     * @return \external_multiple_structure
     */
    public static function submit_tool_edit_form_returns() {
        return self::submit_tool_form_returns();
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function submit_tool_add_form_parameters() {
        return self::submit_generic_form_parameters();
    }

    /**
     * Submit tool form for adding
     * @param String $jsonformdata
     * @return array
     */
    public static function submit_tool_add_form($jsonformdata) {
        return self::submit_tool_form($jsonformdata, "add");
    }

    /**
     * Form return structure
     * @return \external_multiple_structure
     */
    public static function submit_tool_add_form_returns() {
        return self::submit_tool_form_returns();
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function submit_tool_del_form_parameters() {
        return self::submit_generic_form_parameters();
    }

    /**
     * Submit tool form for deleting
     * @param String $jsonformdata
     * @return array
     */
    public static function submit_tool_del_form($jsonformdata) {
        $data = self::getParseData(self::submit_axis_form_parameters(), $jsonformdata);

        $context = self::setPageContext($data[self::CONTEXTID]);

        $customdata = (object) $data;

        $sessionkey = sesskey();
        if ($sessionkey == $customdata->sesskey) {
            $axisid = $customdata->axisid;
            if (admin_editor::del_tool($customdata, $context->id)) {
                $res = array();
                $tools = admin_editor::get_tools_by_axis($axisid);
                if (sizeof($tools) > 0) {
                    foreach ($tools as $toolTmp) {
                        $res[] = array(self::AXEID => $axisid, self::TOOLSELECTED => -1, self::ENABLETOOL => $toolTmp->enabled, self::TOOLID => $toolTmp->id, self::TOOLTYPE => $toolTmp->type, self::BOUTONLIBTOOL => $toolTmp->label, self::MESSAGELIB => '');
                    }
                } else {
                    $res[] = array(self::AXEID => $axisid, self::TOOLSELECTED => -1, self::TOOLID => -1, self::MESSAGELIB => '1');
                }
                return $res;
            }
        }
        return array(self::getMessageError());
    }

    /**
     * Form return structure
     * @return \external_multiple_structure
     */
    public static function submit_tool_del_form_returns() {
        return self::submit_tool_form_returns();
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function submit_axis_import_form_parameters() {
        return self::submit_generic_form_parameters();
    }

    /**
     * Submit axis form for importing
     * @param String $jsonformdata
     * @return array
     */
    public static function submit_axis_import_form($jsonformdata) {

        $data = self::getParseData(self::submit_axis_import_form_parameters(), $jsonformdata);

        $context = self::setPageContext($data[self::CONTEXTID]);

        $customdata = (object) $data;
        $formparams = array($customdata);

        // Data is injected into the form by the last param for the constructor.
        $mform = new axis_import_form(null, $formparams, 'post', '', null, true, $data);
        $validateddata = $mform->get_data();

        if ($validateddata && $validateddata->axeid) {
            $axeToImport = admin_editor::get_axis_by_id($validateddata->axeid);
            $axeNew = admin_editor::import_axis($axeToImport, $context->id);
            if ($axeNew) {
                $tools = admin_editor::get_tools_by_axis($axeToImport->id);
                foreach ($tools as $toolToImport) {
                    admin_editor::import_tool($toolToImport, $axeNew, $context->id);
                }
                $res = array();
                $toolsNew = admin_editor::get_tools_by_axis($axeNew);
                if (sizeof($toolsNew) > 0) {
                    foreach ($toolsNew as $tool) {
                        $res[] = array(self::AXEID => $axeNew, self::AXELIB => $axeToImport->label, self::MESSAGELIB => "", self::ENABLETOOL => $tool->enabled, self::TOOLID => $tool->id, self::TOOLTYPE => $tool->type, self::BOUTONLIBTOOL => $tool->label, self::MESSAGELIB => '');
                    }
                } else {
                    $res = array(array(self::AXEID => $axeNew, self::AXELIB => $axeToImport->label, self::MESSAGELIB => ""));
                }

                return $res;
            }
        }
        return array(self::getMessageError());
    }

    /**
     * Form return structure
     * @return \external_multiple_structure
     */
    public static function submit_axis_import_form_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                        array(
                    self::AXEID => new external_value(PARAM_INT, self::AXEIDDESC, VALUE_OPTIONAL),
                    self::AXELIB => new external_value(PARAM_TEXT, self::AXELIBDESC, VALUE_OPTIONAL),
                    self::MESSAGELIB => new external_value(PARAM_TEXT, self::MESSAGELIB),
                    self::ENABLETOOL => new external_value(PARAM_INT, self::ENABLETOOLDESC, VALUE_OPTIONAL),
                    self::TOOLID => new external_value(PARAM_INT, self::TOOLIDDESC, VALUE_OPTIONAL),
                    self::TOOLTYPE => new external_value(PARAM_INT, self::TOOLTYPEDESC, VALUE_OPTIONAL),
                    self::BOUTONLIBTOOL => new external_value(PARAM_TEXT, self::TOOLLIBDESC, VALUE_OPTIONAL)
                        )
                )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function submit_axis_export_form_parameters() {
        return self::submit_generic_form_parameters();
    }

    /**
     * Submit axis form for exporting
     * @param String $jsonformdata
     * @return array
     */
    public static function submit_axis_export_form($jsonformdata) {
        global $USER;

        $data = self::getParseData(self::submit_axis_export_form_parameters(), $jsonformdata);

        $context = self::setPageContext($data[self::CONTEXTID]);

        $customdata = (object) $data;
        $formparams = array($customdata);

        // Data is injected into the form by the last param for the constructor.
        $mform = new axis_export_form(null, $formparams, 'post', '', null, true, $data);
        $validateddata = $mform->get_data();

        if ($validateddata && $validateddata->axeid) {
            $axeToExport = admin_editor::get_axis_by_id($validateddata->axeid);
            $axeToExport->label = $validateddata->label;
            $axeNew = admin_editor::import_axis($axeToExport, -1);
            if ($axeNew) {
                $tools = admin_editor::get_tools_by_axis($axeToExport->id);
                foreach ($tools as $toolToImport) {
                    admin_editor::import_tool($toolToImport, $axeNew, $context->id);
                }
                $model = admin_editor::addModel($axeNew, $validateddata->label, $USER);
                if ($model > -1) {
                    $res = array();
                    $toolsNew = admin_editor::get_tools_by_axis($axeNew);
                    if (sizeof($toolsNew) > 0) {
                        foreach ($toolsNew as $tool) {
                            $tool->set_design();
                            $res[] = array('modelid' => $model, self::AXEID => $axeNew, self::AXELIB => $validateddata->label, self::MESSAGELIB => "", self::ENABLETOOL => $tool->enabled, self::TOOLID => $tool->id, self::TOOLTYPE => $tool->type, self::BOUTONLIBTOOL => $tool->label, 'style' => $tool->style);
                        }
                    } else {
                        $res = array(array('modelid' => $model, self::AXEID => $axeNew, self::AXELIB => $validateddata->label, self::MESSAGELIB => ""));
                    }

                    return $res;
                }
            }
        }
        return array(self::getMessageError());
    }

    /**
     * Form return structure
     * @return \external_multiple_structure
     */
    public static function submit_axis_export_form_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                        array(
                    'modelid' => new external_value(PARAM_INT, self::AXEIDDESC, VALUE_OPTIONAL),
                    self::AXEID => new external_value(PARAM_INT, self::AXEIDDESC, VALUE_OPTIONAL),
                    self::AXELIB => new external_value(PARAM_TEXT, self::AXELIBDESC, VALUE_OPTIONAL),
                    self::MESSAGELIB => new external_value(PARAM_TEXT, self::MESSAGELIB),
                    self::ENABLETOOL => new external_value(PARAM_INT, self::ENABLETOOLDESC, VALUE_OPTIONAL),
                    self::TOOLID => new external_value(PARAM_INT, self::TOOLIDDESC, VALUE_OPTIONAL),
                    self::TOOLTYPE => new external_value(PARAM_INT, self::TOOLTYPEDESC, VALUE_OPTIONAL),
                    self::BOUTONLIBTOOL => new external_value(PARAM_TEXT, self::TOOLLIBDESC, VALUE_OPTIONAL),
                    'style' => new external_value(PARAM_TEXT, self::TOOLLIBDESC, VALUE_OPTIONAL)
                        )
                )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function submit_model_del_form_parameters() {
        return self::submit_generic_form_parameters();
    }

    /**
     * Submit axis form for deleting
     * @param String $jsonformdata
     * @return array
     */
    public static function submit_model_del_form($jsonformdata) {
        $data = self::getParseData(self::submit_axis_form_parameters(), $jsonformdata);

        self::setPageContext($data[self::CONTEXTID]);

        $customdata = (object) $data;
        $formparams = array($customdata);

        // Data is injected into the form by the last param for the constructor.
        $mform = new model_del_form(null, $formparams, 'post', '', null, true, $data);
        $validateddata = $mform->get_data();

        if ($validateddata && $validateddata->modelid && admin_editor::delModel($validateddata->modelid)) {
            $message = "1";
            return array(array(self::MESSAGELIB => $message));
        }
        $warnings = array();
        $message = get_string('admindeltool_messageko', self::PLUGINNAME);
        $warnings[] = array(self::MESSAGELIB => $message);
        return $warnings;
    }

    /**
     * Form return structure
     * @return \external_multiple_structure
     */
    public static function submit_model_del_form_returns() {
        return new external_multiple_structure(
                self::submit_generic_form_returns()
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function submit_tool_order_form_parameters() {
        return self::submit_generic_form_parameters();
    }

    /**
     * Submit tool form for changing order
     * @param String $jsonformdata
     * @return array
     */
    public static function submit_tool_order_form($jsonformdata) {
        $data = self::getParseData(self::submit_tool_order_form_parameters(), $jsonformdata);

        self::setPageContext($data[self::CONTEXTID]);

        $customdata = (object) $data;

        $sessionkey = sesskey();
        if ($sessionkey == $customdata->sesskey && $customdata->toolid) {
            admin_editor::edit_tool_order($customdata);
            return array(self::MESSAGELIB => 'ok');
        }
        return self::getMessageError();
    }

    /**
     * Form return structure
     * @return \external_multiple_structure
     */
    public static function submit_tool_order_form_returns() {
        return self::submit_generic_form_returns();
    }

}
