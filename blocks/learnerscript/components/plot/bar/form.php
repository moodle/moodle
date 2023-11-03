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

/** LearnerScript
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 */
if (!defined('MOODLE_INTERNAL')) {
    die(get_string('nodirectaccess','block_learnerscript'));    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');

class bar_form extends moodleform {

    function definition() {
        global $DB, $USER, $CFG, $OUTPUT;

        $mform = & $this->_form;
        $options = array();

        $report = $this->_customdata['report'];
        $cid = $this->_customdata['cid'];

        $components = (new block_learnerscript\local\ls)->cr_unserialize($this->_customdata['report']->components);

        if (!is_array($components) || empty($components['columns']['elements']))
            print_error('nocolumns');

        $columns = $components['columns']['elements']; 
        foreach ($columns as $c) {
            if ($report->type != 'sql') {
                if(in_array($c['formdata']->column, $this->_customdata['reportclass']->orderable)){
                    $options[$c['formdata']->column] = $c['formdata']->columname;
                }
            } else {
                $options[$c['formdata']->column] = $c['formdata']->columname;
            }
        }
        // $mform->addElement('header', 'crformheader', get_string('bar', 'block_learnerscript'), '');

        $mform->addElement('text', 'chartname', get_string('chartname', 'block_learnerscript'));
        $mform->setType('chartname', PARAM_RAW);
        $mform->addRule('chartname', get_string('chartnamerequired', 'block_learnerscript'), 'required', null, 'client');

        $mform->addElement('select', 'serieid', get_string('serieid', 'block_learnerscript'), array(null => get_string('choose')) + $options);
        $mform->addRule('serieid', null, 'required', null, 'client');

        $mform->addElement('select', 'yaxis', get_string('yaxis', 'block_learnerscript'), $options, 
                            array('data-select2'=>true,'multiple' => 'multiple', 'onChange' => '(function(e){ require("block_learnerscript/report").AddExpressions(e, " ")})(event);'));
        $mform->addRule('yaxis', null, 'required', null, 'client');

        $sortby = array();
        $ajaxformdata = $this->_ajaxformdata;
        $selectedcalc = false;
        $disabled = [];
        if (empty($ajaxformdata)) {
            $elements = isset($components['plot']['elements']) ?
                                $components['plot']['elements'] : array();
            if (!empty($elements)) {
                foreach ($elements as $e) {
                    if ($e['id'] == $cid) {
                        $selectedcalc = (isset($e['formdata']->calcs) && !empty($e['formdata']->calcs)) ? true : false;
                        $conditions = '';
                        $conditionsymbols = ["=", ">", "<", ">=", "<=", "<>"];
                        foreach ($e['formdata']->yaxis as $k => $column) {
                            $columndata['name'] = $column;
                            $columndata['symbol'] = $e['formdata']->yaxis[$k]; 
                            $columndata['conditionsymbols'] = [];
                            if(!isset($e['formdata']->{$column})){
                                $columndata['disabled'] = 'disabled';
                            }else{
                                $columndata['disabled'] = false;
                                $columndata['value'] = $e['formdata']->{$column.'_value'};
                            }
                            foreach ($conditionsymbols as $value) {
                                if (!empty($e['formdata']->{$column})) {
                                    if($value == $e['formdata']->{$column}){
                                        $columndata['conditionsymbols'][] = ['value' => $value, 'selected' => true];
                                    }else{
                                        $columndata['conditionsymbols'][] = ['value' => $value];
                                    }
                                } else {
                                    $columndata['conditionsymbols'][] = ['value' => $value];
                                }
                            }
                            $conditions .= $OUTPUT->render_from_template('block_learnerscript/plotconditions', 
                                           ['column' => $columndata]);
                        }
                        $mform->addElement('html',  $conditions);
                        break;
                    }
                }
            }
        } else {
            $selectedcalc = (isset($ajaxformdata['calcs']) && !empty($ajaxformdata['calcs'])) ? true : false;
        }
        $calcdisabled = array();
        if($selectedcalc){
            $calcdisabled = array('disabled' => 'disabled');
        }
        $mform->addElement('html', '<div id="yaxis1"></div>');
        $mform->addElement('advcheckbox', 'showlegend', get_string('showlegend', 'block_learnerscript'), '', null, array(0, 1));

        $mform->addElement('advcheckbox', 'datalabels', get_string('datalabels', 'block_learnerscript'), '', null, array(0, 1));

        $mform->addElement('select', 'calcs', get_string('calcs', 'block_learnerscript'),
            array(null => '--SELECT--', 'average' => 'Average', 'max' => 'Max', 'min' => 'Min',
                        'sum' => 'Sum'));
        $sortby[] = $mform->createElement('select', 'columnsort', '', $options, $calcdisabled);
        $mform->setType('columnsort', PARAM_RAW);
        $sortby[] = $mform->createElement('select', 'sorting', '', array(null => '--SELECT--',
                                    'ASC' => 'ASC', 'DESC' => 'DESC'), $calcdisabled);
        $mform->setType('sorting', PARAM_RAW);
        $mform->addGroup($sortby, 'sortby', get_string('sortby', 'block_learnerscript'),
                                array('&nbsp;&nbsp;&nbsp;'), false);
        $mform->setType('sortby', PARAM_RAW);

        $mform->addElement('select', 'limit', get_string('limit', 'block_learnerscript'),
                        array(null => '--SELECT--', 10 => 10, 20 => 20, 50 => 50, 100 => 100), $calcdisabled);

        // buttons
        $this->add_action_buttons(true, get_string('add'));
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (!empty($data['serieid'] && isset($data['yaxis'])) && $data['serieid'] == $data['yaxis'][0]) {
            $errors['yaxis'] = get_string('xandynotequal', 'block_learnerscript');
        }
        return $errors;
    }
}