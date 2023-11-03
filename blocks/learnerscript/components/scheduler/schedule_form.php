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
 * @author: Naveen Kumar
 * @date: 2014
 */
if (!defined('MOODLE_INTERNAL')) {
	die(get_string('nodirectaccess', 'block_learnerscript')); ///  It must be included from a Moodle page
}

require_once $CFG->dirroot . '/lib/formslib.php';
use block_learnerscript\learnerscript;
/**
 * Formslib template for the new report form
 */
class scheduled_reports_form extends moodleform {

	function definition() {
		global $DB, $PAGE, $CFG;
		$mform = &$this->_form;

		$reportid = $this->_customdata['id'];
		$scheduledreportid = $this->_customdata['scheduleid'];
		foreach ($this->_customdata['roles_list'] as $role) {
			$roles_list[$role['key']] = $role['value'];
		 }
		$schusers = $this->_customdata['schusers'];
		$schusersids = $this->_customdata['schusersids'];
		$exportoptions = $this->_customdata['exportoptions'];
		$schedule_list = $this->_customdata['schedule_list'];
		$frequencyselect = $this->_customdata['frequencyselect'];
		if (isset($this->_customdata['AjaxForm']) && $this->_customdata['AjaxForm'] && isset($this->_customdata['instance'])) {
			$instance = $this->_customdata['instance'];
			$ajaxform = 'AjaxForm';
			$mform->_attributes['id'] = "schform$instance";
			$requireclass = "schformreq$instance";
			$reportinstance = $instance;
			$mform->_attributes['data-1instanceid'] = $instance;
		} else {
			$ajaxform = '';
			$requireclass = 'schformelements';
			$reportinstance = $reportid;
			$instance = $reportid;
		}

		$mform->_attributes['class'] = "schform mform $ajaxform schforms$reportinstance";

		$mform->_attributes['data-reportid'] = $reportid;
		$mform->_attributes['data-scheduleid'] = $scheduledreportid;

		// if ($scheduledreportid) {
		// 	$cobaltreports_schedule = $DB->get_record('block_ls_schedule', array('id' => $scheduledreportid));
		// }

		$reportname = $DB->get_field('block_learnerscript', 'name', array('id' => $reportid));
		$exporttofilesystem = true;
		if (get_config('block_learnerscript', 'exportfilesystem') == 1) {
			$exporttofilesystem = true;
		}
		if ($scheduledreportid > 0) {
			$pagename = 'editscheduledreport';
		} else {
			$pagename = 'addschedulereport';
		}

		// $mform->addElement('header', 'general', get_string($pagename, 'block_learnerscript'));

		$mform->addElement('hidden', 'reportid', $reportid);
		$mform->setType('reportid', PARAM_INT);

		// $mform->addElement('hidden', 'id', $reportid);
		// $mform->setType('id', PARAM_INT);

		$mform->addElement('hidden', 'scheduleid', $scheduledreportid, array('id' => 'scheduleid'));
		$mform->setType('scheduleid', PARAM_INT);

		$mform->addElement('select', 'role', get_string('role', 'block_learnerscript'), $roles_list, array('data-select2' => true, 'id' => 'id_role' . $reportinstance, 'data-id' => $reportid, 'data-class' => $requireclass, 'data-element' => 'role',
			'onchange' => '(function(e){ require("block_learnerscript/schedule").rolewiseusers({reportid: ' . $reportid . ', reportinstance : ' . $reportinstance . '}) })(event);',
			'data-placeholder' => get_string('selectroles', 'block_learnerscript')));
		// $mform->getElement('role')->setMultiple('true');
		$mform->setType('role', PARAM_RAW);
		$mform->addRule('role', get_string('PleaseSelectRole', 'block_learnerscript'), 'required', null, 'client');

		$mform->addElement('select', 'users_data', get_string('fullname'), $schusers, array('data-select2-ajax' => true, 'data-ajax--url' => $CFG->wwwroot . '/blocks/learnerscript/ajax.php', 'id' => 'id_users_data' . $reportinstance, 'multiple' => true, 'data-reportid' => $reportid, 'data-2instanceid' => $instance,
			'data-id' => $reportid, 'data-multiple' => true, 'class' => 'schusers_data', 'onchange' => '(function(e){ require("block_learnerscript/schedule").addschusers({reportid: ' . $reportid . ', reportinstance : ' . $reportinstance . '}) })(event);', 'data-class' => $requireclass,
			'data-element' => 'users_data', 'data-placeholder' => get_string('selectusers', 'block_learnerscript')));
		$mform->getElement('users_data')->setMultiple('true');
		$mform->addRule('users_data', get_string('PleaseSelectUser', 'block_learnerscript'), 'required', null, 'client');

		$mform->addElement('hidden', 'schuserslist', $schusersids, array('class' => 'schuserslist', 'id' => 'schuserslist' . $reportinstance));
		$mform->setType('schuserslist', PARAM_RAW);
		// if (empty($ajaxform)) {
		// 	$mform->addElement('static', 'report', get_string('report', 'block_learnerscript'), $reportname);
		// }
		$mform->addElement('select', 'exportformat', get_string('export', 'block_learnerscript'), $exportoptions);

		if ($exporttofilesystem) {
			$exporttofilesystemarray = array();
			$exporttofilesystemarray[] = $mform->createElement('radio', 'exporttofilesystem', '', get_string('exporttoemail', 'block_learnerscript'), REPORT_EMAIL);
			$exporttofilesystemarray[] = $mform->createElement('radio', 'exporttofilesystem', '', get_string('exporttosave', 'block_learnerscript'), REPORT_EXPORT);
			$exporttofilesystemarray[] = $mform->createElement('radio', 'exporttofilesystem', '', get_string('exporttoemailandsave', 'block_learnerscript'), REPORT_EXPORT_AND_EMAIL);
			$mform->addGroup($exporttofilesystemarray, 'exporttofilesystem', get_string('exportfilesystemoptions', 'block_learnerscript'), array('<br/>'), false);
			$mform->setDefault('exporttofilesystem', REPORT_EXPORT_AND_EMAIL);
			$mform->setType('emailsaveorboth', PARAM_INT);
		} else {
			$mform->addElement('hidden', 'emailsaveorboth', REPORT_EXPORT_AND_EMAIL);
			$mform->setType('emailsaveorboth', PARAM_INT);
		}

		$newscheduledata = array();
		$newscheduledata[] = &$mform->createElement('select', 'frequency', get_string('schedule', 'block_learnerscript'), $frequencyselect, array('id' => 'id_frequency' . $reportinstance, 'onchange' => '(function(e){
			require("block_learnerscript/schedule").frequency_schedule({reportid: ' . $reportid . ', reportinstance: ' . $reportinstance . '}) })(event);', 'data-id' => $reportid, 'data-class' => $requireclass, 'data-element' => 'frequency'));

		$newscheduledata[] = &$mform->createElement('select', 'schedule', get_string('updatefrequency', 'block_learnerscript'), $schedule_list, array('id' => 'id_updatefrequency' . $reportinstance, 'data-class' => $requireclass,
			'data-element' => 'schedule'));
		$mform->addGroup($newscheduledata, 'dependency', get_string('dependency', 'block_learnerscript'), array(' '), false);

		$schfrequencyrules = array();
		$schfrequencyrules['frequency'][] = array(get_string('err_required', 'form'), 'required', null, 'client');
		$schfrequencyrules['schedule'][] = array(get_string('err_required', 'form'), 'required', null, 'client');
		$mform->addGroupRule('dependency', $schfrequencyrules);
		$btnstring = get_string('schedule', 'block_learnerscript');
		$btnstring1 = get_string('cancel');
		$this->add_action_buttons($btnstring1, $btnstring);
	}

	function validation($data, $files) {
		$errors = parent::validation($data, $files);
		return $errors;
	}

}
