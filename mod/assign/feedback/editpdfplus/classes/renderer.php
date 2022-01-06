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
 * This file contains the definition for the library class for edit PDF renderer.
 *
 * A custom renderer class that extends the plugin_renderer_base and is used by the editpdfplus feedback plugin.
 * 
 * @package   assignfeedback_editpdfplus
 * @copyright  2016 Université de Lausanne
 * The code is based on mod/assign/feedback/editpdf/classes/renderer.php by Davo Smith.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

use \assignfeedback_editpdfplus\bdd\tool_generic;
use \assignfeedback_editpdfplus\bdd\axis;

class assignfeedback_editpdfplus_renderer extends plugin_renderer_base {

    const PLUGIN_NAME = "assignfeedback_editpdfplus";
    const TOOL_SELECT = "select";
    const TOOL_DRAG = "drag";
    const TOOL_RESIZE = "resize";
    const TOOL_ANNOTATIONCOLOR = "annotationcolour";
    const HTMLCLASS = "class";
    const HTMLDISABLED = "disabled";
    const TOOL_OBJ_LABEL = "label";

    /**
     * Render a HTML button for a Tool.
     * 
     * @param assignfeedback_editpdfplus\bdd\tool $fulltool Object tool to reprensant
     * @param bool $disabled Optional - Is this button disabled.
     * @return string
     */
    private function render_toolbar_button_tool(assignfeedback_editpdfplus\bdd\tool $fulltool, $disabled = false) {
        $displayArray = $fulltool->get_renderer_bouton_html_display($disabled);
        return $this->render_toolbar_button_html($displayArray["content"], $displayArray["parameters"]);
    }

    /**
     * Render a simple HTML button
     * 
     * @param string $content Button's content
     * @param array $parameters Button's parameters. Optional
     * @return string
     */
    private function render_toolbar_button_html($content, $parameters = array()) {
        return html_writer::tag("button", $content, array_merge(array('type' => 'button'), $parameters));
    }

    /**
     * Render an icon from FontAwesome class
     * 
     * @param string $faClass
     * @return string
     */
    private function render_toolbar_button_icon($faClass = "") {
        return html_writer::tag("i", "", array(self::HTMLCLASS => 'fa ' . $faClass,
                    "aria-hidden"));
    }

    /**
     * Render a complete toolbar
     * 
     * @param string $content buttons or whatever inside the toolbar
     * @param string $class Optionals classes
     * @param string $param Optionals parameters
     * @return string
     */
    private function render_toolbar($content, $class = "", $param = array()) {
        return html_writer::div($content, "btn-group btn-group-sm " . $class, array_merge(array('role' => 'group'), $param));
    }

    /**
     * Render a graphic reprensation for an axis in readonly mode (checkbox)
     * 
     * @param axis $axis Axis to display
     * @return string
     */
    private function render_toolbar_axis(axis $axis) {
        $iconhtml = $axis->label;
        $iconparams = array('type' => 'checkbox', self::HTMLCLASS => 'axis', 'id' => 'ctaxis' . $axis->id, 'value' => $axis->id);
        $inputhtml = html_writer::tag('input', "", $iconparams);
        return html_writer::label($inputhtml . $iconhtml, "", true, array(self::HTMLCLASS => 'checkbox-inline mt-2 mr-2'));
    }

