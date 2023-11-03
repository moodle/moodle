<?php

if (!defined('MOODLE_INTERNAL')) {
    die(get_string('nodirectaccess','block_learnerscript'));    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');
use block_learnerscript\local\ls;

class column_form extends moodleform {

    function definition() {
        global $DB, $USER, $CFG;

        $mform = & $this->_form;
        //$options = array(0 => get_string('choose'));

        $report = $this->_customdata['report'];
        $cid = $this->_customdata['cid'];
        $components = (new ls)->cr_unserialize($this->_customdata['report']->components);

        if (!is_array($components) || empty($components['columns']['elements'])) {
            print_error('nocolumns');
        }

        $columns = $components['columns']['elements'];
        foreach ($columns as $c) {
           $options[$c['formdata']->column] = $c['formdata']->columname;
        }

        // $mform->addElement('header', 'crformheader', get_string('column', 'block_learnerscript'), '');

        $mform->addElement('text', 'chartname', get_string('chartname', 'block_learnerscript'));
        $mform->setType('chartname', PARAM_RAW);
        $mform->addRule('chartname', get_string('chartnamerequired', 'block_learnerscript'),
                        'required', null, 'client');

        $mform->addElement('select', 'serieid', get_string('serieid', 'block_learnerscript'), array(null => get_string('choose')) + $options);
        $mform->addRule('serieid', null, 'required', null, 'client');

        $mform->addElement('select', 'yaxis', get_string('yaxis', 'block_learnerscript'),
                            $options, array('multiple' => 'multiple'));
        $mform->addRule('yaxis', null, 'required', null, 'client');

        $mform->addElement('advcheckbox', 'showlegend', get_string('showlegend',
                            'block_learnerscript'), '', null, array(0, 1));

        $mform->addElement('advcheckbox', 'datalabels', get_string('datalabels', 'block_learnerscript'), '', null, array(0, 1));

        $mform->addElement('select', 'calcs', get_string('calcs', 'block_learnerscript'),
                            array(null => '--SELECT--', 'average' => 'Average','max' => 'Max',
                                'min' => 'Min', 'sum' => 'Sum'));

        $sortby = array();
        $ajaxformdata = $this->_ajaxformdata;
        $selectedcalc = false;
        if (empty($ajaxformdata)) {
            $elements = isset($components['plot']['elements']) ?
                                $components['plot']['elements'] : array();
            if ($elements) {
                foreach ($elements as $e) {
                    if ($e['id'] == $cid) {
                        $selectedcalc = (isset($e['formdata']->calcs) && !empty($e['formdata']->calcs)) ? true : false;
                        break;
                        break;
                    }
                }
            }
        } else {
            $selectedcalc = $ajaxformdata['calcs'];
        }
        if ($selectedcalc) {
            $sortby[] = $mform->createElement('select', 'columnsort', '', $options, array('disabled' => $disabled));
            $mform->setType('columnsort', PARAM_RAW);
            $sortby[] = $mform->createElement('select', 'sorting', '', array(null => '--SELECT--',
                                        'ASC' => 'ASC', 'DESC' => 'DESC'), array('disabled' => $disabled));
            $mform->setType('sorting', PARAM_RAW);
            $mform->addGroup($sortby, 'sortby', get_string('sortby', 'block_learnerscript'),
                                    array('&nbsp;&nbsp;&nbsp;'), false);
            $mform->setType('sortby', PARAM_RAW);

            $mform->addElement('select', 'limit', get_string('limit', 'block_learnerscript'),
                            array(null => '--SELECT--', 10 => 10, 20 => 20, 50 => 50, 100 => 100), array('disabled' => $disabled));
        } else {
            $sortby[] = $mform->createElement('select', 'columnsort', '', $options);
            $mform->setType('columnsort', PARAM_RAW);
            $sortby[] = $mform->createElement('select', 'sorting', '', array(null => '--SELECT--', 'ASC' => 'ASC', 'DESC' => 'DESC'));
            $mform->setType('sorting', PARAM_RAW);
            $mform->addGroup($sortby, 'sortby', get_string('sortby', 'block_learnerscript'),
                                    array('&nbsp;&nbsp;&nbsp;'), false);
            $mform->setType('sortby', PARAM_RAW);

            $mform->addElement('select', 'limit', get_string('limit', 'block_learnerscript'),
                            array(null => '--SELECT--', 10 => 10, 20 => 20, 50 => 50, 100 => 100));
        }

        // buttons
        $this->add_action_buttons(true, get_string('add'));
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (!empty($data['serieid']) && $data['serieid'] == $data['yaxis'][0]) {
            $errors['yaxis'] = get_string('xandynotequal', 'block_learnerscript');
        }
        return $errors;
    }
}