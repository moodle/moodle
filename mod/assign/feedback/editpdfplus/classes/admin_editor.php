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
 * This file contains the editor class for the assignfeedback_editpdfplus plugin
 *
 * This class performs crud operations on comments and annotations from a page of a response.
 *
 * No capability checks are done - they should be done by the calling class.
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2017 Université de Lausanne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdfplus;

use assignfeedback_editpdfplus\bdd\axis;
use assignfeedback_editpdfplus\bdd\type_tool;
use assignfeedback_editpdfplus\bdd\tool;

class admin_editor {

    const BDDTABLETOOL = "assignfeedback_editpp_tool";
    const BDDTABLEAXE = "assignfeedback_editpp_axis";
    const BDDTABLETOOLTYPE = "assignfeedback_editpp_typet";
    const BDDTABLEMODELAXIS = "assignfeedback_editpp_modax";
    const BDDTABLEANNOT = "assignfeedback_editpp_annot";
    const CONTEXTIDLIB = "contextid";

    /**
     * Make an SQL moodle select request from the 3 arguments
     * @param string $select the select part
     * @param string $table the moodle table
     * @param string $where the where part
     * @return string the string request
     */
    public static function make_sql_request_select($select, $table, $where = null) {
        $request = 'SELECT ' . $select . ' FROM {' . $table . '} ';
        if ($where) {
            $request .= " WHERE " . $where;
        }
        return $request;
    }

    /**
     * Add an axis
     * @global type $DB
     * @param String $axis_label axis' name
     * @param Integer $context context's id
     * @return Integer id of the created axis
     */
    public static function add_axis($axis_label, $context) {
        global $DB;

        $record = $DB->get_record_sql(self::make_sql_request_select("max(order_axis) as order_max", self::BDDTABLEAXE, self::CONTEXTIDLIB . ' = :' . self::CONTEXTIDLIB)
                , array(self::CONTEXTIDLIB => $context));

        $axis = new axis();
        $axis->contextid = $context;
        $axis->label = $axis_label;
        if ($record->order_max == null) {
            $axis->order_axis = 1;
        } else {
            $axis->order_axis = $record->order_max + 1;
        }

        return $DB->insert_record(self::BDDTABLEAXE, $axis);
    }

    /**
     * Add a tool
     * @global type $DB
     * @param object $data object with contains tool' info
     * @param Integer $contextid context's id
     * @return \assignfeedback_editpdfplus\bdd\tool created tool
     */
    public static function add_tool($data, $contextid) {
        global $DB;

        $maxindice = self::reorder_tool($data->toolaxis);

        $tool = new tool();
        $tool->axis = $data->toolaxis;
        $tool->cartridge = $data->libelle;
        $tool->cartridge_color = $data->catridgecolor;
        $tool->contextid = $contextid;
        $tool->label = $data->button;
        $tool->reply = 0;
        if ($data->reply == "on") {
            $tool->reply = 1;
        }
        $tool->texts = $data->texts;
        $tool->type = $data->typetool;
        $tool->colors = $data->color;
        $reorder = false;
        if ($maxindice == null) {
            $tool->order_tool = 1;
        } else if ($data->order && intval($data->order) < 1000) {
            $tool->order_tool = $data->order;
            $reorder = true;
        } else {
            $tool->order_tool = $maxindice + 1;
        }

        $toolid = $DB->insert_record(self::BDDTABLETOOL, $tool);

        if ($toolid > 0) {
            if ($reorder) {
                self::reorder_tool($data->axisid, $toolid);
            }
            return $tool;
        }
        return null;
    }

