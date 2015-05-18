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
 * This file contains the form add/update a competency framework.
 *
 * @package   tool_lp
 * @copyright 2015 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp\form;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

use moodleform;
use tool_lp\api;

require_once($CFG->libdir.'/formslib.php');

/**
 * Competency framework form.
 *
 * @package   tool_lp
 * @copyright 2015 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_framework extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE;

        $mform = $this->_form;
        $id = $this->_customdata;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', 0);

        $mform->addElement('text', 'shortname',
                           get_string('shortname', 'tool_lp'));
        $mform->setType('shortname', PARAM_TEXT);
        $mform->addRule('shortname', null, 'required', null, 'client');
        $mform->addElement('editor', 'description',
                           get_string('description', 'tool_lp'), array('rows'=>4));
        $mform->setType('description', PARAM_TEXT);
        $mform->addElement('text', 'idnumber',
                           get_string('idnumber', 'tool_lp'));
        $mform->setType('idnumber', PARAM_TEXT);

        $scales = get_scales_menu();
        $mform->addElement('select', 'scaleid', get_string('scale', 'tool_lp'), $scales);
        $mform->setType('scaleid', PARAM_INT);
        $mform->addHelpButton('scaleid', 'scale', 'tool_lp');

        $mform->addElement('button', 'scaleconfigbutton', get_string('configurescale', 'tool_lp'));
        // Add js.
        $PAGE->requires->js_call_amd('tool_lp/scaleconfig', 'init');
        $mform->addElement('hidden', 'scaleconfiguration', '', array('id' => 'tool_lp_scaleconfiguration'));
        $mform->setType('scaleconfiguration', PARAM_RAW);

        $mform->addElement('selectyesno', 'visible',
                           get_string('visible', 'tool_lp'));
        $mform->setDefault('visible', true);
        $mform->addHelpButton('visible', 'visible', 'tool_lp');

        $this->add_action_buttons(true, get_string('savechanges', 'tool_lp'));

        if (!empty($id)) {
            if (!$this->is_submitted()) {
                $framework = api::read_framework($id);
                $record = $framework->to_record();
                // Massage for editor API.
                $record->description = array('text'=>$record->description, 'format'=>$record->descriptionformat);
                $this->set_data($record);
            }
        }

    }
}
