<?php

if (!defined('MOODLE_INTERNAL')) {
	die(get_string('nodirectaccess', 'block_learnerscript')); ///  It must be included from a Moodle page
}

require_once $CFG->libdir . '/formslib.php';
use block_learnerscript\local\ls;

class treemap_form extends moodleform {

	function definition() {
		global $DB, $USER, $CFG;
		$mform = &$this->_form;
		$options = array();
		$report = $this->_customdata['report'];
		$components = (new ls)->cr_unserialize($this->_customdata['report']->components);

		if (!is_array($components) || empty($components['columns']['elements'])) {
			print_error('nocolumns');
		}

		$columns = $components['columns']['elements'];
		foreach ($columns as $c) {
			$options[$c['formdata']->column] = $c['formdata']->columname;
		}

		// $mform->addElement('header', 'crformheader', get_string('treemap', 'block_learnerscript'), '');
		$mform->addElement('text', 'chartname', get_string('chartname', 'block_learnerscript'));
		$mform->addRule('chartname', get_string('chartnamerequired', 'block_learnerscript'), 'required', null, 'client');
		$mform->setType('chartname', PARAM_RAW);
		$mform->addElement('select', 'id', get_string('treemapareaid', 'block_learnerscript'), $options);
		$mform->addElement('select', 'areaname', get_string('treemapareaname', 'block_learnerscript'), $options);
		$mform->addElement('select', 'areavalue', get_string('treemapareavalue', 'block_learnerscript'), $options);
		$mform->addElement('text', 'serieslabel', get_string('serieslabel', 'block_learnerscript'));
		$mform->setType('serieslabel', PARAM_RAW);
		$mform->addElement('advcheckbox', 'showlegend', get_string('showlegend', 'block_learnerscript'), '', null, array(0, 1));
		$mform->addElement('advcheckbox', 'datalabels', get_string('datalabels', 'block_learnerscript'), '', null, array(0, 1));
		$this->add_action_buttons(true, get_string('add'));
	}

}
