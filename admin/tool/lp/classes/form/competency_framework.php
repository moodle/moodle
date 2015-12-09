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

defined('MOODLE_INTERNAL') || die();

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
        $id = $this->_customdata['id'];
        $context = $this->_customdata['context'];
        $framework = null;

        if ($id) {
            $framework = api::read_framework($id);;
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', 0);

        $mform->addElement('header', 'generalhdr', get_string('general'));

        $mform->addElement('text', 'shortname',
                           get_string('shortname', 'tool_lp'));
        $mform->setType('shortname', PARAM_TEXT);
        $mform->addRule('shortname', null, 'required', null, 'client');
        $mform->addElement('editor', 'description',
                           get_string('description', 'tool_lp'), array('rows' => 4));
        $mform->setType('description', PARAM_TEXT);
        $mform->addElement('text', 'idnumber',
                           get_string('idnumber', 'tool_lp'));
        $mform->setType('idnumber', PARAM_TEXT);
        $mform->addRule('idnumber', null, 'required', null, 'client');

        $scales = get_scales_menu();
        $scaleid = $mform->addElement('select', 'scaleid', get_string('scale', 'tool_lp'), $scales);
        $mform->setType('scaleid', PARAM_INT);
        $mform->addHelpButton('scaleid', 'scale', 'tool_lp');
        if ($framework && $framework->has_user_competencies()) {
            // The scale is used so we "freeze" the element. Though, the javascript code for the scale
            // configuration requires this field so we only disable it. It is fine as setting the value
            // as a constant will ensure that nobody can change it. And it's validated in the persistent anyway.
            $scaleid->updateAttributes(array('disabled' => 'disabled'));
            $mform->setConstant('scaleid', $framework->get_scaleid());
        }

        $mform->addElement('button', 'scaleconfigbutton', get_string('configurescale', 'tool_lp'));
        // Add js.
        $PAGE->requires->js_call_amd('tool_lp/scaleconfig', 'init');
        $mform->addElement('hidden', 'scaleconfiguration', '', array('id' => 'tool_lp_scaleconfiguration'));
        $mform->setType('scaleconfiguration', PARAM_RAW);

        $mform->addElement('selectyesno', 'visible',
                           get_string('visible', 'tool_lp'));
        $mform->setDefault('visible', true);
        $mform->addHelpButton('visible', 'visible', 'tool_lp');

        $mform->addElement('static', 'context', get_string('context', 'core_role'));
        $mform->setDefault('context', $context->get_context_name());

        $mform->addElement('header', 'taxonomyhdr', get_string('taxonomies', 'tool_lp'));
        $taxonomies = \tool_lp\competency_framework::get_taxonomies_list();
        $taxdefaults = array();
        for ($i = 1; $i <= \tool_lp\competency_framework::get_taxonomies_max_level(); $i++) {
            $mform->addElement('select', "taxonomies[$i]", get_string('levela', 'tool_lp', $i), $taxonomies);
            $taxdefaults[$i] = \tool_lp\competency_framework::TAXONOMY_COMPETENCY;
        }
        // Not using taxonomies[n] here or it would takes precedence over set_data(array('taxonomies' => ...)).
        $mform->setDefault('taxonomies', $taxdefaults);

        $this->add_action_buttons(true, get_string('savechanges', 'tool_lp'));

        if ($framework && !$this->is_submitted()) {
            $record = $framework->to_record();
            // Massage for editor API.
            $record->description = array('text' => $record->description, 'format' => $record->descriptionformat);
            // New hair cut for taxonomies.
            $record->taxonomies = $framework->get_taxonomies();
            $this->set_data($record);
        }

    }

    /**
     * Get form data.
     * Conveniently removes non-desired properties.
     * @return object
     */
    public function get_data() {
        $data = parent::get_data();
        if (is_object($data)) {
            unset($data->submitbutton);
        }
        return $data;
    }

    /**
     * Form validation.
     * @param  array $data
     * @param  array $files
     * @return array
     */
    public function validation($data, $files) {
        $context = $this->_customdata['context'];

        $data = $this->get_submitted_data();
        unset($data->submitbutton);
        $data->descriptionformat = $data->description['format'];
        $data->description = $data->description['text'];
        $data->contextid = $context->id;
        $data->taxonomies = implode(',', $data->taxonomies);

        $framework = new \tool_lp\competency_framework(0, $data);
        $errors = $framework->get_errors();
        if (isset($errors['scaleconfiguration']) && !isset($errors['scaleid'])) {
            $errors['scaleid'] = $errors['scaleconfiguration'];
            unset($errors['scaleconfiguration']);
        }

        return $errors;
    }

}