    public static function edit_tool_order($data) {
        global $DB;
        $record = $DB->get_record(self::BDDTABLETOOL, array('id' => $data->toolid), '*', MUST_EXIST);
        $tool_current = new tool($record);
        $previousorder = -1;
        $tool_previous = null;
        $tool_next = null;
        if ($data->previoustoolid) {
            $record = $DB->get_record(self::BDDTABLETOOL, array('id' => $data->previoustoolid), '*', MUST_EXIST);
            $tool_previous = new tool($record);
            $previousorder = $tool_previous->order_tool + 1;
        } elseif ($data->nexttoolid) {
            $record = $DB->get_record(self::BDDTABLETOOL, array('id' => $data->nexttoolid), '*', MUST_EXIST);
            $tool_next = new tool($record);
            $previousorder = $tool_next->order_tool - 1;
        }
        if ($previousorder > -1 && ($tool_previous || $tool_next )) {
            if ($previousorder == 0) {
                $previousorder = 1;
            }
            $tool_current->order_tool = $previousorder;
            debugging($previousorder);
            if ($DB->update_record(self::BDDTABLETOOL, $tool_current)) {
                self::reorder_tool($tool_current->axis, $data->toolid);
            }
        }
    }

    /**
     * Order tools of a toolbar
     * @global type $DB
     * @param Integer $axisid axis to reorder
     * @param Integer $toolid optional, can indicate a tool to place into a toolbar
     * @return last order rank
     */
    protected static function reorder_tool($axisid, $toolid = null) {
        global $DB;

        $tools = array();
        $records = $DB->get_records_sql(self::make_sql_request_select("*", self::BDDTABLETOOL, "axis = :axisid ORDER BY order_tool ASC")
                , array('axisid' => $axisid));
        foreach ($records as $record) {
            array_push($tools, new tool($record));
        }
        $compteur_precedent = null;
        $decalage = 1;
        $last_tool = null;
        foreach ($tools as $tool) {
            if ($compteur_precedent == null) {
                $compteur_precedent = $tool->order_tool;
                $last_tool = $tool;
            } else {
                $compteur_courant = $tool->order_tool;
                if ($compteur_courant != $compteur_precedent + $decalage) {
                    if ($toolid && $tool->id == $toolid) {
                        $tool->order_tool = $last_tool->order_tool;
                        $last_tool->order_tool = $compteur_precedent + 1;
                        $DB->update_record(self::BDDTABLETOOL, $tool);
                        $DB->update_record(self::BDDTABLETOOL, $last_tool);
                    } else {
                        $tool->order_tool = $compteur_precedent + $decalage;
                        $DB->update_record(self::BDDTABLETOOL, $tool);
                        $last_tool = $tool;
                    }
                } else {
                    $last_tool = $tool;
                }
                $compteur_precedent++;
            }
        }
        return $compteur_precedent;
    }

    /**
     * Edit an axis
     * @global type $DB
     * @param Integer $axeid axis' id
     * @param String $axis_label new axis' label
     * @return Boolean true if the update is ok
     */
    public static function edit_axis($axeid, $axis_label) {
        global $DB;

        $axis = $DB->get_record(self::BDDTABLEAXE, array('id' => $axeid), '*', MUST_EXIST);
        $axis->label = $axis_label;
        return $DB->update_record(self::BDDTABLEAXE, $axis);
    }

    /**
     * Delete an axis
     * @global type $DB
     * @param Integer $axeid axis' id
     * @return Boolean true if the update is ok
     */
    public static function del_axis($axeid) {
        global $DB;
        $res = true;
        //delete all related tools if possible
        $tools = self::get_tools_by_axis($axeid);
        foreach ($tools as $tool) {
            $res &= self::del_tool($tool);
        }
        if (!$res) {
            return false;
        }
        return $DB->delete_records(self::BDDTABLEAXE, array('id' => $axeid));
    }

    /**
     * Delete a tool
     * @global type $DB
     * @param \assignfeedback_editpdfplus\bdd\tool $tool
     * @return Boolean true if the remove is ok
     */
    public static function del_tool($tool) {
        global $DB;
        //check if this tool is used
        $nbAnnotations = self::getNbAnnotationsForTool($tool->toolid);
        if ($nbAnnotations > 0) {
            return false;
        }
        return $DB->delete_records(self::BDDTABLETOOL, array('id' => $tool->toolid));
    }

