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
 * Settings form for extracttestcase.php.
 *
 * @package    tool
 * @subpackage qeupgradehelper
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * Options form.
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_qeupgradehelper_extract_options_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $behaviour = array(
            'deferredfeedback' => 'Deferred feedback',
            'adaptive' => 'Adaptive',
            'adaptivenopenalty' => 'Adaptive (no penalties)',
        );

        $qtypes = get_plugin_list('qtype');
        foreach ($qtypes as $qtype => $notused) {
            $qtypes[$qtype] = get_string($qtype, 'qtype_' . $qtype);
        }

        $mform->addElement('header', 'h1', 'Either extract a specific question_session');
        $mform->addElement('text', 'attemptid', 'Quiz attempt id', array('size' => '10'));
        $mform->setType('attemptid', PARAM_INT);

        $mform->addElement('text', 'questionid', 'Question id', array('size' => '10'));
        $mform->setType('questionid', PARAM_INT);

        $mform->addElement('header', 'h2', 'Or find and extract an example by type');
        $mform->addElement('select', 'behaviour', 'Behaviour', $behaviour);
        $mform->setType('behaviour', PARAM_ALPHA);

        $mform->addElement('text', 'statehistory', 'State history', array('size' => '10'));
        $mform->setType('statehistory', PARAM_RAW);

        $mform->addElement('select', 'qtype', 'Question type', $qtypes);
        $mform->setType('qtype', PARAM_PLUGIN);

        $mform->addElement('text', 'extratests', 'Extra conditions', array('size' => '50'));
        $mform->setType('extratests', PARAM_RAW);
        $this->add_action_buttons(false, 'Create test case');
    }
}
