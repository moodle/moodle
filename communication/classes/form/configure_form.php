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
 * Configure communication for a given instance - the form definition.
 *
 * @package    core_communication
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_communication\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Defines the configure communication form.
 */
class configure_form extends \moodleform {

    /**
     * @var \core_communication\api $communication The communication api object.
     */
    protected $communication;

    /**
     * Defines the form fields.
     */
    public function definition() {
        $mform = $this->_form;
        $instanceid = $this->_customdata['instanceid'];
        $instancetype = $this->_customdata['instancetype'];
        $component = $this->_customdata['component'];
        $instance = $this->_customdata['instance'];

        // Add communication plugins to the form.
        $this->communication = \core_communication\api::load_by_instance(
            $component,
            $instancetype,
            $instanceid
        );
        $this->communication->form_definition($mform);
        $this->communication->set_data($instance);

        // Form buttons.
        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'saveandreturn', get_string('savechanges'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');

        // Hidden elements.
        $mform->addElement('hidden', 'instanceid', $instanceid);
        $mform->setType('instanceid', PARAM_INT);
        $mform->addElement('hidden', 'instancetype', $instancetype);
        $mform->setType('instancetype', PARAM_TEXT);
        $mform->addElement('hidden', 'component', $component);
        $mform->setType('component', PARAM_TEXT);

        // Finally set the current form data.
        $this->set_data($instance);
    }

    /**
     * Fill in the communication page data depending on provider selected.
     */
    public function definition_after_data() {
        $mform = $this->_form;
        // Add communication plugins to the form with respect to the provider.
        $this->communication->form_definition_for_provider($mform);
    }
}
