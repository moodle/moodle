<?php
// This file is part of Book module for Moodle - http://moodle.org/
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
 * Chapter edit form
 *
 * @package    mod
 * @subpackage book
 * @copyright  2004-2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

class book_chapter_edit_form extends moodleform {

    function definition() {
        global $CFG;
        $mform = $this->_form;
        $cm    = $this->_customdata;

        $mform->addElement('header', 'general', get_string('edit'));

        $mform->addElement('text', 'title', get_string('chaptertitle', 'book'), array('size'=>'30'));
        $mform->setType('title', PARAM_RAW);
        $mform->addRule('title', null, 'required', null, 'client');

        $mform->addElement('advcheckbox', 'subchapter', get_string('subchapter', 'book'));

        $mform->addElement('htmleditor', 'content', get_string('content', 'book'), array('cols'=>50, 'rows'=>30));
        $mform->setType('content', PARAM_RAW);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);

        $mform->addElement('hidden', 'pagenum');
        $mform->setType('pagenum', PARAM_INT);

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        if (false and has_capability('mod/book:import', $context)) { //TODO: after files
            $mform->addElement('static', 'doimport', get_string('importingchapters', 'book').':', '<a href="import.php?id='.$cm->id.'">'.get_string('doimport', 'book').'</a>');
        }

        $this->add_action_buttons(true);
    }

    function definition_after_data() {
        global $CFG;
        $mform = $this->_form;

        if ($mform->getElementValue('id')) {
            if ($mform->elementExists('doimport')) {
                $mform->removeElement('doimport');
            }
        }
    }
}
