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
use core\form\persistent;

/**
 * Competency framework form.
 *
 * @package   tool_lp
 * @copyright 2015 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency extends persistent {

    /** @var core_competency\competency persistent class for form */
    protected static $persistentclass = 'core_competency\\competency';

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $PAGE, $OUTPUT;

        $mform = $this->_form;
        $framework = $this->_customdata['competencyframework'];
        $parent = $this->_customdata['parent'];
        $pagecontextid = $this->_customdata['pagecontextid'];
        $competency = $this->get_persistent();

        $mform->addElement('hidden', 'competencyframeworkid');
        $mform->setType('competencyframeworkid', PARAM_INT);
        $mform->setConstant('competencyframeworkid', $framework->get('id'));

        $mform->addElement('header', 'generalhdr', get_string('general'));

        $mform->addElement('static',
                           'frameworkdesc',
                           get_string('competencyframework', 'tool_lp'),
                           s($framework->get('shortname')));

        $mform->addElement('hidden', 'parentid', '', array('id' => 'tool_lp_parentcompetency'));

        $mform->setType('parentid', PARAM_INT);
        $mform->setConstant('parentid', ($parent) ? $parent->get('id') : 0);
        $parentlevel = ($parent) ? $parent->get_level() : 0;
        $parentname = ($parent) ? $parent->get('shortname') : get_string('competencyframeworkroot', 'tool_lp');
        $parentlabel = '';
        if (!empty($competency->get('id'))) {
            $parentlabel = get_string('taxonomy_parent_' . $framework->get_taxonomy($parentlevel), 'tool_lp');
        } else {
            $parentlabel = get_string('parentcompetency', 'tool_lp');
        }
        $editaction = '';
        if (!$competency->get('id')) {
            $icon = $OUTPUT->pix_icon('t/editinline', get_string('parentcompetency_edit', 'tool_lp'));
            $editaction = $OUTPUT->action_link('#', $icon, null, array('id' => 'id_parentcompetencybutton'));
        }

        $mform->addElement('static',
                           'parentdesc',
                           $parentlabel,
                           "<span id='id_parentdesc'>$parentname</span>&nbsp;".$editaction);
        // Set the picker competency when adding new competency.
        if (!$competency->get('id')) {
            // Call the parentcompetency_form init to initialize the competency picker for parent competency.
            $PAGE->requires->js_call_amd('tool_lp/parentcompetency_form', 'init', array('#id_parentcompetencybutton',
                '#tool_lp_parentcompetency',
                '#id_parentdesc',
                $framework->get('id'),
                $pagecontextid));
        }

        // Name.
        $mform->addElement('text', 'shortname', get_string('shortname', 'tool_lp'), 'maxlength="100"');
        $mform->setType('shortname', PARAM_TEXT);
        $mform->addRule('shortname', null, 'required', null, 'client');
        $mform->addRule('shortname', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
        // Description.
        $mform->addElement('editor', 'description',
                           get_string('description', 'tool_lp'), array('rows' => 4));
        $mform->setType('description', PARAM_CLEANHTML);
        // ID number.
        $mform->addElement('text', 'idnumber', get_string('idnumber', 'tool_lp'), 'maxlength="100"');
        $mform->setType('idnumber', PARAM_RAW);
        $mform->addRule('idnumber', null, 'required', null, 'client');
        $mform->addRule('idnumber', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');

        $scales = array(null => get_string('inheritfromframework', 'tool_lp')) + get_scales_menu();
        $scaleid = $mform->addElement('select', 'scaleid', get_string('scale', 'tool_lp'), $scales);
        $mform->setType('scaleid', PARAM_INT);
        $mform->addHelpButton('scaleid', 'scale', 'tool_lp');

        $mform->addElement('hidden', 'scaleconfiguration', '', array('id' => 'tool_lp_scaleconfiguration'));
        $mform->setType('scaleconfiguration', PARAM_RAW);

        $mform->addElement('button', 'scaleconfigbutton', get_string('configurescale', 'tool_lp'));
        $PAGE->requires->js_call_amd('tool_lp/scaleconfig', 'init', array('#id_scaleid',
            '#tool_lp_scaleconfiguration', '#id_scaleconfigbutton'));

        if ($competency && $competency->has_user_competencies()) {
            // The scale is used so we "freeze" the element. Though, the javascript code for the scale
            // configuration requires this field so we only disable it. It is fine as setting the value
            // as a constant will ensure that nobody can change it. And it's validated in the persistent anyway.
            $scaleid->updateAttributes(array('disabled' => 'disabled'));
            $mform->setConstant('scaleid', $competency->get('scaleid'));
        }

        // Disable short forms.
        $mform->setDisableShortforms();
        $this->add_action_buttons(true, get_string('savechanges', 'tool_lp'));
    }

    /**
     * Convert some fields.
     *
     * @param stdClass $data
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
