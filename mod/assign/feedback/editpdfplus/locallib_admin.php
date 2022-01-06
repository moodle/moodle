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
 * Description of locallib_admin
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2017 Université de Lausanne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

use \assignfeedback_editpdfplus\page_editor;
use \assignfeedback_editpdfplus\widget_admin;
use \assignfeedback_editpdfplus\form\axis_form;
use \assignfeedback_editpdfplus\form\axis_import_form;
use \assignfeedback_editpdfplus\form\axis_del_form;
use \assignfeedback_editpdfplus\form\axis_export_form;
use \assignfeedback_editpdfplus\form\model_del_form;
use \assignfeedback_editpdfplus\form\tool_order_form;
use \assignfeedback_editpdfplus\admin_editor;
use \assignfeedback_editpdfplus\bdd\tool;

class assign_feedback_editpdfplus_admin {

    const PLUGIN_NAME = "assignfeedback_editpdfplus";

    /** @var stdClass $context current context */
    private $context = null;

    function __construct(stdClass $context) {
        $this->context = $context;
    }

    /**
     * Display the admin grid to manage axis and tools.
     *
     * @global $PAGE
     * @return string
     */
    public function view() {
        global $PAGE;

        $html = '';
        $renderer = $PAGE->get_renderer(self::PLUGIN_NAME);

        //create axis import form
        $axisimportform = new axis_import_form(null, array('id' => $this->context->id), null, null, array('id' => "assignfeedback_editpdfplus_import_axis"));
        $axisimportform->id = "assignfeedback_editpdfplus_import_axis";
        $axisimportform->title = "";
        $axisimportform->action = "import";

        //create axis model delete form
        $modeldeleteform = new model_del_form(null, array('id' => $this->context->id), null, null, array('id' => "assignfeedback_editpdfplus_del_model"));
        $modeldeleteform->id = "assignfeedback_editpdfplus_del_model";
        $modeldeleteform->title = "";
        $modeldeleteform->action = "delmodel";

        //create axis delete form
        $axisdeleteform = new axis_del_form(null, array('id' => $this->context->id), null, null, array('id' => "assignfeedback_editpdfplus_del_axis"));
        $axisdeleteform->id = "assignfeedback_editpdfplus_del_axis";
        $axisdeleteform->title = "Supprimer l'axe";
        $axisdeleteform->action = "del";

        //create form to order tool
        $toolorderform = new tool_order_form(null, array('id' => $this->context->id), null, null, array('id' => "assignfeedback_editpdfplus_order_tool"));
        $toolorderform->id = "assignfeedback_editpdfplus_order_tool";
        $toolorderform->title = "";
        $toolorderform->action = "order";

        //create and fill admin widget
        $widget = $this->get_widget();
        $widget->axisimportform = $axisimportform;
        $widget->toolorderform = $toolorderform;
        $widget->modeldeleteform = $modeldeleteform;
        $widget->axisdeleteform = $axisdeleteform;

        //get html renderer
        $html .= $renderer->render_assignfeedback_editpdfplus_widget_admin($widget);
        return $html;
    }

    /**
     * Buid axis moodleform
     * @global $PAGE
     * @global $DB
     * @param $axeid
     * @return string
     */
    public function getAxisForm($axeid = null) {
        global $PAGE, $DB;

        $html = '';
        $toform = null;
        $formAxis = null;
        $axis = null;
        if ($axeid != null) {
            $axis = $DB->get_record('assignfeedback_editpp_axis', array('id' => $axeid), '*', MUST_EXIST);
        }
        if ($axis != null) {
            $formAxis = new axis_form(null, array('id' => $this->context->id), null, null, array('id' => "assignfeedback_editpdfplus_edit_axis")); //Form processing and displaying is done here
            $formAxis->set_data(array('axeid' => $axeid, 'label' => $axis->label));
            $formAxis->id = "assignfeedback_editpdfplus_edit_axis";
            $formAxis->title = "Renommer l'axe";
            $formAxis->action = "edit";
        } else {
            $formAxis = new axis_form(null, array('id' => $this->context->id), null, null, array('id' => "assignfeedback_editpdfplus_add_axis")); //Form processing and displaying is done here
            $formAxis->set_data($toform);
            $formAxis->id = "assignfeedback_editpdfplus_add_axis";
            $formAxis->title = get_string('axis_add', 'assignfeedback_editpdfplus');
            $formAxis->action = "add";
        }
        $renderer = $PAGE->get_renderer(self::PLUGIN_NAME);
        $formAxis->contextid = $this->context->id;
        $html .= $renderer->render_assignfeedback_editpdfplus_widget_admin_axisform($formAxis);
        return $html;
    }

