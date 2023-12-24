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

use core\context;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Defines the configure communication form.
 */
class configure_form extends \moodleform {
    /**
     * @var \core_communication\api $communication The communication api object.
     */
    protected $communication;

    /**
     * Class constructor
     *
     * @param context $context Context object
     * @param int|null $instanceid Instance ID
     * @param string|null $instancetype Instance type
     * @param string|null $component Component name
     * @param string|null $selectedcommunication Selected communication service (provider)
     * @param stdClass|null $instancedata Instance data
     */
    public function __construct(
        context $context,
        ?int $instanceid = null,
        ?string $instancetype = null,
        ?string $component = null,
        ?string $selectedcommunication = null,
        ?stdClass $instancedata = null,
    ) {
        parent::__construct(
            null,
            [
                'context' => $context,
                'instanceid' => $instanceid,
                'instancetype' => $instancetype,
                'component' => $component,
                'selectedcommunication' => $selectedcommunication,
                'instancedata' => $instancedata,
            ],
        );
    }

    /**
     * Defines the form fields.
     */
    public function definition() {
        $mform = $this->_form;
        $context = $this->_customdata['context'];
        $instanceid = $this->_customdata['instanceid'];
        $instancetype = $this->_customdata['instancetype'];
        $component = $this->_customdata['component'];
        $instancedata = $this->_customdata['instancedata'];

        // Add communication plugins to the form.
        $this->communication = \core_communication\api::load_by_instance(
            context: $context,
            component: $component,
            instancetype: $instancetype,
            instanceid: $instanceid,
            provider: $this->_customdata['selectedcommunication'],
        );
        $this->communication->form_definition($mform);
        $this->communication->set_data($instancedata);

        $this->set_form_definition_for_provider();

        // Form buttons.
        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'saveandreturn', get_string('savechanges'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');

        // Hidden elements.
        $mform->addElement('hidden', 'contextid', $context->id);
        $mform->setType('contextid', PARAM_INT);
        $mform->addElement('hidden', 'instanceid', $instanceid);
        $mform->setType('instanceid', PARAM_INT);
        $mform->addElement('hidden', 'instancetype', $instancetype);
        $mform->setType('instancetype', PARAM_TEXT);
        $mform->addElement('hidden', 'component', $component);
        $mform->setType('component', PARAM_TEXT);

        // Finally set the current form data.
        $this->set_data($instancedata);
    }

    /**
     * Defines the requested/current provider
     *
     * Get the selected communication service (provider),
     * and then use it to show the provider form fields.
     */
    private function set_form_definition_for_provider(): void {
        $instancedata = $this->_customdata['instancedata'];
        if ($selectedcommunication = $this->_customdata['selectedcommunication']) {
            // First is to check whether the selected communication was selected from the form.
            $provider = $selectedcommunication;
        } else if (isset($instancedata->selectedcommunication)) {
            // If the form is not yet submitted, get the value from the DB.
            $provider = $instancedata->selectedcommunication;
        } else {
            // Otherwise, set to PROVIDER_NONE.
            $provider = \core_communication\processor::PROVIDER_NONE;
        }

        $this->communication->form_definition_for_provider($this->_form, $provider);
    }
}
