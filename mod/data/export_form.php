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
        $mform =& $this->_form;
        $mform->addElement('header', 'exportformat', get_string('chooseexportformat', 'data'));

        $optionattrs = ['class' => 'mt-1 mb-1'];

        // Export format type radio group.
        $typesarray = array();
        $typesarray[] = $mform->createElement('radio', 'exporttype', null, get_string('csvwithselecteddelimiter', 'data'), 'csv',
            $optionattrs);
        // Temporarily commenting out Excel export option. See MDL-19864.
        //$typesarray[] = $mform->createElement('radio', 'exporttype', null, get_string('excel', 'data'), 'xls');
        $typesarray[] = $mform->createElement('radio', 'exporttype', null, get_string('ods', 'data'), 'ods', $optionattrs);
        $mform->addGroup($typesarray, 'exportar', get_string('exportformat', 'data'), null, false);
        $mform->addRule('exportar', null, 'required');
        $mform->setDefault('exporttype', 'csv');

        // CSV delimiter list.
        $choices = csv_import_reader::get_delimiter_list();
        $key = array_search(';', $choices);
        if ($key !== false) {
            // Array $choices contains the semicolon -> drop it (because its encrypted form also contains a semicolon):
            unset($choices[$key]);
        }
        $mform->addElement('select', 'delimiter_name', get_string('fielddelimiter', 'data'), $choices);
        $mform->hideIf('delimiter_name', 'exporttype', 'neq', 'csv');
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }

        // Fields to be exported.
        $mform->addElement('header', 'exportfieldsheader', get_string('chooseexportfields', 'data'));
        $mform->setExpanded('exportfieldsheader');
        $numfieldsthatcanbeselected = 0;
        $exportfields = [];
        $unsupportedfields = [];
        foreach ($this->_datafields as $field) {
            $label = get_string('fieldnametype', 'data', (object)['name' => $field->field->name, 'type' => $field->name()]);
            if ($field->text_export_supported()) {
                $numfieldsthatcanbeselected++;
                $exportfields[] = $mform->createElement('advcheckbox', 'field_' . $field->field->id, '', $label,
                    array_merge(['group' => 1], $optionattrs));
                $mform->setDefault('field_' . $field->field->id, 1);
            } else {
                $unsupportedfields[] = $label;
            }
        }
        $mform->addGroup($exportfields, 'exportfields', get_string('selectfields', 'data'), ['<br>'], false);

        if ($numfieldsthatcanbeselected > 1) {
            $this->add_checkbox_controller(1, null, null, 1);
        }

        // List fields that cannot be exported.
        if (!empty($unsupportedfields)) {
            $unsupportedfieldslist = html_writer::tag('p', get_string('unsupportedfieldslist', 'data'), ['class' => 'mt-1']);
            $unsupportedfieldslist .= html_writer::alist($unsupportedfields);
            $mform->addElement('static', 'unsupportedfields', get_string('unsupportedfields', 'data'), $unsupportedfieldslist);
        }

        // Export options.
        $mform->addElement('header', 'exportoptionsheader', get_string('exportoptions', 'data'));
        $mform->setExpanded('exportoptionsheader');
        $exportoptions = [];
        if (core_tag_tag::is_enabled('mod_data', 'data_records')) {
            $exportoptions[] = $mform->createElement('checkbox', 'exporttags', get_string('includetags', 'data'), '', $optionattrs);
            $mform->setDefault('exporttags', 1);
        }
        $context = context_module::instance($this->_cm->id);
        if (has_capability('mod/data:exportuserinfo', $context)) {
            $exportoptions[] = $mform->createElement('checkbox', 'exportuser', get_string('includeuserdetails', 'data'), '',
                $optionattrs);
        }
        $exportoptions[] = $mform->createElement('checkbox', 'exporttime', get_string('includetime', 'data'), '', $optionattrs);
        if ($this->_data->approval) {
            $exportoptions[] = $mform->createElement('checkbox', 'exportapproval', get_string('includeapproval', 'data'), '',
                $optionattrs);
        }
        $mform->addGroup($exportoptions, 'exportoptions', get_string('selectexportoptions', 'data'), ['<br>'], false);

        $this->add_action_buttons(true, get_string('exportentries', 'data'));
    }

}