    /**
     * Render the editpdfplus widget in the grading form.
     *
     * @param assignfeedback_editpdfplus_widget $widget - Renderable widget containing assignment, user and attempt number.
     * @return string
     */
    public function render_assignfeedback_editpdfplus_widget(assignfeedback_editpdfplus_widget $widget) {
        $html = '';

        //JS declaration
        $html .= html_writer::div(get_string('jsrequired', self::PLUGIN_NAME), 'hiddenifjs');

        //Random id for plugin identification
        $linkid = html_writer::random_id();
        $labelLaunchedEditor = ($widget->readonly) ? get_string('viewfeedbackonline', self::PLUGIN_NAME) : get_string('launcheditor', self::PLUGIN_NAME);
        $links = html_writer::tag('a', $labelLaunchedEditor, array('id' => $linkid, self::HTMLCLASS => 'btn btn-light', 'href' => '#'));
        $html .= '<input type="hidden" name="assignfeedback_editpdfplus_haschanges" value="false"/>';
        $html .= html_writer::div($links, 'visibleifjs');

        //html header
        $header = get_string('pluginname', self::PLUGIN_NAME);

        $body = '';

        // Create the page navigation.
        $navigation = '';
        // Pick the correct arrow icons for right to left mode.
        if (right_to_left()) {
            $nav_prev = 'nav_next';
            $nav_next = 'nav_prev';
        } else {
            $nav_prev = 'nav_prev';
            $nav_next = 'nav_next';
        }
        $classNav = "btn btn-light ";
        $iconhtmlP = $this->render_toolbar_button_icon("fa-caret-left fa-2x");
        $navigation .= $this->render_toolbar_button_html($iconhtmlP, array(self::HTMLDISABLED => 'true',
            self::HTMLCLASS => $classNav . 'navigate-previous-button'));
        $navigation .= html_writer::tag('select', null, array(self::HTMLDISABLED => 'true',
                    'aria-label' => get_string('gotopage', self::PLUGIN_NAME), self::HTMLCLASS => "navigate-page-select"));
        $iconhtmlN = $this->render_toolbar_button_icon("fa-caret-right fa-2x");
        $navigation .= $this->render_toolbar_button_html($iconhtmlN, array(self::HTMLDISABLED => 'true',
            self::HTMLCLASS => $classNav . "navigate-next-button"));

        $navigationBlock = $this->render_toolbar($navigation, "mr-3");

        $toolbarRotationBlock = '';
        $toolbarBaseBlock = '';
        $toolbarDrawBlock = '';
        $toolbarAdminBlock = '';
        $toolbarCostumdiv = '';
        $toolbarAxis = '';

        if (!$widget->readonly) {
            /** Toolbar n°0 : basic tools * */
            // Rotate Tool.
            $rotateToolLeft = new tool_generic();
            $rotateToolLeft->init(array(self::TOOL_OBJ_LABEL => "rotateleft"));
            $toolbarRotation = $this->render_toolbar_button_tool($rotateToolLeft);
            $rotateToolRight = new tool_generic();
            $rotateToolRight->init(array(self::TOOL_OBJ_LABEL => 'rotateright'));
            $toolbarRotation .= $this->render_toolbar_button_tool($rotateToolRight);
            $toolbarRotationBlock = $this->render_toolbar($toolbarRotation, "mr-3");

            // Select Tool.
            $dragTool = new tool_generic();
            $dragTool->init(array(self::TOOL_OBJ_LABEL => self::TOOL_DRAG));
            $toolbarBase = $this->render_toolbar_button_tool($dragTool);
            $selectTool = new tool_generic();
            $selectTool->init(array(self::TOOL_OBJ_LABEL => self::TOOL_SELECT));
            $toolbarBase .= $this->render_toolbar_button_tool($selectTool);
            $resizeTool = new tool_generic();
            $resizeTool->init(array(self::TOOL_OBJ_LABEL => self::TOOL_RESIZE));
            $toolbarBase .= $this->render_toolbar_button_tool($resizeTool);
            $toolbarBaseBlock = $this->render_toolbar($toolbarBase, "mr-3");

            // Generic Tools.
            $toolbarDraw = '';
            foreach ($widget->genericToolbar as $tool) {
                $toolbarDraw .= $this->render_toolbar_button_tool($tool);
            }
            $colorTool = new tool_generic();
            $colorTool->init(array(self::TOOL_OBJ_LABEL => self::TOOL_ANNOTATIONCOLOR));
            $toolbarDraw .= $this->render_toolbar_button_tool($colorTool);
            $toolbarDrawBlock = $this->render_toolbar($toolbarDraw);

            /** Costum toolbars * */
            $axis = array();
            foreach ($widget->customToolbars as $toolbar) {
                $axis[$toolbar['axeid']] = $toolbar[self::TOOL_OBJ_LABEL];
                $toolbartmp = '';
                foreach ($toolbar['tool'] as $tool) {
                    $toolbartmp .= $this->render_toolbar_button_tool($tool);
                }
                $toolbarCostumdiv .= $this->render_toolbar($toolbartmp, "mr-3 customtoolbar", array('id' => 'toolbaraxis' . $toolbar['axeid']));
            }

            $statuschoice = $this->render_toolbar(html_writer::select($axis, 'axisselection', 0, FALSE), "mr-0");
            $toolbarAxis = $statuschoice;

            // Toolbar pour lien creation palette et aide
            $parentContext = $this->page->context->get_parent_context();
            if ($parentContext->contextlevel == CONTEXT_COURSE) {
                $lienAdmin = new moodle_url('/mod/assign/feedback/editpdfplus/view_admin.php', array('id' => $parentContext->id));
                $toolbarAdmin = $this->render_toolbar_button_html($this->render_toolbar_button_icon("fa-wrench"), array(
                    self::HTMLCLASS => 'btn btn-outline-info',
                    'onclick' => "document.location='" . $lienAdmin->out() . "';"));
            }
            $toolbarAdmin .= $this->render_toolbar_button_html($this->render_toolbar_button_icon("fa-question-circle"), array(self::HTMLCLASS => 'btn btn-outline-info helpmessage'));
            $toolbarAdminBlock = $this->render_toolbar($toolbarAdmin, "mr-3");
        } else {
            //readonly view
            $axis = $widget->axis;
            $toolbaraxisContent = "";
            foreach ($axis as $ax) {
                $toolbaraxisContent .= $this->render_toolbar_axis($ax);
            }
            $toolbarAxis = $this->render_toolbar($toolbaraxisContent, "mr-2");

            $questionchoice = html_writer::select(
                            [get_string('question_select', self::PLUGIN_NAME), get_string('question_select_without', self::PLUGIN_NAME), get_string('question_select_with', self::PLUGIN_NAME)], 'questionselection', 0, FALSE, array(self::HTMLCLASS => 'form-control'));
            $statuschoice = html_writer::select(
                            [get_string('statut_select', self::PLUGIN_NAME), get_string('statut_select_nc', self::PLUGIN_NAME), get_string('statut_select_ok', self::PLUGIN_NAME), get_string('statut_select_ko', self::PLUGIN_NAME)], 'statutselection', 0, FALSE, array(self::HTMLCLASS => 'form-control'));
            $validatebutton = $this->render_toolbar_button_html(get_string('send_pdf_update', self::PLUGIN_NAME), array(self::HTMLCLASS => 'btn btn-light', 'id' => 'student_valide_button'));
            $toolbarAxis .= $this->render_toolbar($statuschoice);
            $toolbarAxis .= $this->render_toolbar($questionchoice, 'mr-3');
            $toolbarAxis .= $this->render_toolbar($validatebutton, 'mr-0');
        }

        $pageheadercontent = "<div class='d-flex align-content-center flex-nowrap align-items-center'>"
                . $navigationBlock
                . $toolbarAdminBlock
                . "<div class='d-fex flex-wrap align-content-center align-items-center'>"
                . $toolbarRotationBlock
                . $toolbarBaseBlock
                . "<div class='btn-group btn-group-sm p-1'>"
                . $toolbarAxis
                . $toolbarCostumdiv
                . '</div>'
                . $toolbarDrawBlock
                . '</div>'
                . '</div>';
        $mainnavigation = html_writer::div($pageheadercontent, "drawingtoolbar btn-toolbar btn-group-sm bg-light p-1", array('role' => 'toolbar'));

        $body .= $mainnavigation;

        // Loading progress bar.
        $progressbar = html_writer::div(html_writer::div('', 'bar'), 'progress progress-info progress-striped active', array('title' => get_string('loadingeditor', self::PLUGIN_NAME),
                    'role' => 'progressbar', 'aria-valuenow' => 0, 'aria-valuemin' => 0,
                    'aria-valuemax' => 100));
        $progressbarlabel = html_writer::div(get_string('generatingpdf', self::PLUGIN_NAME), 'progressbarlabel');
        $loading = html_writer::div($progressbar . $progressbarlabel, 'loading');
        $canvas = html_writer::div(html_writer::div($loading, 'drawingcanvas'), 'drawingregion');

        // Place for messages, but no warnings displayed yet.
        $changesmessage = html_writer::div('', 'warningmessages');
        $canvas .= $changesmessage;

        $changesmessage2 = html_writer::tag('div', get_string('nodraftchangessaved', self::PLUGIN_NAME), array(
                    self::HTMLCLASS => 'assignfeedback_editpdfplus_unsavedchanges_edit warning label label-info'
        ));
        $changesmessage2Div = html_writer::div($changesmessage2, 'unsaved-changes');
        $canvas .= $changesmessage2Div;

        $infoicon = "<i class='fa fa-info-circle p-1'></i>";
        $infomessage = html_writer::div($infoicon, 'assignfeedback_editpdfplus_infoicon');
        $canvas .= $infomessage;

        //help message
        $helpmessageTitle = html_writer::div(get_string('help_title', self::PLUGIN_NAME), null, array('id' => 'afppHelpmessageTitle'));
        $helpmessagecontent = $this->render_from_template('assignfeedback_editpdfplus/help_workspace', array());
        $helpmessageBody = html_writer::div($helpmessagecontent, null, array('id' => 'afppHelpmessageBody'));
        $helpmessageDiv = html_writer::div($helpmessageTitle . $helpmessageBody, 'helpmessage');
        $canvas .= $helpmessageDiv;

        $body .= $canvas;

        $footer = '';

        $editorparams = array(array('header' => $header,
                'body' => $body,
                'footer' => $footer,
                'linkid' => $linkid,
                'assignmentid' => $widget->assignment,
                'userid' => $widget->userid,
                'attemptnumber' => $widget->attemptnumber,
                'readonly' => $widget->readonly));

        $this->page->requires->yui_module('moodle-assignfeedback_editpdfplus-editor', 'M.assignfeedback_editpdfplus.editor.init', $editorparams);

        $this->page->requires->strings_for_js(array(
            'yellow',
            'yellowlemon',
            'white',
            'red',
            'blue',
            'green',
            'black',
            'clear',
            'colourpicker',
            'loadingeditor',
            'pagexofy',
            'addtoquicklist',
            'filter',
            'deleteannotation',
            'cannotopenpdf',
            'pagenumber',
            'partialwarning',
            'draftchangessaved',
            'student_statut_nc',
            'student_answer_lib'
                ), self::PLUGIN_NAME);

        return $html;
    }

