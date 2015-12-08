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

use moodleform;
require_once($CFG->libdir.'/formslib.php');

/**
 * User evidence form class.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_evidence extends moodleform {

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'generalhdr', get_string('general'));

        $mform->addElement('text', 'name', get_string('userevidencename', 'tool_lp'), 'maxlength="100"');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('editor', 'description', get_string('userevidencedescription', 'tool_lp'), array('rows' => 10));
        // TODO MDL-52454 Make PARAM_RAW.
        $mform->setType('description', PARAM_TEXT);

        $mform->addElement('url', 'url', get_string('userevidenceurl', 'tool_lp'), array(), array('usefilepicker' => false));
        $mform->setType('url', PARAM_URL);

        $mform->addElement('filemanager', 'files', get_string('userevidencefiles', 'tool_lp'), array(),
            $this->_customdata['fileareaoptions']);

        $this->add_action_buttons();
    }

    /**
     * Get form data.
     * Conveniently removes non-desired properties.
     * @return object
     */
    public function get_data() {
        $data = parent::get_data();
        if (is_object($data)) {
            unset($data->submitbutton);
        }
        return $data;
    }

    /**
     * Extra validation the form.
     *
     * @param  array $data
     * @param  array $files
     * @return array
     */
    public function validation($data, $files) {
        $data = $this->get_submitted_data();        // To remove extra fields (sesskey, __qf_, ...).
        unset($data->submitbutton);
        unset($data->files);

        $data->descriptionformat = $data->description['format'];
        $data->description = $data->description['text'];
        $data->userid = $this->_customdata['userid'];
        $data->id = $this->_customdata['id'];

        $template = new \tool_lp\user_evidence(0, $data);
        $errors = $template->get_errors();

        return $errors;
    }

}
