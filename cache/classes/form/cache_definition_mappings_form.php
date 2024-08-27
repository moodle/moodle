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

namespace core_cache\form;

use core_cache\administration_helper;
use core_cache\store;
use html_writer;
use moodleform;

/**
 * Form to set definition mappings
 *
 * @package    core_cache
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_definition_mappings_form extends moodleform {
    /**
     * The definition of the form
     */
    final protected function definition() {
        global $OUTPUT;

        $definition = $this->_customdata['definition'];
        $form = $this->_form;

        [$component, $area] = explode('/', $definition, 2);
        [$currentstores, $storeoptions, $defaults] =
                administration_helper::get_definition_store_options($component, $area);

        $storedata = administration_helper::get_definition_summaries();
        if ($storedata[$definition]['mode'] != store::MODE_REQUEST) {
            if (isset($storedata[$definition]['canuselocalstore']) && $storedata[$definition]['canuselocalstore']) {
                $form->addElement('html', $OUTPUT->notification(get_string('localstorenotification', 'cache'), 'notifymessage'));
            } else {
                $form->addElement('html', $OUTPUT->notification(get_string('sharedstorenotification', 'cache'), 'notifymessage'));
            }
        }
        $form->addElement('hidden', 'definition', $definition);
        $form->setType('definition', PARAM_SAFEPATH);
        $form->addElement('hidden', 'action', 'editdefinitionmapping');
        $form->setType('action', PARAM_ALPHA);

        $requiredoptions = max(3, count($currentstores) + 1);
        $requiredoptions = min($requiredoptions, count($storeoptions));

        $options = ['' => get_string('none')];
        foreach ($storeoptions as $option => $def) {
            $options[$option] = $option;
            if ($def['default']) {
                $options[$option] .= ' ' . get_string('mappingdefault', 'cache');
            }
        }

        for ($i = 0; $i < $requiredoptions; $i++) {
            $title = '...';
            if ($i === 0) {
                $title = get_string('mappingprimary', 'cache');
            } else if ($i === $requiredoptions - 1) {
                $title = get_string('mappingfinal', 'cache');
            }
            $form->addElement('select', 'mappings[' . $i . ']', $title, $options);
        }
        $i = 0;
        foreach ($currentstores as $store => $def) {
            $form->setDefault('mappings[' . $i . ']', $store);
            $i++;
        }

        if (!empty($defaults)) {
            $form->addElement(
                'static',
                'defaults',
                get_string('defaultmappings', 'cache'),
                html_writer::tag('strong', join(', ', $defaults))
            );
            $form->addHelpButton('defaults', 'defaultmappings', 'cache');
        }

        $this->add_action_buttons();
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(cache_definition_mappings_form::class, \cache_definition_mappings_form::class);
