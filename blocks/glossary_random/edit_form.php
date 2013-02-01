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
 * Form for editing HTML block instances.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing Random glossary entry block instances.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_glossary_random_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $DB;

        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('title', 'block_glossary_random'));
        $mform->setDefault('config_title', get_string('pluginname','block_glossary_random'));
        $mform->setType('config_title', PARAM_TEXT);

        // Select glossaries to put in dropdown box ...
        $glossaries = $DB->get_records_select_menu('glossary', 'course = ? OR globalglossary = ?', array($this->block->course->id, 1), 'name', 'id,name');
        foreach($glossaries as $key => $value) {
            $glossaries[$key] = strip_tags(format_string($value, true));
        }
        $mform->addElement('select', 'config_glossary', get_string('select_glossary', 'block_glossary_random'), $glossaries);

        $mform->addElement('text', 'config_refresh', get_string('refresh', 'block_glossary_random'), array('size' => 5));
        $mform->setDefault('config_refresh', 0);
        $mform->setType('config_refresh', PARAM_INT);

        // and select quotetypes to put in dropdown box
        $types = array(
            0 => get_string('random','block_glossary_random'),
            1 => get_string('lastmodified','block_glossary_random'),
            2 => get_string('nextone','block_glossary_random'),
            3 => get_string('nextalpha','block_glossary_random')
        );
        $mform->addElement('select', 'config_type', get_string('type', 'block_glossary_random'), $types);

        $mform->addElement('selectyesno', 'config_showconcept', get_string('showconcept', 'block_glossary_random'));
        $mform->setDefault('config_showconcept', 1);

        $mform->addElement('static', 'footerdescription', '', get_string('whichfooter', 'block_glossary_random'));

        $mform->addElement('text', 'config_addentry', get_string('askaddentry', 'block_glossary_random'));
        $mform->setDefault('config_addentry', get_string('addentry', 'block_glossary_random'));
        $mform->setType('config_addentry', PARAM_NOTAGS);

        $mform->addElement('text', 'config_viewglossary', get_string('askviewglossary', 'block_glossary_random'));
        $mform->setDefault('config_viewglossary', get_string('viewglossary', 'block_glossary_random'));
        $mform->setType('config_viewglossary', PARAM_NOTAGS);

        $mform->addElement('text', 'config_invisible', get_string('askinvisible', 'block_glossary_random'));
        $mform->setDefault('config_invisible', get_string('invisible', 'block_glossary_random'));
        $mform->setType('config_invisible', PARAM_NOTAGS);
    }
}
