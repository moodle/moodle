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
 * User evidence form.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp\form;
defined('MOODLE_INTERNAL') || die();

/**
 * User evidence form class.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_evidence extends persistent {

    protected static $persistentclass = 'core_competency\\user_evidence';

    protected static $foreignfields = array('files');

    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);
        $mform->setConstant('userid', $this->_customdata['userid']);

        $mform->addElement('header', 'generalhdr', get_string('general'));

        // Name.
        $mform->addElement('text', 'name', get_string('userevidencename', 'tool_lp'), 'maxlength="100"');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
        // Description.
        $mform->addElement('editor', 'description', get_string('userevidencedescription', 'tool_lp'), array('rows' => 10));
        $mform->setType('description', PARAM_CLEANHTML);

        $mform->addElement('url', 'url', get_string('userevidenceurl', 'tool_lp'), array('size' => '60'), array('usefilepicker' => false));
        $mform->setType('url', PARAM_RAW_TRIMMED);      // Can not use PARAM_URL, it silently converts bad URLs to ''.
        $mform->addHelpButton('url', 'userevidenceurl', 'tool_lp');

        $mform->addElement('filemanager', 'files', get_string('userevidencefiles', 'tool_lp'), array(),
            $this->_customdata['fileareaoptions']);
        // Disable short forms.
        $mform->setDisableShortforms();

        $this->add_action_buttons();
    }

}
