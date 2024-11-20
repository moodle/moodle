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

namespace core_sms\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * SMS gateway instance form.
 *
 * @package    core_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sms_gateway_form extends moodleform {

    protected function definition() {
        global $PAGE;
        $PAGE->requires->js_call_amd('core_sms/smsgatewaychooser', 'init');

        $mform = $this->_form;
        $gatewayconfigs = $this->_customdata['gatewayconfigs'] ?? [];
        $returnurl = $this->_customdata['returnurl'] ?? null;

        $smsplugins = [];
        $smsgatewayplugins = \core\plugininfo\smsgateway::get_enabled_plugins();
        foreach ($smsgatewayplugins as $pluginname => $notusing) {
            $plugin = 'smsgateway_' . $pluginname;
            $smsplugins[$plugin] = get_string('pluginname', $plugin);
        }

        $mform->addElement(
            'select',
            'smsgateway',
            get_string('select_sms_gateways', 'sms'),
            $smsplugins,
            ['data-smsgatewaychooser-field' => 'selector'],
        );
        $mform->setDefault('smsgateway', 'smsgateway_aws');
        if (isset($gatewayconfigs->id)) {
            $mform->hardFreeze('smsgateway');
        }

        $mform->addElement(
            'text',
            'name',
            get_string('gateway_name', 'sms'),
            'maxlength="255" size="20"',
        );
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255);

        $mform->addElement(
            'text',
            'countrycode',
            get_string('countrycode', 'sms'),
            'maxlength="255" size="20"',
        );
        $mform->setType('countrycode', PARAM_TEXT);
        $mform->setDefault(
            elementName: 'countrycode',
            defaultValue: 0,
        );

        $mform->addElement('static', 'information', ' ', get_string('countrycode_help', 'core_sms'));

        $mform->registerNoSubmitButton('updatesmsgateway');
        $mform->addElement(
            'submit',
            'updatesmsgateway',
            'update sms gateway',
            ['data-smsgatewaychooser-field' => 'updateButton', 'class' => 'd-none']
        );

        // Dispatch a hook for plugins to add their fields.
        $hook = new \core_sms\hook\after_sms_gateway_form_hook(
            mform: $mform,
            plugin: $gatewayconfigs->smsgateway ?? 'smsgateway_aws',
        );
        \core\di::get(\core\hook\manager::class)->dispatch($hook);

        // Form buttons.
        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'saveandreturn', get_string('savechanges'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');

        if ($returnurl) {
            $mform->addElement('hidden', 'returnurl', $returnurl);
            $mform->setType('returnurl', PARAM_LOCALURL);
        }

        if (isset($gatewayconfigs->id)) {
            $mform->addElement('hidden', 'id', $gatewayconfigs->id);
            $mform->setType('id', PARAM_INT);
        }

        $this->set_data($gatewayconfigs);
    }
}
