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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_microlearning\forms;

defined('MOODLE_INTERNAL') || die;

use \moodleform;

class nugget_edit_form extends \moodleform {

    public function __construct($actionurl, $threadid, $nuggetid = 0) {
        global $DB;

        $nuggetcount = $DB->count_records('microlearning_nugget', array('threadid' => $threadid));
        if (empty($nuggetid)) {
            // We are adding so count is whatever is there plus this one.
            $nuggetcount++;
        }
        $this->orderselect = array();
        $count = 1;
        while ($count <= $nuggetcount) {
            $this->orderselect[$count - 1] = $count;
            $count++;
        }

        parent::__construct($actionurl);
    }


    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'threadid');
        $mform->setType('threadid', PARAM_INT);

        $mform->addElement('text', 'name',
                            get_string('nuggetname', 'block_iomad_microlearning'),
                            'maxlength = "254" size = "50"');
        $mform->addHelpButton('name', 'nuggetname', 'block_iomad_microlearning');
        $mform->addRule('name',
                        get_string('missingname', 'block_iomad_microlearning'),
                        'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('text', 'sectionid',
                            get_string('sectionid', 'block_iomad_microlearning'));
        $mform->addHelpButton('sectionid', 'sectionid', 'block_iomad_microlearning');
        $mform->setType('sectionid', PARAM_INT);

        $mform->addElement('text', 'cmid',
                            get_string('cmid', 'block_iomad_microlearning'));
        $mform->addHelpButton('cmid', 'cmid', 'block_iomad_microlearning');
        $mform->setType('cmid', PARAM_INT);

        $mform->addElement('text', 'url',
                            get_string('url', 'block_iomad_microlearning'));
        $mform->addHelpButton('url', 'url', 'block_iomad_microlearning');
        $mform->setType('url', PARAM_URL);

        $mform->addElement('hidden', 'halt_until_fulfilled');
        $mform->setType('halt_until_fulfilled', PARAM_INT);

        $mform->addElement('hidden', 'nuggetorder');
        $mform->setType('nuggetorder', PARAM_INT);

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        global $CFG, $DB;

        $errors = array();

        if ($nuggetbyname = $DB->get_record('microlearning_nugget', array('threadid' => $data['threadid'], 'name' => trim($data['name'])))) {
            if ($nuggetbyname->id != $data['id']) {
                $errors['name'] = get_string('nameinuse', 'block_iomad_microlearning');
            }
        }
        if (empty($data['sectionid']) && empty($data['cmid']) && empty($data['url'])) {
            $errors['sectionid'] = get_string('missingsectionorcmid', 'block_iomad_microlearning');
        }
        if (!empty($data['cmid']) && $DB->get_records_sql("SELECT id FROM {microlearning_nugget}
                                                          WHERE threadid = :threadid
                                                          AND cmid = :cmid
                                                          AND id != :id", $data)) {
            $errors['cmid'] = get_string('cmidalreadyinuse', 'block_iomad_microlearning');
        } else if (!empty($data['sectionid']) && $DB->get_records_sql("SELECT id FROM {microlearning_nugget}
                                                          WHERE threadid = :threadid
                                                          AND sectionid = :sectionid
                                                          AND id != :id", $data)) {
            $errors['cmid'] = get_string('sectionidalreadyinuse', 'block_iomad_microlearning');
        }
        if (!empty($data['url']) && strpos($data['url'], $CFG->wwwroot) === false) {
            $errors['url'] = get_string('incorrecturl', 'block_iomad_microlearning');
        }
        return $errors;
    }
}
