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

namespace core_ai\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Generate action settings form.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_settings_form extends moodleform {
    #[\Override]
    protected function definition() {
    }

    #[\Override]
    protected function after_definition() {
        parent::after_definition();
        $this->_form->_registerCancelButton('cancel');
    }

    #[\Override]
    public function definition_after_data() {
        // Dispatch a hook for plugins to add their fields.
        $hook = new \core_ai\hook\after_ai_action_settings_form_hook(
            mform: $this->_form,
            plugin: $this->_customdata['providername'],
        );
        \core\di::get(\core\hook\manager::class)->dispatch($hook);
        // Add action buttons.
        $this->add_action_buttons();
    }

    /**
     * Get the default values for the form.
     *
     * @return array
     */
    public function get_defaults(): array {
        $data = $this->_form->exportValues();
        unset(
            $data['sesskey'], // We do not need to return sesskey.
            $data['_qf__'.$this->_formname], // We do not need the submission marker.
            $data['provider'], // We do not need the provider.
            $data['providerid'], // We do not need the provider id.
            $data['action'] // We do not need the action.
        );
        if (empty($data)) {
            return [];
        } else {
            return $data;
        }
    }
}
