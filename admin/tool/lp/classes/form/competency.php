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

use stdClass;

/**
 * Competency framework form.
 *
 * @package   tool_lp
 * @copyright 2015 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency extends persistent {

    protected static $persistentclass = 'tool_lp\\competency';

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE;

        $mform = $this->_form;
        $framework = $this->_customdata['competencyframework'];
        $parent = $this->_customdata['parent'];
        $competency = $this->get_persistent();

        $mform->addElement('hidden', 'competencyframeworkid');
        $mform->setType('competencyframeworkid', PARAM_INT);
        $mform->setConstant('competencyframeworkid', $framework->get_id());

        $mform->addElement('header', 'generalhdr', get_string('general'));

        $mform->addElement('static',
                           'frameworkdesc',
                           get_string('competencyframework', 'tool_lp'),
                           s($framework->get_shortname()));
        if ($parent) {
            $mform->addElement('static',
                               'parentdesc',
                               get_string('taxonomy_parent_' . $framework->get_taxonomy($parent->get_level()), 'tool_lp'),
                               s($parent->get_shortname()));
        }

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

        $frameworkscale = $framework->get_scale();
        $scales = array(null => get_string('inheritfromframework', 'tool_lp')) + get_scales_menu();

        $scaleid = $mform->addElement('select', 'scaleid', get_string('scale', 'tool_lp'), $scales);
        $mform->setType('scaleid', PARAM_INT);
        $mform->addHelpButton('scaleid', 'scale', 'tool_lp');

        $mform->addElement('hidden', 'scaleconfiguration', '', array('id' => 'tool_lp_scaleconfiguration'));
        $mform->setType('scaleconfiguration', PARAM_RAW);

        $scaleconfig = $mform->addElement('button', 'scaleconfigbutton', get_string('configurescale', 'tool_lp'));
        $PAGE->requires->js_call_amd('tool_lp/scaleconfig', 'init');

        if ($competency && $competency->has_user_competencies()) {
            // The scale is used so we "freeze" the element. Though, the javascript code for the scale
            // configuration requires this field so we only disable it. It is fine as setting the value
            // as a constant will ensure that nobody can change it. And it's validated in the persistent anyway.
            $scaleid->updateAttributes(array('disabled' => 'disabled'));
            $mform->setConstant('scaleid', $competency->get_scaleid());
        }

        $this->add_action_buttons(true, get_string('savechanges', 'tool_lp'));
    }

    /**
     * Convert some fields.
     *
     * @return object
     */
    protected static function convert_fields(stdClass $data) {
        $data = parent::convert_fields($data);
        if (empty($data->scaleid)) {
            $data->scaleid = null;
            $data->scaleconfiguration = null;
        }
        return $data;
    }

    /**
     * Extra validation.
     *
     * @param  stdClass $data Data to validate.
     * @param  array $files Array of files.
     * @param  array $errors Currently reported errors.
     * @return array of additional errors, or overridden errors.
     */
    protected function extra_validation($data, $files, array &$errors) {
        $newerrors = array();
        // Move the error from scaleconfiguration to the form element scale ID.
        if (isset($errors['scaleconfiguration']) && !isset($errors['scaleid'])) {
            $newerrors['scaleid'] = $errors['scaleconfiguration'];
            unset($errors['scaleconfiguration']);
        }
        return $newerrors;
    }

}
