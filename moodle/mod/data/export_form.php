<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden!');
}
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/csvlib.class.php');

class mod_data_export_form extends moodleform {
    var $_datafields = array();
    var $_cm;
    var $_data;

     // @param string $url: the url to post to
     // @param array $datafields: objects in this database
    public function __construct($url, $datafields, $cm, $data) {
        $this->_datafields = $datafields;
        $this->_cm = $cm;
        $this->_data = $data;
        parent::__construct($url);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function mod_data_export_form($url, $datafields, $cm, $data) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($url, $datafields, $cm, $data);
    }

    function definition() {
        global $CFG;
        $mform =& $this->_form;
        $mform->addElement('header', 'notice', get_string('chooseexportformat', 'data'));
        $choices = csv_import_reader::get_delimiter_list();
        $key = array_search(';', $choices);
        if (! $key === FALSE) {
            // array $choices contains the semicolon -> drop it (because its encrypted form also contains a semicolon):
            unset($choices[$key]);
        }
        $typesarray = array();
        $str = get_string('csvwithselecteddelimiter', 'data');
        $typesarray[] = $mform->createElement('radio', 'exporttype', null, $str . '&nbsp;', 'csv');
        $typesarray[] = $mform->createElement('select', 'delimiter_name', null, $choices);
        //temporarily commenting out Excel export option. See MDL-19864
        //$typesarray[] = $mform->createElement('radio', 'exporttype', null, get_string('excel', 'data'), 'xls');
        $typesarray[] = $mform->createElement('radio', 'exporttype', null, get_string('ods', 'data'), 'ods');
        $mform->addGroup($typesarray, 'exportar', '', array(''), false);
        $mform->addRule('exportar', null, 'required');
        $mform->setDefault('exporttype', 'csv');
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }
        $mform->addElement('header', 'notice', get_string('chooseexportfields', 'data'));
        foreach($this->_datafields as $field) {
            if($field->text_export_supported()) {
                $html = '<div title="' . s($field->field->description) . '" ' .
                        'class="d-inline-block">' . $field->field->name . '</div>';
                $name = ' (' . $field->name() . ')';
                $mform->addElement('advcheckbox', 'field_' . $field->field->id, $html, $name, array('group' => 1));
                $mform->setDefault('field_' . $field->field->id, 1);
            } else {
                $a = new stdClass();
                $a->fieldtype = $field->name();
                $str = get_string('unsupportedexport', 'data', $a);
                $mform->addElement('static', 'unsupported' . $field->field->id, $field->field->name, $str);
            }
        }
        $this->add_checkbox_controller(1, null, null, 1);
        $context = context_module::instance($this->_cm->id);
        if (has_capability('mod/data:exportuserinfo', $context)) {
            $mform->addElement('checkbox', 'exportuser', get_string('includeuserdetails', 'data'));
        }
        $mform->addElement('checkbox', 'exporttime', get_string('includetime', 'data'));
        if ($this->_data->approval) {
            $mform->addElement('checkbox', 'exportapproval', get_string('includeapproval', 'data'));
        }
        $this->add_action_buttons(true, get_string('exportentries', 'data'));
    }

}


