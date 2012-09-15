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
 * @package    core_tag
 * @category   tag
 * @copyright  2007 Luiz Cruz <luiz.laydner@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Defines the form for editing tags
 *
 * @package    core_tag
 * @category   tag
 * @copyright  2007 Luiz Cruz <luiz.laydner@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tag_edit_form extends moodleform {

    /**
     * Overrides the abstract moodleform::definition method for defining what the form that is to be
     * presented to the user.
     */
    function definition () {

        $mform =& $this->_form;

        $mform->addElement('header', 'tag', get_string('description','tag'));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $systemcontext   = context_system::instance();

        if (has_capability('moodle/tag:manage', $systemcontext)) {
            $mform->addElement('text', 'rawname', get_string('name', 'tag'),
                    'maxlength="'.TAG_MAX_LENGTH.'" size="'.TAG_MAX_LENGTH.'"');
        }

        $mform->addElement('editor', 'description_editor', get_string('description', 'tag'), null, $this->_customdata['editoroptions']);

        if (has_capability('moodle/tag:manage', $systemcontext)) {
           $mform->addElement('checkbox', 'tagtype', get_string('officialtag', 'tag'));
        }

        $mform->addElement('html', '<br/><div id="relatedtags-autocomplete-container">');
        $mform->addElement('textarea', 'relatedtags', get_string('relatedtags','tag'), 'cols="50" rows="3"');
        $mform->setType('relatedtags', PARAM_TAGLIST);
        $mform->addElement('html', '<div id="relatedtags-autocomplete"></div>');
        $mform->addElement('html', '</div>');

        $this->add_action_buttons(false, get_string('updatetag', 'tag'));

    }

}