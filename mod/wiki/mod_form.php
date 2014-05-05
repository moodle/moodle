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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file defines de main wiki configuration form
 *
 * @package mod_wiki
 * @copyright 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Jordi Piguillem
 * @author Marc Alier
 * @author David Jimenez
 * @author Josep Arus
 * @author Kenneth Riba
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once('moodleform_mod.php');
require_once($CFG->dirroot . '/mod/wiki/locallib.php');
require_once($CFG->dirroot . '/lib/datalib.php');

class mod_wiki_mod_form extends moodleform_mod {

    protected function definition() {
        $mform = $this->_form;
        $required = get_string('required');

        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('wikiname', 'wiki'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', $required, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        // Adding the optional "intro" and "introformat" pair of fields
        $this->add_intro_editor(true, get_string('wikiintro', 'wiki'));

        $wikimodeoptions = array ('collaborative' => get_string('wikimodecollaborative', 'wiki'), 'individual' => get_string('wikimodeindividual', 'wiki'));
        // Don't allow changes to the wiki type once it is set.
        $wikitype_attr = array();
        if (!empty($this->_instance)) {
            $wikitype_attr['disabled'] = 'disabled';
        }
        $mform->addElement('select', 'wikimode', get_string('wikimode', 'wiki'), $wikimodeoptions, $wikitype_attr);
        $mform->addHelpButton('wikimode', 'wikimode', 'wiki');

        $attr = array('size' => '20');
        if (!empty($this->_instance)) {
            $attr['disabled'] = 'disabled';
        }
        $mform->addElement('text', 'firstpagetitle', get_string('firstpagetitle', 'wiki'), $attr);
        $mform->addHelpButton('firstpagetitle', 'firstpagetitle', 'wiki');
        $mform->setType('firstpagetitle', PARAM_TEXT);
        if (empty($this->_instance)) {
            $mform->addRule('firstpagetitle', $required, 'required', null, 'client');
        }

        // Format.
        $mform->addElement('header', 'wikifieldset', get_string('format'));

        $formats = wiki_get_formats();
        $editoroptions = array();
        foreach ($formats as $format) {
            $editoroptions[$format] = get_string($format, 'wiki');
        }
        $mform->addElement('select', 'defaultformat', get_string('defaultformat', 'wiki'), $editoroptions);
        $mform->addHelpButton('defaultformat', 'defaultformat', 'wiki');

        $mform->addElement('checkbox', 'forceformat', get_string('forceformat', 'wiki'));
        $mform->addHelpButton('forceformat', 'forceformat', 'wiki');

        //-------------------------------------------------------------------------------
        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // Add standard buttons, common to all modules.
        $this->add_action_buttons();

    }
}
