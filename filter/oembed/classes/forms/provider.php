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
 * Provider mform.
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2016 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace filter_oembed\forms;

use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

class provider extends moodleform {
    /**
     * Define this form - is called from parent constructor.
     */
    public function definition() {
        $mform = $this->_form;

        // Form configuration.
        $config = (object)[
            'id'           => ['required' => true, 'type' => 'hidden', 'paramtype' => PARAM_INT],
            'providername' => ['required' => true, 'type' => 'text', 'paramtype' => PARAM_TEXT],
            'providerurl'  => ['required' => true, 'type' => 'text', 'paramtype' => PARAM_URL],
            'endpoints'    => ['required' => true, 'type' => 'textarea', 'paramtype' => PARAM_TEXT],
            'enabled'      => ['required' => false, 'type' => 'checkbox', 'paramtype' => PARAM_INT],
            'source'       => ['required' => true, 'type' => 'hidden', 'paramtype' => PARAM_TEXT],
        ];

        // The source type is stored in "_customdata".
        $sourcetype = $this->_customdata;
        // Common attributes to be appleid to all fields.
        $commonattributes = null;
        if ($sourcetype === \filter_oembed\provider\provider::PROVIDER_SOURCE_PLUGIN) {
            $commonattributes = 'disabled="disabled"';
        }

        // Define form according to configuration.
        foreach ($config as $fieldname => $row) {
            $row = (object)$row;
            if ($row->type == 'hidden') {
                $fieldlabel = '';
            } else {
                $fieldlabel = get_string($fieldname, 'filter_oembed');
            }
            $el = $mform->addElement($row->type, $fieldname, $fieldlabel);
            if (!empty($commonattributes)) {
                $el->updateAttributes($commonattributes);
            }
            $mform->setType($fieldname, $row->paramtype);
            if ($row->required) {
                $mform->addRule($fieldname, get_string('requiredfield', 'filter_oembed', $fieldlabel), 'required');
            }
        }

        $mform->addElement('static', 'sourcetext', get_string('source', 'filter_oembed'));

        if ($sourcetype === \filter_oembed\provider\provider::PROVIDER_SOURCE_PLUGIN) {
            // Plugins can't be edited.
            $mform->addElement('cancel');
        } else {
            if ($sourcetype == \filter_oembed\provider\provider::PROVIDER_SOURCE_DOWNLOAD) {
                // Downloads can be saved as new locals.
                $label = get_string('saveasnew', 'filter_oembed');
            } else {
                // Locals can be edited.
                $label = null;
            }
            $this->add_action_buttons(true, $label);
        }
    }
}