    /**
     * Buid axis moodleform for exporting
     * @global $PAGE
     * @global $DB
     * @param $axeid
     * @return string
     */
    public function getAxisExportForm($axeid) {
        global $PAGE, $DB;

        $html = '';
        if ($axeid == null) {
            return;
        }
        $axis = $DB->get_record('assignfeedback_editpp_axis', array('id' => $axeid), '*', MUST_EXIST);
        $formAxisExport = new axis_export_form();
        $formAxisExport->set_data(array('contextid' => $this->context->id, 'axeid' => $axeid, 'label' => $axis->label));
        $formAxisExport->id = "assignfeedback_editpdfplus_export_axis";
        $formAxisExport->action = "saveaxisexport";
        $formAxisExport->title = "Exporter l'axe";
        $formAxisExport->contextid = $this->context->id;
        $renderer = $PAGE->get_renderer(self::PLUGIN_NAME);
        $widget = new stdClass();
        $widget->title = "Exporter l'axe";
        $widget->form = $formAxisExport;
        $html .= $renderer->render_assignfeedback_editpdfplus_widget_admin_axisexportform($widget);
        return $html;
    }

    /**
     * Buid tool moodleform
     * @global $PAGE
     * @global $DB
     * @param $toolid
     * @param $axeid
     * @return string
     */
    public function getToolForm($toolid = null, $axisid = null) {
        global $PAGE, $DB;

        $html = '';
        $data = new stdClass;
        $data->contextid = $this->context->id;
        $data->sesskey = sesskey();
        $data->actionurl = "/moodle/lib/ajax/service.php";
        $data->formid = "assignfeedback_editpdfplus_edit_tool";
        $tool = new tool();
        if ($toolid != null) {
            $record = $DB->get_record('assignfeedback_editpp_tool', array('id' => $toolid), '*', MUST_EXIST);
            $tool = new tool($record);
            $nbEnregistrements = $DB->get_record_sql('SELECT count(*) as val FROM {assignfeedback_editpp_annot} WHERE toolid = ?', array('toolid' => $toolid));
            $tool->removable = !($nbEnregistrements->val > 0);
        } else {
            $tool->init(array("contextid" => $this->context->id, "axisid" => $axisid));
        }
        $tool->init_tool_texts_array();
        $data->tool = $tool;
        $data->tools = admin_editor::get_typetools();
        foreach ($data->tools as $toolRef) {
            $toolRef->libelle = get_string('typetool_' . $toolRef->label, self::PLUGIN_NAME);
        }
        $axis = page_editor::get_axis(array($this->context->id));
        $data->axis = $axis;
        $renderer = $PAGE->get_renderer(self::PLUGIN_NAME);
        $html .= $renderer->render_assignfeedback_editpdfplus_widget_admin_toolform($data);
        return $html;
    }

    /**
     * Create a admin widget for rendering the editor.
     *
     * @return assignfeedback_editpdfplus_widget_admin
     */
    private function get_widget() {
        global $USER;

        // get the costum toolbars
        $coursecontexts = array_filter(explode('/', $this->context->path), 'strlen');
        $tools = page_editor::get_tools($coursecontexts);
        $typetools = page_editor::get_typetools(null);
        $axis = page_editor::get_axis(array($this->context->id));
        $toolbars = $this->prepareToolbar($axis, $tools);

        //get all favorite axis
        $models = admin_editor::getCategoryModel($USER->id);
        $axisDispo = array();
        $toolbarsDispo = array();
        foreach ($models as $model) {
            $axistmp = page_editor::get_axis_by_id($model->axis);
            if ($axistmp) {
                $axisDispo[] = $axistmp;
            }
            $tooltmp = page_editor::get_tools_by_axis($model->axis);
            foreach ($tooltmp as $tool) {
                $tool->set_design();
            }
            $tmp = new stdClass();
            $tmp->model = $model;
            $tmp->axis = $axistmp;
            $tmp->tools = $tooltmp;
            $toolbarsDispo[] = $tmp;
        }
        $widget = new widget_admin($this->context, $USER, $toolbars, $axis, $typetools, $toolbarsDispo);
        return $widget;
    }

    /**
     * Build display for specific tools on toolbars
     * @param axis $axis
     * @param array<tool> $tools
     * @return array<tool>
     */
    private function prepareToolbar($axis, $tools) {
        global $DB;
        $toolbars = array();
        foreach ($axis as $ax) {
            $ax->children = 0;
            $ax->canbedelete = true;
            $toolbar = new stdClass();
            $toolbar->axis = $ax;
            $toolbar->tools = array();
            foreach ($tools as $tool) {
                if ($tool->axis != $ax->id) {
                    continue;
                }
                //search if this tool is used
                if ($ax->canbedelete) {
                    $nbannotation = admin_editor::getNbAnnotationsForTool($tool->id);
                    $ax->canbedelete &= $nbannotation == 0;
                }
                $tool->set_design();
                $toolbar->tools[] = $tool;
                $ax->children++;
            }
            $ax->containannotations = !$ax->canbedelete;
            $toolbars[] = $toolbar;
        }
        return $toolbars;
    }

}
