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

namespace aiprovider_deepseek\form;

use aiprovider_deepseek\aimodel\deepseek_base;


/**
 * Generate text action provider settings form.
 *
 * @package    aiprovider_deepseek
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_generate_text_form extends action_form {
    #[\Override]
    protected function definition(): void {
        parent::definition();
        $mform = $this->_form;

        $this->add_model_fields(deepseek_base::MODEL_TYPE_TEXT);

        // API endpoint.
        $mform->addElement(
            'text',
            'endpoint',
            get_string("action:{$this->actionname}:endpoint", 'aiprovider_deepseek'),
            'maxlength="255" size="30"',
        );
        $mform->setType('endpoint', PARAM_URL);
        $mform->addRule('endpoint', null, 'required', null, 'client');
        $mform->setDefault('endpoint', $actionconfig['endpoint'] ?? 'https://api.deepseek.com/chat/completions');

        // System Instructions.
        $mform->addElement(
            'textarea',
            'systeminstruction',
            get_string("action:{$this->actionname}:systeminstruction", 'aiprovider_deepseek'),
            'wrap="virtual" rows="5" cols="20"',
        );
        $mform->setType('systeminstruction', PARAM_TEXT);
        $mform->setDefault('systeminstruction', $actionconfig['systeminstruction'] ?? $this->action::get_system_instruction());
        $mform->addHelpButton('systeminstruction', "action:{$this->actionname}:systeminstruction", 'aiprovider_deepseek');

        if ($this->returnurl) {
            $mform->addElement('hidden', 'returnurl', $this->returnurl);
            $mform->setType('returnurl', PARAM_LOCALURL);
        }

        // Add the action class as a hidden field.
        $mform->addElement('hidden', 'action', $this->action);
        $mform->setType('action', PARAM_TEXT);

        // Add the provider class as a hidden field.
        $mform->addElement('hidden', 'provider', $this->providername);
        $mform->setType('provider', PARAM_TEXT);

        // Add the provider id as a hidden field.
        $mform->addElement('hidden', 'providerid', $this->providerid);
        $mform->setType('providerid', PARAM_INT);

        $this->set_data($this->actionconfig);
    }
}
