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
 * Form for editing settings navigation instances.
 *
 * @since 2.0
 * @package blocks
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for setting navigation instances.
 *
 * @package blocks
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_settings_navigation_tree_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $yesnooptions = array('yes'=>get_string('yes'), 'no'=>get_string('no'));

        $mform->addElement('select', 'config_enablehoverexpansion', get_string('enablehoverexpansion', $this->block->blockname), $yesnooptions);
        if (empty($this->block->config->enablehoverexpansion) || $this->block->config->enablehoverexpansion=='no') {
            $mform->getElement('config_enablehoverexpansion')->setSelected('no');
        } else {
            $mform->getElement('config_enablehoverexpansion')->setSelected('yes');
        }

        $mform->addElement('select', 'config_enablesidebarpopout', get_string('enablesidebarpopout', $this->block->blockname), $yesnooptions);
        if (empty($this->block->config->enablesidebarpopout) || $this->block->config->enablesidebarpopout=='no') {
            $mform->getElement('config_enablesidebarpopout')->setSelected('no');
        } else {
            $mform->getElement('config_enablesidebarpopout')->setSelected('yes');
        }
    }
}