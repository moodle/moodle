<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden!');
}
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/csvlib.class.php');

class mod_data_export_form extends moodleform {
    var $_datafields = array();
    var $_cm;

     // @param string $url: the url to post to
     // @param array $datafields: objects in this database
    function mod_data_export_form($url, $datafields, $cm) {
        $this->_datafields = $datafields;
        $this->_cm = $cm;
        parent::moodleform($url);
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
        $typesarray[] = &MoodleQuickForm::createElement('radio', 'exporttype', null, get_string('csvwithselecteddelimiter', 'data') . '&nbsp;', 'csv');
        $typesarray[] = &MoodleQuickForm::createElement('select', 'delimiter_name', null, $choices);
        $typesarray[] = &MoodleQuickForm::createElement('radio', 'exporttype', null, get_string('excel', 'data'), 'xls');
        $typesarray[] = &MoodleQuickForm::createElement('radio', 'exporttype', null, get_string('ods', 'data'), 'ods');
        if ($CFG->enableportfolios) {
            $typesarray[] = &MoodleQuickForm::createElement('radio', 'exporttype', null, get_string('format_leap2a', 'portfolio'), 'leap2a');
        }
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
                $mform->addElement('advcheckbox', 'field_'.$field->field->id, '<div title="' . s($field->field->description) . '">' . $field->field->name . '</div>', ' (' . $field->name() . ')', array('group'=>1));
                $mform->setDefault('field_'.$field->field->id, 1);
            } else {
                $a = new object;
                $a->fieldtype = $field->name();
                $mform->addElement('static', 'unsupported'.$field->field->id, $field->field->name, get_string('unsupportedexport', 'data', $a));
            }
        }
        $this->add_checkbox_controller(1, null, null, 1);
        if ($CFG->enableportfolios && has_capability('mod/data:exportallentries', get_context_instance(CONTEXT_MODULE, $this->_cm->id))) {
            require_once($CFG->libdir . '/portfoliolib.php');
            require_once($CFG->dirroot . '/mod/data/locallib.php');
            if ($portfoliooptions = portfolio_instance_select(
                portfolio_instances(),
                call_user_func(array('data_portfolio_caller', 'base_supported_formats')),
                'data_portfolio_caller',
                mimeinfo('type', 'export.csv'),
                'instance',
                true,
                true)) {
                    $mform->addElement('header', 'notice', get_string('portfolionotfile', 'data') . ':');
                    $portfoliooptions[0] = get_string('none');
                    ksort($portfoliooptions);
                    $mform->addElement('select', 'portfolio', get_string('portfolio', 'portfolio'), $portfoliooptions);
            }
        }
        $this->add_action_buttons(true, get_string('exportdatabaserecords', 'data'));
    }

}