    /**
     * Get all tools by an axis' id
     * @global type $DB
     * @param Integer $axisid axis' id
     * @return array<\assignfeedback_editpdfplus\bdd\tool> the toolbar, order by order_tool
     */
    public static function get_tools_by_axis($axisid) {
        global $DB;
        $tools = array();
        $records = $DB->get_records(self::BDDTABLETOOL, array('axis' => $axisid));
        foreach ($records as $record) {
            array_push($tools, new tool($record));
        }
        usort($tools, function($a, $b) {
            $al = $a->order_tool;
            $bl = $b->order_tool;
            if ($al == $bl) {
                return 0;
            }
            return ($al > $bl) ? +1 : -1;
        });
        return $tools;
    }

    /**
     * Get all different contexts id
     * @global type $DB
     * @return array<\assignfeedback_editpdfplus\bdd\axis> the axis with just their contextid
     */
    public static function get_all_different_contexts() {
        global $DB;
        return $DB->get_records_sql(self::make_sql_request_select("DISTINCT(" . self::CONTEXTIDLIB . ")", self::BDDTABLEAXE));
    }

    /**
     * Update a tool
     * @global type $DB
     * @param object $tool_json object contains tool's values to update
     * @return \assignfeedback_editpdfplus\bdd\tool
     */
    public static function edit_tool($tool_json) {
        global $DB;

        $record = $DB->get_record(self::BDDTABLETOOL, array('id' => $tool_json->toolid), '*', MUST_EXIST);
        $tool = new tool($record);
        $tool->axis = $tool_json->toolaxis;
        $tool->type = $tool_json->typetool;
        $tool->colors = $tool_json->color;
        $tool->cartridge = $tool_json->libelle;
        $tool->cartridge_color = $tool_json->catridgecolor;
        $tool->texts = $tool_json->texts;
        $tool->label = $tool_json->button;
        $tool->enabled = $tool_json->enabled;
        if ($tool_json->reply == "on") {
            $tool->reply = 1;
        } else {
            $tool->reply = 0;
        }
        $reorder = false;
        if ($tool->order_tool != $tool_json->order) {
            $tool->order_tool = $tool_json->order;
            $reorder = true;
        }
        if ($DB->update_record(self::BDDTABLETOOL, $tool)) {
            if ($reorder) {
                self::reorder_tool($tool->axis, $tool->id);
            }
            return $tool;
        }
        return null;
    }

    /**
     * Get number of annotations related to this tool
     * @global $DB
     * @param int $toolid
     * @return int number of annotations 
     */
    public static function getNbAnnotationsForTool($toolid) {
        global $DB;
        return $DB->count_records(self::BDDTABLEANNOT, array('toolid' => $toolid));
    }

    /**
     * Get all the type tools which are configurabled.
     * @return array<\assignfeedback_editpdfplus\bdd\type_tool> array of type tools
     */
    public static function get_typetools() {
        global $DB;
        $typetools = array();
        $records = $DB->get_records(self::BDDTABLETOOLTYPE, array('configurable' => 1));
        foreach ($records as $record) {
            $new_type_tool = page_editor::custom_type_tool(new type_tool($record));
            if ($new_type_tool->configurable > 0) {
                array_push($typetools, $new_type_tool);
            }
        }
        return $typetools;
    }

    /**
     * Get axis by its id
     * @global type $DB
     * @param Integer $axeid axis' id
     * @return \assignfeedback_editpdfplus\bdd\axis the axis
     */
    public static function get_axis_by_id($axeid) {
        global $DB;
        return $DB->get_record(self::BDDTABLEAXE, array('id' => $axeid), '*', MUST_EXIST);
    }

