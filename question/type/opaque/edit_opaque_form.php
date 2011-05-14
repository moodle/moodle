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
 * Defines the editing form for the Opaque question type.
 *
 * @package    qtype
 * @subpackage opaque
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/locallib.php');


/**
 * Form definition base class. This defines the common fields that
 * all question types need. Question types should define their own
 * class that inherits from this one, and implements the definition_inner()
 * method.
 *
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_opaque_edit_form extends question_edit_form {
    protected function definition() {
        parent::definition();
        $mform = $this->_form;
        $mform->removeElement('questiontext');
        $mform->removeElement('generalfeedback');
        $mform->removeElement('defaultmark');
        $mform->addElement('hidden', 'defaultmark');
        $mform->setType('defaultmark', PARAM_INT);
        $mform->setDefault('defaultmark', 1);
    }

    protected function definition_inner($mform) {
        $mform->addElement('select', 'engineid', get_string('questionengine', 'qtype_opaque'),
                qtype_opaque_installed_engine_choices());
        $mform->setType('engineid', PARAM_INT);
        $mform->addRule('engineid', null, 'required', null, 'client');
        $mform->addHelpButton('engineid', 'questionengine', 'qtype_opaque');

        $mform->addElement('text', 'remoteid',
                get_string('questionid', 'qtype_opaque'), array('size' => 50));
        $mform->setType('remoteid', PARAM_RAW);
        $mform->addRule('remoteid', null, 'required', null, 'client');
        $mform->addHelpButton('remoteid', 'questionid', 'qtype_opaque');

        $mform->addElement('text', 'remoteversion',
                get_string('questionversion', 'qtype_opaque'), array('size' => 3));
        $mform->setType('remoteversion', PARAM_RAW);
        $mform->addRule('remoteversion', null, 'required', null, 'client');
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check we can connect to this questoin engine.
        $engine = qtype_opaque_load_engine_def($data['engineid']);
        if (is_string($engine)) {
            $errors['engineid'] = $engine;
        }

        $remoteidok = true;
        $partregexp = '[_a-z][_a-zA-Z0-9]*';
        if (!preg_match("/^$partregexp(\\.$partregexp)*\$/", $data['remoteid'])) {
            $errors['remoteid'] = get_string('invalidquestionidsyntax', 'qtype_opaque');
            $remoteidok = false;
        }
        if (!preg_match('/^\d+\.\d+$/', $data['remoteversion'])) {
            $errors['remoteversion'] = get_string('invalidquestionversionsyntax', 'qtype_opaque');
            $remoteidok = false;
        }

        // Try connecting to the remote question engine both as extra validation of the id, and
        // also to get the default grade.
        if ($remoteidok) {
            $metadata = qtype_opaque_get_question_metadata($engine,
                    $data['remoteid'], $data['remoteversion']);
            if (is_string($metadata)) {
                $errors['remoteid'] = $metadata;
            } else if (!isset($metadata['questionmetadata']['#']['scoring']
                    [0]['#']['marks'][0]['#'])) {
                $errors['remoteid'] = get_string('maxgradenotreturned');
            } else {
                $this->_defaultmark = $metadata['questionmetadata']['#']['scoring']
                        [0]['#']['marks'][0]['#'];
            }
        }

        return $errors;
    }

    public function get_data($slashed = true) {
        // We override get_data to to add the defaultmark, which was determined
        // during validation, to the data that is returned.
        $data = parent::get_data($slashed);
        if (is_object($data) && isset($this->_defaultmark)) {
            $data->defaultmark = $this->_defaultmark;
        }
        return $data;
    }

    public function qtype() {
        return 'opaque';
    }
}
