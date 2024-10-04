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

use core_cache\store;
use moodleform;

/**
 * Form to set the mappings for a mode.
 *
 * @package    core_cache
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_mode_mappings_form extends moodleform {
    /**
     * The definition of the form
     */
    protected function definition() {
        $form = $this->_form;
        $stores = $this->_customdata;

        $options = [
            store::MODE_APPLICATION => [],
            store::MODE_SESSION => [],
            store::MODE_REQUEST => [],
        ];
        foreach ($stores as $storename => $store) {
            foreach ($store['modes'] as $mode => $enabled) {
                if ($enabled && ($mode !== store::MODE_SESSION || $store['supports']['searchable'])) {
                    if (empty($store['default'])) {
                        $options[$mode][$storename] = $store['name'];
                    } else {
                        $options[$mode][$storename] = get_string('store_' . $store['name'], 'cache');
                    }
                }
            }
        }

        $form->addElement('hidden', 'action', 'editmodemappings');
        $form->setType('action', PARAM_ALPHA);
        foreach ($options as $mode => $optionset) {
            $form->addElement('select', 'mode_' . $mode, get_string('mode_' . $mode, 'cache'), $optionset);
        }

        $this->add_action_buttons();
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(cache_mode_mappings_form::class, \cache_mode_mappings_form::class);