    /**
     * Clone an axis to the context given in parameter
     * @global type $DB
     * @param \assignfeedback_editpdfplus\bdd\axis $axis_origin
     * @param Integer $context context's id
     * @return Integer id of the imported axis
     */
    public static function import_axis($axis_origin, $context) {
        global $DB;
        $record = $DB->get_record_sql(self::make_sql_request_select("max(order_axis) as order_max", self::BDDTABLEAXE, self::CONTEXTIDLIB . ' = :' . self::CONTEXTIDLIB)
                , array(self::CONTEXTIDLIB => $context));

        $axis = new axis();
        $axis->contextid = $context;
        $axis->label = $axis_origin->label;
        if ($record->order_max == null) {
            $axis->order_axis = 1;
        } else {
            $axis->order_axis = $record->order_max + 1;
        }

        return $DB->insert_record(self::BDDTABLEAXE, $axis);
    }

    /**
     * Clone a tool to a new axis
     * @global type $DB
     * @param \assignfeedback_editpdfplus\bdd\tool $tool_to_import tool to duplicate
     * @param \assignfeedback_editpdfplus\bdd\axis $axeNew axis to attached new tool
     * @param Integer $context context's id
     * @return Integer id of tool's created
     */
    public static function import_tool($tool_to_import, $axeNew, $context) {
        global $DB;
        $record = $DB->get_record_sql(self::make_sql_request_select("max(order_tool) as order_max", self::BDDTABLETOOL, self::CONTEXTIDLIB . ' = :' . self::CONTEXTIDLIB)
                , array('axis' => $axeNew->id, self::CONTEXTIDLIB => $context));

        $tool = new tool();
        $tool->axis = $axeNew;
        $tool->cartridge = $tool_to_import->cartridge;
        $tool->cartridge_color = $tool_to_import->cartridge_color;
        $tool->colors = $tool_to_import->colors;
        $tool->contextid = $context;
        $tool->enabled = $tool_to_import->enabled;
        $tool->label = $tool_to_import->label;
        $tool->reply = $tool_to_import->reply;
        $tool->texts = $tool_to_import->texts;
        $tool->type = $tool_to_import->type;
        if ($record->order_max == null) {
            $tool->order_tool = 1;
        } else {
            $tool->order_tool = $record->order_max + 1;
        }
        return $DB->insert_record(self::BDDTABLETOOL, $tool);
    }

    /**
     * get model axis for a given user
     * @global $DB
     * @param $user moodle user
     * @return array set of model axis
     */
    public static function getCategoryModel($user) {
        global $DB;
        return $DB->get_records(self::BDDTABLEMODELAXIS, array('user' => $user));
    }

    /**
     * Add a new model on database
     * @global $DB
     * @param int $axeid axe's id
     * @param string $label label for the new model
     * @param USER $user moodle user
     * @return int id of the new model
     */
    public static function addModel($axeid, $label, $user) {
        global $DB;
        $model = new \stdClass();
        $model->axis = $axeid;
        $model->label = $label;
        $model->user = $user->id;
        return $DB->insert_record(self::BDDTABLEMODELAXIS, $model);
    }

    /**
     * get a model with from its id
     * @global $DB
     * @param int $modelid model's id
     * @return record model record
     */
    public static function gelModel($modelid) {
        global $DB;
        return $DB->get_record(self::BDDTABLEMODELAXIS, array('id' => $modelid), '*', MUST_EXIST);
    }

    /**
     * delete a given model
     * @global $DB
     * @param int $modelid id of the model to delete
     * @return boolean true if the delete is success
     */
    public static function delModel($modelid) {
        global $DB;
        $model = self::gelModel($modelid);
        if (!$model) {
            return false;
        }
        $axe = self::get_axis_by_id($model->axis);
        if (!$axe) {
            return false;
        }
        if (!self::del_axis($axe->id)) {
            return false;
        }
        return $DB->delete_records(self::BDDTABLEMODELAXIS, array('id' => $modelid));
    }

}