    /**
     * Display admin view
     * @param assignfeedback_editpdfplus\widget_admin $widget
     * @return String
     */
    public function render_assignfeedback_editpdfplus_widget_admin(assignfeedback_editpdfplus\widget_admin $widget) {
        return $this->render_from_template('assignfeedback_editpdfplus/admin', $widget);
    }

    /**
     * Display axis form (add and edit)
     * @param moodleform $form
     * @return String
     */
    public function render_assignfeedback_editpdfplus_widget_admin_axisform(moodleform $form) {
        return $this->render_from_template('assignfeedback_editpdfplus/axis_form', $form);
    }

    /**
     * Display axis form (export)
     * @param stdClass $widget
     * @return String
     */
    public function render_assignfeedback_editpdfplus_widget_admin_axisexportform($widget) {
        return $this->render_from_template('assignfeedback_editpdfplus/axis_export_form', $widget);
    }

    /**
     * Display tool form, with preview
     * @param object $data
     * @return String
     */
    public function render_assignfeedback_editpdfplus_widget_admin_toolform($data) {
        $data->map01 = $this->image_url('map01', self::PLUGIN_NAME);
        $data->map02 = $this->image_url('map02', self::PLUGIN_NAME);
        $data->map03 = $this->image_url('map03', self::PLUGIN_NAME);
        return $this->render_from_template('assignfeedback_editpdfplus/tool_form', $data);
    }

}
