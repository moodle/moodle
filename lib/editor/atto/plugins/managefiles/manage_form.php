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
 * Atto text editor manage files plugin form.
 *
 * @package   atto_managefiles
 * @copyright 2014 Frédéric Massart
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir."/formslib.php");

/**
 * Form allowing to edit files in one draft area.
 *
 * No buttons are necessary since the draft area files are saved immediately using AJAX.
 *
 * @package   atto_managefiles
 * @copyright 2014 Frédéric Massart
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class atto_managefiles_manage_form extends moodleform {

    function definition() {
        global $PAGE, $USER;
        $mform = $this->_form;

        $mform->setDisableShortforms(true);

        $itemid = $this->_customdata['draftitemid'];
        $elementid = $this->_customdata['elementid'];
        $options = $this->_customdata['options'];
        $files = $this->_customdata['files'];

        $mform->addElement('header', 'filemanagerhdr', get_string('filemanager', 'atto_managefiles'));

        $mform->addElement('hidden', 'itemid');
        $mform->setType('itemid', PARAM_INT);
        $mform->addElement('hidden', 'maxbytes');
        $mform->setType('maxbytes', PARAM_INT);
        $mform->addElement('hidden', 'subdirs');
        $mform->setType('subdirs', PARAM_INT);
        $mform->addElement('hidden', 'accepted_types');
        $mform->setType('accepted_types', PARAM_RAW);
        $mform->addElement('hidden', 'return_types');
        $mform->setType('return_types', PARAM_INT);
        $mform->addElement('hidden', 'context');
        $mform->setType('context', PARAM_INT);
        $mform->addElement('hidden', 'areamaxbytes');
        $mform->setType('areamaxbytes', PARAM_INT);
        $mform->addElement('hidden', 'elementid');
        $mform->setType('elementid', PARAM_TEXT);

        $mform->addElement('filemanager', 'files_filemanager', '', null, $options);

        $mform->addElement('header', 'missingfileshdr', get_string('missingfiles', 'atto_managefiles'));
        $mform->addElement('static', '', '',
            html_writer::tag('div',
                html_writer::tag('div', get_string('hasmissingfiles', 'atto_managefiles')) .
                html_writer::tag('div', '', array('class' => 'missing-files')
            ),
            array('class' => 'file-status'))
        );

        $mform->addElement('header', 'deletefileshdr', get_string('unusedfilesheader', 'atto_managefiles'));
        $mform->addElement('static', '', '',
            html_writer::tag('div', get_string('unusedfilesdesc', 'atto_managefiles')));

        foreach ($files as $hash => $file) {
            $mform->addElement('checkbox', 'deletefile[' . $hash . ']', '', $file, array('data-filename' => $file));
            $mform->setType('deletefile[' . $hash . ']', PARAM_INT);
        }

        $mform->addElement('submit', 'delete', get_string('deleteselected', 'atto_managefiles'));

        $PAGE->requires->yui_module('moodle-atto_managefiles-usedfiles', 'M.atto_managefiles.usedfiles.init',
            array(array(
                'files' => array_flip($files),
                'usercontext' => context_user::instance($USER->id)->id,
                'itemid' => $itemid,
                'elementid' => $elementid,
            )));

        $this->set_data(array(
            'files_filemanager' => $itemid,
            'itemid' => $itemid,
            'elementid' => $elementid,
            'subdirs' => $options['subdirs'],
            'maxbytes' => $options['maxbytes'],
            'areamaxbytes' => $options['areamaxbytes'],
            'accepted_types' => $options['accepted_types'],
            'return_types' => $options['return_types'],
            'context' => $options['context']->id,
        ));
    }
}
